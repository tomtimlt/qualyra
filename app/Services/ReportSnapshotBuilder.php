<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Organization;

class ReportSnapshotBuilder
{
    /**
     * Construit un snapshot figé de l'état de conformité d'une organisation
     * (organisation + tous ses usages + dernier assessment + réponses).
     * Le snapshot est persisté tel quel sur Report::snapshot pour rester
     * lisible même si les usages sous-jacents évoluent ou sont supprimés.
     *
     * @return array<string, mixed>
     */
    public function build(Organization $organization): array
    {
        $organization->load([
            'aiUsages.responses',
            'aiUsages.assessments' => fn ($q) => $q->latest('computed_at')->limit(1),
        ]);

        $usages = $organization->aiUsages->map(function ($usage) {
            $assessment = $usage->assessments->first();

            return [
                'id' => $usage->id,
                'name' => $usage->name,
                'description' => $usage->description,
                'type' => $usage->type,
                'domain' => $usage->domain,
                'responses' => $usage->responses
                    ->pluck('variable_value', 'variable_key')
                    ->toArray(),
                'assessment' => $assessment ? [
                    'niveau' => $assessment->niveau,
                    'regle_id' => $assessment->regle_id,
                    'article' => $assessment->article,
                    'raison' => $assessment->raison,
                    'alertes' => $assessment->alertes,
                    'type_regle' => $assessment->type_regle,
                    'computed_at' => $assessment->computed_at?->toIso8601String(),
                ] : null,
            ];
        })->values()->all();

        return [
            'generated_at' => now()->toIso8601String(),
            'organization' => [
                'name' => $organization->name,
                'siret' => $organization->siret,
                'size' => $organization->size,
                'sector' => $organization->sector,
            ],
            'usages' => $usages,
            'summary' => $this->summarize($usages),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $usages
     * @return array<string, int>
     */
    private function summarize(array $usages): array
    {
        $counts = [
            'INACCEPTABLE' => 0,
            'HAUT_RISQUE' => 0,
            'RISQUE_LIMITE' => 0,
            'RISQUE_MINIMAL' => 0,
            'NON_EVALUE' => 0,
        ];

        foreach ($usages as $usage) {
            $niveau = $usage['assessment']['niveau'] ?? 'NON_EVALUE';
            $counts[$niveau] = ($counts[$niveau] ?? 0) + 1;
        }

        return $counts;
    }
}
