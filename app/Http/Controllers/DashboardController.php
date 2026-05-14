<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord.
     *
     * Si l'utilisateur n'a pas encore créé son organisation, on présente
     * un appel à l'action pour la créer. Sinon on affiche la liste de ses
     * usages d'IA déclarés.
     */
    public function __invoke(Request $request): View
    {
        $organization = $request->user()->organization;

        $aiUsages = $organization
            ? $organization->aiUsages()->latest()->get()
            : collect();

        // ── Heatmap "domaine × niveau de risque" (score pondéré) ──
        $heatmapDomains = ['RH', 'EDUCATION', 'CREDIT', 'SANTE', 'SECURITE', 'MARKETING', 'PROD_INT', 'DEV_LOG', 'AUTRE'];
        $heatmapLevels = ['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL'];
        $heatmapWeights = ['INACCEPTABLE' => 4, 'HAUT_RISQUE' => 3, 'RISQUE_LIMITE' => 2, 'RISQUE_MINIMAL' => 1];

        $flatUsages = [];
        $cells = [];

        foreach ($heatmapDomains as $dom) {
            $cells[$dom] = [];
            foreach ($heatmapLevels as $lvl) {
                $cells[$dom][$lvl] = ['count' => 0, 'score' => 0, 'recent' => []];
            }
        }

        foreach ($aiUsages as $usage) {
            $latestAssessment = $usage->assessments()->latest('computed_at')->first();
            $niveau = $latestAssessment?->niveau;
            $domain = $usage->domain;

            if (! in_array($niveau, $heatmapLevels, true) || ! in_array($domain, $heatmapDomains, true)) {
                continue;
            }

            $cells[$domain][$niveau]['count']++;
            $cells[$domain][$niveau]['score'] = $cells[$domain][$niveau]['count'] * $heatmapWeights[$niveau];

            if (count($cells[$domain][$niveau]['recent']) < 3) {
                $cells[$domain][$niveau]['recent'][] = $usage->name;
            }

            $flatUsages[] = [
                'id' => $usage->id,
                'name' => $usage->name,
                'domain' => $domain,
                'niveau' => $niveau,
            ];
        }

        $maxScore = 0;
        foreach ($cells as $row) {
            foreach ($row as $cell) {
                if ($cell['score'] > $maxScore) {
                    $maxScore = $cell['score'];
                }
            }
        }

        $matrixData = [];
        foreach ($heatmapDomains as $dom) {
            foreach ($heatmapLevels as $lvl) {
                $cell = $cells[$dom][$lvl];
                $matrixData[] = [
                    'x' => $lvl,
                    'y' => $dom,
                    'v' => $cell['score'],
                    'count' => $cell['count'],
                    'recent' => $cell['recent'],
                ];
            }
        }

        $heatmap = [
            'domains' => $heatmapDomains,
            'levels' => $heatmapLevels,
            'weights' => $heatmapWeights,
            'maxScore' => $maxScore,
            'matrix' => $matrixData,
            'allUsages' => $flatUsages,
        ];

        return view('dashboard', [
            'organization' => $organization,
            'aiUsages' => $aiUsages,
            'heatmap' => $heatmap,
        ]);
    }
}
