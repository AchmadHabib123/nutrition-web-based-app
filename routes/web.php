<?php

use App\Http\Controllers\BahanMakananController;
use App\Http\Controllers\TenagaGiziPatientController;
use App\Http\Controllers\TenagaGiziController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\FoodConsumptionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\JadwalMakananController;
use App\Models\FoodConsumption;

// Halaman welcome
Route::get('/', function () {
    return view('auth.login');
});

// Rute autentikasi Breeze
require __DIR__.'/auth.php';

// Rute yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    // Rute Profile Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Dashboard utama
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'ahli-gizi') {
            return redirect()->route('ahli-gizi.dashboard');
        }
        return redirect()->route('tenaga-gizi.dashboard');
    })->name('dashboard');
    Route::get('/bahan_makanans', function () {
        if (auth()->user()->role === 'ahli-gizi') {
            return redirect()->route('ahli-gizi.bahan_makanans.index');
        }
        return redirect()->route('tenaga-gizi.bahan_makanans');
    })->name('bahan_makanans');
});

// Rute Admin
Route::middleware(['auth', 'role:ahli-gizi'])->prefix('ahli-gizi')->name('ahli-gizi.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Route::get('/jadwal-makanan-per-tanggal', [JadwalMakananController::class, 'byTanggal'])->name('jadwal-makanan-per-tanggal');
    Route::resource('menus', MenuController::class);
    Route::prefix('logistics')->name('logistics.')->group(function () {
        Route::get('/', [BahanMakananController::class, 'index'])->name('index');
        // Jika kamu ingin tambahkan resource untuk bahan_makanans dalam konteks logistics
        Route::resource('bahan_makanans', BahanMakananController::class)->names([
            'index' => 'bahan_makanans.index',
            'create' => 'bahan_makanans.create',
            'store' => 'bahan_makanans.store',
            'show' => 'bahan_makanans.show',
            'edit' => 'bahan_makanans.edit',
            'update' => 'bahan_makanans.update',
            'destroy' => 'bahan_makanans.destroy',
        ]);

        Route::post('bahan_makanans/import', [BahanMakananController::class, 'importCsv'])->name('bahan_makanans.import');
    });
    // });
    Route::prefix('jadwal-makanans')->name('jadwal-makanans.')->group(function () {
        Route::resource('/', JadwalMakananController::class)->parameters([
            '' => 'jadwal_makanan',
        ])->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
    });
    Route::prefix('patients')->name('patients.')->group(function () {
        // Perubahan rute filter ini
        Route::get('filter', [PatientController::class, 'filterByDate'])->name('filter'); // <--- PASTIKAN INI ADA DI SINI
        Route::resource('/', PatientController::class)->parameters([
            '' => 'patients',
        ])->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'show' => 'show',
            'edit' => 'edit',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);

        Route::resource('food-consumptions', FoodConsumptionController::class)->only([
            'store', 'edit', 'update', 'destroy'
        ])->names([
            'store' => 'food-consumptions.store',
            'edit' => 'food-consumptions.edit',
            'update' => 'food-consumptions.update',
            'destroy' => 'food-consumptions.destroy',
        ]);
    });
    Route::get('food-consumptions/create', [FoodConsumptionController::class, 'create'])->name('food-consumptions.create');
    Route::post('food-consumptions', [FoodConsumptionController::class, 'store'])->name('food-consumptions.store');
});

// Rute User
Route::middleware(['auth', 'role:tenaga-gizi'])->prefix('tenaga-gizi')->name('tenaga-gizi.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    // Tambahkan rute user lainnya di sini
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('filter', [TenagaGiziPatientController::class, 'filterByDate'])->name('filter');
        Route::resource('/', TenagaGiziPatientController::class)->parameters([
            '' => 'patients',
        ])->names([
            'index' => 'index',
            'show' => 'show',
        ]);
    });
    Route::get('food-delivery', [TenagaGiziController::class, 'index'])->name('food-delivery.index');
    Route::post('food-delivery/{foodConsumption}/mark-delivered', [TenagaGiziController::class, 'markAsDelivered'])->name('food-delivery.mark-delivered');
});
