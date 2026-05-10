<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Crée les deux comptes de référence et délègue au DemoSeeder pour le
     * cas concret « MediCare Imaging » qui peuple demo@example.com.
     *
     * Les anciennes organisations TechStart SAS et MedCare Solutions ont été
     * retirées : leurs réponses étaient générées aléatoirement par
     * ResponseFactory, ce qui (a) violait la contrainte unique
     * (ai_usage_id, variable_key) ajoutée en mai, et (b) ne reflétait plus
     * les variables matrice v1.1 attendues par le moteur. Seul DemoSeeder
     * peuple désormais des données utiles.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo User', 'password' => bcrypt('password')]
        );

        $this->call(DemoSeeder::class);
    }
}
