<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Services\AiActClassifier;
use Illuminate\Database\Seeder;

/*
 * Seeder dédié au compte de démo (demo@example.com).
 *
 * Construit un cas concret riche : "Nova Conseil & Services", PME française
 * multi-services (220 salariés, conseil + RH externalisé + développement IT).
 * Les 30 usages IA déclarés couvrent les 9 domaines et les 4 niveaux AI Act
 * pour une démo visuellement parlante (heatmap remplie sur toutes les lignes)
 * et pédagogiquement complète. Distribution :
 *   -  2 INACCEPTABLE  (Art. 5)
 *   -  8 HAUT_RISQUE   (Annexe III)
 *   -  8 RISQUE_LIMITE (Art. 50)
 *   - 12 RISQUE_MINIMAL (DEFAULT)
 *
 * Toutes les responses sont alignées sur config/questionnaire.php
 * et conçues pour déclencher la règle attendue dans config/ai_act_rules.php.
 *
 * IDÉMPOTENT : repasser le seeder purge l'organisation existante de demo@
 * et la recrée à zéro. Ne touche jamais à un autre user.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo User', 'password' => bcrypt('password')]
        );

        // Purge propre : la suppression de l'organisation cascade sur ai_usages,
        // responses, assessments et reports (FK onDelete cascade).
        $user->organization?->delete();
        $user->refresh();

        $organization = $user->organization()->create([
            'name' => 'Nova Conseil & Services',
            'siret' => '78451209300048',
            'size' => '150+',
            'sector' => 'Conseil & services aux entreprises',
        ]);

        $classifier = app(AiActClassifier::class);

        foreach ($this->usages() as $payload) {
            $usage = $organization->aiUsages()->create($payload['usage']);

            foreach ($payload['answers'] as $key => $value) {
                $usage->responses()->create([
                    'variable_key' => $key,
                    'variable_value' => $value,
                ]);
            }

            $classifier->persist($usage->fresh('responses'));
        }
    }

    /**
     * @return array<int, array{usage: array<string, string>, answers: array<string, string>}>
     */
    private function usages(): array
    {
        return [
            // =================================================================
            // INACCEPTABLE (2)
            // =================================================================

            // R-I-05 : Identification biométrique temps réel public
            [
                'usage' => [
                    'name' => 'Reconnaissance faciale au siège (pilote sécurité)',
                    'description' => 'Caméra à l\'entrée du siège qui identifie en temps réel les visiteurs et alerte si correspondance avec une liste de personnes signalées par un prestataire.',
                    'type' => 'IA_BIO',
                    'domain' => 'SECURITE',
                ],
                'answers' => [
                    'finality' => 'Identifier instantanément les visiteurs entrant dans le hall et déclencher une alerte si correspondance.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'GRAND_PUBLIC,VULNERABLES',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'yes',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'NON',
                    'bio_type' => 'IDENTIFICATION',
                    'bio_modality' => 'face',
                    'bio_source_donnees' => 'AUTRE_SOURCE',
                    'bio_attr_sensibles' => 'NON',
                    'bio_realtime' => 'yes',
                    'bio_consent' => 'no',
                ],
            ],

            // R-I-03 : Social scoring transversal (multi-domaines)
            [
                'usage' => [
                    'name' => 'Scoring social interne salariés (récompenses & sanctions)',
                    'description' => 'Note transversale calculée pour chaque salarié à partir de la présence, productivité, comportement social interne, satisfaction collègues et activité hors travail. Déclenche automatiquement primes ou avertissements.',
                    'type' => 'IA_SCORING',
                    'domain' => 'RH',
                ],
                'answers' => [
                    'finality' => 'Évaluation transversale comportementale des salariés, multi-domaines, avec conséquences automatiques.',
                    'dec' => 'SEMI_AUTO',
                    'pub' => 'EMPLOYES',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'yes',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'never',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'NON',
                    'scoring_target' => 'social',
                    'scoring_decision_binding' => 'yes',
                    'scoring_explainability' => 'opaque',
                    'scoring_portee' => 'GLOBAL_MULTI_DOMAINES',
                    'prediction_criminelle' => 'NON',
                    'rh_usage' => 'EVAL_PERFORMANCE',
                ],
            ],

            // =================================================================
            // HAUT_RISQUE (8)
            // =================================================================

            // R-H-02 : RH sélection candidats
            [
                'usage' => [
                    'name' => 'Pré-tri CV pour clients RH externalisé',
                    'description' => 'Filtrage automatique des CV reçus dans le cadre des missions de recrutement délégué (clients PME). Score d\'adéquation au poste avant entretien consultant.',
                    'type' => 'IA_SCORING',
                    'domain' => 'RH',
                ],
                'answers' => [
                    'finality' => 'Réduire le temps de pré-sélection en classant les CV selon leur adéquation au poste.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'EMPLOYES',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'NON',
                    'scoring_target' => 'recruitment',
                    'scoring_decision_binding' => 'no',
                    'scoring_explainability' => 'partial',
                    'scoring_portee' => 'CONTEXTUEL',
                    'prediction_criminelle' => 'NON',
                    'rh_usage' => 'TRI_CV',
                ],
            ],

            // R-H-03 : RH évaluation performance / surveillance salariés
            [
                'usage' => [
                    'name' => 'Évaluation annuelle des consultants',
                    'description' => 'Synthèse automatique des KPI de chaque consultant (CA généré, satisfaction client, ponctualité reporting) pour préparer l\'entretien annuel et arbitrer les promotions.',
                    'type' => 'IA_SCORING',
                    'domain' => 'RH',
                ],
                'answers' => [
                    'finality' => 'Préparer les évaluations annuelles avec une synthèse standardisée des indicateurs.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'EMPLOYES',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'NON',
                    'scoring_target' => 'performance',
                    'scoring_decision_binding' => 'no',
                    'scoring_explainability' => 'partial',
                    'scoring_portee' => 'CONTEXTUEL',
                    'prediction_criminelle' => 'NON',
                    'rh_usage' => 'EVAL_PERFORMANCE',
                ],
            ],

            // R-H-05 : Scoring crédit / solvabilité
            [
                'usage' => [
                    'name' => 'Scoring solvabilité prospects entreprises',
                    'description' => 'Évaluation automatisée du risque de défaut de paiement des prospects PME pour décider de l\'acceptation et des conditions de paiement (acompte, échéancier).',
                    'type' => 'IA_SCORING',
                    'domain' => 'CREDIT',
                ],
                'answers' => [
                    'finality' => 'Décider rapidement des conditions de paiement à proposer aux prospects.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'CLIENTS',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'NON',
                    'scoring_target' => 'credit',
                    'scoring_decision_binding' => 'yes',
                    'scoring_explainability' => 'partial',
                    'scoring_portee' => 'CONTEXTUEL',
                    'prediction_criminelle' => 'NON',
                ],
            ],

            // R-H-08 : Décision médicale clinique (zone grise MDR)
            [
                'usage' => [
                    'name' => 'Aide diagnostique pour clinique cliente',
                    'description' => 'Assistance déployée chez un client clinique : suggestion de diagnostics différentiels à partir des résultats d\'examens. Le médecin valide systématiquement.',
                    'type' => 'IA_SCORING',
                    'domain' => 'SANTE',
                ],
                'answers' => [
                    'finality' => 'Aider les médecins à ne pas oublier de pistes diagnostiques rares.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'CLIENTS',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'yes',
                    'diff' => 'TIERS',
                    'human_oversight' => 'always',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'NON',
                    'scoring_target' => 'medical',
                    'scoring_decision_binding' => 'no',
                    'scoring_explainability' => 'partial',
                    'scoring_portee' => 'CONTEXTUEL',
                    'prediction_criminelle' => 'NON',
                    'sante_finalite' => 'DECISION_MEDICALE',
                ],
            ],

            // R-H-01 : Biométrie contrôle d'accès (haut risque)
            [
                'usage' => [
                    'name' => 'Contrôle d\'accès biométrique data center',
                    'description' => 'Empreinte digitale + visage pour autoriser l\'entrée au data center hébergeant les données client. Consentement signé des techniciens.',
                    'type' => 'IA_BIO',
                    'domain' => 'SECURITE',
                ],
                'answers' => [
                    'finality' => 'Sécuriser physiquement l\'accès au data center.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'EMPLOYES',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'yes',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'NON',
                    'bio_type' => 'CONTROLE_ACCES',
                    'bio_modality' => 'fingerprint',
                    'bio_source_donnees' => 'FOURNIES_LICITES',
                    'bio_attr_sensibles' => 'NON',
                    'bio_realtime' => 'no',
                    'bio_consent' => 'yes',
                ],
            ],

            // R-H-04 : Évaluation académique
            [
                'usage' => [
                    'name' => 'Évaluation des certifications professionnelles internes',
                    'description' => 'Notation automatique des QCM de certification métier passés par les consultants pour valider leurs accréditations.',
                    'type' => 'IA_SCORING',
                    'domain' => 'EDUCATION',
                ],
                'answers' => [
                    'finality' => 'Évaluer rapidement et de manière homogène les certifications internes.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'EMPLOYES',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'NON',
                    'scoring_target' => 'evaluation',
                    'scoring_decision_binding' => 'yes',
                    'scoring_explainability' => 'partial',
                    'scoring_portee' => 'CONTEXTUEL',
                    'prediction_criminelle' => 'NON',
                    'educ_usage' => 'EVALUATION_ACADEMIQUE',
                ],
            ],

            // R-H-06 : Assurance santé
            [
                'usage' => [
                    'name' => 'Scoring risque santé pour assurance clients PME',
                    'description' => 'Estimation du risque santé des dirigeants des PME clientes pour calibrer les contrats d\'assurance homme-clé proposés.',
                    'type' => 'IA_SCORING',
                    'domain' => 'SANTE',
                ],
                'answers' => [
                    'finality' => 'Tarifer les contrats d\'assurance homme-clé selon le profil de risque santé.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'CLIENTS',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'yes',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'NON',
                    'scoring_target' => 'insurance',
                    'scoring_decision_binding' => 'yes',
                    'scoring_explainability' => 'partial',
                    'scoring_portee' => 'CONTEXTUEL',
                    'prediction_criminelle' => 'NON',
                    'sante_finalite' => 'ASSURANCE_RISQUE',
                ],
            ],

            // R-H-07 : Prestations essentielles
            [
                'usage' => [
                    'name' => 'Pré-évaluation droits aux aides sociales (mission CCAS)',
                    'description' => 'Outil déployé pour un CCAS client : pré-qualification des dossiers d\'aide sociale (RSA, FSL) avant instruction par un agent.',
                    'type' => 'IA_SCORING',
                    'domain' => 'AUTRE',
                ],
                'answers' => [
                    'finality' => 'Accélérer l\'instruction des dossiers d\'aide sociale.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'GRAND_PUBLIC,VULNERABLES',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'yes',
                    'diff' => 'TIERS',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'OUI',
                    'scoring_target' => 'eligibility',
                    'scoring_decision_binding' => 'no',
                    'scoring_explainability' => 'partial',
                    'scoring_portee' => 'CONTEXTUEL',
                    'prediction_criminelle' => 'NON',
                ],
            ],

            // =================================================================
            // RISQUE_LIMITE (8)
            // =================================================================

            // R-L-01 : Chatbot interaction directe public
            [
                'usage' => [
                    'name' => 'Chatbot d\'accueil sur nova-conseil.fr',
                    'description' => 'Bot conversationnel sur le site web public pour qualifier les demandes entrantes et orienter vers le bon consultant.',
                    'type' => 'LLM_GEN',
                    'domain' => 'MARKETING',
                ],
                'answers' => [
                    'finality' => 'Qualifier les demandes entrantes 24/7 et orienter vers le commercial pertinent.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'GRAND_PUBLIC,CLIENTS',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'no',
                    'diff' => 'PUBLIC',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'openai',
                    'llm_data_input' => 'Messages des visiteurs du site web.',
                    'llm_output_published' => 'yes',
                    'interaction_directe' => 'OUI',
                ],
            ],

            // R-L-02 : Génération images publiées
            [
                'usage' => [
                    'name' => 'Visuels marketing générés (campagnes LinkedIn)',
                    'description' => 'Création d\'illustrations photoréalistes pour les posts LinkedIn de l\'entreprise (équipe, témoignages clients fictifs typés).',
                    'type' => 'IA_GEN',
                    'domain' => 'MARKETING',
                ],
                'answers' => [
                    'finality' => 'Disposer rapidement de visuels pour les campagnes social media sans séances photo.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'GRAND_PUBLIC',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'PUBLIC',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'gen_contenu' => 'IMAGE',
                    'gen_disclosure' => 'sometimes',
                    'interaction_directe' => 'NON',
                    'techniques_subliminales' => 'NON',
                    'persuasion_psychologique' => 'NON',
                ],
            ],

            // R-L-03 : Deepfakes
            [
                'usage' => [
                    'name' => 'Avatars synthétiques pour modules e-learning clients',
                    'description' => 'Vidéos de formation où des avatars vidéo synthétiques (apparence humaine) présentent les modules — diffusées aux clients via le LMS.',
                    'type' => 'IA_GEN',
                    'domain' => 'EDUCATION',
                ],
                'answers' => [
                    'finality' => 'Industrialiser la production de modules e-learning sans tournages.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'CLIENTS',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'TIERS',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'gen_contenu' => 'DEEPFAKE',
                    'gen_disclosure' => 'always',
                    'interaction_directe' => 'NON',
                    'techniques_subliminales' => 'NON',
                    'persuasion_psychologique' => 'NON',
                    'educ_usage' => 'AUTRE',
                ],
            ],

            // R-L-06 : Texte généré publié
            [
                'usage' => [
                    'name' => 'Articles de blog SEO générés et publiés',
                    'description' => 'Production d\'articles longs (1500 mots) pour le blog corporate sur des sujets de fond. Relecture humaine avant publication.',
                    'type' => 'LLM_GEN',
                    'domain' => 'MARKETING',
                ],
                'answers' => [
                    'finality' => 'Maintenir une cadence éditoriale SEO sans mobiliser de rédacteurs à plein temps.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'GRAND_PUBLIC',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'PUBLIC',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'anthropic',
                    'llm_data_input' => 'Briefs SEO et mots-clés.',
                    'llm_output_published' => 'yes',
                    'gen_contenu' => 'TEXTE',
                    'gen_disclosure' => 'sometimes',
                    'interaction_directe' => 'NON',
                ],
            ],

            // R-L-05 : Catégorisation biométrique sans attributs sensibles
            [
                'usage' => [
                    'name' => 'Tracking d\'affluence et catégorisation visiteurs salons',
                    'description' => 'Comptage et catégorisation grossière (âge approximatif, genre déclaré au scan badge) des visiteurs des stands lors des salons professionnels.',
                    'type' => 'IA_BIO',
                    'domain' => 'MARKETING',
                ],
                'answers' => [
                    'finality' => 'Mesurer la fréquentation et adapter les ressources commerciales.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'GRAND_PUBLIC',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'bio_type' => 'CATEGORISATION',
                    'bio_modality' => 'face',
                    'bio_source_donnees' => 'FOURNIES_LICITES',
                    'bio_attr_sensibles' => 'NON',
                    'bio_realtime' => 'no',
                    'bio_consent' => 'yes',
                ],
            ],

            // R-L-04 : Reconnaissance émotions hors RH/EDU
            [
                'usage' => [
                    'name' => 'Analyse de satisfaction post-mission par caméra (R&D)',
                    'description' => 'Pilote R&D : analyse des micro-expressions des clients lors de la restitution de mission pour mesurer leur satisfaction réelle.',
                    'type' => 'IA_BIO',
                    'domain' => 'MARKETING',
                ],
                'answers' => [
                    'finality' => 'Compléter les NPS écrits par une mesure non déclarative.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'CLIENTS',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'yes',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'bio_type' => 'RECOG_EMOTIONS',
                    'bio_modality' => 'face',
                    'bio_source_donnees' => 'FOURNIES_LICITES',
                    'bio_attr_sensibles' => 'NON',
                    'bio_realtime' => 'no',
                    'bio_consent' => 'yes',
                ],
            ],

            // R-L-01 : Chatbot RH (interaction directe candidats)
            [
                'usage' => [
                    'name' => 'Chatbot d\'orientation candidats sur portail recrutement',
                    'description' => 'Bot conversationnel sur le portail carrière pour répondre aux questions des candidats et les orienter vers les offres pertinentes.',
                    'type' => 'LLM_GEN',
                    'domain' => 'RH',
                ],
                'answers' => [
                    'finality' => 'Améliorer l\'expérience candidat et qualifier les profils en amont.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'GRAND_PUBLIC',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'no',
                    'diff' => 'TIERS',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'openai',
                    'llm_data_input' => 'Messages des candidats sur le portail recrutement.',
                    'llm_output_published' => 'yes',
                    'interaction_directe' => 'OUI',
                    'rh_usage' => 'REDACTION_OFFRES',
                ],
            ],

            // R-L-02 : Audio synthétique (podcast)
            [
                'usage' => [
                    'name' => 'Podcasts d\'expert générés en voix synthétique',
                    'description' => 'Adaptation audio des articles de blog en podcast hebdomadaire avec voix synthétique de "l\'expert maison" (personnage fictif).',
                    'type' => 'IA_GEN',
                    'domain' => 'MARKETING',
                ],
                'answers' => [
                    'finality' => 'Diffuser le contenu en format audio pour toucher une audience mobile.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'GRAND_PUBLIC',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'PUBLIC',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'gen_contenu' => 'AUDIO',
                    'gen_disclosure' => 'sometimes',
                    'interaction_directe' => 'NON',
                    'techniques_subliminales' => 'NON',
                    'persuasion_psychologique' => 'NON',
                ],
            ],

            // =================================================================
            // RISQUE_MINIMAL (12) — DEFAULT (aucune règle ne matche)
            // =================================================================

            // 1. Copilot dev
            [
                'usage' => [
                    'name' => 'GitHub Copilot pour l\'équipe Dev',
                    'description' => 'Assistant de complétion de code utilisé par les 12 développeurs internes sur les outils métiers et les missions client.',
                    'type' => 'LLM_GEN',
                    'domain' => 'DEV_LOG',
                ],
                'answers' => [
                    'finality' => 'Accélérer le développement en suggérant du code à la volée.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'openai',
                    'llm_data_input' => 'Code source des projets internes et clients.',
                    'llm_output_published' => 'no',
                    'interaction_directe' => 'NON',
                ],
            ],

            // 2. ChatGPT propositions commerciales
            [
                'usage' => [
                    'name' => 'ChatGPT pour rédaction de propositions commerciales',
                    'description' => 'Outil utilisé par les commerciaux pour reformuler les propositions tarifaires et notes de cadrage avant envoi.',
                    'type' => 'LLM_GEN',
                    'domain' => 'PROD_INT',
                ],
                'answers' => [
                    'finality' => 'Gagner du temps sur la rédaction commerciale et homogénéiser le ton.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'openai',
                    'llm_data_input' => 'Brouillons de propositions commerciales.',
                    'llm_output_published' => 'no',
                    'interaction_directe' => 'NON',
                ],
            ],

            // 3. Notion AI résumés réunions
            [
                'usage' => [
                    'name' => 'Notion AI pour résumés de réunions internes',
                    'description' => 'Synthèse automatique des comptes-rendus de réunion équipe stockés dans Notion.',
                    'type' => 'LLM_GEN',
                    'domain' => 'PROD_INT',
                ],
                'answers' => [
                    'finality' => 'Disposer de TL;DR exploitables sans relire les CR complets.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'anthropic',
                    'llm_data_input' => 'Comptes-rendus de réunion.',
                    'llm_output_published' => 'no',
                    'interaction_directe' => 'NON',
                ],
            ],

            // 4. OCR factures
            [
                'usage' => [
                    'name' => 'OCR factures fournisseurs (saisie comptable)',
                    'description' => 'Reconnaissance optique des factures fournisseurs pour pré-remplissage automatique de l\'outil compta.',
                    'type' => 'AUTRE',
                    'domain' => 'AUTRE',
                ],
                'answers' => [
                    'finality' => 'Éviter la saisie manuelle des factures par le service compta.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                ],
            ],

            // 5. Auto-complétion Outlook
            [
                'usage' => [
                    'name' => 'Auto-complétion intelligente Outlook',
                    'description' => 'Suggestions de phrases et de complétion automatique dans la rédaction des emails internes.',
                    'type' => 'LLM_GEN',
                    'domain' => 'PROD_INT',
                ],
                'answers' => [
                    'finality' => 'Accélérer la rédaction des emails.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'microsoft',
                    'llm_data_input' => 'Emails en cours de rédaction.',
                    'llm_output_published' => 'no',
                    'interaction_directe' => 'NON',
                ],
            ],

            // 6. Suggestions Teams
            [
                'usage' => [
                    'name' => 'Suggestions de réponses Microsoft Teams',
                    'description' => 'Réponses suggérées automatiquement dans les chats Teams (ack rapide).',
                    'type' => 'LLM_GEN',
                    'domain' => 'PROD_INT',
                ],
                'answers' => [
                    'finality' => 'Accélérer les réponses courtes en chat.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'microsoft',
                    'llm_data_input' => 'Conversations Teams en cours.',
                    'llm_output_published' => 'no',
                    'interaction_directe' => 'NON',
                ],
            ],

            // 7. Tagging tickets support
            [
                'usage' => [
                    'name' => 'Tagging automatique des tickets support interne',
                    'description' => 'Classification automatique des tickets support IT internes (catégorie, urgence) à partir du libellé.',
                    'type' => 'IA_SCORING',
                    'domain' => 'PROD_INT',
                ],
                'answers' => [
                    'finality' => 'Trier les tickets pour les router au bon technicien.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'scoring_target' => 'classification',
                    'scoring_decision_binding' => 'no',
                    'scoring_explainability' => 'partial',
                    'scoring_portee' => 'CONTEXTUEL',
                    'prediction_criminelle' => 'NON',
                ],
            ],

            // 8. Anti-spam
            [
                'usage' => [
                    'name' => 'Filtre anti-spam des boîtes mail entreprise',
                    'description' => 'Filtre intégré au serveur mail qui détecte et déplace automatiquement les courriels indésirables.',
                    'type' => 'AUTRE',
                    'domain' => 'PROD_INT',
                ],
                'answers' => [
                    'finality' => 'Préserver la productivité en filtrant les emails indésirables.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                ],
            ],

            // 9. Détection anomalies logs
            [
                'usage' => [
                    'name' => 'Détection d\'anomalies sur logs serveur (SOC interne)',
                    'description' => 'Surveillance automatique des logs Linux/Nginx pour signaler les patterns suspects à l\'équipe sécurité.',
                    'type' => 'IA_SCORING',
                    'domain' => 'DEV_LOG',
                ],
                'answers' => [
                    'finality' => 'Détecter rapidement les incidents de sécurité.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'scoring_target' => 'anomaly',
                    'scoring_decision_binding' => 'no',
                    'scoring_explainability' => 'partial',
                    'scoring_portee' => 'CONTEXTUEL',
                    'prediction_criminelle' => 'NON',
                ],
            ],

            // 10. Auto-complétion BDD
            [
                'usage' => [
                    'name' => 'Suggestion de schémas SQL via LLM',
                    'description' => 'Outil interne pour aider les devs à créer/modifier des schémas BDD à partir d\'une description en langage naturel.',
                    'type' => 'LLM_GEN',
                    'domain' => 'DEV_LOG',
                ],
                'answers' => [
                    'finality' => 'Accélérer les phases de modélisation BDD.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'anthropic',
                    'llm_data_input' => 'Descriptions de schémas en langage naturel.',
                    'llm_output_published' => 'no',
                    'interaction_directe' => 'NON',
                ],
            ],

            // 11. Scoring fournisseurs interne (PAS R-H-05 car pub=AUCUN)
            [
                'usage' => [
                    'name' => 'Scoring interne risque fournisseurs (achats)',
                    'description' => 'Évaluation interne du risque de défaillance des fournisseurs pour aider la direction achats. N\'expose rien aux fournisseurs eux-mêmes.',
                    'type' => 'IA_SCORING',
                    'domain' => 'CREDIT',
                ],
                'answers' => [
                    'finality' => 'Anticiper les défaillances fournisseurs et diversifier les sources critiques.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'scoring_target' => 'supplier_risk',
                    'scoring_decision_binding' => 'no',
                    'scoring_explainability' => 'partial',
                    'scoring_portee' => 'CONTEXTUEL',
                    'prediction_criminelle' => 'NON',
                ],
            ],

            // 12. Visualisations RH internes (PAS R-L-02 car diff=INTERNE)
            [
                'usage' => [
                    'name' => 'Visualisations RH générées (rapports internes)',
                    'description' => 'Génération automatique de graphiques et infographies pour les rapports RH internes (turnover, parité, formations).',
                    'type' => 'IA_GEN',
                    'domain' => 'RH',
                ],
                'answers' => [
                    'finality' => 'Produire des visuels lisibles pour les comités RH internes.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'EMPLOYES',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'gen_contenu' => 'IMAGE',
                    'gen_disclosure' => 'always',
                    'interaction_directe' => 'NON',
                    'techniques_subliminales' => 'NON',
                    'persuasion_psychologique' => 'NON',
                    'rh_usage' => 'REDACTION_OFFRES',
                ],
            ],
        ];
    }
}
