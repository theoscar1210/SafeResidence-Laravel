<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EntryController as AdminEntryController;
use App\Http\Controllers\Admin\FamilyMemberController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Propietario\AuthorizationController as PropietarioAuthorizationController;
use App\Http\Controllers\Propietario\DashboardController as PropietarioDashboardController;
use App\Http\Controllers\Propietario\HistoryController as PropietarioHistoryController;
use App\Http\Controllers\Vigilante\AuthorizationController as VigilanteAuthorizationController;
use App\Http\Controllers\Vigilante\DashboardController as VigDashboardController;
use App\Http\Controllers\Vigilante\EntryController;
use App\Http\Controllers\Vigilante\ExitController;
use App\Http\Controllers\Vigilante\ReportController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Administrador
    Route::middleware('role:Administrador')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('entries', [AdminEntryController::class, 'index'])->name('entries.index');

        // Inmuebles
        Route::resource('properties', PropertyController::class)->only(['index', 'create', 'store', 'show', 'update', 'destroy']);
        Route::post('properties/{property}/assign-owner', [PropertyController::class, 'assignOwner'])->name('properties.assign-owner');
        Route::delete('properties/{property}/owners/{user}', [PropertyController::class, 'removeOwner'])->name('properties.remove-owner');
        Route::post('properties/{property}/assign-tenant', [PropertyController::class, 'assignTenant'])->name('properties.assign-tenant');
        Route::post('properties/{property}/end-rental', [PropertyController::class, 'endRental'])->name('properties.end-rental');

        // Núcleo familiar
        Route::post('family-members', [FamilyMemberController::class, 'store'])->name('family-members.store');
        Route::put('family-members/{familyMember}', [FamilyMemberController::class, 'update'])->name('family-members.update');
        Route::delete('family-members/{familyMember}', [FamilyMemberController::class, 'destroy'])->name('family-members.destroy');
    });

    // Vigilante
    Route::middleware('role:Vigilante')->prefix('vigilante')->name('vigilante.')->group(function () {
        Route::get('dashboard', [VigDashboardController::class, 'index'])->name('dashboard');
        Route::get('entries', [EntryController::class,                  'index'])->name('entries.index');
        Route::get('entries/create', [EntryController::class,                  'create'])->name('entries.create');
        Route::post('entries', [EntryController::class,                  'store'])->name('entries.store');
        Route::get('entries/lookup', [EntryController::class,                  'lookup'])->name('entries.lookup');
        Route::get('entries/lookup-plate', [EntryController::class,             'lookupByPlate'])->name('entries.lookup-plate');
        Route::get('exits', [ExitController::class,                   'index'])->name('exits.index');
        Route::post('exits', [ExitController::class,                   'store'])->name('exits.store');
        Route::get('authorizations', [VigilanteAuthorizationController::class, 'index'])->name('authorizations.index');
        Route::get('reports', [ReportController::class,                 'index'])->name('reports.index');
        Route::post('reports/export', [ReportController::class,                 'export'])->name('reports.export');
    });

    // Propietario
    Route::middleware('role:Propietario')->prefix('propietario')->name('propietario.')->group(function () {
        Route::get('dashboard', [PropietarioDashboardController::class, 'index'])->name('dashboard');
        Route::get('authorizations', [PropietarioAuthorizationController::class, 'index'])->name('authorizations.index');
        Route::get('authorizations/create', [PropietarioAuthorizationController::class, 'create'])->name('authorizations.create');
        Route::post('authorizations', [PropietarioAuthorizationController::class, 'store'])->name('authorizations.store');
        Route::delete('authorizations/{authorization}', [PropietarioAuthorizationController::class, 'destroy'])->name('authorizations.destroy');
        Route::get('history', [PropietarioHistoryController::class, 'index'])->name('history.index');
    });

    // Residente
    Route::middleware('role:Residente')->prefix('residente')->name('residente.')->group(function () {
        Route::inertia('dashboard', 'residente/Dashboard')->name('dashboard');
    });
});

require __DIR__.'/settings.php';
