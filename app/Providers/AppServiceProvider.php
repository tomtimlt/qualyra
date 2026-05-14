<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Seed automatiquement le compte démo après migrate:fresh
        if ($this->app->runningInConsole()) {
            try {
                if (User::count() === 0) {
                    $this->app->make(DatabaseSeeder::class)->run();
                }
            } catch (\Exception) {
                // La table n'existe pas encore (première migration)
            }
        }
    }
}
