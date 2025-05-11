<?php

use App\Http\Controllers\BahanMakananController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\FoodConsumptionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\JadwalMakananController;

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
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');
    Route::get('/bahan_makanans', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.bahan_makanans.index');
        }
        return redirect()->route('user.bahan_makanans');
    })->name('bahan_makanans');
});

// Rute Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
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
    Route::prefix('jadwal-makanans')->name('jadwal-makanans.')->group(function () {
        Route::get('create', [JadwalMakananController::class, 'create'])->name('create');
        Route::post('/', [JadwalMakananController::class, 'store'])->name('store');
        Route::get('/', [JadwalMakananController::class, 'index'])->name('index');
        Route::get('/{jadwal_makanan}', [JadwalMakananController::class, 'show'])->name('show');

    });
    
    // Route::resource('bahan_makanans', BahanMakananController::class);
    // Route::post('/bahan_makanans/import', [BahanMakananController::class, 'importCsv'])->name('bahan_makanans.import');
    // Route::get('/admin/bahan_makanans/create', [MenuController::class, 'create'])->name('admin.bahan_makanans.create');
    // Tambahkan rute admin lainnya di sini
    Route::resource('patients', PatientController::class);
    // Di dalam grup middleware 'auth' dan 'role:admin'
    Route::resource('patients.food-consumptions', FoodConsumptionController::class)->only(['store', 'edit', 'update', 'destroy']);
     // Rute untuk Menu Masakan
 
});
Route::get('/patients/filter', [PatientController::class, 'filterByDate']);
// Rute User
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    // Tambahkan rute user lainnya di sini
});
