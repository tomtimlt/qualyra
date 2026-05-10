<?php

declare(strict_types=1);

/*
 * Définition des questions du questionnaire AI Act — alignée matrice v1.1.
 *
 * Structure :
 *   - 'common'  : questions posées pour TOUS les types et tous les domaines.
 *   - 'types'   : questions additionnelles propres à chaque type d'IA
 *                 (clés : LLM_GEN, IA_GEN, IA_SCORING, IA_BIO, AUTRE).
 *   - 'domains' : questions additionnelles propres à un domaine d'usage
 *                 (clés : RH, EDUCATION, SANTE, MARKETING). Les domaines non
 *                 listés (PROD_INT, DEV_LOG, AUTRE, CREDIT, SECURITE) n'ont
 *                 pas de question conditionnelle dédiée.
 *
 * Le controller fusionne common + types[type] + domains[domain] pour
 * construire le formulaire d'un usage donné.
 *
 * Chaque question :
 *   - key         : identifiant stable persisté en BDD (responses.variable_key)
 *                   — NE PAS RENOMMER sans migration des données.
 *   - label       : intitulé affiché à l'utilisateur.
 *   - help        : texte d'aide (facultatif).
 *   - type        : 'radio' | 'select' | 'textarea' | 'checkbox'.
 *   - options     : ['valeur' => 'libellé'] (pour radio/select/checkbox).
 *   - required    : bool.
 *
 * Les `key` et les `option values` servent directement à la matrice de
 * classification (config/ai_act_rules.php). Les valeurs internes sont en
 * majuscules pour les énums matrice (DEC, PUB, DIFF, BIO_TYPE, RH_USAGE…)
 * et en minuscules pour les variables historiques (data_personal yes/no,
 * human_oversight always/sometimes/never, bio_realtime yes/no).
 */

