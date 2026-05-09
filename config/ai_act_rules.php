<?php

declare(strict_types=1);

/*
 * Matrice de classification AI Act.
 *
 * Niveaux (Règlement UE 2024/1689) :
 *   - INACCEPTABLE   : Article 5 — pratiques prohibées (notation sociale, manipulation,
 *                      identification biométrique temps réel dans l'espace public...)
 *   - HAUT_RISQUE    : Article 6 + Annexe III — IA dans recrutement, scoring crédit,
 *                      éducation, biométrie, santé...
 *   - RISQUE_LIMITE  : Article 50 — obligations de transparence (chatbots, contenu généré)
 *   - RISQUE_MINIMAL : reste — pas d'obligation spécifique
 *
 * Évaluation séquentielle : on prend la PREMIÈRE règle qui matche par ordre décroissant
 * de sévérité (INACCEPTABLE > HAUT_RISQUE > RISQUE_LIMITE > RISQUE_MINIMAL).
 *
 * type_regle :
 *   - TEXTE_EXPLICITE : règle directement citée par l'AI Act
 *   - INTERPRETATION  : déduction raisonnable mais non littérale
 *   - NA              : fallback (risque minimal par défaut)
 */

return [
    // -----------------------------------------------------------------------
    // INACCEPTABLE (Article 5)
    // -----------------------------------------------------------------------
    [
        'id' => 'art5_bio_realtime_public',
        'niveau' => 'INACCEPTABLE',
        'article' => 'Article 5 §1 (h)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'raison' => 'L\'identification biométrique en temps réel dans des espaces accessibles au public à des fins répressives est prohibée par l\'AI Act (sauf exceptions strictes encadrées).',
        'when' => [
            'ai_usage.type' => 'IA_BIO',
            'response.bio_realtime' => 'yes',
        ],
    ],
    [
        'id' => 'art5_social_scoring',
        'niveau' => 'INACCEPTABLE',
        'article' => 'Article 5 §1 (c)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'raison' => 'La notation sociale d\'individus par les autorités ou pour leur compte est prohibée.',
        'when' => [
            'ai_usage.type' => 'IA_SCORING',
            'response.scoring_target' => 'social',
        ],
    ],

    // -----------------------------------------------------------------------
    // HAUT_RISQUE (Annexe III)
    // -----------------------------------------------------------------------
    [
        'id' => 'annexe3_recrutement',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Annexe III §4',
        'type_regle' => 'TEXTE_EXPLICITE',
        'raison' => 'Les IA utilisées dans le recrutement (tri de CV, évaluation de candidats) sont classées à haut risque par l\'Annexe III de l\'AI Act.',
        'when' => [
            'ai_usage.domain' => 'RH',
        ],
    ],
    [
        'id' => 'annexe3_credit',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Annexe III §5 (b)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'raison' => 'Les IA évaluant la solvabilité ou notant le crédit des personnes physiques sont à haut risque.',
        'when' => [
            'ai_usage.domain' => 'CREDIT',
        ],
    ],
    [
        'id' => 'annexe3_scoring_recrutement',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Annexe III §4',
        'type_regle' => 'TEXTE_EXPLICITE',
        'raison' => 'Le scoring sur le recrutement entre dans le champ haut risque de l\'Annexe III.',
        'when' => [
            'ai_usage.type' => 'IA_SCORING',
            'response.scoring_target' => 'recruitment',
        ],
    ],
    [
        'id' => 'annexe3_education',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Annexe III §3',
        'type_regle' => 'TEXTE_EXPLICITE',
        'raison' => 'Les IA d\'évaluation et d\'orientation dans l\'éducation et la formation professionnelle sont à haut risque.',
        'when' => [
            'ai_usage.domain' => 'EDUCATION',
        ],
    ],
    [
        'id' => 'annexe3_bio_categorisation',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Annexe III §1',
        'type_regle' => 'TEXTE_EXPLICITE',
        'raison' => 'Les systèmes biométriques (hors temps réel public) sont classés à haut risque par l\'Annexe III.',
        'when' => [
            'ai_usage.type' => 'IA_BIO',
        ],
    ],
    [
        'id' => 'haut_risque_decision_automatique_impactante',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Article 6 §2',
        'type_regle' => 'INTERPRETATION',
        'raison' => 'Une décision entièrement automatisée ayant un impact significatif sur des personnes (sans supervision humaine) relève du haut risque.',
        'when' => [
            'response.impact_individual' => 'yes',
            'response.human_oversight' => 'never',
        ],
    ],

    // -----------------------------------------------------------------------
    // RISQUE_LIMITE (Article 50)
    // -----------------------------------------------------------------------
    [
        'id' => 'art50_deepfake',
        'niveau' => 'RISQUE_LIMITE',
        'article' => 'Article 50 §4',
        'type_regle' => 'TEXTE_EXPLICITE',
        'raison' => 'Les contenus pouvant représenter des personnes réelles (deepfake) doivent être étiquetés comme générés par IA.',
        'when' => [
            'ai_usage.type' => 'IA_GEN',
            'response.gen_deepfake_risk' => 'yes',
        ],
    ],
    [
        'id' => 'art50_contenu_genere',
        'niveau' => 'RISQUE_LIMITE',
        'article' => 'Article 50 §2',
        'type_regle' => 'TEXTE_EXPLICITE',
        'raison' => 'Les contenus générés par IA doivent être identifiés comme tels (transparence).',
        'when' => [
            'ai_usage.type' => 'IA_GEN',
        ],
    ],
    [
        'id' => 'art50_chatbot_llm',
        'niveau' => 'RISQUE_LIMITE',
        'article' => 'Article 50 §1',
        'type_regle' => 'TEXTE_EXPLICITE',
        'raison' => 'Les LLM en interaction directe avec des utilisateurs doivent les informer qu\'ils dialoguent avec une IA.',
        'when' => [
            'ai_usage.type' => 'LLM_GEN',
        ],
    ],

    // -----------------------------------------------------------------------
    // RISQUE_MINIMAL (fallback)
    // -----------------------------------------------------------------------
    [
        'id' => 'fallback_risque_minimal',
        'niveau' => 'RISQUE_MINIMAL',
        'article' => 'Article 95',
        'type_regle' => 'NA',
        'raison' => 'Aucune obligation spécifique au titre de l\'AI Act. Codes de conduite volontaires recommandés.',
        'when' => [], // matche toujours
    ],
];
