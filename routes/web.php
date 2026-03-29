<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\IuranController;
use App\Http\Controllers\KasController;

use App\Http\Controllers\WargaController;

Route::get('/', function () {
    return Inertia::render('Index');
})->name('home');


Route::prefix('iuran')->name('iuran.')->group(function () {
    Route::get('/', [IuranController::class, 'index'])->name('index');
    Route::post('/toggle', [IuranController::class, 'toggle'])->name('toggle');
});

Route::prefix('kas')->name('kas.')->group(function () {
    Route::get('/', [KasController::class, 'index'])->name('index');
    Route::post('/', [KasController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('/dashboard/warga', WargaController::class);

    Route::prefix('dashboard/iuran')->name('dashboard.iuran.')->group(function () {
        Route::get('/', [IuranController::class, 'dashboardIndex'])->name('index');
        Route::post('/toggle', [IuranController::class, 'toggle'])->name('toggle');
    });

    Route::get('/dashboard/kas', [KasController::class, 'dashboardIndex'])->name('dashboard.kas.index');
    Route::resource('/dashboard/kas', KasController::class)->except(['index', 'create', 'show', 'edit'])->names([
        'store' => 'dashboard.kas.store',
        'update' => 'dashboard.kas.update',
        'destroy' => 'dashboard.kas.destroy',
    ]);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
