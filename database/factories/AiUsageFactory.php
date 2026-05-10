<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiUsage;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiUsage>
 */
class AiUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['LLM_GEN', 'IA_GEN', 'IA_SCORING', 'IA_BIO', 'AUTRE'];
        $domains = ['RH', 'EDUCATION', 'CREDIT', 'SANTE', 'SECURITE', 'MARKETING', 'PROD_INT', 'DEV_LOG', 'AUTRE'];

        $usageNames = [
            'ChatGPT pour rédaction emails',
            'Générateur de CV automatisé',
            'Analyse de sentiments clients',
            'Scoring de candidatures',
            'Assistant code pour développeurs',
            'Génération de descriptions produits',
            'Chatbot support client',
            'Analyse prédictive des ventes',
        ];

        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->randomElement($usageNames),
            'description' => fake()->optional()->sentence(),
            'type' => fake()->randomElement($types),
            'domain' => fake()->randomElement($domains),
        ];
    }
}
