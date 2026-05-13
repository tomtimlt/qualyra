<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Crée le compte démo et délègue au DemoSeeder pour le cas concret
     * « MediCare Imaging » qui peuple demo@example.com.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo User', 'password' => bcrypt('password')]
        );

        $this->call(DemoSeeder::class);
    }
}
