<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->company(),
            'siret' => fake()->optional(0.7)->numerify('##############'),
            'size' => fake()->randomElement(['1-19', '20-49', '50-149', '150+']),
            'sector' => fake()->optional()->word(),
        ];
    }

    /**
     * Rétrocompatibilité : le créateur (user_id) devient membre "owner" via
     * le pivot, pour que Organization::factory()->for($user) produise le même
     * monde qu'avant la refonte membership (tests existants inchangés).
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Organization $organization) {
            if ($organization->user_id !== null) {
                $organization->members()->syncWithoutDetaching([
                    $organization->user_id => ['role' => 'owner'],
                ]);
            }
        });
    }
}
