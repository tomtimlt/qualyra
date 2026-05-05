<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiUsage;
use App\Models\Assessment;
use App\Models\Organization;
use App\Models\Response;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user1 = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo User', 'password' => bcrypt('password')]
        );

        $org1 = Organization::firstOrCreate(
            ['name' => 'TechStart SAS'],
            ['user_id' => $user1->id, 'size' => '1-19', 'sector' => 'Technologie']
        );

        $org2 = Organization::firstOrCreate(
            ['name' => 'MedCare Solutions'],
            ['user_id' => $user2->id, 'size' => '20-49', 'sector' => 'Santé']
        );

        if ($org1->aiUsages()->count() === 0) {
            $aiUsages1 = AiUsage::factory()->count(4)->create([
                'organization_id' => $org1->id,
            ]);

            foreach ($aiUsages1 as $aiUsage) {
                Response::factory()->count(rand(2, 4))->create([
                    'ai_usage_id' => $aiUsage->id,
                ]);

                Assessment::factory()->create([
                    'ai_usage_id' => $aiUsage->id,
                ]);
            }
        }

        if ($org2->aiUsages()->count() === 0) {
            $aiUsages2 = AiUsage::factory()->count(4)->create([
                'organization_id' => $org2->id,
            ]);

            foreach ($aiUsages2 as $aiUsage) {
                Response::factory()->count(rand(2, 4))->create([
                    'ai_usage_id' => $aiUsage->id,
                ]);

                Assessment::factory()->create([
                    'ai_usage_id' => $aiUsage->id,
                ]);
            }
        }
    }
}
