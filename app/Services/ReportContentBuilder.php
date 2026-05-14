<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Assemble le contenu rédactionnel du rapport d'audit à partir des données
 * brutes (organisation + usages + assessments + responses) en s'appuyant sur
 * la bibliothèque config('report_templates').
 *
 * Responsabilités :
 *   - calculer les variables d'injection (compteurs, niveau global, listes
 *     d'usages par niveau) ;
 *   - sélectionner les encadrés réglementaires applicables à chaque usage
 *     selon le mini-DSL `declenche_si` des templates ;
 *   - filtrer les actions du plan d'action selon les conditions du portefeuille
 *     entier (présence d'INACCEPTABLE, alertes RGPD, etc.) ;
 *   - construire les 3 priorités de la synthèse exécutive ;
 *   - retourner un payload prêt à être figé dans Report::snapshot.
 *
 * Le service NE LIT PAS Report directement : il consomme l'array de données
 * produit par ReportSnapshotBuilder pour rester testable en isolation.
 */
class ReportContentBuilder
{
    /**
     * @param  array<string, mixed>  $data  Données brutes (output de
     *                                      ReportSnapshotBuilder::buildData) — voir contrat plus bas.
     * @return array<string, mixed> Contenu résolu, structure stable pour
     *                              les vues Blade.
     */
    public function build(array $data): array
    {
        $templates = config('report_templates');

        $organization = $data['organization'];
        $usages = $data['usages'];
        $generatedAt = $data['generated_at'];

        $meta = $this->buildMeta($organization, $usages, $generatedAt);
        $counts = $this->countByLevel($usages);
        $usagesByLevel = $this->groupByLevel($usages);
        $highestLevel = $this->highestLevel($counts);

        $variables = array_merge($meta, [
            'nb_usages_inacceptable' => $counts['INACCEPTABLE'],
            'nb_usages_haut_risque' => $counts['HAUT_RISQUE'],
            'nb_usages_limite' => $counts['RISQUE_LIMITE'],
            'nb_usages_minimal' => $counts['RISQUE_MINIMAL'],
            'nb_usages_non_evalue' => $counts['NON_EVALUE'],
            'niveau_risque_global' => $templates['niveau_risque_global'][$highestLevel],
            'usages_inacceptable_list' => $this->namesList($usagesByLevel['INACCEPTABLE']),
            'usages_haut_risque_list' => $this->namesList($usagesByLevel['HAUT_RISQUE']),
            'usages_risque_limite_list' => $this->namesList($usagesByLevel['RISQUE_LIMITE']),
        ]);

        $reportFlags = $this->reportFlags($usages, $counts);
        $priorites = $this->buildPriorites($templates, $counts, $reportFlags, $variables);

        // Injection finale : on remplace les {variables} dans toutes les
        // chaînes textuelles susceptibles d'en contenir.
        $variables = array_merge($variables, [
            'priorite_1' => $priorites[0] ?? null,
            'priorite_2' => $priorites[1] ?? null,
            'priorite_3' => $priorites[2] ?? null,
        ]);

        return [
            'meta' => $meta,
            'compteurs_par_niveau' => $counts,
            'niveau_risque_global' => $variables['niveau_risque_global'],
            'priorites' => $priorites,

            'introduction' => $this->render($templates['introduction'], $variables),
            'methodologie_short' => $templates['methodologie_short'],
            'cadre_reglementaire' => $templates['cadre_reglementaire'],

            'synthese_executive' => [
                'header' => $this->render($templates['synthese_executive']['header'], $variables),
                'repartition' => $this->render($templates['synthese_executive']['repartition'], $variables),
                'sanctions' => $this->render($templates['synthese_executive']['sanctions'], $variables),
                'priorites_intro' => $this->render($templates['synthese_executive']['priorites_intro'], $variables),
            ],

            'usages' => $this->buildUsages($usages, $templates),

            'plan_action' => $this->buildPlanAction($templates, $reportFlags, $variables),
            'checklist' => $templates['checklist_finale'],
            'zones_grises' => $this->renderZonesGrises($templates['zones_grises'], $variables),
            'disclaimer' => $this->renderDisclaimer($templates['disclaimer'], $variables),

            'niveau_labels' => $templates['niveau_labels'],
        ];
    }

    /**
     * @param  array<string, mixed>  $organization
     * @param  array<int, array<string, mixed>>  $usages
     * @return array<string, mixed>
     */
    private function buildMeta(array $organization, array $usages, string $generatedAt): array
    {
        return [
            'nom_pme' => $organization['name'] ?? 'Organisation',
            'siret' => $organization['siret'] ?? null,
            'size' => $organization['size'] ?? null,
            'sector' => $organization['sector'] ?? null,
            'date_audit' => $this->formatDateFr($generatedAt),
            'generated_at_iso' => $generatedAt,
            'nb_usages_declares' => count($usages),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $usages
     * @return array<string, int>
     */
    private function countByLevel(array $usages): array
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

    /**
     * @param  array<int, array<string, mixed>>  $usages
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function groupByLevel(array $usages): array
    {
        $grouped = [
            'INACCEPTABLE' => [],
            'HAUT_RISQUE' => [],
            'RISQUE_LIMITE' => [],
            'RISQUE_MINIMAL' => [],
            'NON_EVALUE' => [],
        ];

        foreach ($usages as $usage) {
            $niveau = $usage['assessment']['niveau'] ?? 'NON_EVALUE';
            $grouped[$niveau][] = $usage;
        }

        return $grouped;
    }

    /**
     * @param  array<string, int>  $counts
     */
    private function highestLevel(array $counts): string
    {
        foreach (['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL'] as $niveau) {
            if (($counts[$niveau] ?? 0) > 0) {
                return $niveau;
            }
        }

        return 'NON_EVALUE';
    }

    /**
     * @param  array<int, array<string, mixed>>  $usages
     */
    private function namesList(array $usages): string
    {
        if ($usages === []) {
            return 'aucun';
        }

        $names = array_map(fn ($u) => $u['name'], $usages);

        return '« '.implode(' », « ', $names).' »';
    }

    /**
     * Calcule les flags qui s'appliquent à l'ensemble du portefeuille
     * (utilisés par les prédicats `report:*` du plan d'action).
     *
     * @param  array<int, array<string, mixed>>  $usages
     * @param  array<string, int>  $counts
     * @return array<string, bool>
     */
    private function reportFlags(array $usages, array $counts): array
    {
        $hasAlerte = function (string $code) use ($usages): bool {
            foreach ($usages as $usage) {
                foreach ($usage['assessment']['alertes'] ?? [] as $alerte) {
                    if (($alerte['code'] ?? null) === $code) {
                        return true;
                    }
                }
            }

            return false;
        };

        $hasHautRisqueEmployes = false;
        foreach ($usages as $usage) {
            if (($usage['assessment']['niveau'] ?? null) !== 'HAUT_RISQUE') {
                continue;
            }
            $pub = (string) ($usage['responses']['pub'] ?? '');
            if (in_array('EMPLOYES', array_map('trim', explode(',', $pub)), true)) {
                $hasHautRisqueEmployes = true;
                break;
            }
        }

        return [
            'always' => true,
            'has_inacceptable' => $counts['INACCEPTABLE'] > 0,
            'has_haut_risque' => $counts['HAUT_RISQUE'] > 0,
            'has_haut_risque_employes' => $hasHautRisqueEmployes,
            'has_risque_limite' => $counts['RISQUE_LIMITE'] > 0,
            'has_alerte_rgpd_art9' => $hasAlerte('rgpd_art9'),
            'has_alerte_rgpd_art22' => $hasAlerte('rgpd_art22'),
        ];
    }

    /**
     * Construit les 3 priorités personnalisées en cascadant les templates
     * du plus sévère au plus permissif. On ne dépasse jamais 3 entrées.
     *
     * @param  array<string, mixed>  $templates
     * @param  array<string, int>  $counts
     * @param  array<string, bool>  $flags
     * @param  array<string, mixed>  $variables
     * @return array<int, string>
     */
    private function buildPriorites(array $templates, array $counts, array $flags, array $variables): array
    {
        $candidates = [];

        if ($flags['has_inacceptable']) {
            $candidates[] = $templates['priorites_templates']['priorite_inacceptable'];
        }
        if ($flags['has_haut_risque']) {
            $candidates[] = $templates['priorites_templates']['priorite_haut_risque'];
        }
        if ($flags['has_risque_limite']) {
            $candidates[] = $templates['priorites_templates']['priorite_risque_limite'];
        }

        // Charte IA — recommandation universelle si IA générative présente.
        $candidates[] = $templates['priorites_templates']['priorite_charte_ia'];

        // Fallback : si aucun risque significatif, on insiste sur le registre.
        if (! $flags['has_inacceptable']
            && ! $flags['has_haut_risque']
            && ! $flags['has_risque_limite']) {
            $candidates[] = $templates['priorites_templates']['priorite_registre'];
        }

        $top3 = array_slice($candidates, 0, 3);

        return array_map(fn ($t) => $this->render($t, $variables), $top3);
    }

    /**
     * Pour chaque usage, calcule le paragraphe niveau, les encadrés
     * applicables (selon le mini-DSL des templates) et structure les données
     * pour la vue.
     *
     * @param  array<int, array<string, mixed>>  $usages
     * @param  array<string, mixed>  $templates
     * @return array<int, array<string, mixed>>
     */
    private function buildUsages(array $usages, array $templates): array
    {
        $resolved = [];

        foreach ($usages as $usage) {
            $niveau = $usage['assessment']['niveau'] ?? 'NON_EVALUE';
            $paragraphe = $templates['paragraphes_par_niveau'][$niveau]
                ?? $templates['paragraphes_par_niveau']['NON_EVALUE'];

            $encadres = [];
            foreach ($templates['encadres_obligations'] as $key => $encadre) {
                if ($this->encadreApplies($encadre['declenche_si'], $usage, $templates)) {
                    $encadres[] = [
                        'key' => $key,
                        'titre' => $encadre['titre'],
                        'contenu' => $encadre['contenu'],
                    ];
                }
            }

            $resolved[] = [
                'id' => $usage['id'] ?? null,
                'name' => $usage['name'] ?? '',
                'description' => $usage['description'] ?? null,
                'type' => $usage['type'] ?? null,
                'domain' => $usage['domain'] ?? null,
                'niveau' => $niveau,
                'niveau_label' => $templates['niveau_labels'][$niveau] ?? $niveau,
                'regle_id' => $usage['assessment']['regle_id'] ?? null,
                'article' => $usage['assessment']['article'] ?? null,
                'raison' => $usage['assessment']['raison'] ?? null,
                'type_regle' => $usage['assessment']['type_regle'] ?? null,
                'paragraphe_niveau' => $paragraphe,
                'encadres' => $encadres,
                'alertes' => $usage['assessment']['alertes'] ?? [],
            ];
        }

        return $resolved;
    }

    /**
     * Évalue le mini-DSL des conditions d'apparition d'un encadré usage-level.
     * Logique : OR sur la liste de prédicats. Chaque prédicat peut être
     * composé (AND interne via `+`).
     *
     * @param  array<int, string>  $predicats
     * @param  array<string, mixed>  $usage
     * @param  array<string, mixed>  $templates
     */
    private function encadreApplies(array $predicats, array $usage, array $templates): bool
    {
        foreach ($predicats as $predicat) {
            if ($this->evalPredicat($predicat, $usage, $templates)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $usage
     * @param  array<string, mixed>  $templates
     */
    private function evalPredicat(string $predicat, array $usage, array $templates): bool
    {
        // AND interne : 'niveau:HAUT_RISQUE+pub:EMPLOYES'
        if (str_contains($predicat, '+')) {
            foreach (explode('+', $predicat) as $part) {
                if (! $this->evalPredicat(trim($part), $usage, $templates)) {
                    return false;
                }
            }

            return true;
        }

        [$op, $value] = array_pad(explode(':', $predicat, 2), 2, '');
        $assessment = $usage['assessment'] ?? [];
        $responses = $usage['responses'] ?? [];

        return match ($op) {
            'niveau' => ($assessment['niveau'] ?? null) === $value,
            'regle' => ($assessment['regle_id'] ?? null) === $value,
            'regle_in' => in_array($assessment['regle_id'] ?? null, explode(',', $value), true),
            'type' => ($usage['type'] ?? null) === $value,
            'domain' => ($usage['domain'] ?? null) === $value,
            'pub' => $this->csvContains((string) ($responses['pub'] ?? ''), $value),
            'alerte' => $this->hasAlerte($assessment['alertes'] ?? [], $value),
            'response' => $this->responseEquals($responses, $value),
            'provider_us' => in_array(($responses['llm_provider'] ?? null), $templates['llm_providers_us'], true),
            default => false,
        };
    }

    private function csvContains(string $csv, string $needle): bool
    {
        $parts = array_map('trim', explode(',', $csv));

        return in_array($needle, $parts, true);
    }

    /**
     * @param  array<int, array<string, mixed>>  $alertes
     */
    private function hasAlerte(array $alertes, string $code): bool
    {
        foreach ($alertes as $alerte) {
            if (($alerte['code'] ?? null) === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format attendu : 'data_personal=yes' → responses[data_personal] === 'yes'.
     *
     * @param  array<string, string>  $responses
     */
    private function responseEquals(array $responses, string $expr): bool
    {
        [$key, $value] = array_pad(explode('=', $expr, 2), 2, '');

        return ($responses[$key] ?? null) === $value;
    }

    /**
     * @param  array<string, mixed>  $templates
     * @param  array<string, bool>  $flags
     * @param  array<string, mixed>  $variables
     * @return array<string, mixed>
     */
    private function buildPlanAction(array $templates, array $flags, array $variables): array
    {
        $tpl = $templates['plan_action'];

        $resolvePhase = function (array $phase) use ($flags, $variables) {
            $actions = [];
            foreach ($phase['actions'] as $action) {
                $applies = false;
                foreach ($action['declenche_si'] as $predicat) {
                    if (str_starts_with($predicat, 'report:')) {
                        $flag = substr($predicat, 7);
                        if (! empty($flags[$flag])) {
                            $applies = true;
                            break;
                        }
                    }
                }
                if (! $applies) {
                    continue;
                }
                $actions[] = [
                    'titre' => $action['titre'],
                    'contenu' => $this->render($action['contenu'], $variables),
                    'effort' => $action['effort'],
                    'responsable' => $action['responsable'],
                ];
            }

            return [
                'intro' => $phase['intro'],
                'actions' => $actions,
            ];
        };

        return [
            'header' => $this->render($tpl['header'], $variables),
            'tableau' => $tpl['tableau'],
            'phase_1m' => $resolvePhase($tpl['phase_1m']),
            'phase_6m' => $resolvePhase($tpl['phase_6m']),
            'phase_1y' => $resolvePhase($tpl['phase_1y']),
        ];
    }

    /**
     * @param  array<string, string>  $zg
     * @param  array<string, mixed>  $variables
     * @return array<string, string>
     */
    private function renderZonesGrises(array $zg, array $variables): array
    {
        return [
            'intro' => $this->render($zg['intro'], $variables),
            'digital_omnibus' => $zg['digital_omnibus'],
            'human_washing' => $zg['human_washing'],
            'dpf' => $zg['dpf'],
        ];
    }

    /**
     * @param  array<string, mixed>  $disclaimer
     * @param  array<string, mixed>  $variables
     * @return array<string, mixed>
     */
    private function renderDisclaimer(array $disclaimer, array $variables): array
    {
        return [
            'exclusion_responsabilite' => [
                'titre' => $disclaimer['exclusion_responsabilite']['titre'],
                'contenu' => $this->render($disclaimer['exclusion_responsabilite']['contenu'], $variables),
            ],
            'peremption_normative' => [
                'titre' => $disclaimer['peremption_normative']['titre'],
                'contenu' => $disclaimer['peremption_normative']['contenu'],
            ],
            'recommandation_assistance' => [
                'titre' => $disclaimer['recommandation_assistance']['titre'],
                'contenu' => $disclaimer['recommandation_assistance']['contenu'],
            ],
        ];
    }

    /**
     * Substitue les {clé} d'un texte par les valeurs présentes dans
     * $variables. Les clés non trouvées sont laissées telles quelles
     * (signal visible côté PDF qu'une variable est manquante).
     *
     * @param  array<string, mixed>  $variables
     */
    private function render(string $text, array $variables): string
    {
        return preg_replace_callback(
            '/\{(\w+)\}/',
            function (array $m) use ($variables) {
                $key = $m[1];

                return array_key_exists($key, $variables) && $variables[$key] !== null
                    ? (string) $variables[$key]
                    : $m[0];
            },
            $text
        );
    }

    private function formatDateFr(string $iso): string
    {
        $months = [
            'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
            'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre',
        ];

        $dt = new \DateTimeImmutable($iso);

        return sprintf('%d %s %d', (int) $dt->format('d'), $months[(int) $dt->format('n') - 1], (int) $dt->format('Y'));
    }
}
