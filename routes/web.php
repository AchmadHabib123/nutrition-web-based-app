<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\FoodConsumptionController;
use App\Http\Controllers\MenuController;

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
    Route::get('/menus', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.menus.index');
        }
        return redirect()->route('user.menus');
    })->name('menus');
});

// Rute Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::resource('menus', MenuController::class);
    Route::get('/admin/menus/create', [MenuController::class, 'create'])->name('admin.menus.create');
    // Tambahkan rute admin lainnya di sini
    Route::resource('patients', PatientController::class);
    // Di dalam grup middleware 'auth' dan 'role:admin'
    Route::resource('patients.food-consumptions', FoodConsumptionController::class)->only(['store', 'edit', 'update', 'destroy']);
     // Rute untuk Menu Masakan
    
    

});

// Rute User
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    // Tambahkan rute user lainnya di sini
});
