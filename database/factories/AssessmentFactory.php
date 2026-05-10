<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\AiUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $niveaux = ['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL'];
        $typeRegles = ['TEXTE_EXPLICITE', 'INTERPRETATION', 'NA'];
        $regleIds = ['R-H-02', 'R-I-01', 'R-L-03', 'R-M-01', 'DEFAULT'];
        $articles = ['Article 5', 'Article 6', 'Article 15', 'Article 35', 'Annexe III'];

        return [
            'ai_usage_id' => AiUsage::factory(),
            'niveau' => fake()->randomElement($niveaux),
            'regle_id' => fake()->randomElement($regleIds),
            'article' => fake()->randomElement($articles),
            'raison' => fake()->paragraph(),
            'alertes' => json_encode([]),
            'type_regle' => fake()->randomElement($typeRegles),
            'computed_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
