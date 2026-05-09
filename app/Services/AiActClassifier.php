<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AiUsage;
use App\Models\Assessment;
use Illuminate\Support\Carbon;

class AiActClassifier
{
    /**
     * Classifie un AiUsage selon la matrice de config/ai_act_rules.php.
     *
     * - Hydrate les données nécessaires (responses indexées par variable_key).
     * - Évalue les règles dans l'ordre du fichier (priorité = ordre).
     * - Retourne le PREMIER match (la matrice est rangée du plus sévère au plus laxiste).
     * - Collecte des "alertes" indépendamment du niveau choisi (signaux RGPD/data sensible).
     *
     * Renvoie un array compatible avec Assessment::fillable, sans persistance.
     * La persistance est gérée par persist() pour permettre dry-run en test.
     *
     * @return array<string, mixed>
     */
    public function classify(AiUsage $aiUsage): array
    {
        $responses = $aiUsage->responses->pluck('variable_value', 'variable_key')->toArray();
        $rules = config('ai_act_rules');

        $matched = null;
        foreach ($rules as $rule) {
            if ($this->ruleMatches($rule, $aiUsage, $responses)) {
                $matched = $rule;
                break;
            }
        }

        // Le fallback `RISQUE_MINIMAL` (when => []) garantit qu'on a toujours un match,
        // mais si jamais la config est vidée, on dégrade proprement.
        $matched ??= [
            'id' => 'no_rule',
            'niveau' => 'RISQUE_MINIMAL',
            'article' => 'N/A',
            'type_regle' => 'NA',
            'raison' => 'Aucune règle applicable trouvée.',
        ];

        return [
            'niveau' => $matched['niveau'],
            'regle_id' => $matched['id'],
            'article' => $matched['article'],
            'raison' => $matched['raison'],
            'alertes' => $this->collectAlerts($aiUsage, $responses),
            'type_regle' => $matched['type_regle'],
            'computed_at' => Carbon::now(),
        ];
    }

    /**
     * Calcule + persiste l'évaluation. Remplace toute évaluation antérieure :
     * un seul Assessment "courant" par AiUsage suffit pour la version PME.
     */
    public function persist(AiUsage $aiUsage): Assessment
    {
        $payload = $this->classify($aiUsage);
        $aiUsage->assessments()->delete();

        return $aiUsage->assessments()->create($payload);
    }

    /**
     * @param  array<string, mixed>  $rule
     * @param  array<string, string>  $responses
     */
    private function ruleMatches(array $rule, AiUsage $aiUsage, array $responses): bool
    {
        $conditions = $rule['when'] ?? [];

        // Pas de condition = match permanent (cas du fallback RISQUE_MINIMAL).
        if ($conditions === []) {
            return true;
        }

        foreach ($conditions as $key => $expected) {
            $actual = $this->resolveField($key, $aiUsage, $responses);
            if ($actual !== $expected) {
                return false;
            }
        }

        return true;
    }

    /**
     * Lit une clé dot-notation depuis l'AiUsage ou ses réponses.
     * Format supporté :
     *   - ai_usage.<champ>     ex: ai_usage.type, ai_usage.domain
     *   - response.<key>       ex: response.bio_realtime
     *
     * @param  array<string, string>  $responses
     */
    private function resolveField(string $path, AiUsage $aiUsage, array $responses): ?string
    {
        [$scope, $key] = array_pad(explode('.', $path, 2), 2, null);

        return match ($scope) {
            'ai_usage' => $aiUsage->{$key} ?? null,
            'response' => $responses[$key] ?? null,
            default => null,
        };
    }

    /**
     * Alertes transversales (RGPD, biais, transparence) — affichées en plus
     * du niveau pour aider la PME à prioriser ses actions.
     *
     * @param  array<string, string>  $responses
     * @return array<int, array<string, string>>
     */
    private function collectAlerts(AiUsage $aiUsage, array $responses): array
    {
        $alerts = [];

        if (($responses['data_sensitive'] ?? null) === 'yes') {
            $alerts[] = [
                'code' => 'rgpd_art9',
                'message' => 'Traitement de données sensibles : analyse d\'impact (DPIA) RGPD obligatoire.',
            ];
        }

        if (($responses['data_personal'] ?? null) === 'yes'
            && ($responses['human_oversight'] ?? null) === 'never') {
            $alerts[] = [
                'code' => 'rgpd_art22',
                'message' => 'Décision entièrement automatisée sur données personnelles : Article 22 RGPD applicable.',
            ];
        }

        if ($aiUsage->type === 'LLM_GEN'
            && ($responses['llm_output_published'] ?? null) === 'yes') {
            $alerts[] = [
                'code' => 'llm_no_review',
                'message' => 'Sorties LLM publiées sans relecture humaine : risque de désinformation et d\'hallucinations.',
            ];
        }

        if ($aiUsage->type === 'IA_GEN'
            && ($responses['gen_disclosure'] ?? null) === 'never') {
            $alerts[] = [
                'code' => 'no_ai_disclosure',
                'message' => 'Contenus IA non étiquetés : non-conformité Article 50 (transparence).',
            ];
        }

        return $alerts;
    }
}
