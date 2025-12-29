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
        Route::prefix('kursus/{kursus}')->name('kursus.materials.')->group(function () {
            // CRUD Materials
            Route::get('materials', [MaterialController::class, 'index'])->name('index');
            Route::get('materials/create', [MaterialController::class, 'create'])->name('create');
            Route::post('materials', [MaterialController::class, 'store'])->name('store');
            Route::get('materials/{material}/edit', [MaterialController::class, 'edit'])->name('edit');
            Route::put('materials/{material}', [MaterialController::class, 'update'])->name('update');
            Route::delete('materials/{material}', [MaterialController::class, 'destroy'])->name('destroy');
            
            // Status & Ordering
            Route::post('materials/{material}/status', [MaterialController::class, 'updateStatus'])->name('status');
            
            // NEW: Drag and Drop Routes
            Route::post('materials/update-order', [MaterialController::class, 'updateOrder'])->name('update-order');
            Route::get('materials/{material}/progress-stats', [MaterialController::class, 'getProgressStats'])->name('progress-stats');
            
            // Video Related Routes
            Route::get('materials/{material}/video-questions', [MaterialController::class, 'videoQuestions'])->name('video-questions');
            Route::get('materials/{material}/video-preview', [MaterialController::class, 'videoPreview'])->name('video-preview');
            Route::get('materials/{material}/video-stats', [MaterialController::class, 'videoStats'])->name('video-stats');
            
            // Update Video Questions
            Route::post('materials/{material}/video-questions/update', [MaterialController::class, 'updateVideoQuestions'])->name('video-questions.update');
            
            // Update Player Config
            Route::post('materials/{material}/player-config/update', [MaterialController::class, 'updatePlayerConfig'])->name('player-config.update');
            
            // Download & Import
            Route::get('materials/{material}/download', [MaterialController::class, 'downloadMaterialFile'])->name('download');
            Route::post('materials/import-soal', [MaterialController::class, 'importSoal'])->name('import-soal');
            
            // Video View
            Route::get('materials/{material}/video', [MaterialController::class, 'viewMaterialVideo'])->name('video.view');
            
            // Video Direct Link
            Route::get('video/{material}/direct-link', [MaterialController::class, 'getDirectVideoLink'])->name('video.direct-link');
        });
        
        // Template soal route
        Route::get('template-soal', [MaterialController::class, 'downloadTemplate'])->name('kursus.materials.download-template');

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
        
        // Material Routes untuk Mitra
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
            // VIDEO ROUTES
            // ============================
            
            // Route untuk menampilkan video player
            Route::get('/materials/{material}/video', [MitraKursusController::class, 'viewMaterialVideo'])
                ->name('kursus.material.video');
            
            // Route untuk video progress tracking
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

use App\Http\Controllers\Admin\LaporanController;
use Barryvdh\DomPDF\Facade\Pdf;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::prefix('laporan')->name('laporan.')->group(function () {

            // ======================
            // LIST KURSUS
            // ======================
            Route::get('/kursus', [LaporanController::class, 'kursusIndex'])
                ->name('kursus');
            Route::get('/mitra', [LaporanController::class, 'mitraIndex'])
                ->name('mitra');

            // ======================
            // DETAIL KURSUS
            // ======================
            Route::get('/kursus/{kursus}', [LaporanController::class, 'kursusDetail'])
                ->name('kursus.detail');
            Route::get('/mitra/{mitra}', [LaporanController::class, 'mitraDetail'])
                ->name('mitra.detail');

            // ======================
            // EXPORT PDF RINGKAS (dari halaman index)
            // ======================
            Route::get('/kursus/{kursus}/pdf-ringkas', [LaporanController::class, 'exportKursusPdfRingkas'])
                ->name('kursus.pdf.ringkas');
            Route::get('/mitra/{mitra}/pdf-ringkas', [LaporanController::class, 'exportMitraPdfRingkas'])
                ->name('mitra.pdf.ringkas');

            // ======================
            // EXPORT PDF DETAIL (dari halaman detail)
            // ======================
            Route::get('/kursus/{kursus}/pdf-detail', [LaporanController::class, 'exportKursusPdfDetail'])
                ->name('kursus.pdf.detail');
            Route::get('/mitra/{mitra}/pdf-detail', [LaporanController::class, 'exportMitraPdfDetail'])
                ->name('mitra.pdf.detail');

            // ======================
            // EXPORT PDF (LEGACY - bisa dihapus jika tidak diperlukan)
            // ======================
            Route::get('/kursus/{kursus}/pdf', [LaporanController::class, 'exportKursusPdf'])
                ->name('kursus.pdf');
            Route::get('/mitra/{mitra}/pdf', [LaporanController::class, 'exportMitraPdf'])
                ->name('mitra.pdf');

            // ======================
            // TEST PDF (SEMENTARA)
            // ======================
            Route::get('/test-pdf', function () {
                return Pdf::loadHTML('
                    <h2 style="font-family: DejaVu Sans, sans-serif;">
                        ✅ DomPDF LMS BERHASIL
                    </h2>
                    <p>Export PDF sudah aktif di modul laporan.</p>
                    <p><strong>Versi:</strong> Dual PDF (Ringkas & Detail)</p>
                    <ul>
                        <li><strong>/admin/laporan/kursus/{id}/pdf-ringkas</strong> - PDF Ringkas</li>
                        <li><strong>/admin/laporan/kursus/{id}/pdf-detail</strong> - PDF Detail Lengkap</li>
                    </ul>
                ')->stream('test-laporan.pdf');
            })->name('test.pdf');

        });
    });

