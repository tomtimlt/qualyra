<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Carbon;

class TemporalRuleEvaluator
{
    /**
     * Détermine si une règle de la matrice AI Act est applicable à une date
     * donnée selon son champ optionnel `applicable_from` (format YYYY-MM-DD).
     *
     * Une règle sans `applicable_from` est considérée toujours applicable
     * (cas du DEFAULT et de toute règle dont la date d'entrée en vigueur
     * n'est pas pertinente à filtrer).
     *
     * Une date `applicable_from` future par rapport à `$at` rend la règle
     * inopposable — le moteur la skip. Permet de produire deux audits :
     * la photo réglementaire d'aujourd'hui ET la projection à T+1y / T+2y.
     *
     * @param  array<string, mixed>  $rule
     */
    public function isApplicable(array $rule, Carbon $at): bool
    {
        $from = $rule['applicable_from'] ?? null;

        if ($from === null) {
            return true;
        }

        return $at->greaterThanOrEqualTo(Carbon::parse((string) $from));
    }
}
