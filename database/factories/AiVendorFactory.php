<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiVendor;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiVendor>
 */
class AiVendorFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->randomElement(['OpenAI', 'Anthropic', 'Mistral AI', 'Google AI', 'Microsoft Copilot', 'Solution interne']),
            'type_contractuel' => fake()->randomElement(['INTERNE', 'SAAS', 'API_PUBLIC', 'OPEN_SOURCE']),
            'pays_hebergement' => fake()->randomElement(['FR', 'US', 'IE', 'DE']),
            'hors_ue' => false,
            'declaration_conformite_art47' => false,
            'dpa_art28_signe' => false,
            'cct_signees' => null,
            'notes' => null,
        ];
    }

    /**
     * Vendor hors UE sans CCT — déclenche l'alerte transfert RGPD Ch. V.
     */
    public function horsUeSansCct(): self
    {
        return $this->state(fn () => [
            'pays_hebergement' => 'US',
            'hors_ue' => true,
            'cct_signees' => false,
        ]);
    }

    /**
     * Vendor pleinement conforme : Art. 47 + DPA + CCT signés.
     */
    public function conforme(): self
    {
        return $this->state(fn () => [
            'declaration_conformite_art47' => true,
            'dpa_art28_signe' => true,
            'cct_signees' => true,
        ]);
    }
}
