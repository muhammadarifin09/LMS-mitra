<?php

use App\Models\Enrollment;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KursusController;
use App\Http\Controllers\Admin\BiodataController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Mitra\BerandaController;
use App\Http\Controllers\Mitra\KursusController as MitraKursusController;
use App\Http\Controllers\Mitra\DashboardController;
use App\Http\Controllers\Mitra\CertificateController;

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

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        
        // Biodata Routes
        Route::get('/biodata', [BiodataController::class, 'index'])->name('biodata.index');
        Route::get('/biodata/create', [BiodataController::class, 'create'])->name('biodata.create');
        Route::post('/biodata', [BiodataController::class, 'store'])->name('biodata.store');
        Route::get('/biodata/{id_sobat}/edit', [BiodataController::class, 'edit'])->name('biodata.edit');
        Route::put('/biodata/{id_sobat}', [BiodataController::class, 'update'])->name('biodata.update');
        Route::delete('/biodata/{id_sobat}', [BiodataController::class, 'destroy'])->name('biodata.destroy');

        // Kursus Routes
        Route::get('kursus', [KursusController::class, 'index'])->name('kursus.index');
        Route::get('kursus/create', [KursusController::class, 'create'])->name('kursus.create');
        Route::post('kursus', [KursusController::class, 'store'])->name('kursus.store');
        Route::get('kursus/{kursus}', [KursusController::class, 'show'])->name('kursus.show');
        Route::get('kursus/{kursus}/edit', [KursusController::class, 'edit'])->name('kursus.edit');
        Route::put('kursus/{kursus}', [KursusController::class, 'update'])->name('kursus.update');
        Route::delete('kursus/{kursus}', [KursusController::class, 'destroy'])->name('kursus.destroy');
        Route::post('kursus/{kursus}/status', [KursusController::class, 'updateStatus'])->name('kursus.updateStatus');

        // Material Routes
        Route::prefix('kursus/{kursus}')->group(function () {
            Route::get('materials', [MaterialController::class, 'index'])->name('kursus.materials.index');
            Route::get('materials/create', [MaterialController::class, 'create'])->name('kursus.materials.create');
            Route::post('materials', [MaterialController::class, 'store'])->name('kursus.materials.store');
            Route::get('materials/{material}/edit', [MaterialController::class, 'edit'])->name('kursus.materials.edit');
            Route::put('materials/{material}', [MaterialController::class, 'update'])->name('kursus.materials.update');
            Route::delete('materials/{material}', [MaterialController::class, 'destroy'])->name('kursus.materials.destroy');
            Route::post('materials/{material}/status', [MaterialController::class, 'updateStatus'])->name('kursus.materials.status');
            
            // Material Ordering
            Route::post('materials/reorder', [MaterialController::class, 'reorder'])->name('kursus.materials.reorder');
            Route::post('materials/{material}/fix-order', [MaterialController::class, 'fixOrder'])->name('kursus.materials.fix-order');
            
            // Download & Video Routes untuk Admin
            Route::get('materials/{material}/download', [MaterialController::class, 'downloadMaterialFile'])
                ->name('kursus.materials.download');
            Route::get('materials/{material}/video', [MaterialController::class, 'viewMaterialVideo'])
                ->name('kursus.materials.video.view');
        });

        // Notification Routes
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    });
});

