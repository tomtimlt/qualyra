<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class VisionController extends Controller
{
    public function __invoke(Request $request): View
    {
        $organization = $request->user()->organization;

        $aiUsages = $organization
            ? $organization->aiUsages()->latest()->get()
            : collect();

        $niveauLabels = [
            'INACCEPTABLE' => 'Inacceptable',
            'HAUT_RISQUE' => 'Haut risque',
            'RISQUE_LIMITE' => 'Risque limité',
            'RISQUE_MINIMAL' => 'Risque minimal',
            'NON_EVAL' => 'Non évalué',
        ];
        $domainLabels = [
            'RH' => 'Ressources humaines', 'EDUCATION' => 'Éducation', 'CREDIT' => 'Crédit',
            'SANTE' => 'Santé', 'SECURITE' => 'Sécurité', 'MARKETING' => 'Marketing',
            'PROD_INT' => 'Productivité interne', 'DEV_LOG' => 'Développement', 'AUTRE' => 'Autre',
        ];
        $typeLabels = [
            'LLM_GEN' => 'LLM génératif',
            'IA_GEN' => 'IA générative',
            'IA_SCORING' => 'IA de scoring',
            'IA_BIO' => 'IA biométrique',
            'AUTRE' => 'Autre',
        ];

        $heatmapDomains = ['RH', 'EDUCATION', 'CREDIT', 'SANTE', 'SECURITE', 'MARKETING', 'PROD_INT', 'DEV_LOG', 'AUTRE'];
        $heatmapTypes = ['LLM_GEN', 'IA_GEN', 'IA_SCORING', 'IA_BIO', 'AUTRE'];
        $heatmapLevels = ['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL', 'NON_EVAL'];
        $niveauPriority = ['INACCEPTABLE' => 4, 'HAUT_RISQUE' => 3, 'RISQUE_LIMITE' => 2, 'RISQUE_MINIMAL' => 1, 'NON_EVAL' => 0];

        // ── Matrix : Domain × Type (couleur = pire risque, opacité = volume) ──
        $cells = [];
        foreach ($heatmapDomains as $dom) {
            foreach ($heatmapTypes as $type) {
                $cells[$dom][$type] = ['count' => 0, 'worstNiveau' => 'NON_EVAL', 'breakdown' => array_fill_keys($heatmapLevels, 0), 'usages' => []];
            }
        }

        $maxCount = 0;

        foreach ($aiUsages as $usage) {
            $latestAssessment = $usage->assessments()->latest('computed_at')->first();
            $niveau = $latestAssessment?->niveau ?? 'NON_EVAL';
            $domain = $usage->domain;
            $type = $usage->type;

            if (!in_array($domain, $heatmapDomains, true) || !in_array($type, $heatmapTypes, true)) {
                continue;
            }

            $cells[$domain][$type]['count']++;
            $cells[$domain][$type]['breakdown'][$niveau]++;

            if (($niveauPriority[$niveau] ?? 0) > ($niveauPriority[$cells[$domain][$type]['worstNiveau']] ?? 0)) {
                $cells[$domain][$type]['worstNiveau'] = $niveau;
            }

            if (count($cells[$domain][$type]['usages']) < 20) {
                $cells[$domain][$type]['usages'][] = [
                    'id' => $usage->id,
                    'name' => $usage->name,
                    'niveau' => $niveau,
                ];
            }

            if ($cells[$domain][$type]['count'] > $maxCount) {
                $maxCount = $cells[$domain][$type]['count'];
            }
        }

        $matrixData = [];
        foreach ($heatmapDomains as $dom) {
            foreach ($heatmapTypes as $type) {
                $cell = $cells[$dom][$type];
                $matrixData[] = [
                    'x' => $type,
                    'y' => $dom,
                    'count' => $cell['count'],
                    'worstNiveau' => $cell['worstNiveau'],
                    'breakdown' => $cell['breakdown'],
                    'usages' => $cell['usages'],
                ];
            }
        }

        $matrix = [
            'domains' => $heatmapDomains,
            'types' => $heatmapTypes,
            'domainLabels' => $domainLabels,
            'typeLabels' => $typeLabels,
            'niveauLabels' => $niveauLabels,
            'cells' => $matrixData,
            'maxCount' => $maxCount,
        ];

        // ── Sankey : Domaine → Type → Risque ──
        $sankeyNodes = [];
        $sankeyNodeIds = [];
        $sankeyLinks = [];
        $sankeyLevelLabels = [];

        // Domain nodes (layer 0)
        foreach ($heatmapDomains as $dom) {
            $id = 'D:' . $dom;
            $sankeyNodes[] = ['id' => $id, 'name' => $domainLabels[$dom] ?? $dom, 'layer' => 0, 'code' => $dom, 'label' => 'domain'];
            $sankeyNodeIds[$id] = true;
        }

        // Type nodes (layer 1)
        foreach ($heatmapTypes as $type) {
            $id = 'T:' . $type;
            $sankeyNodes[] = ['id' => $id, 'name' => $typeLabels[$type] ?? $type, 'layer' => 1, 'code' => $type, 'label' => 'type'];
            $sankeyNodeIds[$id] = true;
        }

        // Risk nodes (layer 2)
        foreach ($heatmapLevels as $lvl) {
            $id = 'R:' . $lvl;
            $sankeyNodes[] = ['id' => $id, 'name' => $niveauLabels[$lvl] ?? $lvl, 'layer' => 2, 'code' => $lvl, 'label' => 'niveau'];
            $sankeyNodeIds[$id] = true;
            $sankeyLevelLabels[$lvl] = $niveauLabels[$lvl] ?? $lvl;
        }

        // Aggregate links: Domain → Type
        $dtLinks = [];
        foreach ($aiUsages as $usage) {
            $domain = $usage->domain;
            $type = $usage->type;
            $latestAssessment = $usage->assessments()->latest('computed_at')->first();
            $niveau = $latestAssessment?->niveau ?? 'NON_EVAL';

            if (!in_array($domain, $heatmapDomains, true) || !in_array($type, $heatmapTypes, true)) continue;

            $key = 'D:' . $domain . '→T:' . $type;
            if (!isset($dtLinks[$key])) {
                $dtLinks[$key] = ['count' => 0, 'worstNiveau' => 'NON_EVAL'];
            }
            $dtLinks[$key]['count']++;
            if (($niveauPriority[$niveau] ?? 0) > ($niveauPriority[$dtLinks[$key]['worstNiveau']] ?? 0)) {
                $dtLinks[$key]['worstNiveau'] = $niveau;
            }
        }

        foreach ($dtLinks as $key => $data) {
            [$sourceId, $targetId] = explode('→', $key);
            $sankeyLinks[] = ['source' => $sourceId, 'target' => $targetId, 'value' => $data['count'], 'niveau' => $data['worstNiveau']];
        }

        // Aggregate links: Type → Niveau
        $tnLinks = [];
        foreach ($aiUsages as $usage) {
            $type = $usage->type;
            $latestAssessment = $usage->assessments()->latest('computed_at')->first();
            $niveau = $latestAssessment?->niveau ?? 'NON_EVAL';

            if (!in_array($type, $heatmapTypes, true) || !in_array($niveau, $heatmapLevels, true)) continue;

            $key = 'T:' . $type . '→R:' . $niveau;
            if (!isset($tnLinks[$key])) {
                $tnLinks[$key] = 0;
            }
            $tnLinks[$key]++;
        }

        foreach ($tnLinks as $key => $count) {
            [$sourceId, $targetId] = explode('→', $key);
            $niveau = str_replace('R:', '', $targetId);
            $sankeyLinks[] = ['source' => $sourceId, 'target' => $targetId, 'value' => $count, 'niveau' => $niveau];
        }

        $sankey = [
            'nodes' => $sankeyNodes,
            'links' => $sankeyLinks,
            'levelLabels' => $sankeyLevelLabels,
        ];

        // ── Graph réseau : un nœud par usage ──
        $graphNodes = $aiUsages->map(function ($u) use ($niveauLabels, $domainLabels, $typeLabels) {
            $latest = $u->assessments()->latest('computed_at')->first();
            $niveau = $latest?->niveau ?? 'NON_EVAL';
            return [
                'id' => $u->id,
                'name' => $u->name,
                'domain' => $u->domain,
                'domain_label' => $domainLabels[$u->domain] ?? $u->domain,
                'type' => $u->type,
                'type_label' => $typeLabels[$u->type] ?? $u->type,
                'niveau' => $niveau,
                'niveau_label' => $niveauLabels[$niveau] ?? $niveau,
                'url' => route('usages.show', $u),
            ];
        })->values();

        $graph = [
            'usages' => $graphNodes,
            'domainLabels' => $domainLabels,
            'typeLabels' => $typeLabels,
            'niveauLabels' => $niveauLabels,
        ];

        return view('vision', [
            'organization' => $organization,
            'aiUsages' => $aiUsages,
            'matrix' => $matrix,
            'sankey' => $sankey,
            'graph' => $graph,
        ]);
    }
}
