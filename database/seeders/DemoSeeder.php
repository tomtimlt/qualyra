<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Services\AiActClassifier;
use Illuminate\Database\Seeder;

/*
 * Seeder dédié au compte de démo (demo@example.com).
 *
 * Construit un cas concret réaliste : "MediCare Imaging", cabinet de radiologie
 * privé d'une trentaine de salariés. Les 6 usages IA déclarés couvrent
 * volontairement plusieurs niveaux AI Act pour une démo expressive — alignée
 * sur la matrice v1.1 et les variables conditionnelles de config/questionnaire.php :
 *   - 1 INACCEPTABLE  : R-I-05 (bio identification temps réel public)
 *   - 2 HAUT_RISQUE   : R-H-02 (tri CV) + R-H-05 (scoring crédit)
 *   - 1 RISQUE_LIMITE : R-L-03 (deepfake diffusé)
 *   - 2 RISQUE_MINIMAL : LLM interne (rédaction CR, Copilot dev)
 *
 * IDÉMPOTENT : repasser le seeder purge l'organisation existante de demo@
 * et la recrée à zéro. Ne touche jamais à test@example.com ni à un autre user.
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
            'name' => 'MediCare Imaging',
            'siret' => '83472910500023',
            'size' => '20-49',
            'sector' => 'Santé — Radiologie',
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
            // -----------------------------------------------------------------
            // INACCEPTABLE — R-I-05 (Art. 5 §1 h) : bio identification temps réel public
            // -----------------------------------------------------------------
            [
                'usage' => [
                    'name' => 'Contrôle d\'accès biométrique en temps réel',
                    'description' => 'Reconnaissance faciale en temps réel à l\'accueil patients pour identifier les personnes recherchées par les autorités. Pilote installé sur conseil d\'un prestataire de sécurité.',
                    'type' => 'IA_BIO',
                    'domain' => 'SECURITE',
                ],
                'answers' => [
                    'finality' => 'Identification en temps réel des personnes entrant dans le hall, avec alerte automatique si correspondance avec une base de personnes recherchées.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'GRAND_PUBLIC,VULNERABLES',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'yes',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'never',
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

            // -----------------------------------------------------------------
            // HAUT_RISQUE — R-H-02 (Annexe III §4 a) : tri de CV
            // -----------------------------------------------------------------
            [
                'usage' => [
                    'name' => 'Pré-tri automatisé des candidatures (manipulateurs radio)',
                    'description' => 'Filtrage automatisé des CV reçus pour les postes de manipulateurs en électroradiologie médicale. Score attribué à chaque candidat avant entretien RH.',
                    'type' => 'LLM_GEN',
                    'domain' => 'RH',
                ],
                'answers' => [
                    'finality' => 'Réduire le temps de pré-sélection des candidats en classant les CV selon leur adéquation au poste.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'EMPLOYES',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'sometimes',
                    'impact_individual' => 'yes',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'openai',
                    'llm_data_input' => 'CV au format PDF, lettre de motivation, parcours professionnel.',
                    'llm_output_published' => 'no',
                    'interaction_directe' => 'NON',
                    'rh_usage' => 'TRI_CV',
                ],
            ],

            // -----------------------------------------------------------------
            // HAUT_RISQUE — R-H-05 (Annexe III §5 b) : scoring crédit
            // -----------------------------------------------------------------
            [
                'usage' => [
                    'name' => 'Scoring assurance complémentaire patients',
                    'description' => 'Évaluation du risque de défaut de paiement des patients sans tiers payant pour décider d\'accepter un échelonnement. Score calculé sur antécédents et données socio-démographiques.',
                    'type' => 'IA_SCORING',
                    'domain' => 'CREDIT',
                ],
                'answers' => [
                    'finality' => 'Décider automatiquement si un patient peut bénéficier d\'un paiement échelonné en fonction de son score de solvabilité.',
                    'dec' => 'SEMI_AUTO',
                    'pub' => 'CLIENTS',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'yes',
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

            // -----------------------------------------------------------------
            // RISQUE_LIMITE — R-L-03 (Art. 50 §4) : deepfake diffusé publiquement
            // -----------------------------------------------------------------
            [
                'usage' => [
                    'name' => 'Génération de visuels pour campagne de prévention',
                    'description' => 'Création d\'images photoréalistes représentant des patients (acteurs synthétiques) pour les supports de communication sur le dépistage précoce.',
                    'type' => 'IA_GEN',
                    'domain' => 'MARKETING',
                ],
                'answers' => [
                    'finality' => 'Produire des visuels diversifiés sans recourir à des séances photo, en illustrant des situations cliniques sensibles.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'GRAND_PUBLIC',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'PUBLIC',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'gen_contenu' => 'DEEPFAKE',
                    'gen_disclosure' => 'sometimes',
                    'interaction_directe' => 'NON',
                    'techniques_subliminales' => 'NON',
                    'persuasion_psychologique' => 'NON',
                ],
            ],

            // -----------------------------------------------------------------
            // RISQUE_MINIMAL — DEFAULT : LLM en usage interne (DIFF=INTERNE)
            // R-L-01 ne s'applique pas (DIFF=INTERNE), donc DEFAULT.
            // -----------------------------------------------------------------
            [
                'usage' => [
                    'name' => 'Assistant de rédaction de comptes-rendus radiologiques',
                    'description' => 'Le radiologue dicte ses observations, l\'IA structure le compte-rendu selon le modèle standard du cabinet. Validation systématique du praticien avant envoi au patient.',
                    'type' => 'LLM_GEN',
                    'domain' => 'PROD_INT',
                ],
                'answers' => [
                    'finality' => 'Gagner du temps sur la mise en forme des comptes-rendus en automatisant la structuration et la reformulation.',
                    'dec' => 'AIDE_DEC',
                    'pub' => 'AUCUN',
                    'data_personal' => 'yes',
                    'data_sensitive' => 'yes',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'mistral',
                    'llm_data_input' => 'Dictée vocale du radiologue, antécédents médicaux du patient, éléments d\'imagerie.',
                    'llm_output_published' => 'no',
                    'interaction_directe' => 'NON',
                ],
            ],

            // -----------------------------------------------------------------
            // RISQUE_MINIMAL — DEFAULT : Copilot interne pour développeurs
            // -----------------------------------------------------------------
            [
                'usage' => [
                    'name' => 'GitHub Copilot pour l\'équipe IT',
                    'description' => 'Assistant de complétion de code utilisé par les deux développeurs internes sur les outils métiers (planning, facturation).',
                    'type' => 'LLM_GEN',
                    'domain' => 'DEV_LOG',
                ],
                'answers' => [
                    'finality' => 'Accélérer le développement des outils internes en suggérant du code à la volée.',
                    'dec' => 'INFORMATIF',
                    'pub' => 'AUCUN',
                    'data_personal' => 'no',
                    'data_sensitive' => 'no',
                    'diff' => 'INTERNE',
                    'human_oversight' => 'always',
                    'impact_individual' => 'no',
                    'usage_prestations_essentielles' => 'NON',
                    'llm_provider' => 'openai',
                    'llm_data_input' => 'Code source des applications internes (planning RDV, facturation).',
                    'llm_output_published' => 'no',
                    'interaction_directe' => 'NON',
                ],
            ],
        ];
    }
}