// Routes untuk Mitra
Route::middleware(['auth', 'role:mitra'])->group(function () {
    // Route tanpa prefix (untuk kompatibilitas)
    Route::get('/beranda', [BerandaController::class, 'index'])->name('mitra.beranda');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('mitra.dashboard');
    
    // Route dengan prefix mitra
    Route::prefix('mitra')->name('mitra.')->group(function () {
        // Kursus Routes
        Route::get('/kursus', [MitraKursusController::class, 'index'])->name('kursus.index');
        Route::get('/kursus/{kursus}', [MitraKursusController::class, 'show'])->name('kursus.show');
        Route::post('/kursus/{kursus}/enroll', [MitraKursusController::class, 'enroll'])->name('kursus.enroll');
        Route::get('/kursus-saya', [MitraKursusController::class, 'kursusSaya'])->name('kursus.saya');
        
        // Route untuk myCourses (alternatif)
        Route::get('/my-courses', [MitraKursusController::class, 'myCourses'])->name('kursus.my');
        
        // Material Routes untuk Mitra - REORGANIZED UNTUK MENGHINDARI DUPLIKASI
        Route::prefix('kursus/{kursus}')->group(function () {
            // Download File
            Route::get('/materials/{material}/download', [MitraKursusController::class, 'downloadMaterialFile'])
                ->name('kursus.material.download');
            
            // Show material files (optional)
            Route::get('/materials/{material}/files', [MitraKursusController::class, 'showMaterialFiles'])
                ->name('kursus.material.files');
            
            // Test Routes
            Route::get('/test/{material}/{testType}', [MitraKursusController::class, 'showTest'])
                ->name('kursus.test.show');
            Route::post('/test/{material}/{testType}/submit', [MitraKursusController::class, 'submitTest'])
                ->name('kursus.test.submit');
            
            // Recap Routes
            Route::get('/recap/{material}', [MitraKursusController::class, 'showRecap'])
                ->name('kursus.recap.show');
            
            // Route attendance
            Route::post('/materials/{material}/attendance', [MitraKursusController::class, 'markAttendance'])
                ->name('kursus.material.attendance');
            
            // Route untuk complete material
            Route::post('/materials/{material}/complete', [MitraKursusController::class, 'completeMaterial'])
                ->name('kursus.material.complete');

            Route::get('/materials/{material}/refresh-status', [MitraKursusController::class, 'refreshMaterialStatus'])
                ->name('kursus.material.refresh-status');
    
            // Route untuk get status semua material setelah update
            Route::get('/materials/refresh-all-status', [MitraKursusController::class, 'refreshAllMaterialsStatus'])
                ->name('kursus.materials.refresh-all-status');
            
            // Route untuk subtasks
            Route::get('/materials/{material}/subtasks', [MitraKursusController::class, 'getMaterialSubtasks'])
                ->name('kursus.material.subtasks');
            
            // API untuk real-time progress
            Route::get('/progress', [MitraKursusController::class, 'getProgress'])
                ->name('kursus.progress');
            
            // ============================
            // VIDEO ROUTES - PERBAIKAN BESAR
            // ============================
            
            // Route untuk menampilkan video player
            Route::get('/materials/{material}/video', [MitraKursusController::class, 'viewMaterialVideo'])
                ->name('kursus.material.video');
            
            // Route untuk video progress tracking
            Route::post('/materials/{material}/video/progress', [MitraKursusController::class, 'updateVideoProgress'])
                ->name('kursus.material.video.progress');
                Route::post('/materials/{material}/video/complete', [MitraKursusController::class, 'markVideoAsComplete'])
            ->name('kursus.material.video.complete');
            
        Route::post('/materials/{material}/video/progress', [MitraKursusController::class, 'updateVideoProgress'])
            ->name('kursus.material.video.progress');
            
            // Route untuk menandai video sudah ditonton
            Route::post('/materials/{material}/video/watched', [MitraKursusController::class, 'markVideoAsWatched'])
                ->name('kursus.material.video.watched');
        });
    });
});

// Profil Routes (untuk semua user yang terautentikasi)
Route::middleware(['auth'])->prefix('profil')->group(function () {
    Route::get('/', [ProfilController::class, 'index'])->name('profil.index');
    Route::get('/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/update', [ProfilController::class, 'update'])->name('profil.update');
    Route::delete('/hapus-foto', [ProfilController::class, 'hapusFoto'])->name('profil.hapus-foto');
});

// Sertifikat Routes
// Route::middleware(['auth'])->prefix('certificates')->group(function () {
//     Route::get('/', [CertificateController::class, 'index'])->name('index');
//     Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
//     Route::get('/{certificate}/unduh', [CertificateController::class, 'download'])->name('download');
//     Route::get('/{certificate}/preview', [CertificateController::class, 'preview'])->name('preview');
//     Route::get('/kursus/{kursus}/cek', [CertificateController::class, 'checkCertificate'])->name('check');
// });

Route::middleware(['auth'])->prefix('sertifikat')->name('sertifikat.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::get('/{certificate}/unduh', [CertificateController::class, 'download'])->name('download');
});
