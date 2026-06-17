<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Carbon;

class ComplianceTimelineBuilder
{
    public function __construct(private AiActClassifier $classifier) {}

    /**
     * Construit une projection prospective de la conformité d'une organisation
     * à trois horizons : aujourd'hui, T+1 an et T+2 ans.
     *
     * Pour chaque horizon, on reclassifie tous les usages avec le filtre
     * temporel actif (les règles non encore applicables sont ignorées). On
     * en déduit le compteur de niveaux et la liste des usages qui basculent
     * (apparition d'un niveau plus sévère par rapport à l'horizon précédent).
     *
     * Le rapport peut ainsi avertir le déployeur : « Aujourd'hui votre IA est
     * RISQUE_MINIMAL ; au 2 août 2026 elle deviendra HAUT_RISQUE. »
     *
     * @return array<int, array<string, mixed>>
     */
    public function build(Organization $organization, ?Carbon $now = null): array
    {
        $now ??= Carbon::now();

        $horizons = [
            ['label' => 'now', 'at' => $now->copy()],
            ['label' => 'plus_1y', 'at' => $now->copy()->addYear()],
            ['label' => 'plus_2y', 'at' => $now->copy()->addYears(2)],
        ];

        $organization->load('aiUsages.responses');

        $previousLevels = null;
        $snapshots = [];

        foreach ($horizons as $horizon) {
            $byUsage = [];
            $counts = $this->emptyCounts();

            foreach ($organization->aiUsages as $usage) {
                $result = $this->classifier->classify($usage, $horizon['at']);
                $niveau = $result['niveau'];
                $counts[$niveau] = ($counts[$niveau] ?? 0) + 1;

                $byUsage[$usage->id] = [
                    'usage_id' => $usage->id,
                    'name' => $usage->name,
                    'niveau' => $niveau,
                    'regle_id' => $result['regle_id'],
                ];
            }

            $transitions = $previousLevels === null
                ? []
                : $this->computeTransitions($previousLevels, $byUsage);

            $snapshots[] = [
                'label' => $horizon['label'],
                'date' => $horizon['at']->toIso8601String(),
                'counts' => $counts,
                'transitions' => $transitions,
            ];

            $previousLevels = $byUsage;
        }

        return $snapshots;
    }

    /**
     * Compare deux passes pour identifier les usages dont le niveau de risque
     * s'aggrave (ex : RISQUE_MINIMAL → HAUT_RISQUE à l'entrée en vigueur du
     * 2 août 2026). On ignore les transitions descendantes — elles ne peuvent
     * de toute façon pas se produire puisque une règle ne devient jamais
     * inapplicable rétroactivement dans la matrice actuelle.
     *
     * @param  array<int, array<string, mixed>>  $previous
     * @param  array<int, array<string, mixed>>  $current
     * @return array<int, array<string, mixed>>
     */
    private function computeTransitions(array $previous, array $current): array
    {
        $severity = [
            'NON_EVALUE' => 0,
            'RISQUE_MINIMAL' => 1,
            'RISQUE_LIMITE' => 2,
            'HAUT_RISQUE' => 3,
            'INACCEPTABLE' => 4,
        ];

        $transitions = [];

        foreach ($current as $usageId => $entry) {
            $before = $previous[$usageId]['niveau'] ?? 'NON_EVALUE';
            $after = $entry['niveau'];

            if (($severity[$after] ?? 0) > ($severity[$before] ?? 0)) {
                $transitions[] = [
                    'usage_id' => $usageId,
                    'name' => $entry['name'],
                    'from' => $before,
                    'to' => $after,
                    'regle_id' => $entry['regle_id'],
                ];
            }
        }

        return $transitions;
    }

    /**
     * @return array<string, int>
     */
    private function emptyCounts(): array
    {
        return [
            'INACCEPTABLE' => 0,
            'HAUT_RISQUE' => 0,
            'RISQUE_LIMITE' => 0,
            'RISQUE_MINIMAL' => 0,
            'NON_EVALUE' => 0,
        ];
    }
}
