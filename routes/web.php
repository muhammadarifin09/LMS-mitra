<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Mail; 
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\BiodataController;
use App\Http\Controllers\Admin\KursusController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Mitra\KursusController as MitraKursusController;

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
    
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
});

// Mitra
Route::middleware(['auth', 'role:mitra'])->group(function () {
    Route::get('/beranda', fn() => view('mitra.beranda'))->name('mitra.beranda');
    Route::get('/dashboard', fn() => view('mitra.dashboard'))->name('mitra.dashboard');
    Route::get('/kursus', fn() => view('mitra.kursus'))->name('mitra.kursus');
});

// Routes untuk Admin - PERBAIKI DI SINI
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Ganti resource dengan definisi manual
    Route::get('kursus', [KursusController::class, 'index'])->name('kursus.index');
    Route::get('kursus/create', [KursusController::class, 'create'])->name('kursus.create');
    Route::post('kursus', [KursusController::class, 'store'])->name('kursus.store');
    Route::get('kursus/{kursus}', [KursusController::class, 'show'])->name('kursus.show');
    Route::get('/kursus/{id}', [KursusController::class, 'detailKursus'])->name('mitra.detail-kursus');
    Route::get('kursus/{kursus}/edit', [KursusController::class, 'edit'])->name('kursus.edit');
    Route::put('kursus/{kursus}', [KursusController::class, 'update'])->name('kursus.update');
    Route::delete('kursus/{kursus}', [KursusController::class, 'destroy'])->name('kursus.destroy');
    
    Route::post('kursus/{kursus}/status', [KursusController::class, 'updateStatus'])->name('kursus.updateStatus');
});

// Routes untuk Mitra
Route::prefix('mitra')->name('mitra.')->middleware(['auth', 'role:mitra'])->group(function () {
    Route::get('/kursus', [MitraKursusController::class, 'index'])->name('kursus.index');
    Route::get('/kursus/{id}', [MitraKursusController::class, 'show'])->name('kursus.show');
    Route::get('/kursus/{id}/edit', [MitraKursusController::class, 'edit'])->name('kursus.edit');
    Route::post('/kursus/{id}/enroll', [MitraKursusController::class, 'enroll'])->name('kursus.enroll');
    Route::get('/kursus-saya', [MitraKursusController::class, 'myCourses'])->name('kursus.saya');
});

// Profil Routes
Route::prefix('profil')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::get('/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/update', [ProfilController::class, 'update'])->name('profil.update');
    Route::delete('/hapus-foto', [ProfilController::class, 'hapusFoto'])->name('profil.hapus-foto');
});