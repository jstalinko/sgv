<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebSettingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\IuranController;
use App\Http\Controllers\KasController;

use App\Http\Controllers\WargaController;

Route::get('/',[HomeController::class,'index'])->name('home');


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
        $totalWarga = \App\Models\Warga::count();
        $allMasuk = \App\Models\Kas::where('type', 'masuk')->sum('jumlah');
        $allKeluar = \App\Models\Kas::where('type', 'keluar')->sum('jumlah');
        $saldo = $allMasuk - $allKeluar;

        return Inertia::render('Dashboard', [
            'totalWarga' => $totalWarga,
            'totalPemasukan' => $allMasuk,
            'totalPengeluaran' => $allKeluar,
            'totalKas' => $saldo,
        ]);
    })->name('dashboard');

    Route::resource('/dashboard/warga', WargaController::class);
    Route::get('/dashboard/web-setting',[WebSettingController::class,'index'])->name('dashboard.web-setting');
    Route::post('/dashboard/web-setting',[WebSettingController::class,'update'])->name('dashboard.web-setting.update');

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

    Route::get('/dashboard/kegiatan', [\App\Http\Controllers\KegiatanController::class, 'dashboardIndex'])->name('dashboard.kegiatan.index');
    Route::resource('/dashboard/kegiatan', \App\Http\Controllers\KegiatanController::class)->except(['index', 'create', 'show', 'edit'])->names([
        'store' => 'dashboard.kegiatan.store',
        'update' => 'dashboard.kegiatan.update',
        'destroy' => 'dashboard.kegiatan.destroy',
    ]);

    Route::get('/dashboard/galeri', [\App\Http\Controllers\GaleriController::class, 'dashboardIndex'])->name('dashboard.galeri.index');
    Route::resource('/dashboard/galeri', \App\Http\Controllers\GaleriController::class)->except(['index', 'create', 'show', 'edit'])->names([
        'store' => 'dashboard.galeri.store',
        'update' => 'dashboard.galeri.update',
        'destroy' => 'dashboard.galeri.destroy',
    ]);
});

Route::get('/kegiatan', [\App\Http\Controllers\KegiatanController::class, 'index'])->name('kegiatan.index');
Route::get('/jadwal', [\App\Http\Controllers\KegiatanController::class, 'jadwalIndex'])->name('jadwal.index');
Route::get('/galeri', [\App\Http\Controllers\GaleriController::class, 'index'])->name('galeri.index');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
