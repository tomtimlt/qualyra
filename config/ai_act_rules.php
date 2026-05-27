<?php

declare(strict_types=1);

/*
 * Matrice de classification AI Act — version 1.1.
 *
 * Source de vérité : docs/Matrice de Décision AI Act.md (v1.1).
 *
 * Niveaux (Règlement UE 2024/1689) :
 *   - INACCEPTABLE   : Article 5 — pratiques prohibées (8 règles R-I-XX)
 *   - HAUT_RISQUE    : Article 6 §2 + Annexe III (8 règles R-H-XX)
 *   - RISQUE_LIMITE  : Article 50 — transparence (6 règles R-L-XX)
 *   - RISQUE_MINIMAL : reste — pas d'obligation spécifique (DEFAULT)
 *
 * Évaluation séquentielle : on prend la PREMIÈRE règle dont les conditions
 * matchent. La matrice est rangée du plus sévère au plus laxiste, donc :
 * INACCEPTABLE > HAUT_RISQUE > RISQUE_LIMITE > RISQUE_MINIMAL.
 *
 * type_regle :
 *   - TEXTE_EXPLICITE : règle directement citée par l'AI Act
 *   - INTERPRETATION  : déduction raisonnable mais non littérale
 *   - NA              : fallback (risque minimal par défaut)
 *
 * Mini-DSL des conditions `when` (toutes en string scalaire pour rester
 * compatible avec la colonne responses.variable_value) :
 *   - 'response.x' => 'V'                      → x === V (égalité)
 *   - 'response.x' => '@in:A,B,C'              → x ∈ {A, B, C}
 *   - 'response.x' => '@contains:A'            → x est un CSV qui contient A
 *   - 'response.x' => '@intersects:A,B,C'      → x est un CSV qui partage au
 *                                                  moins une valeur avec {A,B,C}
 *
 * Les clés peuvent référer à 'ai_usage.<champ>' (type, domain) ou
 * 'response.<variable_key>' (toutes les autres réponses au questionnaire).
 *
 * Une règle peut déclarer :
 *   - 'when'      : conditions de déclenchement classificatoires (toutes ET)
 *   - 'classify'  : true (défaut) → la règle fixe le niveau ; false → la règle
 *                   ajoute juste une alerte sans changer le niveau (utilisé
 *                   pour R-H-BORDERLINE et l'AGGRAVATION CTRL=AUCUN)
 *   - 'alerte'    : ['code' => ..., 'message' => ...] ajoutée si la règle
 *                   matche (peut s'ajouter même sur une règle classificatoire)
 *   - 'requires_niveau' : pour les règles non classificatoires qui ne
 *                   s'évaluent qu'après détermination d'un niveau (ex:
 *                   AGGRAVATION ne se déclenche que si niveau = HAUT_RISQUE)
 *   - 'applicable_from' : date YYYY-MM-DD à partir de laquelle la règle entre
 *                   en vigueur. Le moteur ignore les règles non encore
 *                   applicables à la date d'audit. Calendrier officiel du
 *                   Règlement UE 2024/1689 :
 *                     - 2025-02-02 : pratiques interdites (Art. 5)
 *                     - 2026-08-02 : haut risque Annexe III + transparence Art. 50
 *                     - 2027-08-02 : haut risque Annexe I (MDR/IVDR)
 *                   Absence = règle toujours applicable (cas du DEFAULT).
 */

