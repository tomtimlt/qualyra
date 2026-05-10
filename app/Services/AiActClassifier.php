<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AiUsage;
use App\Models\Assessment;
use Illuminate\Support\Carbon;
use RuntimeException;

class AiActClassifier
{
    /**
     * Classifie un AiUsage selon la matrice config/ai_act_rules.php (v1.1).
     *
     * Algorithme :
     *   1. Si l'usage n'a aucune réponse → NON_EVALUE explicite (pas de
     *      fallback silencieux RISQUE_MINIMAL).
     *   2. On parcourt les règles dans l'ordre. La PREMIÈRE règle classificatoire
     *      qui matche fixe le niveau (priorité par ordre du fichier :
     *      INACCEPTABLE > HAUT_RISQUE > RISQUE_LIMITE > DEFAULT).
     *   3. Les règles non classificatoires (`classify => false`) qui matchent
     *      ajoutent une alerte (FLAG_ZONE_GRISE, AGGRAVATION) sans changer le
     *      niveau. Elles peuvent contraindre leur déclenchement à un niveau
     *      donné via `requires_niveau` (ex : AGGRAVATION CTRL=AUCUN ne se
     *      déclenche que sur HAUT_RISQUE).
     *   4. Une règle classificatoire peut aussi déclarer une `alerte` qui sera
     *      ajoutée si la règle matche (ex : R-H-08 — flag médical).
     *   5. Les alertes RGPD transversales (Art. 9, Art. 22, etc.) sont
     *      toujours collectées en plus.
     *
     * Renvoie un array compatible avec Assessment::fillable (sauf NON_EVALUE
     * qui n'est pas un niveau persistable — l'enum BDD ne le contient pas).
     *
     * @return array<string, mixed>
     */
    public function classify(AiUsage $aiUsage): array
    {
        // Garde explicite : un usage sans aucune réponse n'est pas évaluable.
        // Sans cette garde, toutes les conditions échoueraient et on retomberait
        // silencieusement sur le DEFAULT (RISQUE_MINIMAL) — faux négatif.
        if ($aiUsage->responses->isEmpty()) {
            return [
                'niveau' => 'NON_EVALUE',
                'regle_id' => 'NON_EVALUE',
                'article' => 'N/A',
                'raison' => 'Questionnaire non renseigné — usage non évaluable.',
                'alertes' => [],
                'type_regle' => 'NA',
                'computed_at' => Carbon::now(),
            ];
        }

        $responses = $aiUsage->responses->pluck('variable_value', 'variable_key')->toArray();
        $rules = config('ai_act_rules');

        $matched = null;
        $ruleAlerts = [];

        // Première passe — règles classificatoires : on prend la première qui matche.
        foreach ($rules as $rule) {
            $isClassifying = $rule['classify'] ?? true;
            if (! $isClassifying) {
                continue;
            }

            // Les règles à `requires_niveau` ne sont jamais classificatoires —
            // elles vivent dans la 2e passe.
            if (isset($rule['requires_niveau'])) {
                continue;
            }

            if ($this->ruleMatches($rule, $aiUsage, $responses)) {
                $matched = $rule;
                if (isset($rule['alerte'])) {
                    $ruleAlerts[] = $this->normalizeAlert($rule['alerte']);
                }
                break;
            }
        }

        // Filet : si la matrice était mal configurée, on dégrade proprement.
        $matched ??= [
            'id' => 'NO_RULE',
            'niveau' => 'RISQUE_MINIMAL',
            'article' => 'N/A',
            'type_regle' => 'NA',
            'raison' => "Aucune règle applicable (matrice incomplète) — dégradation par défaut.",
        ];

        // Deuxième passe — alertes non classificatoires (FLAG_ZONE_GRISE,
        // AGGRAVATION) qui peuvent dépendre du niveau retenu.
        foreach ($rules as $rule) {
            $isClassifying = $rule['classify'] ?? true;
            if ($isClassifying) {
                continue;
            }

            if (isset($rule['requires_niveau']) && $rule['requires_niveau'] !== $matched['niveau']) {
                continue;
            }

            if ($this->ruleMatches($rule, $aiUsage, $responses)) {
                $ruleAlerts[] = $this->normalizeAlert($rule['alerte'], $rule['id'] ?? null);
            }
        }

        // Alertes transversales RGPD/transparence — indépendantes du niveau.
        $transversalAlerts = $this->collectTransversalAlerts($aiUsage, $responses);

        return [
            'niveau' => $matched['niveau'],
            'regle_id' => $matched['id'],
            'article' => $matched['article'],
            'raison' => $matched['raison'],
            'alertes' => array_values(array_merge($ruleAlerts, $transversalAlerts)),
            'type_regle' => $matched['type_regle'],
            'computed_at' => Carbon::now(),
        ];
    }

