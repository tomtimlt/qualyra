<?php

declare(strict_types=1);

use App\Http\Controllers\AiUsageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Routes protégées : utilisateur authentifié
Route::middleware(['auth'])->group(function () {
    // Tableau de bord (page d'atterrissage post-login)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Profil utilisateur (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Création de l'organisation rattachée au compte (1 user = 1 PME)
    Route::get('/organization/create', [OrganizationController::class, 'create'])->name('organization.create');
    Route::post('/organization', [OrganizationController::class, 'store'])->name('organization.store');

    // CRUD des usages d'IA déclarés par l'organisation
    Route::resource('usages', AiUsageController::class)->parameters([
        'usages' => 'aiUsage',
    ]);
});

require __DIR__.'/auth.php';
