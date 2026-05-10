<?php

declare(strict_types=1);

/*
 * Définition des questions du questionnaire AI Act.
 *
 * Structure :
 *   - 'common' : questions posées pour TOUS les types d'IA
 *   - <TYPE>   : questions additionnelles propres à chaque type
 *                (clés alignées sur AiUsage::type : LLM_GEN, IA_GEN, IA_SCORING, IA_BIO, AUTRE)
 *
 * Chaque question :
 *   - key         : identifiant stable persisté en BDD (responses.variable_key) — NE PAS RENOMMER
 *   - label       : intitulé affiché à l'utilisateur
 *   - help        : texte d'aide (facultatif)
 *   - type        : 'radio' | 'select' | 'textarea'
 *   - options     : ['valeur' => 'libellé'] (pour radio/select)
 *   - required    : bool
 *
 * Les `key` servent aussi à la matrice de classification de la semaine 5
 * (moteur de scoring AI Act). Toute modification de clé = migration de données.
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
        // Variable matrice DEC — nature de la décision produite par l'IA.
        // Valeurs internes alignées strictement sur la matrice v1.1.
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
        // Variable matrice PUB — multi-valeur. Stockage : CSV dans variable_value
        // (ex: "EMPLOYES,CLIENTS"). Choix CSV plutôt que JSON pour rester
        // compatible avec la colonne string + contrainte unique (ai_usage_id, key)
        // sans migration. L'UI utilise des cases à cocher (type 'checkbox').
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
        // Variable matrice DIFF — diffusion de la sortie.
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
    ],

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
    ],

    'IA_GEN' => [
        [
            'key' => 'gen_content_type',
            'label' => 'Type de contenu généré',
            'type' => 'select',
            'options' => [
                'image' => 'Image',
                'audio' => 'Audio / voix',
                'video' => 'Vidéo',
                'mixed' => 'Mixte',
            ],
            'required' => true,
        ],
        [
            'key' => 'gen_deepfake_risk',
            'label' => 'Le contenu généré peut-il représenter une personne réelle (risque deepfake) ?',
            'type' => 'radio',
            'options' => [
                'yes' => 'Oui',
                'no' => 'Non',
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
    ],

    'IA_BIO' => [
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
        [
            'key' => 'bio_realtime',
            'label' => 'L\'identification se fait-elle en temps réel dans un espace public ?',
            'help' => 'Cas explicitement encadré par l\'AI Act (Article 5).',
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
];
