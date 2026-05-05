<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiUsage;
use App\Models\Response;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Response>
 */
class ResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $variableKeys = ['DEC', 'PUB', 'DATA', 'RH_USAGE', 'PROFILING', 'SENSITIVE_DATA', 'AUTOMATION_LEVEL'];
        $variableValues = ['INFORMATIF', 'EMPLOYES,VULNERABLES', 'OUI', 'NON', 'PARTIEL', 'COMPLET'];

        return [
            'ai_usage_id' => AiUsage::factory(),
            'variable_key' => fake()->randomElement($variableKeys),
            'variable_value' => fake()->randomElement($variableValues),
        ];
    }
}
