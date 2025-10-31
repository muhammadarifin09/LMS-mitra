<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Mail; 
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\BiodataController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfilController;

// Login
Route::get('/', fn() => view('login'))->name('login.page');
Route::post('/', [AuthController::class, 'login'])->name('login');

// Forgot Password Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');


    Route::get('/biodata', [BiodataController::class, 'index'])->name('biodata.index');
    Route::get('/biodata/create', [BiodataController::class, 'create'])->name('biodata.create');
    Route::post('/biodata', [BiodataController::class, 'store'])->name('biodata.store');
    Route::get('/biodata/{id_sobat}/edit', [BiodataController::class, 'edit'])->name('biodata.edit');
    Route::put('/biodata/{id_sobat}', [BiodataController::class, 'update'])->name('biodata.update');
    Route::delete('/biodata/{id_sobat}', [BiodataController::class, 'destroy'])->name('biodata.destroy');

    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
});

// Mitra
Route::middleware(['auth', 'role:mitra'])->group(function () {
    Route::get('/beranda', fn() => view('mitra.beranda'))->name('mitra.beranda');
});

Route::middleware(['auth', 'role:mitra'])->group(function () {
    Route::get('/dashboard', fn() => view('mitra.dashboard'))->name('mitra.dashboard');
});

Route::middleware(['auth', 'role:mitra'])->group(function () {
    Route::get('/kursus', fn() => view('mitra.kursus'))->name('mitra.kursus');
});

// routes/web.php

// routes/web.php
Route::prefix('profil')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::get('/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/update', [ProfilController::class, 'update'])->name('profil.update'); // PUT method
    Route::delete('/hapus-foto', [ProfilController::class, 'hapusFoto'])->name('profil.hapus-foto');
});
