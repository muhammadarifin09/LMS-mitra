<?php

namespace App\Http\Controllers\Mitra;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Kursus;
use App\Models\Materials;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Models\MaterialProgress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class KursusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kursus = Kursus::where('status', 'aktif')
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('mitra.kursus', compact('kursus'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kursus = Kursus::where('status', 'aktif')
                        ->where('id', $id)
                        ->with(['materials' => function($query) {
                            // HANYA ambil materi yang AKTIF
                            $query->where('is_active', true)
                                  ->orderBy('order');
                        }])
                        ->firstOrFail();

        $user = Auth::user();
        
        // Cek enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
                            ->where('kursus_id', $id)
                            ->firstOrFail();

        // Process materials dengan status (hanya yang aktif)
        $materials = $this->getMaterialsWithStatus($kursus->materials, $user, $id);
        
        // Calculate progress
        $totalMaterials = count($materials);
        $completedMaterials = 0;
        
        foreach ($materials as $material) {
            if ($material['status'] === 'completed') {
                $completedMaterials++;
            }
        }
        
        $progressPercentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;

        return view('mitra.kursus-detail', compact(
            'kursus',
            'enrollment', 
            'materials',
            'progressPercentage',
            'completedMaterials',
            'totalMaterials'
        ));
    }

    // Untuk halaman kursus saya (yang sudah di-enroll) + FILTER
    public function kursusSaya(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $userId = Auth::id();

        $enrollments = Enrollment::where('user_id', $userId)
            ->with(['kursus' => function($query) {
                $query->where('status', 'aktif')
                    ->with(['materials' => function($q) {
                        $q->where('is_active', true)
                            ->orderBy('order');
                    }]);
            }])
            ->whereHas('kursus', function($query) {
                $query->where('status', 'aktif');
            });

        // Apply filter
        if ($filter === 'in_progress') {
            $enrollments->where('progress_percentage', '<', 100);
        }

        if ($filter === 'completed') {
            $enrollments->where('progress_percentage', '=', 100);
        }

        $enrollments = $enrollments->orderBy('updated_at', 'desc')
                                ->get();

        // Hitung progress untuk setiap enrollment
        foreach ($enrollments as $enrollment) {
            if ($enrollment->kursus && $enrollment->kursus->materials) {
                // Gunakan method yang sama dengan halaman detail
                $materials = $this->getMaterialsWithStatus($enrollment->kursus->materials, Auth::user(), $enrollment->kursus_id);
                
                $totalMaterials = count($materials);
                $completedMaterials = collect($materials)->where('status', 'completed')->count();
                $progressPercentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;
                
                // Update enrollment
                $enrollment->completed_activities = $completedMaterials;
                $enrollment->total_activities = $totalMaterials;
                $enrollment->progress_percentage = $progressPercentage;
                $enrollment->status = $progressPercentage == 100 ? 'completed' : 'in_progress';
            }
        }

        return view('mitra.kursus-saya', compact('enrollments', 'filter'));
    }

    /**
     * PERBAIKAN BESAR: Logika status material
     */
    private function getMaterialsWithStatus($materials, $user, $kursusId)
    {
        $processedMaterials = [];
        
        // Ambil semua progress user untuk kursus ini
        $userProgress = MaterialProgress::where('user_id', $user->id)
            ->whereIn('material_id', $materials->pluck('id'))
            ->get()
            ->keyBy('material_id');
        
        $previousMaterialCompleted = true; // Material pertama selalu bisa diakses
        
        foreach ($materials->sortBy('order') as $index => $material) {
            // Skip jika materi tidak aktif
            if (!$material->is_active) {
                continue;
            }
            
            $progress = $userProgress[$material->id] ?? null;
            
            // Parse file_path untuk mendapatkan file paths
            $filePaths = [];
            $totalFiles = 0;
            $hasMaterial = !empty($material->file_path);
            
            if ($hasMaterial) {
                $filePaths = json_decode($material->file_path, true) ?? [$material->file_path];
                $totalFiles = count($filePaths);
            }
            
            // **PERBAIKAN PENTING: Cek apakah memiliki video**
            // Video bisa dari video_url (YouTube/Vimeo) atau video_file (Google Drive)
            $hasVideo = false;
            $videoData = null;
            
            if (!empty($material->video_url) || !empty($material->video_file)) {
                $hasVideo = true;
                
                // Jika ada video_file (Google Drive), parse data
                if (!empty($material->video_file)) {
                    try {
                        $videoData = json_decode($material->video_file, true);
                    } catch (\Exception $e) {
                        Log::error('Error parsing video_file data: ' . $e->getMessage());
                    }
                }
            }
            
            // **PERBAIKAN KRUSIAL: Tentukan apakah material ini bisa diakses**
            // Material pertama selalu bisa diakses
            if ($material->order === 1) {
                $isAccessible = true;
            } else {
                // Cek apakah material sebelumnya sudah selesai
                $previousMaterial = $materials->where('order', $material->order - 1)->first();
                if ($previousMaterial) {
                    $previousProgress = $userProgress[$previousMaterial->id] ?? null;
                    $isAccessible = $this->isMaterialCompleted($previousProgress, $previousMaterial);
                } else {
                    $isAccessible = true;
                }
            }
            
            // Tentukan apakah material ini sudah selesai
            $isCompleted = $this->isMaterialCompleted($progress, $material);
            
            // **PERBAIKAN: Tentukan status dengan benar**
            if ($isCompleted) {
                $status = 'completed';
                $statusClass = 'completed';
            } elseif ($isAccessible) {
                $status = 'current';
                $statusClass = 'current';
            } else {
                $status = 'locked';
                $statusClass = 'locked';
            }
            
            // Get specific status for each task
            $attendanceStatus = $progress ? ($progress->attendance_status ?? 'pending') : 'pending';
            $materialStatus = $progress ? ($progress->material_status ?? 'pending') : 'pending';
            $videoStatus = $progress ? ($progress->video_status ?? 'pending') : 'pending';
            
            // For test types, check if completed
            $isTestCompleted = false;
            $testScore = null;
            
            if ($material->type === 'pre_test' && $progress && $progress->pretest_score !== null) {
                $isTestCompleted = true;
                $testScore = $progress->pretest_score;
            } elseif ($material->type === 'post_test' && $progress && $progress->posttest_score !== null) {
                $isTestCompleted = true;
                $testScore = $progress->posttest_score;
            }

            // Ambil downloaded_files
            $downloadedFiles = [];
            if ($progress && $progress->downloaded_files) {
                $downloadedFiles = json_decode($progress->downloaded_files, true) ?? [];
            }
            
            // Determine available content types
            $hasAttendance = $material->attendance_required ?? true;
            
            $processedMaterials[] = [
                'id' => $material->id,
                'title' => $material->title,
                'type' => $material->type,
                'order' => $material->order,
                'status' => $status,
                'status_class' => $statusClass,
                'attendance_status' => $attendanceStatus,
                'material_status' => $materialStatus,
                'video_status' => $videoStatus,
                'is_test_completed' => $isTestCompleted,
                'test_score' => $testScore,
                'soal_pretest' => $material->soal_pretest,
                'soal_posttest' => $material->soal_posttest,
                'durasi_pretest' => $material->durasi_pretest,
                'durasi_posttest' => $material->durasi_posttest,
                'passing_grade' => $material->passing_grade,
                'file_path' => $material->file_path,
                'video_url' => $material->video_url,
                'video_type' => $material->video_type,
                'video_file' => $videoData, // Data video dari Google Drive
                'player_config' => $material->player_config,
                'description' => $material->description,
                'downloaded_files' => $downloadedFiles,
                'total_files' => $totalFiles,
                // Tambahan untuk menentukan konten yang tersedia
                'attendance_required' => $hasAttendance,
                'has_material' => $hasMaterial,
                'has_video' => $hasVideo, // PERBAIKAN: Sekarang termasuk video dari Google Drive
                'file_paths' => $filePaths
            ];
        }
        
        return $processedMaterials;
    }

    /**
     * Helper method untuk cek semua materi sebelumnya sudah selesai
     */
    private function allPreviousMaterialsCompleted($userId, $kursusId, $currentOrder)
    {
        if ($currentOrder <= 1) {
            return true;
        }
        
        // Ambil semua materi sebelumnya yang AKTIF
        $previousMaterials = Materials::where('course_id', $kursusId)
            ->where('is_active', true)
            ->where('order', '<', $currentOrder)
            ->orderBy('order')
            ->get();
        
        foreach ($previousMaterials as $material) {
            $progress = MaterialProgress::where('user_id', $userId)
                ->where('material_id', $material->id)
                ->first();
            
            if (!$this->isMaterialCompleted($progress, $material)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Helper method untuk cek apakah material sudah selesai (dengan multiple files)
     */
    private function isMaterialCompleted($progress, $material)
    {
        // Jika tidak ada progress, maka belum selesai
        if (!$progress) {
            return false;
        }

        // For test materials, check if test is completed
        if ($material->type === 'pre_test') {
            return $progress->pretest_score !== null;
        } elseif ($material->type === 'post_test') {
            return $progress->posttest_score !== null;
        } elseif ($material->type === 'recap') {
            return true; // Recap selalu bisa diakses setelah terbuka
        } else {
            // Untuk material reguler, cek semua status yang diperlukan
            
            // PERBAIKAN: Tentukan apakah memiliki video (termasuk dari Google Drive)
            $hasVideo = !empty($material->video_url) || !empty($material->video_file);
            
            $attendanceRequired = $material->attendance_required ?? true;
            $hasMaterial = !empty($material->file_path);
            
            $attendanceCompleted = !$attendanceRequired || $progress->attendance_status === 'completed';
            $videoCompleted = !$hasVideo || $progress->video_status === 'completed';
            
            // Cek material completion
            $materialCompleted = true; // default jika tidak ada material
            
            if ($hasMaterial) {
                if ($progress->all_files_downloaded) {
                    $materialCompleted = true;
                } else {
                    // Parse file_path untuk mendapatkan jumlah file
                    $filePaths = json_decode($material->file_path, true);
                    if (!is_array($filePaths)) {
                        $filePaths = [$material->file_path];
                    }
                    $totalFiles = count($filePaths);
                    
                    $downloadedFiles = json_decode($progress->downloaded_files, true) ?? [];
                    $materialCompleted = (count($downloadedFiles) >= $totalFiles);
                }
            }
            
            // PERBAIKAN: Gunakan material_status sebagai fallback
            if (!$materialCompleted && $progress->material_status === 'completed') {
                $materialCompleted = true;
            }
            
            return $attendanceCompleted && $materialCompleted && $videoCompleted;
        }
    }


    /**
     * Enroll to course
     */
    public function enroll(Request $request, $id)
    {
        $kursus = Kursus::where('status', 'aktif')
                        ->where('id', $id)
                        ->firstOrFail();

        $user = Auth::user();
        
        $alreadyEnrolled = Enrollment::where('user_id', $user->id)
                                   ->where('kursus_id', $id)
                                   ->exists();

        if ($alreadyEnrolled) {
            return redirect()->back()
                           ->with('error', 'Anda sudah mengikuti kursus ini.');
        }

        if ($kursus->kuota_peserta && 
            $kursus->peserta_terdaftar >= $kursus->kuota_peserta) {
            return redirect()->back()
                           ->with('error', 'Maaf, kuota kursus sudah penuh.');
        }

        try {
            // Create enrollment
            $totalMaterials = Materials::where('course_id', $id)
                                        ->where('is_active', true)
                                        ->count();
            
            Enrollment::create([
                'user_id' => $user->id,
                'kursus_id' => $id,
                'total_activities' => $totalMaterials,
                'enrolled_at' => now()
            ]);

            $kursus->increment('peserta_terdaftar');

            return redirect()->route('mitra.kursus.saya')
                            ->with('success', 'Berhasil mengikuti kursus!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * My courses
     */
    public function myCourses()
    {
        $user = Auth::user();
        
        $enrolledCourses = Enrollment::with('kursus')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mitra.kursus-saya', compact('enrolledCourses'));
    }

    // MARK: - Progress Tracking Methods
    public function markAttendance(Request $request, $kursus, $material)
    {
        Log::info('Mark Attendance:', [
            'kursus' => $kursus,
            'material' => $material,
            'user_id' => Auth::id()
        ]);

        $user = Auth::user();
        
        // Validasi user terenroll di kursus ini
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $kursus)
            ->firstOrFail();

        $materialRecord = Materials::where('is_active', true)
            ->where('id', $material)
            ->where('course_id', $kursus)
            ->firstOrFail();

        // Update atau buat progress
        $progress = MaterialProgress::where('user_id', $user->id)
            ->where('material_id', $material)
            ->first();
        
        if ($progress) {
            $progress->attendance_status = 'completed';
            $progress->save();
        } else {
            $progress = MaterialProgress::create([
                'user_id' => $user->id,
                'material_id' => $material,
                'attendance_status' => 'completed',
                'material_status' => empty($materialRecord->file_path) ? 'completed' : 'pending',
                'video_status' => empty($materialRecord->video_url) ? 'completed' : 'pending'
            ]);
        }

        // PERBAIKAN: Otomatis cek dan unlock material berikutnya
        $this->checkAndUnlockNextMaterial($user->id, $material, $kursus);

        // Update enrollment progress
        $this->updateEnrollmentProgress($user->id, $kursus);

        return response()->json([
            'success' => true,
            'message' => 'Kehadiran berhasil dicatat',
            'material_id' => $material,
            'attendance_status' => 'completed'
        ]);
    }

    // MARK: - File Download Methods
    /**
     * Download material file (ZIP atau single file)
     */
    public function downloadMaterialFile($kursus, $material)
    {
        Log::info('Download Material File:', [
            'kursus' => $kursus,
            'material' => $material,
            'user_id' => Auth::id()
        ]);
        
        $user = Auth::user();
        
        // Pastikan user terenroll di kursus ini
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $kursus)
            ->firstOrFail();
        
        // Ambil materi yang aktif
        $materialRecord = Materials::where('is_active', true)
            ->where('id', $material)
            ->where('course_id', $kursus)
            ->firstOrFail();
        
        // Cek apakah materi memiliki file
        if (!$materialRecord->file_path || empty($materialRecord->file_path)) {
            abort(404, 'File materi tidak tersedia');
        }
        
        try {
            // Parse file_path (bisa string atau JSON array)
            $filePaths = json_decode($materialRecord->file_path, true);
            
            // Jika bukan array, buat array dari string
            if (!is_array($filePaths)) {
                $filePaths = [$materialRecord->file_path];
            }
            
            // Filter hanya file yang valid
            $validFilePaths = [];
            foreach ($filePaths as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    $validFilePaths[] = $filePath;
                }
            }
            
            if (empty($validFilePaths)) {
                abort(404, 'Tidak ada file yang tersedia');
            }
            
            // Catat SEMUA file sebagai downloaded
            $this->recordAllFilesDownloaded($user->id, $material, count($validFilePaths));
            
            // PERBAIKAN: Otomatis cek dan unlock material berikutnya
            $this->checkAndUnlockNextMaterial($user->id, $material, $kursus);
            
            // Update enrollment progress
            $this->updateEnrollmentProgress($user->id, $kursus);
            
            // Jika hanya ada 1 file, download langsung
            if (count($validFilePaths) === 1) {
                $filePath = $validFilePaths[0];
                $fileName = basename($filePath);
                
                return Storage::disk('public')->download($filePath, $fileName);
            }
            
            // Jika ada multiple files, buat zip archive
            return $this->createZipDownload($validFilePaths, $materialRecord->title);
            
        } catch (\Exception $e) {
            Log::error('Error downloading material file:', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'material' => $material
            ]);
            
            return back()->with('error', 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }

    /**
     * Membuat zip archive dari multiple files
     */
    private function createZipDownload(array $filePaths, string $materialTitle)
    {
        $zipFileName = 'materi-' . Str::slug($materialTitle) . '-' . time() . '.zip';
        $zipPath = storage_path('app/public/temp/' . $zipFileName);
        
        if (!Storage::disk('public')->exists('temp')) {
            Storage::disk('public')->makeDirectory('temp');
        }

        $zip = new ZipArchive;
        
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($filePaths as $filePath) {
                $fileName = basename($filePath);
                $zip->addFile(Storage::disk('public')->path($filePath), $fileName);
            }
            
            $zip->close();
            
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            throw new \Exception('Gagal membuat file zip');
        }
    }

    private function recordAllFilesDownloaded($userId, $materialId, $totalFiles)
    {
        try {
            $progress = MaterialProgress::where('user_id', $userId)
                ->where('material_id', $materialId)
                ->first();
            
            $downloadedFiles = range(0, $totalFiles - 1);
            
            if (!$progress) {
                MaterialProgress::create([
                    'user_id' => $userId,
                    'material_id' => $materialId,
                    'downloaded_files' => json_encode($downloadedFiles),
                    'total_files' => $totalFiles,
                    'all_files_downloaded' => true,
                    'material_status' => 'completed',
                    'attendance_status' => 'pending',
                    'video_status' => 'pending'
                ]);
            } else {
                $progress->update([
                    'downloaded_files' => json_encode($downloadedFiles),
                    'total_files' => $totalFiles,
                    'all_files_downloaded' => true,
                    'material_status' => 'completed'
                ]);
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error recording all files downloaded:', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'material_id' => $materialId
            ]);
            return false;
        }
    }

    /**
     * API untuk mencatat video telah ditonton
     */
    public function recordVideoWatched(Request $request, $kursus, $material)
    {
        try {
            $user = Auth::user();
            
            // Update progress video
            $progress = MaterialProgress::where('user_id', $user->id)
                ->where('material_id', $material)
                ->first();
            
            if ($progress) {
                $progress->video_status = 'completed';
                $progress->save();
            } else {
                $materialRecord = Materials::find($material);
                MaterialProgress::create([
                    'user_id' => $user->id,
                    'material_id' => $material,
                    'video_status' => 'completed',
                    'material_status' => ($materialRecord && $materialRecord->file_path) ? 'pending' : 'completed',
                    'attendance_status' => ($materialRecord && ($materialRecord->attendance_required ?? true)) ? 'pending' : 'completed'
                ]);
            }

            // PERBAIKAN: Otomatis cek dan unlock material berikutnya
            $this->checkAndUnlockNextMaterial($user->id, $material, $kursus);

            // Update enrollment progress
            $this->updateEnrollmentProgress($user->id, $kursus);

            return response()->json([
                'success' => true,
                'message' => 'Video telah ditandai sebagai telah ditonton'
            ]);

        } catch (\Exception $e) {
            Log::error('Error recording video watched:', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'material' => $material
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat progress video'
            ], 500);
        }
    }

    /**
     * PERBAIKAN: Method untuk cek dan unlock material berikutnya
     */
    private function checkAndUnlockNextMaterial($userId, $currentMaterialId, $kursusId)
    {
        try {
            $currentMaterial = Materials::where('is_active', true)
                ->where('id', $currentMaterialId)
                ->where('course_id', $kursusId)
                ->first();
            
            if (!$currentMaterial) {
                return false;
            }
            
            // Cek apakah material saat ini sudah selesai
            $progress = MaterialProgress::where('user_id', $userId)
                ->where('material_id', $currentMaterialId)
                ->first();
            
            if (!$progress) {
                return false;
            }
            
            $isCurrentCompleted = $this->isMaterialCompleted($progress, $currentMaterial);
            
            if ($isCurrentCompleted) {
                // Cari material berikutnya
                $nextMaterial = Materials::where('course_id', $kursusId)
                    ->where('is_active', true)
                    ->where('order', '>', $currentMaterial->order)
                    ->orderBy('order')
                    ->first();
                
                if ($nextMaterial) {
                    // Untuk material berikutnya, kita tidak perlu melakukan apa-apa di database
                    // Karena status akan dihitung ulang saat getMaterialsWithStatus dipanggil
                    Log::info('Material berikutnya siap dibuka:', [
                        'current_material_id' => $currentMaterialId,
                        'next_material_id' => $nextMaterial->id,
                        'next_material_order' => $nextMaterial->order
                    ]);
                    
                    return true;
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error checking and unlocking next material:', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'current_material_id' => $currentMaterialId,
                'kursus_id' => $kursusId
            ]);
            return false;
        }
    }

    /**
     * Method baru: Refresh status material untuk real-time update
     */
    public function refreshMaterialStatus($kursusId, $materialId)
    {
        try {
            $user = Auth::user();
            
            $kursus = Kursus::where('status', 'aktif')
                ->where('id', $kursusId)
                ->with(['materials' => function($query) {
                    $query->where('is_active', true)
                          ->orderBy('order');
                }])
                ->firstOrFail();
            
            // Cari material yang diminta
            $targetMaterial = null;
            foreach ($kursus->materials as $material) {
                if ($material->id == $materialId) {
                    $targetMaterial = $material;
                    break;
                }
            }
            
            if (!$targetMaterial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material tidak ditemukan'
                ], 404);
            }
            
            // Hitung ulang status untuk material ini dan berikutnya
            $userProgress = MaterialProgress::where('user_id', $user->id)
                ->whereIn('material_id', $kursus->materials->pluck('id'))
                ->get()
                ->keyBy('material_id');
            
            $materialsWithStatus = [];
            
            foreach ($kursus->materials->sortBy('order') as $material) {
                $progress = $userProgress[$material->id] ?? null;
                
                // Tentukan status untuk setiap material
                if ($material->order === 1) {
                    $isAccessible = true;
                } else {
                    $previousMaterial = $kursus->materials->where('order', $material->order - 1)->first();
                    if ($previousMaterial) {
                        $previousProgress = $userProgress[$previousMaterial->id] ?? null;
                        $isAccessible = $this->isMaterialCompleted($previousProgress, $previousMaterial);
                    } else {
                        $isAccessible = true;
                    }
                }
                
                $isCompleted = $this->isMaterialCompleted($progress, $material);
                
                if ($isCompleted) {
                    $status = 'completed';
                    $statusClass = 'completed';
                } elseif ($isAccessible) {
                    $status = 'current';
                    $statusClass = 'current';
                } else {
                    $status = 'locked';
                    $statusClass = 'locked';
                }
                
                $materialsWithStatus[] = [
                    'id' => $material->id,
                    'title' => $material->title,
                    'order' => $material->order,
                    'status' => $status,
                    'status_class' => $statusClass
                ];
            }
            
            return response()->json([
                'success' => true,
                'materials' => $materialsWithStatus
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error refreshing material status:', [
                'error' => $e->getMessage(),
                'kursus_id' => $kursusId,
                'material_id' => $materialId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal refresh status material'
            ], 500);
        }
    }

    public function completeMaterial(Request $request, $kursusId, $materialId)
    {
        try {
            $user = Auth::user();
            
            // Validasi user terenroll
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('kursus_id', $kursusId)
                ->firstOrFail();
            
            // Update material progress
            $progress = MaterialProgress::where('user_id', $user->id)
                ->where('material_id', $materialId)
                ->first();
            
            if ($progress) {
                // Update material_status ke completed
                $progress->material_status = 'completed';
                $progress->save();
                
                // PERBAIKAN: Otomatis cek dan unlock material berikutnya
                $this->checkAndUnlockNextMaterial($user->id, $materialId, $kursusId);
                
                // Update enrollment progress
                $this->updateEnrollmentProgress($user->id, $kursusId);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Material berhasil diselesaikan'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Progress tidak ditemukan'
                ], 404);
            }
            
        } catch (\Exception $e) {
            Log::error('Error completing material:', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'material_id' => $materialId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai material selesai'
            ], 500);
        }
    }

    /**
     * Untuk menampilkan daftar file yang tersedia (optional)
     */
    public function showMaterialFiles($kursus, $material)
    {
        $user = Auth::user();
        
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $kursus)
            ->firstOrFail();

        $materialRecord = Materials::where('is_active', true)
            ->where('id', $material)
            ->where('course_id', $kursus)
            ->firstOrFail();

        // Parse file_path
        $filePaths = json_decode($materialRecord->file_path, true);
        
        if (!is_array($filePaths)) {
            $filePaths = [$materialRecord->file_path];
        }

        // Siapkan data file
        $files = [];
        foreach ($filePaths as $index => $filePath) {
            if (Storage::disk('public')->exists($filePath)) {
                $files[] = [
                    'index' => $index,
                    'name' => basename($filePath),
                    'size' => Storage::disk('public')->size($filePath),
                    'extension' => pathinfo($filePath, PATHINFO_EXTENSION),
                    'download_url' => route('mitra.kursus.material.download', [
                        'kursus' => $kursus,
                        'material' => $material
                    ])
                ];
            }
        }

        return view('mitra.material-files', [
            'kursus' => $enrollment->kursus,
            'material' => $materialRecord,
            'files' => $files,
            'hasMultiple' => count($files) > 1,
            'downloadAllUrl' => route('mitra.kursus.material.download', [
                'kursus' => $kursus,
                'material' => $material
            ])
        ]);
    }

    public function viewMaterialVideo($kursus, $material)
    {
        Log::info('View Material Video - PERBAIKAN:', [
            'kursus' => $kursus,
            'material' => $material,
            'user_id' => Auth::id()
        ]);

        $user = Auth::user();
        
        // Pastikan user terenroll di kursus ini
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $kursus)
            ->firstOrFail();

        // Ambil materi yang aktif
        $materialRecord = Materials::where('is_active', true)
            ->where('id', $material)
            ->where('course_id', $kursus)
            ->firstOrFail();

        // Cek apakah materi memiliki video
        // PERBAIKAN: Cek baik video_url maupun video_file (Google Drive)
        if (empty($materialRecord->video_url) && empty($materialRecord->video_file)) {
            abort(404, 'Video materi tidak tersedia');
        }

        // Update progress video status
        $progress = MaterialProgress::where('user_id', $user->id)
            ->where('material_id', $material)
            ->first();
        
        if ($progress) {
            $progress->video_status = 'completed';
            $progress->save();
        } else {
            MaterialProgress::create([
                'user_id' => $user->id,
                'material_id' => $material,
                'video_status' => 'completed',
                'material_status' => empty($materialRecord->file_path) ? 'completed' : 'pending',
                'attendance_status' => ($materialRecord->attendance_required ?? true) ? 'pending' : 'completed'
            ]);
        }

        // PERBAIKAN: Otomatis cek dan unlock material berikutnya
        $this->checkAndUnlockNextMaterial($user->id, $material, $kursus);

        // Update enrollment progress
        $this->updateEnrollmentProgress($user->id, $kursus);

        // **PERBAIKAN PENTING: Siapkan data video berdasarkan tipe**
        $videoData = $this->prepareVideoData($materialRecord);
        
        // Load video questions jika ada
        $videoQuestions = [];
        if ($materialRecord->has_video_questions) {
            $videoQuestions = \App\Models\VideoQuestion::where('material_id', $materialRecord->id)
                ->orderBy('order')
                ->get();
        }

        // **Tampilkan halaman view video yang sudah diperbaiki**
        return view('mitra.kursus.video-player', [
            'kursus' => $enrollment->kursus,
            'material' => $materialRecord,
            'videoData' => $videoData,
            'videoQuestions' => $videoQuestions,
            'progress' => $progress
        ]);
    }

    private function prepareVideoData($material)
{
    $videoData = [
        'type' => $material->video_type ?? 'external',
        'url' => $material->video_url,
        'embed_url' => null,
        'is_hosted' => false,
        'drive_data' => null,
        'player_config' => $material->player_config ? json_decode($material->player_config, true) : [],
    ];
    
    // **PERBAIKAN: Video dari Google Drive (hosted)**
    if ($material->video_type === 'hosted' && $material->video_file) {
        try {
            $videoInfo = is_string($material->video_file) 
                ? json_decode($material->video_file, true) 
                : $material->video_file;
            
            if ($videoInfo) {
                $videoData['is_hosted'] = true;
                $videoData['drive_data'] = $videoInfo;
                
                // **PERBAIKAN UTAMA: Pastikan embed URL benar**
                if (isset($videoInfo['embed_link'])) {
                    // Jika sudah ada embed_link langsung di data
                    $videoData['embed_url'] = $videoInfo['embed_link'];
                } elseif (isset($videoInfo['web_view_link'])) {
                    // Coba beberapa format embed
                    $originalUrl = $videoInfo['web_view_link'];
                    
                    // Cek berbagai format Google Drive URL
                    if (strpos($originalUrl, '/file/d/') !== false) {
                        // Format: https://drive.google.com/file/d/FILE_ID/view
                        $embedUrl = str_replace('/view', '/preview', $originalUrl);
                        $videoData['embed_url'] = $embedUrl;
                    } elseif (isset($videoInfo['id'])) {
                        // Gunakan file ID langsung
                        $videoData['embed_url'] = 'https://drive.google.com/file/d/' . $videoInfo['id'] . '/preview';
                    } else {
                        // Fallback ke web_view_link
                        $videoData['embed_url'] = $originalUrl;
                    }
                    
                    // **PERBAIKAN TAMBAHAN: Tambahkan parameter untuk mencegah akses ditolak**
                    if ($videoData['embed_url'] && strpos($videoData['embed_url'], 'preview') !== false) {
                        $videoData['embed_url'] .= '?authuser=0&embedded=true';
                    }
                }
                
                $videoData['url'] = $videoInfo['web_view_link'] ?? $material->video_url;
                
                Log::info('Google Drive video prepared:', [
                    'video_info' => $videoInfo,
                    'embed_url' => $videoData['embed_url']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error parsing video_file data: ' . $e->getMessage());
        }
    }
    
    // **Untuk YouTube**
    if ($material->video_type === 'youtube' && $material->video_url) {
        $videoId = $this->extractYouTubeId($material->video_url);
        if ($videoId) {
            $videoData['embed_url'] = 'https://www.youtube.com/embed/' . $videoId . '?rel=0&showinfo=0&controls=1';
        }
    }
    
    // **Untuk Vimeo**
    if ($material->video_type === 'vimeo' && $material->video_url) {
        $videoId = $this->extractVimeoId($material->video_url);
        if ($videoId) {
            $videoData['embed_url'] = 'https://player.vimeo.com/video/' . $videoId;
        }
    }
    
    // **Untuk video eksternal lainnya**
    if (!$videoData['embed_url'] && $material->video_url) {
        $videoData['embed_url'] = $material->video_url;
    }
    
    return $videoData;
}
    private function extractYouTubeId($url)
    {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Extract Vimeo video ID from URL
     */
    private function extractVimeoId($url)
    {
        preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    public function updateVideoProgress(Request $request, $kursus, $material)
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'progress_percentage' => 'required|numeric|min:0|max:100',
                'current_time' => 'required|numeric',
                'duration' => 'required|numeric',
            ]);
            
            // Cek apakah video sudah selesai berdasarkan persentase
            $materialRecord = Materials::where('is_active', true)
                ->where('id', $material)
                ->first();
            
            if (!$materialRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material tidak ditemukan'
                ], 404);
            }
            
            // Update atau buat progress
            $progress = MaterialProgress::where('user_id', $user->id)
                ->where('material_id', $material)
                ->first();
            
            $minWatchPercentage = 90; // Default 90%
            
            if ($materialRecord->player_config) {
                $playerConfig = json_decode($materialRecord->player_config, true);
                $minWatchPercentage = $playerConfig['min_watch_percentage'] ?? 90;
            }
            
            $isCompleted = $request->progress_percentage >= $minWatchPercentage;
            
            if ($progress) {
                $progress->video_status = $isCompleted ? 'completed' : 'in_progress';
                $progress->video_progress = $request->progress_percentage;
                $progress->video_current_time = $request->current_time;
                $progress->video_duration = $request->duration;
                $progress->save();
            } else {
                $progress = MaterialProgress::create([
                    'user_id' => $user->id,
                    'material_id' => $material,
                    'video_status' => $isCompleted ? 'completed' : 'in_progress',
                    'video_progress' => $request->progress_percentage,
                    'video_current_time' => $request->current_time,
                    'video_duration' => $request->duration,
                    'material_status' => 'pending',
                    'attendance_status' => 'pending'
                ]);
            }
            
            // Jika video selesai, cek dan unlock material berikutnya
            if ($isCompleted && $progress->video_status === 'completed') {
                $this->checkAndUnlockNextMaterial($user->id, $material, $kursus);
                $this->updateEnrollmentProgress($user->id, $kursus);
            }
            
            return response()->json([
                'success' => true,
                'progress' => $progress,
                'is_completed' => $isCompleted
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating video progress:', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'material' => $material
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal update progress video'
            ], 500);
        }
    }

