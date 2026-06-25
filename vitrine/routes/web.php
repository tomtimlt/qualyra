<?php

declare(strict_types=1);

use App\Http\Controllers\{HomeController, ContactController, AdminController, LocaleController};
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/locale/{locale}', LocaleController::class)
    ->whereIn('locale', ['fr', 'en'])
    ->name('locale');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/a867f3/login', [AdminController::class, 'loginForm'])->name('admin.login');

Route::post('/a867f3/login', [AdminController::class, 'login'])
    ->middleware('throttle:5,1');

Route::post('/a867f3/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::get('/a867f3', [AdminController::class, 'dashboard'])
    ->middleware(\App\Http\Middleware\EnsureAdmin::class)
    ->name('admin.dashboard');
