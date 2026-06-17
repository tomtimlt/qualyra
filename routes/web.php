<?php

declare(strict_types=1);

use App\Http\Controllers\AiUsageController;
use App\Http\Controllers\AiVendorController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VisionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $diffDays = (int) now()->diffInDays(\Carbon\Carbon::parse('2026-08-02'));

    return view('home', compact('diffDays'));
})->name('home');

// Routes protégées : utilisateur authentifié
Route::middleware(['auth'])->group(function () {
    // Tableau de bord (page d'atterrissage post-login)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Vision — cartographie des risques (heatmap)
    Route::get('/vision', VisionController::class)->name('vision');

    // Profil utilisateur (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/theme', [ProfileController::class, 'updateTheme'])->name('profile.theme');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Organisation rattachée au compte (1 user = 1 organisation)
    Route::get('/organization/create', [OrganizationController::class, 'create'])->name('organization.create');
    Route::post('/organization', [OrganizationController::class, 'store'])->name('organization.store');
    Route::get('/organization', [OrganizationController::class, 'show'])->name('organization.show');
    Route::get('/organization/edit', [OrganizationController::class, 'edit'])->name('organization.edit');
    Route::patch('/organization', [OrganizationController::class, 'update'])->name('organization.update');

    // CRUD des usages d'IA déclarés par l'organisation
    Route::resource('usages', AiUsageController::class)->parameters([
        'usages' => 'aiUsage',
    ]);

    // CRUD des fournisseurs IA (vendors) rattachés à l'organisation
    Route::resource('vendors', AiVendorController::class)->parameters([
        'vendors' => 'aiVendor',
    ]);

    // Questionnaire AI Act rattaché à un usage IA (1 questionnaire par usage)
    Route::get('/usages/{aiUsage}/questionnaire', [QuestionnaireController::class, 'show'])
        ->name('usages.questionnaire.show');
    Route::post('/usages/{aiUsage}/questionnaire', [QuestionnaireController::class, 'store'])
        ->name('usages.questionnaire.store');

    // Évaluation AI Act (calcul du niveau de risque)
    Route::post('/usages/{aiUsage}/assessment', [AssessmentController::class, 'store'])
        ->name('usages.assessment.store');

    // Rapports de conformité (génération PDF + paiement Stripe)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');

    Route::post('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::get('/checkout/{report}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/{report}/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
});

require __DIR__.'/auth.php';