// Tambahkan method ini di controller
private function validateAndFixDriveUrl($url)
{
    if (!$url) {
        return null;
    }
    
    // Jika URL tidak valid, coba perbaiki
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        // Coba extract file ID dari berbagai format
        preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches);
        if (isset($matches[1])) {
            return 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
        }
        return null;
    }
    
    // Pastikan URL menggunakan HTTPS
    $url = str_replace('http://', 'https://', $url);
    
    // Jika sudah preview, return as is
    if (strpos($url, '/preview') !== false) {
        return $url;
    }
    
    // Ubah view ke preview
    if (strpos($url, '/view') !== false) {
        return str_replace('/view', '/preview', $url);
    }
    
    // Tambahkan /preview jika belum ada
    if (strpos($url, '/file/d/') !== false && !strpos($url, '/preview') && !strpos($url, '/view')) {
        return $url . '/preview';
    }
    
    return $url;
}

    public function markVideoAsWatched(Request $request, $kursus, $material)
    {
        Log::info('Mark Video as Watched:', [
            'kursus' => $kursus,
            'material' => $material,
            'user_id' => Auth::id()
        ]);

        $user = Auth::user();
        
        try {
            // Update progress video
            $progress = MaterialProgress::where('user_id', $user->id)
                ->where('material_id', $material)
                ->first();
            
            if ($progress) {
                $progress->video_status = 'completed';
                $progress->save();
            } else {
                $materialRecord = Materials::find($material);
                MaterialProgress::create([
                    'user_id' => $user->id,
                    'material_id' => $material,
                    'video_status' => 'completed',
                    'material_status' => ($materialRecord && $materialRecord->file_path) ? 'pending' : 'completed',
                    'attendance_status' => ($materialRecord && ($materialRecord->attendance_required ?? true)) ? 'pending' : 'completed'
                ]);
            }

            // PERBAIKAN: Otomatis cek dan unlock material berikutnya
            $this->checkAndUnlockNextMaterial($user->id, $material, $kursus);

            // Update enrollment progress
            $this->updateEnrollmentProgress($user->id, $kursus);

            return response()->json([
                'success' => true,
                'message' => 'Video telah ditandai sebagai telah ditonton'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking video as watched:', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'material' => $material
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai video: ' . $e->getMessage()
            ], 500);
        }
    }

    // MARK: - Test Methods
    public function showTest($kursus, $material, $testType)
{
    Log::info('Show Test:', [
        'kursus' => $kursus,
        'material' => $material,
        'test_type' => $testType,
        'user_id' => Auth::id()
    ]);

    $kursusRecord = Kursus::findOrFail($kursus);
    
    // HANYA ambil materi yang AKTIF
    $materialRecord = Materials::where('is_active', true)
                        ->where('id', $material)
                        ->firstOrFail();
    
    $user = Auth::user();

    // Validate test type
    if ($materialRecord->type !== $testType) {
        abort(404);
    }

    // Tentukan soal berdasarkan jenis test
    if ($testType === 'pre_test') {
        $soalTest = $materialRecord->soal_pretest;
        $durasi = $materialRecord->durasi_pretest;
    } else {
        $soalTest = $materialRecord->soal_posttest;
        $durasi = $materialRecord->durasi_posttest;
    }

    // Validasi apakah soal tersedia
    if (empty($soalTest)) {
        return redirect()->route('mitra.kursus.show', $kursus)
            ->with('error', 'Soal ' . ($testType === 'pre_test' ? 'pretest' : 'posttest') . ' belum tersedia.');
    }

    // Check if user can access this test
    if (!$this->canAccessMaterial($user->id, $kursus, $materialRecord)) {
        return redirect()->route('mitra.kursus.show', $kursus)
            ->with('error', 'Silakan selesaikan materi sebelumnya terlebih dahulu.');
    }

    // Cek apakah user sudah mengerjakan test ini
    $progress = MaterialProgress::where('user_id', $user->id)
        ->where('material_id', $material)
        ->first();

    if ($testType === 'pre_test' && $progress && $progress->pretest_score !== null) {
        return redirect()->route('mitra.kursus.show', $kursus)
            ->with('info', 'Anda sudah mengerjakan pretest ini.');
    }

    if ($testType === 'post_test' && $progress && $progress->posttest_score !== null) {
        return redirect()->route('mitra.kursus.show', $kursus)
            ->with('info', 'Anda sudah mengerjakan posttest ini.');
    }

    // PERBAIKAN: Kirim variabel dengan nama yang benar
    return view('mitra.test', [
        'kursus' => $kursusRecord,
        'material' => $materialRecord, // Ini yang harusnya digunakan di view
        'testType' => $testType,
        'soalTest' => $soalTest,
        'durasi' => $durasi
    ]);
}

    public function submitTest(Request $request, $kursus, $material, $testType)
    {
        Log::info('=== TEST SUBMISSION START ===', [
            'kursus' => $kursus,
            'material' => $material,
            'test_type' => $testType,
            'user_id' => Auth::id()
        ]);

        try {
            // HANYA ambil materi yang AKTIF
            $materialRecord = Materials::where('is_active', true)
                                ->findOrFail($material);
            $user = Auth::user();

            if ($materialRecord->type !== $testType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis test tidak sesuai'
                ], 404);
            }

            // Validasi lebih fleksibel untuk menerima answers kosong
            $validated = $request->validate([
                'answers' => 'sometimes|array',
                'answers.*' => 'sometimes|nullable|integer'
            ]);

            $userAnswers = $validated['answers'] ?? [];
            $score = 0;

            // Tentukan soal yang akan digunakan berdasarkan tipe test
            if ($testType === 'pre_test') {
                $soalTest = $materialRecord->soal_pretest;
                $totalQuestions = count($materialRecord->soal_pretest ?? []);
            } else {
                $soalTest = $materialRecord->soal_posttest;
                $totalQuestions = count($materialRecord->soal_posttest ?? []);
            }

            // Validasi: pastikan ada soal yang tersedia
            if ($totalQuestions === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada soal yang tersedia untuk test ini'
                ], 400);
            }

            // Hitung score berdasarkan tipe test
            foreach ($soalTest as $index => $soal) {
                if (isset($userAnswers[$index]) && 
                    $userAnswers[$index] !== null && 
                    $userAnswers[$index] !== '' && 
                    $userAnswers[$index] == ($soal['jawaban_benar'] ?? null)) {
                    $score++;
                }
            }

            $finalScore = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;
            $isPassed = $finalScore >= $materialRecord->passing_grade;

            // Save progress
            $progressData = [
                'attempts' => DB::raw('COALESCE(attempts, 0) + 1'),
                'is_completed' => $isPassed ? 1 : 0
            ];

            if ($testType === 'pre_test') {
                $progressData['pretest_score'] = $finalScore;
                $progressData['pretest_completed_at'] = now();
            } else {
                $progressData['posttest_score'] = $finalScore;
                $progressData['posttest_completed_at'] = now();
            }

            // Gunakan transaction untuk memastikan data konsisten
            DB::beginTransaction();
            
            try {
                $progress = MaterialProgress::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'material_id' => $material
                    ],
                    $progressData
                );

                // PERBAIKAN: Otomatis cek dan unlock material berikutnya
                $this->checkAndUnlockNextMaterial($user->id, $material, $kursus);
                
                // Update enrollment progress
                $this->updateEnrollmentProgress($user->id, $kursus);
                
                DB::commit();

                return response()->json([
                    'success' => true,
                    'score' => round($finalScore, 2),
                    'is_passed' => $isPassed,
                    'passing_grade' => $materialRecord->passing_grade,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $score,
                    'answered_count' => count($userAnswers),
                    'message' => 'Test berhasil disubmit'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);
            
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorMessages[] = "$field: $message";
                }
            }
            $errorMessage = implode(', ', $errorMessages);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $errorMessage
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Test Submission Error:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateEnrollmentProgress($userId, $kursusId)
    {
        try {
            $enrollment = Enrollment::where('user_id', $userId)
                ->where('kursus_id', $kursusId)
                ->first();
            
            if ($enrollment) {
                // Hitung progress berdasarkan materi yang sudah diselesaikan
                $totalMaterials = Materials::where('course_id', $kursusId)
                                            ->where('is_active', true)
                                            ->count();
                
                // Hitung materi yang sudah diselesaikan
                $completedMaterials = 0;
                $allMaterials = Materials::where('course_id', $kursusId)
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get();
                
                foreach ($allMaterials as $material) {
                    $progress = MaterialProgress::where('user_id', $userId)
                        ->where('material_id', $material->id)
                        ->first();
                    
                    if ($this->isMaterialCompleted($progress, $material)) {
                        $completedMaterials++;
                    }
                }
                
                $progressPercentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;
                
                // Update status enrollment
                $status = ($progressPercentage >= 100) ? 'completed' : 'in_progress';
                
                $enrollment->update([
                    'progress_percentage' => $progressPercentage,
                    'status' => $status,
                    'completed_at' => $status === 'completed' ? now() : null
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating enrollment progress:', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'kursus_id' => $kursusId
            ]);
        }
    }

    /**
     * API untuk mendapatkan progress terbaru
     */
    public function getProgress($kursusId)
    {
        try {
            $user = Auth::user();
            
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('kursus_id', $kursusId)
                ->firstOrFail();
            
            // Hitung progress berdasarkan materi yang sudah diselesaikan
            $totalMaterials = Materials::where('course_id', $kursusId)
                                        ->where('is_active', true)
                                        ->count();
            
            // Hitung berapa banyak material yang sudah selesai
            $completedMaterials = 0;
            $allMaterials = Materials::where('course_id', $kursusId)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
            
            foreach ($allMaterials as $material) {
                $progress = MaterialProgress::where('user_id', $user->id)
                    ->where('material_id', $material->id)
                    ->first();
                
                if ($this->isMaterialCompleted($progress, $material)) {
                    $completedMaterials++;
                }
            }
            
            $progressPercentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;
            
            return response()->json([
                'success' => true,
                'progress_percentage' => $progressPercentage,
                'completed_materials' => $completedMaterials,
                'total_materials' => $totalMaterials
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting progress:', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'kursus_id' => $kursusId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan progress'
            ], 500);
        }
    }

    /**
     * API untuk mendapatkan status material terbaru
     */
    public function getMaterialStatus($kursusId, $materialId)
    {
        try {
            $user = Auth::user();
            
            $materialRecord = Materials::where('is_active', true)
                ->where('id', $materialId)
                ->where('course_id', $kursusId)
                ->firstOrFail();
            
            $progress = MaterialProgress::where('user_id', $user->id)
                ->where('material_id', $materialId)
                ->first();
            
            // Tentukan status
            if ($materialRecord->order === 1) {
                $isAccessible = true;
            } else {
                $previousMaterial = Materials::where('course_id', $kursusId)
                    ->where('is_active', true)
                    ->where('order', $materialRecord->order - 1)
                    ->first();
                
                if (!$previousMaterial) {
                    $isAccessible = true;
                } else {
                    $previousProgress = MaterialProgress::where('user_id', $user->id)
                        ->where('material_id', $previousMaterial->id)
                        ->first();
                    
                    $isAccessible = $this->isMaterialCompleted($previousProgress, $previousMaterial);
                }
            }
            
            $isCompleted = $this->isMaterialCompleted($progress, $materialRecord);
            
            if ($isCompleted) {
                $status = 'completed';
            } elseif ($isAccessible) {
                $status = 'current';
            } else {
                $status = 'locked';
            }
            
            return response()->json([
                'success' => true,
                'material_id' => $materialId,
                'status' => $status,
                'attendance_status' => $progress ? $progress->attendance_status : 'pending',
                'material_status' => $progress ? $progress->material_status : 'pending',
                'video_status' => $progress ? $progress->video_status : 'pending',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting material status:', [
                'error' => $e->getMessage(),
                'kursus_id' => $kursusId,
                'material_id' => $materialId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan status material'
            ], 500);
        }
    }

    /**
 * Refresh status semua material dalam kursus
 */
public function refreshAllMaterialsStatus($kursusId)
{
    try {
        $user = Auth::user();
        
        $kursus = Kursus::where('status', 'aktif')
            ->where('id', $kursusId)
            ->with(['materials' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('order');
            }])
            ->firstOrFail();
        
        $materials = $this->getMaterialsWithStatus($kursus->materials, $user, $kursusId);
        
        return response()->json([
            'success' => true,
            'materials' => $materials
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error refreshing all materials status:', [
            'error' => $e->getMessage(),
            'kursus_id' => $kursusId
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal refresh status material'
        ], 500);
    }
}

    public function getMaterialSubtasks($kursusId, $materialId)
    {
        try {
            $kursus = Kursus::findOrFail($kursusId);
            $material = Materials::where('is_active', true)
                                ->where('id', $materialId)
                                ->where('course_id', $kursusId)
                                ->firstOrFail();
            
            $user = Auth::user();
            
            // Pastikan user terenroll
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('kursus_id', $kursusId)
                ->firstOrFail();
            
            // Dapatkan progress untuk material ini
            $progress = MaterialProgress::where('user_id', $user->id)
                ->where('material_id', $materialId)
                ->first();
            
            // Tentukan status menggunakan logika yang sama
            if ($material->order === 1) {
                $isAccessible = true;
            } else {
                $previousMaterial = Materials::where('course_id', $kursusId)
                    ->where('is_active', true)
                    ->where('order', $material->order - 1)
                    ->first();
                
                if (!$previousMaterial) {
                    $isAccessible = true;
                } else {
                    $previousProgress = MaterialProgress::where('user_id', $user->id)
                        ->where('material_id', $previousMaterial->id)
                        ->first();
                    
                    $isAccessible = $this->isMaterialCompleted($previousProgress, $previousMaterial);
                }
            }
            
            $isCompleted = $this->isMaterialCompleted($progress, $material);
            
            if ($isCompleted) {
                $status = 'completed';
                $statusClass = 'completed';
            } elseif ($isAccessible) {
                $status = 'current';
                $statusClass = 'current';
            } else {
                $status = 'locked';
                $statusClass = 'locked';
            }
            
            // Parse file_path
            $filePaths = json_decode($material->file_path, true) ?? [$material->file_path];
            $totalFiles = count($filePaths);
            
            // Prepare material data
            $materialData = [
                'id' => $material->id,
                'title' => $material->title,
                'description' => $material->description,
                'type' => 'material',
                'status' => $status,
                'status_class' => $statusClass,
                'attendance_required' => $material->attendance_required ?? true,
                'attendance_status' => $progress && $progress->attendance_status === 'completed' ? 'completed' : 'pending',
                'has_material' => !empty($material->file_path),
                'material_status' => $progress && $progress->material_status === 'completed' ? 'completed' : 'pending',
                'file_path' => $material->file_path,
                'file_paths' => $filePaths,
                'total_files' => $totalFiles,
                'has_video' => !empty($material->video_url),
                'video_status' => $progress && $progress->video_status === 'completed' ? 'completed' : 'pending',
                'video_url' => $material->video_url,
            ];
            
            return response()->json([
                'success' => true,
                'html' => view('mitra.partials.material-subtasks', [
                    'material' => $materialData,
                    'kursus' => $kursus
                ])->render()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMaterialSubtasks:', [
                'error' => $e->getMessage(),
                'kursusId' => $kursusId,
                'materialId' => $materialId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading material subtasks'
            ], 500);
        }
    }

    public function showRecap($kursus, $material)
    {
        Log::info('Show Recap:', [
            'kursus' => $kursus,
            'material' => $material,
            'user_id' => Auth::id()
        ]);

        $kursusRecord = Kursus::findOrFail($kursus);
        // HANYA ambil materi yang AKTIF
        $materialRecord = Materials::where('is_active', true)
                            ->findOrFail($material);
        
        $user = Auth::user();

        if ($materialRecord->type !== 'recap') {
            abort(404);
        }

        // Get all progress for this course
        $progress = MaterialProgress::where('user_id', $user->id)
            ->whereIn('material_id', $kursusRecord->materials()
                                            ->where('is_active', true)
                                            ->pluck('id'))
            ->with('material')
            ->get();

        return view('mitra.recap', compact('kursusRecord', 'materialRecord', 'progress'));
    }

    // Helper method to check if user can access material
    private function canAccessMaterial($userId, $kursusId, $material)
    {
        // First material is always accessible
        if ($material->order === 1) {
            return true;
        }

        // Get previous material (HANYA YANG AKTIF)
        $previousMaterial = Materials::where('course_id', $kursusId)
            ->where('is_active', true)
            ->where('order', $material->order - 1)
            ->first();

        if (!$previousMaterial) {
            return true;
        }

        // Check if previous material is completed
        $previousProgress = MaterialProgress::where('user_id', $userId)
            ->where('material_id', $previousMaterial->id)
            ->first();

        if (!$previousProgress) {
            return false;
        }

        return $this->isMaterialCompleted($previousProgress, $previousMaterial);
    }
}