return [

    // =========================================================================
    // BLOC 1 — INACCEPTABLE (Article 5)
    // Applicable depuis le 2 février 2025.
    // =========================================================================

    [
        'id' => 'R-I-01',
        'niveau' => 'INACCEPTABLE',
        'article' => 'Art. 5 §1 f)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2025-02-02',
        'raison' => "L'inférence des émotions par IA sur le lieu de travail ou dans l'enseignement est explicitement interdite, sauf usage médical ou sécuritaire dûment justifié.",
        'when' => [
            'ai_usage.type' => 'IA_BIO',
            'response.bio_type' => 'RECOG_EMOTIONS',
            'ai_usage.domain' => '@in:RH,EDUCATION',
        ],
    ],

    [
        'id' => 'R-I-02',
        'niveau' => 'INACCEPTABLE',
        'article' => 'Art. 5 §1 g)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2025-02-02',
        'raison' => "La catégorisation biométrique inférant des caractéristiques protégées (race, religion, orientation sexuelle, etc.) est interdite, sauf usage des forces de l'ordre expressément encadré.",
        'when' => [
            'ai_usage.type' => 'IA_BIO',
            'response.bio_type' => 'CATEGORISATION',
            'response.bio_attr_sensibles' => 'OUI',
        ],
    ],

    [
        'id' => 'R-I-03',
        'niveau' => 'INACCEPTABLE',
        'article' => 'Art. 5 §1 c)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2025-02-02',
        'raison' => 'Un système de notation sociale globale des individus sur plusieurs domaines de vie (social scoring), entraînant un traitement défavorable ou disproportionné, est interdit.',
        'when' => [
            'ai_usage.type' => 'IA_SCORING',
            'response.scoring_portee' => 'GLOBAL_MULTI_DOMAINES',
            'response.dec' => '@in:SEMI_AUTO,FULL_AUTO',
        ],
    ],

    [
        'id' => 'R-I-04',
        'niveau' => 'INACCEPTABLE',
        'article' => 'Art. 5 §1 b)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2025-02-02',
        'raison' => "L'exploitation des vulnérabilités (âge, handicap, précarité socio-économique) pour manipuler substantiellement le comportement commercial est interdite.",
        'when' => [
            'response.pub' => '@contains:VULNERABLES',
            'ai_usage.domain' => 'MARKETING',
            'response.persuasion_psychologique' => 'OUI',
        ],
    ],

    [
        'id' => 'R-I-05',
        'niveau' => 'INACCEPTABLE',
        'article' => 'Art. 5 §1 h)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2025-02-02',
        'raison' => "L'identification biométrique à distance en temps réel dans les espaces publics est interdite pour les acteurs privés. Les exceptions, strictement encadrées, sont réservées aux forces de l'ordre — hors scope PME.",
        'when' => [
            'ai_usage.type' => 'IA_BIO',
            'response.bio_type' => 'IDENTIFICATION',
            'response.bio_realtime' => 'yes',
        ],
    ],

    [
        'id' => 'R-I-06',
        'niveau' => 'INACCEPTABLE',
        'article' => 'Art. 5 §1 e)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2025-02-02',
        'raison' => "La création ou l'expansion de bases de données de reconnaissance faciale par collecte non ciblée sur Internet ou par CCTV est interdite.",
        'when' => [
            'ai_usage.type' => 'IA_BIO',
            'response.bio_type' => 'IDENTIFICATION',
            'response.bio_source_donnees' => '@in:SCRAPING_INTERNET,CCTV_NON_CIBLE',
        ],
    ],

    [
        'id' => 'R-I-07',
        'niveau' => 'INACCEPTABLE',
        'article' => 'Art. 5 §1 a)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2025-02-02',
        'raison' => 'Les techniques subliminales ou délibérément trompeuses altérant substantiellement le comportement et causant un préjudice sont interdites.',
        'when' => [
            'response.techniques_subliminales' => 'OUI',
        ],
    ],

    [
        'id' => 'R-I-08',
        'niveau' => 'INACCEPTABLE',
        'article' => 'Art. 5 §1 d)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2025-02-02',
        'raison' => "L'évaluation du risque qu'une personne commette une infraction pénale, fondée uniquement sur le profilage ou les traits de personnalité, est interdite.",
        'when' => [
            'ai_usage.domain' => 'SECURITE',
            'ai_usage.type' => 'IA_SCORING',
            'response.prediction_criminelle' => 'OUI',
        ],
    ],

    // =========================================================================
    // BLOC 2 — HAUT RISQUE (Art. 6 §2 + Annexe III)
    // Applicable au 2 août 2026 pour les déployeurs PME.
    // =========================================================================

    [
        'id' => 'R-H-01',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Art. 6 §2 + Annexe III §1 a)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => "Les systèmes biométriques d'identification des personnes ou de contrôle d'accès sont des systèmes d'IA à haut risque.",
        'when' => [
            'ai_usage.type' => 'IA_BIO',
            'response.bio_type' => '@in:IDENTIFICATION,CONTROLE_ACCES',
            'response.dec' => '@in:AIDE_DEC,SEMI_AUTO,FULL_AUTO',
        ],
    ],

    [
        'id' => 'R-H-02',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Art. 6 §2 + Annexe III §4 a)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => "Tout système IA influençant la présélection, le classement ou la sélection des candidats à l'embauche est haut risque.",
        'when' => [
            'ai_usage.domain' => 'RH',
            'response.dec' => '@in:AIDE_DEC,SEMI_AUTO,FULL_AUTO',
            'response.pub' => '@contains:EMPLOYES',
            'response.rh_usage' => '@in:TRI_CV,SCORING_CANDIDATS,DECISION_EMBAUCHE',
        ],
    ],

    [
        'id' => 'R-H-03',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Art. 6 §2 + Annexe III §4 b)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => "Tout système IA influençant la gestion, la surveillance ou l'évaluation des salariés avec impact sur leurs conditions de travail est haut risque.",
        'when' => [
            'ai_usage.domain' => 'RH',
            'response.dec' => '@in:AIDE_DEC,SEMI_AUTO,FULL_AUTO',
            'response.pub' => '@contains:EMPLOYES',
            'response.rh_usage' => '@in:EVAL_PERFORMANCE,SURVEILLANCE_COMPORTEMENT,ATTRIBUTION_TACHES,DECISION_PROMOTION,DECISION_LICENCIEMENT',
        ],
    ],

    [
        'id' => 'R-H-04',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Art. 6 §2 + Annexe III §3',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => "Les systèmes IA déterminant l'accès à la formation, les résultats d'évaluation académique ou l'orientation professionnelle sont haut risque.",
        'when' => [
            'ai_usage.domain' => 'EDUCATION',
            'response.dec' => '@in:AIDE_DEC,SEMI_AUTO,FULL_AUTO',
            'response.educ_usage' => '@in:ADMISSION,ORIENTATION,EVALUATION_ACADEMIQUE',
        ],
    ],

    [
        'id' => 'R-H-05',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Art. 6 §2 + Annexe III §5 b)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => "Les systèmes IA d'évaluation de la solvabilité, de scoring crédit ou d'établissement de profil de risque financier pour des personnes physiques sont haut risque.",
        'when' => [
            'ai_usage.domain' => 'CREDIT',
            'response.dec' => '@in:AIDE_DEC,SEMI_AUTO,FULL_AUTO',
            'response.pub' => '@intersects:CLIENTS,GRAND_PUBLIC',
        ],
    ],

    [
        'id' => 'R-H-06',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Art. 6 §2 + Annexe III §5 c)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => "Les systèmes IA évaluant le risque de santé ou de vie pour la tarification ou l'éligibilité à une assurance sont haut risque.",
        'when' => [
            'ai_usage.domain' => 'SANTE',
            'response.sante_finalite' => 'ASSURANCE_RISQUE',
            'response.dec' => '@in:AIDE_DEC,SEMI_AUTO,FULL_AUTO',
            'response.pub' => '@intersects:CLIENTS,GRAND_PUBLIC',
        ],
    ],

    [
        'id' => 'R-H-07',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Art. 6 §2 + Annexe III §5 a)',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => "Les systèmes IA déterminant l'accès à des services ou prestations essentiels pour les personnes physiques sont haut risque.",
        'when' => [
            'response.dec' => '@in:AIDE_DEC,SEMI_AUTO,FULL_AUTO',
            'response.pub' => '@intersects:GRAND_PUBLIC,VULNERABLES',
            'response.usage_prestations_essentielles' => 'OUI',
        ],
    ],

    [
        'id' => 'R-H-08',
        'niveau' => 'HAUT_RISQUE',
        'article' => 'Art. 6 §1 + Annexe I (interprétation, Art. 6 §2 par analogie)',
        'type_regle' => 'INTERPRETATION',
        'applicable_from' => '2027-08-02',
        'raison' => 'Les systèmes IA influençant des décisions médicales cliniques sont présumés haut risque. Vérifier le régime applicable (MDR 2017/745 ou IVDR 2017/746).',
        'alerte' => [
            'code' => 'flag_zone_grise_medical',
            'type' => 'FLAG_ZONE_GRISE',
            'message' => 'Usage médical clinique détecté. Peut relever de Art. 6 §1 + Annexe I (dispositif médical) applicable au 2 août 2027. Consulter un juriste.',
            'article' => 'Art. 6 §1 + Annexe I (MDR/IVDR)',
        ],
        'when' => [
            'ai_usage.domain' => 'SANTE',
            'response.sante_finalite' => 'DECISION_MEDICALE',
            'response.dec' => '@in:AIDE_DEC,SEMI_AUTO,FULL_AUTO',
        ],
    ],

    // -------------------------------------------------------------------------
    // R-H-BORDERLINE — Zone grise RH déclarée informative.
    // Non classificatoire (n'écrase pas le niveau de base) : ajoute uniquement
    // une alerte forte FLAG_ZONE_GRISE selon le pseudo-code patché v1.1.
    // L'évaluation continue vers les blocs suivants.
    // -------------------------------------------------------------------------
    [
        'id' => 'R-H-BORDERLINE',
        'classify' => false,
        'applicable_from' => '2026-08-02',
        'alerte' => [
            'code' => 'flag_zone_grise_rh_informatif',
            'type' => 'FLAG_ZONE_GRISE',
            'message' => "Usage IA en RH déclaré 'informatif' mais portant sur un domaine sensible. ZG-01 applicable : si la sortie IA influence en pratique la décision finale (ce qui est souvent le cas), classification HAUT_RISQUE recommandée par précaution. Vérification juridique conseillée.",
            'article' => 'Annexe III §4 a) + ZG-01',
        ],
        'when' => [
            'ai_usage.domain' => 'RH',
            'response.rh_usage' => '@in:TRI_CV,SCORING_CANDIDATS,EVAL_PERFORMANCE,SURVEILLANCE_COMPORTEMENT,DECISION_EMBAUCHE,DECISION_PROMOTION,DECISION_LICENCIEMENT',
            'response.dec' => 'INFORMATIF',
            'response.pub' => '@contains:EMPLOYES',
        ],
    ],

    // -------------------------------------------------------------------------
    // AGGRAVATION CTRL=AUCUN sur HAUT_RISQUE.
    // Non classificatoire, post-classification : ne se déclenche que si le
    // niveau retenu est HAUT_RISQUE (Art. 26 §2 — contrôle humain effectif).
    // -------------------------------------------------------------------------
    [
        'id' => 'AGGRAVATION-CTRL',
        'classify' => false,
        'requires_niveau' => 'HAUT_RISQUE',
        'applicable_from' => '2026-08-02',
        'alerte' => [
            'code' => 'aggravation_pas_de_controle_humain',
            'type' => 'AGGRAVATION',
            'message' => 'Absence totale de contrôle humain sur un système haut risque. Art. 26 §2 exige un contrôle humain effectif par des personnes compétentes. Non-conformité caractérisée.',
            'article' => 'Art. 26 §2',
        ],
        'when' => [
            'response.human_oversight' => 'never',
        ],
    ],

    // =========================================================================
    // BLOC 3 — RISQUE LIMITÉ (Art. 50)
    // Applicable au 2 août 2026.
    // =========================================================================

    [
        'id' => 'R-L-01',
        'niveau' => 'RISQUE_LIMITE',
        'article' => 'Art. 50 §1',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => "Tout système IA interagissant directement avec des personnes doit les informer clairement qu'elles interagissent avec une IA (sauf si cela est évident).",
        'when' => [
            'ai_usage.type' => '@in:LLM_GEN,IA_GEN',
            'response.diff' => '@in:TIERS,PUBLIC',
            'response.pub' => '@intersects:CLIENTS,GRAND_PUBLIC,VULNERABLES',
            'response.interaction_directe' => 'OUI',
        ],
    ],

    [
        'id' => 'R-L-02',
        'niveau' => 'RISQUE_LIMITE',
        'article' => 'Art. 50 §2',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => 'Les contenus synthétiques (images, sons, vidéos) générés par IA doivent être signalés comme tels avec un marquage lisible par machine (ex : standard C2PA).',
        'when' => [
            'ai_usage.type' => 'IA_GEN',
            'response.gen_contenu' => '@in:IMAGE,AUDIO,VIDEO',
            'response.diff' => '@in:TIERS,PUBLIC',
        ],
    ],

    [
        'id' => 'R-L-03',
        'niveau' => 'RISQUE_LIMITE',
        'article' => 'Art. 50 §4',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => 'Les hypertrucages (deepfakes) doivent être explicitement déclarés comme générés par IA lors de leur diffusion, sauf parodie ou satire clairement identifiée.',
        'when' => [
            'ai_usage.type' => 'IA_GEN',
            'response.gen_contenu' => 'DEEPFAKE',
            'response.diff' => '@in:TIERS,PUBLIC',
        ],
    ],

    [
        'id' => 'R-L-04',
        'niveau' => 'RISQUE_LIMITE',
        'article' => 'Art. 50 §3',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => "Tout système de reconnaissance d'émotions utilisé hors lieu de travail et hors éducation doit informer les personnes de son utilisation.",
        'when' => [
            'ai_usage.type' => 'IA_BIO',
            'response.bio_type' => 'RECOG_EMOTIONS',
            // R-I-01 traite déjà DOM ∈ {RH, EDUCATION} en INACCEPTABLE — donc
            // si on arrive ici, on est nécessairement hors RH/EDUCATION, mais
            // on garde la condition explicite pour la lisibilité de la règle.
            'ai_usage.domain' => '@in:CREDIT,SANTE,SECURITE,MARKETING,PROD_INT,DEV_LOG,AUTRE',
        ],
    ],

    [
        'id' => 'R-L-05',
        'niveau' => 'RISQUE_LIMITE',
        'article' => 'Art. 50 §3',
        'type_regle' => 'TEXTE_EXPLICITE',
        'applicable_from' => '2026-08-02',
        'raison' => 'Tout système de catégorisation biométrique doit informer les personnes concernées de son utilisation.',
        'when' => [
            'ai_usage.type' => 'IA_BIO',
            'response.bio_type' => 'CATEGORISATION',
            'response.bio_attr_sensibles' => 'NON',
        ],
    ],

    [
        'id' => 'R-L-06',
        'niveau' => 'RISQUE_LIMITE',
        'article' => 'Art. 50 §4 (interprétation — texte non couvert explicitement)',
        'type_regle' => 'INTERPRETATION',
        'applicable_from' => '2026-08-02',
        'raison' => "La diffusion publique de contenu textuel généré par IA est recommandée d'être signalée. Le texte n'est pas explicitement visé par Art. 50 §4, mais la bonne pratique l'impose.",
        'when' => [
            'ai_usage.type' => '@in:LLM_GEN,IA_GEN',
            'response.gen_contenu' => 'TEXTE',
            'response.diff' => 'PUBLIC',
            'response.pub' => '@intersects:CLIENTS,GRAND_PUBLIC',
            'response.interaction_directe' => 'NON',
        ],
    ],

    // =========================================================================
    // BLOC VENDOR — Chaîne d'approvisionnement (non classificatoires)
    // Ces règles ajoutent des alertes liées au fournisseur IA rattaché à
    // l'usage. Elles ne modifient jamais le niveau de risque retenu (la
    // classification AI Act dépend de l'usage, pas du fournisseur), mais
    // signalent au déployeur des manquements contractuels exigés par le
    // Règlement (Art. 47) ou le RGPD (Art. 28 + Ch. V transferts).
    // =========================================================================

    [
        'id' => 'R-VENDOR-TRANSFERT-RGPD',
        'classify' => false,
        'alerte' => [
            'code' => 'vendor_transfert_hors_ue_sans_cct',
            'type' => 'AGGRAVATION',
            'message' => "Fournisseur situé hors UE sans clauses contractuelles types (CCT) signées : transfert de données non encadré au sens du Chapitre V du RGPD. Risque d'invalidation à la Schrems III.",
            'article' => 'Art. 44-49 RGPD',
        ],
        'when' => [
            'vendor.hors_ue' => 'true',
            'vendor.cct_signees' => 'false',
        ],
    ],

    [
        'id' => 'R-VENDOR-ART47',
        'classify' => false,
        'requires_niveau' => 'HAUT_RISQUE',
        'alerte' => [
            'code' => 'vendor_pas_de_declaration_art47',
            'type' => 'AGGRAVATION',
            'message' => "Système haut risque mais le fournisseur n'a pas remis sa déclaration de conformité Art. 47 AI Act. Demandez impérativement le document et conservez-le dans votre dossier de conformité.",
            'article' => 'Art. 47 AI Act',
        ],
        'when' => [
            'vendor.declaration_conformite_art47' => 'false',
        ],
    ],

    [
        'id' => 'R-VENDOR-DPA-ART28',
        'classify' => false,
        'alerte' => [
            'code' => 'vendor_pas_de_dpa_art28',
            'type' => 'FLAG_ZONE_GRISE',
            'message' => 'Traitement de données personnelles confié à un fournisseur sans contrat de sous-traitance Art. 28 RGPD signé. À régulariser avant toute mise en production.',
            'article' => 'Art. 28 RGPD',
        ],
        'when' => [
            'response.data_personal' => 'yes',
            'vendor.dpa_art28_signe' => 'false',
        ],
    ],

    [
        'id' => 'R-VENDOR-MULTITENANT',
        'classify' => false,
        'alerte' => [
            'code' => 'vendor_saas_minimisation_donnees',
            'type' => 'FLAG_ZONE_GRISE',
            'message' => "Solution SaaS multi-tenant utilisée avec données sensibles : vérifiez la minimisation des données transmises au fournisseur (Art. 5.1.c RGPD). Privilégiez la pseudonymisation lorsque c'est possible.",
            'article' => 'Art. 5.1.c RGPD',
        ],
        'when' => [
            'vendor.type_contractuel' => 'SAAS',
            'response.data_sensitive' => 'yes',
        ],
    ],

    // =========================================================================
    // DEFAULT — RISQUE MINIMAL
    // =========================================================================
    [
        'id' => 'DEFAULT',
        'niveau' => 'RISQUE_MINIMAL',
        'article' => 'N/A',
        'type_regle' => 'NA',
        'raison' => "Aucun critère INACCEPTABLE, HAUT_RISQUE ou RISQUE_LIMITE n'est satisfait. Aucune obligation réglementaire AI Act spécifique.",
        'when' => [],
    ],
];