    /**
     * Calcule + persiste l'évaluation. Remplace toute évaluation antérieure :
     * un seul Assessment "courant" par AiUsage suffit pour la version PME.
     *
     * Lève une RuntimeException si l'usage n'a pas de réponses : le niveau
     * NON_EVALUE n'est pas persistable (enum BDD strict). Le controller a une
     * garde en amont, donc en pratique cette exception ne devrait jamais
     * remonter à l'utilisateur — elle protège des appels directs (jobs, CLI).
     */
    public function persist(AiUsage $aiUsage): Assessment
    {
        $payload = $this->classify($aiUsage);

        if ($payload['niveau'] === 'NON_EVALUE') {
            throw new RuntimeException(
                "Impossible de persister une évaluation NON_EVALUE : l'usage n'a pas de réponses au questionnaire."
            );
        }

        $aiUsage->assessments()->delete();

        return $aiUsage->assessments()->create($payload);
    }

    /**
     * Évalue une condition `when` selon le mini-DSL :
     *   - 'V'                          → eq
     *   - '@in:A,B,C'                  → in
     *   - '@contains:A'                → CSV de actual contient A
     *   - '@intersects:A,B'            → CSV de actual ∩ {A,B} non vide
     *
     * @param  array<string, mixed>  $rule
     * @param  array<string, string>  $responses
     */
    private function ruleMatches(array $rule, AiUsage $aiUsage, array $responses): bool
    {
        $conditions = $rule['when'] ?? [];

        // Pas de condition = match permanent (cas du DEFAULT).
        if ($conditions === []) {
            return true;
        }

        foreach ($conditions as $key => $expected) {
            $actual = $this->resolveField($key, $aiUsage, $responses);

            if (! $this->conditionMatches($actual, (string) $expected)) {
                return false;
            }
        }

        return true;
    }

    private function conditionMatches(?string $actual, string $expected): bool
    {
        if (str_starts_with($expected, '@in:')) {
            $values = $this->splitCsv(substr($expected, 4));

            return $actual !== null && in_array($actual, $values, true);
        }

        if (str_starts_with($expected, '@contains:')) {
            $needle = substr($expected, 10);
            $haystack = $this->splitCsv((string) $actual);

            return in_array($needle, $haystack, true);
        }

        if (str_starts_with($expected, '@intersects:')) {
            $needles = $this->splitCsv(substr($expected, 12));
            $haystack = $this->splitCsv((string) $actual);

            return array_intersect($needles, $haystack) !== [];
        }

        return $actual === $expected;
    }

    /**
     * @return array<int, string>
     */
    private function splitCsv(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
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
     * Normalise une alerte issue de la matrice (rules) en payload Assessment.
     *
     * @param  array<string, string>  $alerte
     * @return array<string, string>
     */
    private function normalizeAlert(array $alerte, ?string $regleId = null): array
    {
        $out = [
            'code' => $alerte['code'] ?? ($regleId ?? 'alerte'),
            'message' => $alerte['message'] ?? '',
        ];

        if (isset($alerte['type'])) {
            $out['type'] = $alerte['type'];
        }

        if (isset($alerte['article'])) {
            $out['article'] = $alerte['article'];
        }

        if ($regleId !== null) {
            $out['regle_id'] = $regleId;
        }

        return $out;
    }

    /**
     * Alertes transversales (RGPD, biais, transparence) — affichées en plus
     * du niveau pour aider la PME à prioriser ses actions. Elles ne dépendent
     * pas de la matrice de classification mais des réponses au questionnaire.
     *
     * @param  array<string, string>  $responses
     * @return array<int, array<string, string>>
     */
    private function collectTransversalAlerts(AiUsage $aiUsage, array $responses): array
    {
        $alerts = [];

        if (($responses['data_sensitive'] ?? null) === 'yes') {
            $alerts[] = [
                'code' => 'rgpd_art9',
                'message' => "Traitement de données sensibles : analyse d'impact (DPIA) RGPD obligatoire.",
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
                'message' => "Sorties LLM publiées sans relecture humaine : risque de désinformation et d'hallucinations.",
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
