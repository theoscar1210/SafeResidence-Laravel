<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Administrador
    Route::middleware('role:Administrador')->prefix('admin')->name('admin.')->group(function () {
        Route::inertia('dashboard', 'admin/Dashboard')->name('dashboard');
    });

    // Vigilante
    Route::middleware('role:Vigilante')->prefix('vigilante')->name('vigilante.')->group(function () {
        Route::inertia('dashboard', 'vigilante/Dashboard')->name('dashboard');
    });

    // Propietario
    Route::middleware('role:Propietario')->prefix('propietario')->name('propietario.')->group(function () {
        Route::inertia('dashboard', 'propietario/Dashboard')->name('dashboard');
    });

    // Residente
    Route::middleware('role:Residente')->prefix('residente')->name('residente.')->group(function () {
        Route::inertia('dashboard', 'residente/Dashboard')->name('dashboard');
    });
});

require __DIR__.'/settings.php';
