<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Assessment;
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
        $heatmapWeights = ['INACCEPTABLE' => 1000, 'HAUT_RISQUE' => 100, 'RISQUE_LIMITE' => 10, 'RISQUE_MINIMAL' => 1];

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
                    'x' => $dom,
                    'y' => $lvl,
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

        // ── Timeline 6 mois : usages déclarés, évaluations, rapports ──
        $timelineLabels = [];
        $timelineKeys = [];
        $usagesByMonth = array_fill(0, 6, 0);
        $assessmentsByMonth = array_fill(0, 6, 0);
        $reportsByMonth = array_fill(0, 6, 0);

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->startOfMonth();
            $timelineLabels[] = $month->translatedFormat('M Y');
            $timelineKeys[] = $month->format('Y-m');
        }
        $indexByKey = array_flip($timelineKeys);

        if ($organization) {
            $sixMonthsAgo = now()->subMonths(5)->startOfMonth();

            // 1. Usages déclarés (ai_usages.created_at)
            $usagesAgg = $organization->aiUsages()
                ->where('created_at', '>=', $sixMonthsAgo)
                ->selectRaw("strftime('%Y-%m', created_at) as period, COUNT(*) as total")
                ->groupBy('period')
                ->pluck('total', 'period');
            foreach ($usagesAgg as $period => $total) {
                if (isset($indexByKey[$period])) {
                    $usagesByMonth[$indexByKey[$period]] = (int) $total;
                }
            }

            // 2. Évaluations (assessments.computed_at, JOIN ai_usages pour filtre tenant)
            $assessmentsAgg = Assessment::query()
                ->join('ai_usages', 'assessments.ai_usage_id', '=', 'ai_usages.id')
                ->where('ai_usages.organization_id', $organization->id)
                ->where('assessments.computed_at', '>=', $sixMonthsAgo)
                ->selectRaw("strftime('%Y-%m', assessments.computed_at) as period, COUNT(*) as total")
                ->groupBy('period')
                ->pluck('total', 'period');
            foreach ($assessmentsAgg as $period => $total) {
                if (isset($indexByKey[$period])) {
                    $assessmentsByMonth[$indexByKey[$period]] = (int) $total;
                }
            }

            // 3. Rapports (reports.created_at)
            $reportsAgg = $organization->reports()
                ->where('created_at', '>=', $sixMonthsAgo)
                ->selectRaw("strftime('%Y-%m', created_at) as period, COUNT(*) as total")
                ->groupBy('period')
                ->pluck('total', 'period');
            foreach ($reportsAgg as $period => $total) {
                if (isset($indexByKey[$period])) {
                    $reportsByMonth[$indexByKey[$period]] = (int) $total;
                }
            }
        }

        $totalActivity = array_sum($usagesByMonth) + array_sum($assessmentsByMonth) + array_sum($reportsByMonth);

        $activityTimeline = [
            'labels' => $timelineLabels,
            'usages' => $usagesByMonth,
            'assessments' => $assessmentsByMonth,
            'reports' => $reportsByMonth,
            'hasData' => $totalActivity > 0,
        ];

        return view('dashboard', [
            'organization' => $organization,
            'aiUsages' => $aiUsages,
            'heatmap' => $heatmap,
            'activityTimeline' => $activityTimeline,
        ]);
    }
}