//     Route::prefix('admin/laporan')->name('admin.laporan.')->group(function () {

//     Route::get('/kursus/export-excel', [LaporanController::class, 'exportKursusExcel'])
//         ->name('kursus.excel');

//     Route::get('/kursus/{kursus}/export-excel', [LaporanController::class, 'exportKursusDetailExcel'])
//         ->name('kursus.detail.excel');   
// });

Route::prefix('admin/laporan')->name('admin.laporan.')->group(function () {

    // =====================
    // CSV (HARUS DI ATAS)
    // =====================
    Route::get('/kursus/export-csv', [LaporanController::class, 'exportKursusCsv'])
        ->name('kursus.csv');
    Route::get('/mitra/export-csv', [LaporanController::class, 'exportMitraCsv'])
        ->name('mitra.csv');

    Route::get('/kursus/{kursus}/export-csv', [LaporanController::class, 'exportKursusDetailCsv'])
        ->name('kursus.detail.csv');
    Route::get('/mitra/{mitra}/export-csv', [LaporanController::class, 'exportMitraDetailCsv'])
        ->name('mitra.detail.csv');

    // =====================
    // VIEW (DI BAWAH)
    // =====================
    Route::get('/kursus', [LaporanController::class, 'kursusIndex'])
        ->name('kursus');
    Route::get('/mitra', [LaporanController::class, 'mitraIndex'])
        ->name('mitra');

    Route::get('/kursus/{kursus}', [LaporanController::class, 'kursusDetail'])
        ->name('kursus.detail');
    Route::get('/mitra/{mitra}', [LaporanController::class, 'mitraDetail'])
        ->name('mitra.detail');
});




Route::get('/test-csv', [\App\Http\Controllers\Admin\LaporanController::class, 'exportKursusCsv']);
Route::get('/test-csv', [\App\Http\Controllers\Admin\LaporanController::class, 'exportKursusCsv'])
    ->name('test.csv');
Route::post(
    '/admin/laporan/kursus/{kursus}/generate',
    [LaporanController::class, 'generateLaporanKursus']
)->name('admin.laporan.kursus.generate');
Route::post(
    '/admin/laporan/mitra/{mitra}/generate',
    [LaporanController::class, 'generateLaporanMitra']
)->name('admin.laporan.mitra.generate');



// Sertifikat Routes
// Route::middleware(['auth'])->prefix('certificates')->group(function () {
//     Route::get('/', [CertificateController::class, 'index'])->name('index');
//     Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
//     Route::get('/{certificate}/unduh', [CertificateController::class, 'download'])->name('download');
//     Route::get('/{certificate}/preview', [CertificateController::class, 'preview'])->name('preview');
//     Route::get('/kursus/{kursus}/cek', [CertificateController::class, 'checkCertificate'])->name('check');
// });

Route::middleware(['auth'])->prefix('dashboard/sertifikat')->name('sertifikat.')->group(function () {
    Route::get('/', [CertificateController::class, 'index'])->name('index');
    Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
    Route::get('/{certificate}/unduh', [CertificateController::class, 'download'])->name('download');
});


// routes/web.php (tambahkan di bawah)
Route::get('/test-certificate-qr', function () {
    $certificate = \App\Models\Certificate::with(['user', 'kursus', 'enrollment'])
        ->whereNotNull('id_kredensial')
        ->first();
    
    if (!$certificate) {
        return 'No certificate with id_kredensial found';
    }
    
    return view('mitra.sertifikat.template', [
        'certificate' => $certificate,
        'user' => $certificate->user,
        'kursus' => $certificate->kursus,
        'enrollment' => $certificate->enrollment,
    ]);
});

// Validasi sertifikat via QR code
Route::get('/sertifikat/{id_kredensial}', [App\Http\Controllers\Mitra\CertificateController::class, 'validateCertificate'])
    ->name('certificates.validate');
    
Route::get('/sertifikat/{id_kredensial}/pdf', 
    [App\Http\Controllers\Mitra\CertificateController::class, 'publicPdf']
)->name('certificates.publicPdf');