return [
    'common' => [
        [
            'key' => 'finality',
            'label' => 'Finalité principale de l\'usage',
            'help' => 'Décrivez ce que l\'IA permet concrètement de faire dans votre activité.',
            'type' => 'textarea',
            'required' => true,
        ],
        [
            'key' => 'dec',
            'label' => 'Comment qualifieriez-vous la nature des décisions ou sorties produites par cette IA ?',
            'type' => 'radio',
            'options' => [
                'INFORMATIF' => 'Purement informatif (l\'humain décide entièrement)',
                'AIDE_DEC' => 'Aide à la décision (l\'humain décide après lecture)',
                'SEMI_AUTO' => 'Semi-automatique (l\'IA propose, l\'humain valide)',
                'FULL_AUTO' => 'Entièrement automatique (l\'IA décide sans validation)',
            ],
            'required' => true,
        ],
        // PUB multi-valeur stocké en CSV scalaire (cf. note dans la migration
        // responses : compatible colonne string + contrainte unique).
        [
            'key' => 'pub',
            'label' => 'Quel(s) public(s) est/sont concerné(s) par les sorties de cette IA ?',
            'help' => 'Plusieurs choix possibles.',
            'type' => 'checkbox',
            'options' => [
                'AUCUN' => 'Aucun public externe à l\'équipe',
                'EMPLOYES' => 'Salariés / collaborateurs internes',
                'CLIENTS' => 'Clients de l\'entreprise',
                'GRAND_PUBLIC' => 'Grand public',
                'VULNERABLES' => 'Personnes vulnérables (mineurs, précaires, handicap…)',
            ],
            'required' => true,
        ],
        [
            'key' => 'data_personal',
            'label' => 'L\'IA traite-t-elle des données personnelles ?',
            'type' => 'radio',
            'options' => [
                'yes' => 'Oui',
                'no' => 'Non',
                'unknown' => 'Je ne sais pas',
            ],
            'required' => true,
        ],
        [
            'key' => 'data_sensitive',
            'label' => 'L\'IA traite-t-elle des données sensibles (santé, biométrie, opinions) ?',
            'help' => 'Au sens de l\'article 9 du RGPD.',
            'type' => 'radio',
            'options' => [
                'yes' => 'Oui',
                'no' => 'Non',
                'unknown' => 'Je ne sais pas',
            ],
            'required' => true,
        ],
        [
            'key' => 'diff',
            'label' => 'Comment les sorties de cette IA sont-elles diffusées ?',
            'type' => 'radio',
            'options' => [
                'INTERNE' => 'Usage interne uniquement',
                'TIERS' => 'Diffusion à des tiers identifiés (clients, partenaires)',
                'PUBLIC' => 'Diffusion publique (site, réseaux sociaux, presse)',
            ],
            'required' => true,
        ],
        [
            'key' => 'human_oversight',
            'label' => 'Une supervision humaine est-elle prévue avant toute décision ?',
            'help' => 'Variable matrice CTRL : SYSTEMATIQUE / ECHANTILLON / AUCUN.',
            'type' => 'radio',
            'options' => [
                'always' => 'Oui, systématiquement',
                'sometimes' => 'Parfois (échantillonnage)',
                'never' => 'Non, décisions automatisées',
            ],
            'required' => true,
        ],
        [
            'key' => 'impact_individual',
            'label' => 'L\'IA peut-elle avoir un impact significatif sur des personnes ?',
            'help' => 'Recrutement, accès à un service, sanction, etc.',
            'type' => 'radio',
            'options' => [
                'yes' => 'Oui',
                'no' => 'Non',
            ],
            'required' => true,
        ],
        // Variable matrice USAGE_PRESTATIONS_ESSENTIELLES — transverse (la
        // condition d'affichage matrice combine DEC, PUB et DOM, ce qui ne se
        // prête pas à une simple section). On la pose toujours, non required,
        // avec une valeur par défaut implicite NON via "Non concerné".
        [
            'key' => 'usage_prestations_essentielles',
            'label' => 'Cet outil détermine-t-il l\'accès des personnes physiques à des prestations essentielles ?',
            'help' => 'Aide sociale, énergie, eau, services d\'urgence, soins de santé. Cocher uniquement si applicable — sinon laisser sur "Non concerné".',
            'type' => 'radio',
            'options' => [
                'NON' => 'Non concerné / sans rapport avec des prestations essentielles',
                'OUI' => 'Oui, l\'IA détermine ou influence l\'accès',
            ],
            'required' => false,
        ],
    ],

    'types' => [

        'LLM_GEN' => [
            [
                'key' => 'llm_provider',
                'label' => 'Fournisseur du LLM utilisé',
                'type' => 'select',
                'options' => [
                    'openai' => 'OpenAI (ChatGPT, GPT-4)',
                    'anthropic' => 'Anthropic (Claude)',
                    'google' => 'Google (Gemini)',
                    'mistral' => 'Mistral AI',
                    'meta' => 'Meta (Llama)',
                    'self_hosted' => 'Auto-hébergé',
                    'other' => 'Autre',
                ],
                'required' => true,
            ],
            [
                'key' => 'llm_data_input',
                'label' => 'Quelles données sont saisies dans le LLM ?',
                'type' => 'textarea',
                'required' => true,
            ],
            [
                'key' => 'llm_output_published',
                'label' => 'Les sorties du LLM sont-elles publiées sans relecture humaine ?',
                'type' => 'radio',
                'options' => [
                    'yes' => 'Oui',
                    'no' => 'Non',
                ],
                'required' => true,
            ],
            // Variable matrice INTERACTION_DIRECTE — déclenche R-L-01 et écarte R-L-06.
            [
                'key' => 'interaction_directe',
                'label' => 'Ce système interagit-il directement avec des utilisateurs en temps réel ?',
                'help' => 'Chatbot, agent conversationnel, assistant vocal.',
                'type' => 'radio',
                'options' => [
                    'OUI' => 'Oui',
                    'NON' => 'Non',
                ],
                'required' => true,
            ],
        ],

        'IA_GEN' => [
            // Variable matrice GEN_CONTENU. Remplace les anciennes
            // gen_content_type + gen_deepfake_risk pour aligner sur la matrice
            // (DEEPFAKE est une valeur de GEN_CONTENU, pas une variable parallèle).
            [
                'key' => 'gen_contenu',
                'label' => 'Quel type de contenu ce système génère-t-il ?',
                'type' => 'select',
                'options' => [
                    'TEXTE' => 'Texte',
                    'IMAGE' => 'Image',
                    'AUDIO' => 'Audio / voix',
                    'VIDEO' => 'Vidéo',
                    'DEEPFAKE' => 'Hypertrucage (deepfake) représentant des personnes réelles',
                ],
                'required' => true,
            ],
            [
                'key' => 'gen_disclosure',
                'label' => 'Les contenus générés sont-ils étiquetés comme produits par une IA ?',
                'type' => 'radio',
                'options' => [
                    'always' => 'Oui, toujours',
                    'sometimes' => 'Parfois',
                    'never' => 'Non',
                ],
                'required' => true,
            ],
            [
                'key' => 'interaction_directe',
                'label' => 'Ce système interagit-il directement avec des utilisateurs en temps réel ?',
                'help' => 'Avatar conversationnel, agent voix interactif.',
                'type' => 'radio',
                'options' => [
                    'OUI' => 'Oui',
                    'NON' => 'Non',
                ],
                'required' => true,
            ],
        ],

        'IA_SCORING' => [
            [
                'key' => 'scoring_target',
                'label' => 'Sur quoi porte le scoring ?',
                'type' => 'select',
                'options' => [
                    'credit' => 'Solvabilité / crédit',
                    'recruitment' => 'Recrutement / RH',
                    'insurance' => 'Assurance',
                    'education' => 'Évaluation éducative',
                    'social' => 'Notation sociale',
                    'other' => 'Autre',
                ],
                'required' => true,
            ],
            [
                'key' => 'scoring_decision_binding',
                'label' => 'Le score conditionne-t-il une décision automatique (refus, sanction) ?',
                'type' => 'radio',
                'options' => [
                    'yes' => 'Oui',
                    'no' => 'Non',
                ],
                'required' => true,
            ],
            [
                'key' => 'scoring_explainability',
                'label' => 'Le score est-il explicable à la personne concernée ?',
                'type' => 'radio',
                'options' => [
                    'yes' => 'Oui',
                    'partial' => 'Partiellement',
                    'no' => 'Non',
                ],
                'required' => true,
            ],
            // Variable matrice SCORING_PORTEE — déclenche R-I-03 (social scoring global).
            [
                'key' => 'scoring_portee',
                'label' => 'Ce scoring couvre-t-il plusieurs domaines de vie pour établir un profil global ?',
                'help' => 'Ex : croisement emploi + finances + comportement social. Distinct d\'un scoring contextuel.',
                'type' => 'radio',
                'options' => [
                    'CONTEXTUEL' => 'Non, scoring strictement contextuel (un seul domaine)',
                    'GLOBAL_MULTI_DOMAINES' => 'Oui, scoring transversal multi-domaines',
                ],
                'required' => true,
            ],
            // Variable matrice PREDICTION_CRIMINELLE — déclenche R-I-08
            // (uniquement pertinent si DOM=SECURITE ; on pose la question pour
            // tout IA_SCORING avec un help text explicite).
            [
                'key' => 'prediction_criminelle',
                'label' => 'Ce système évalue-t-il le risque qu\'une personne commette une infraction pénale ?',
                'help' => 'Profilage ou traits de personnalité comme base d\'évaluation. Cocher OUI uniquement si applicable.',
                'type' => 'radio',
                'options' => [
                    'NON' => 'Non',
                    'OUI' => 'Oui',
                ],
                'required' => true,
            ],
        ],

        'IA_BIO' => [
            // Variable matrice BIO_TYPE — sous-type fonctionnel (orthogonal à la
            // modalité technique bio_modality qui reste pour l'inventaire RGPD).
            [
                'key' => 'bio_type',
                'label' => 'Quel est le sous-type de ce système biométrique ?',
                'type' => 'select',
                'options' => [
                    'IDENTIFICATION' => 'Identification de personnes (qui est-ce ?)',
                    'CONTROLE_ACCES' => 'Contrôle d\'accès (autorisé / non autorisé)',
                    'RECOG_EMOTIONS' => 'Reconnaissance d\'émotions',
                    'CATEGORISATION' => 'Catégorisation (segmentation par caractéristiques)',
                ],
                'required' => true,
            ],
            [
                'key' => 'bio_modality',
                'label' => 'Modalité biométrique utilisée',
                'type' => 'select',
                'options' => [
                    'face' => 'Reconnaissance faciale',
                    'voice' => 'Empreinte vocale',
                    'fingerprint' => 'Empreinte digitale',
                    'iris' => 'Iris / rétine',
                    'gait' => 'Démarche / posture',
                    'other' => 'Autre',
                ],
                'required' => true,
            ],
            // Variable matrice BIO_SOURCE_DONNEES — déclenche R-I-06.
            [
                'key' => 'bio_source_donnees',
                'label' => 'Quelle est la source des données biométriques utilisées ?',
                'type' => 'select',
                'options' => [
                    'FOURNIES_LICITES' => 'Datasets fournis avec base légale claire',
                    'SCRAPING_INTERNET' => 'Collecte non ciblée sur Internet (scraping web/réseaux sociaux)',
                    'CCTV_NON_CIBLE' => 'Vidéosurveillance générale non ciblée',
                    'AUTRE_SOURCE' => 'Autre source',
                ],
                'required' => true,
            ],
            // Variable matrice BIO_ATTR_SENSIBLES — déclenche R-I-02 ou R-L-05.
            [
                'key' => 'bio_attr_sensibles',
                'label' => 'Ce système infère-t-il des attributs sensibles (race, religion, orientation sexuelle, opinions politiques, syndicat) ?',
                'help' => 'Pertinent uniquement si BIO_TYPE = catégorisation. Sinon répondre NON.',
                'type' => 'radio',
                'options' => [
                    'NON' => 'Non',
                    'OUI' => 'Oui',
                ],
                'required' => true,
            ],
            [
                'key' => 'bio_realtime',
                'label' => 'L\'identification se fait-elle en temps réel dans un espace public ?',
                'help' => 'Cas explicitement encadré par l\'AI Act (Article 5 §1 h).',
                'type' => 'radio',
                'options' => [
                    'yes' => 'Oui',
                    'no' => 'Non',
                ],
                'required' => true,
            ],
            [
                'key' => 'bio_consent',
                'label' => 'Les personnes sont-elles informées et ont-elles donné leur consentement ?',
                'type' => 'radio',
                'options' => [
                    'yes' => 'Oui',
                    'no' => 'Non',
                    'partial' => 'Partiellement',
                ],
                'required' => true,
            ],
        ],

        'AUTRE' => [
            [
                'key' => 'other_description',
                'label' => 'Décrivez le fonctionnement de l\'IA',
                'help' => 'Type d\'algorithme, fournisseur, données utilisées.',
                'type' => 'textarea',
                'required' => true,
            ],
            [
                'key' => 'other_automation_level',
                'label' => 'Niveau d\'automatisation des décisions',
                'type' => 'radio',
                'options' => [
                    'full' => 'Décisions entièrement automatisées',
                    'assisted' => 'Aide à la décision humaine',
                    'none' => 'Aucune décision impactante',
                ],
                'required' => true,
            ],
        ],
    ],

    'domains' => [

        // Variable matrice RH_USAGE — déclenche R-H-02, R-H-03, R-H-BORDERLINE.
        'RH' => [
            [
                'key' => 'rh_usage',
                'label' => 'Quel est l\'usage RH spécifique de ce système ?',
                'type' => 'select',
                'options' => [
                    'TRI_CV' => 'Tri / présélection de CV',
                    'SCORING_CANDIDATS' => 'Scoring de candidats',
                    'DECISION_EMBAUCHE' => 'Décision d\'embauche',
                    'EVAL_PERFORMANCE' => 'Évaluation de la performance des salariés',
                    'SURVEILLANCE_COMPORTEMENT' => 'Surveillance comportementale (productivité, télétravail)',
                    'DECISION_PROMOTION' => 'Décision de promotion',
                    'DECISION_LICENCIEMENT' => 'Décision de licenciement',
                    'ATTRIBUTION_TACHES' => 'Attribution / répartition des tâches',
                    'REDACTION_OFFRES' => 'Rédaction d\'offres d\'emploi (sans contact candidat)',
                ],
                'required' => true,
            ],
        ],

        // Variable matrice EDUC_USAGE — déclenche R-H-04.
        'EDUCATION' => [
            [
                'key' => 'educ_usage',
                'label' => 'Quel est l\'usage éducatif spécifique de ce système ?',
                'type' => 'select',
                'options' => [
                    'ADMISSION' => 'Décision d\'admission / sélection en formation',
                    'ORIENTATION' => 'Orientation professionnelle ou de filière',
                    'EVALUATION_ACADEMIQUE' => 'Notation / évaluation académique automatisée',
                    'RECOMMENDATION_CONTENU' => 'Recommandation de contenu pédagogique',
                    'AUTRE' => 'Autre usage éducatif',
                ],
                'required' => true,
            ],
        ],

        // Variable matrice SANTE_FINALITE — déclenche R-H-06 et R-H-08.
        'SANTE' => [
            [
                'key' => 'sante_finalite',
                'label' => 'Quelle est la finalité de cet outil dans le domaine de la santé ?',
                'type' => 'select',
                'options' => [
                    'GESTION_INTERNE' => 'Gestion administrative interne (planning, dossiers, factures)',
                    'ASSURANCE_RISQUE' => 'Évaluation de risque pour assurance santé / vie',
                    'DECISION_MEDICALE' => 'Aide à la décision médicale clinique',
                ],
                'required' => true,
            ],
        ],

        // Variables matrice TECHNIQUES_SUBLIMINALES (R-I-07) et
        // PERSUASION_PSYCHOLOGIQUE (R-I-04). Posées en MARKETING car c'est le
        // domaine où le risque est concentré ; la condition d'affichage de la
        // matrice mentionne aussi PUB∋VULNERABLES, mais l'évaluation finale
        // par les rules suffit pour ne pas faire de faux positif.
        'MARKETING' => [
            [
                'key' => 'techniques_subliminales',
                'label' => 'Le système utilise-t-il des techniques subliminales ou délibérément trompeuses pour influencer le comportement ?',
                'help' => 'Réponse OUI = pratique prohibée (Art. 5 §1 a).',
                'type' => 'radio',
                'options' => [
                    'NON' => 'Non',
                    'OUI' => 'Oui',
                ],
                'required' => true,
            ],
            [
                'key' => 'persuasion_psychologique',
                'label' => 'Le système utilise-t-il des techniques de persuasion ciblées sur les vulnérabilités des personnes (âge, handicap, précarité) ?',
                'help' => 'Réponse OUI + PUB∋VULNERABLES = pratique prohibée (Art. 5 §1 b).',
                'type' => 'radio',
                'options' => [
                    'NON' => 'Non',
                    'OUI' => 'Oui',
                ],
                'required' => true,
            ],
        ],
    ],
];
