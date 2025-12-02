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
        $materials = $this->getMaterialsWithStatus($kursus->materials, $user);
        
        // Calculate progress
        $totalMaterials = $kursus->materials->count();
        $completedMaterials = collect($materials)->where('status', 'completed')->count();
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

        // Query enrollments, bukan kursus
        $enrollments = Enrollment::where('user_id', $userId)
            ->with(['kursus' => function($query) {
                $query->where('status', 'aktif');
            }])
            ->whereHas('kursus', function($query) {
                $query->where('status', 'aktif');
            });

        // Apply filter
        if ($filter === 'in_progress') {
            $enrollments->where('status', 'in_progress')
                    ->where('progress_percentage', '<', 100);
        }

        if ($filter === 'completed') {
            $enrollments->where('status', 'completed')
                    ->orWhere('progress_percentage', 100);
        }

        $enrollments = $enrollments->orderBy('updated_at', 'desc')
                                ->get();

        return view('mitra.kursus-saya', compact('enrollments', 'filter'));
    }

    private function getMaterialsWithStatus($materials, $user)
    {
        $processedMaterials = [];
        $previousCompleted = true; // First material is always accessible
        
        foreach ($materials->sortBy('order') as $material) {
            // Skip jika materi tidak aktif
            if (!$material->is_active) {
                continue;
            }
            
            $progress = MaterialProgress::where('user_id', $user->id)
                                    ->where('material_id', $material->id)
                                    ->first();
            
            // Determine if material is accessible
            $isAccessible = $previousCompleted;
            
            // Determine status
            if ($progress && $this->isMaterialCompleted($progress, $material)) {
                $status = 'completed';
                $statusClass = 'completed';
                $previousCompleted = true;
            } elseif ($isAccessible) {
                $status = 'current';
                $statusClass = 'current';
                $previousCompleted = false;
            } else {
                $status = 'locked';
                $statusClass = 'locked';
                $previousCompleted = false;
            }
            
            // Get specific status for each task
            $attendanceStatus = $progress->attendance_status ?? 'pending';
            $materialStatus = $progress->material_status ?? 'pending';
            $videoStatus = $progress->video_status ?? 'pending';
            
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
            
            // Determine available content types
            $hasAttendance = $material->attendance_required ?? true;
            $hasMaterial = !empty($material->file_path);
            $hasVideo = !empty($material->video_url);
            
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
                'description' => $material->description,
                // Tambahan untuk menentukan konten yang tersedia
                'attendance_required' => $hasAttendance,
                'has_material' => $hasMaterial,
                'has_video' => $hasVideo
            ];
        }
        
        return $processedMaterials;
    }

    private function isMaterialCompleted($progress, $material)
    {
        // For test materials, check if test is completed
        if ($material->type === 'pre_test') {
            return $progress->pretest_score !== null;
        } elseif ($material->type === 'post_test') {
            return $progress->posttest_score !== null;
        } elseif ($material->type === 'recap') {
            return true; // Recap is always accessible once unlocked
        } else {
            // For regular materials, check all statuses
            return $progress->attendance_status === 'completed' &&
                   $progress->material_status === 'completed' &&
                   $progress->video_status === 'completed';
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

            // Hitung real total materials dari kursus ini (HANYA YANG AKTIF)
            $totalMaterials = Materials::where('course_id', $id)
                                        ->where('is_active', true)
                                        ->count();
            
            Enrollment::create([
                'user_id' => $user->id,
                'kursus_id' => $id,
                'total_activities' => $totalMaterials, // ← PAKAI REAL COUNT (hanya yang aktif)
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
    public function markAttendance($materialId)
    {
        $user = Auth::user();
        $material = Materials::where('is_active', true)
                            ->findOrFail($materialId);
        
        $enrollment = Enrollment::where('user_id', $user->id)
                            ->where('kursus_id', $material->course_id)
                            ->firstOrFail();

        MaterialProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'material_id' => $materialId
            ],
            [
                'attendance_status' => 'completed'
            ]
        );
        
        return response()->json(['success' => true]);
    }

    public function markMaterialCompleted($materialId)
    {
        $user = Auth::user();
        $material = Materials::where('is_active', true)
                            ->findOrFail($materialId);
        
        $enrollment = Enrollment::where('user_id', $user->id)
                            ->where('kursus_id', $material->course_id)
                            ->firstOrFail();

        MaterialProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'material_id' => $materialId
            ],
            [
                'material_status' => 'completed'
            ]
        );
        
        return response()->json(['success' => true]);
    }

    public function markVideoCompleted($materialId)
    {
        $user = Auth::user();
        $material = Materials::where('is_active', true)
                            ->findOrFail($materialId);
        
        $enrollment = Enrollment::where('user_id', $user->id)
                            ->where('kursus_id', $material->course_id)
                            ->firstOrFail();

        MaterialProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'material_id' => $materialId
            ],
            [
                'video_status' => 'completed'
            ]
        );
        
        return response()->json(['success' => true]);
    }

    // MARK: - Test Methods
    public function showTest($kursusId, $materialId, $testType)
    {
        $kursus = Kursus::findOrFail($kursusId);
        
        // HANYA ambil materi yang AKTIF
        $material = Materials::where('is_active', true)
                            ->where('id', $materialId)
                            ->firstOrFail();
        
        $user = Auth::user();

        // Validate test type
        if ($material->type !== $testType) {
            abort(404);
        }

        // ⚠️ PERBAIKAN: Tentukan soal berdasarkan jenis test
        if ($testType === 'pre_test') {
            $soalTest = $material->soal_pretest;
            $durasi = $material->durasi_pretest;
        } else {
            $soalTest = $material->soal_posttest;
            $durasi = $material->durasi_posttest;
        }

        // Validasi apakah soal tersedia
        if (empty($soalTest)) {
            return redirect()->route('mitra.kursus.show', $kursusId)
                ->with('error', 'Soal ' . ($testType === 'pre_test' ? 'pretest' : 'posttest') . ' belum tersedia.');
        }

        // Check if user can access this test
        if (!$this->canAccessMaterial($user->id, $kursusId, $material)) {
            return redirect()->route('mitra.kursus.show', $kursusId)
                ->with('error', 'Silakan selesaikan materi sebelumnya terlebih dahulu.');
        }

        // ⚠️ PERBAIKAN: Cek apakah user sudah mengerjakan test ini
        $progress = MaterialProgress::where('user_id', $user->id)
            ->where('material_id', $materialId)
            ->first();

        if ($testType === 'pre_test' && $progress && $progress->pretest_score !== null) {
            return redirect()->route('mitra.kursus.show', $kursusId)
                ->with('info', 'Anda sudah mengerjakan pretest ini.');
        }

        if ($testType === 'post_test' && $progress && $progress->posttest_score !== null) {
            return redirect()->route('mitra.kursus.show', $kursusId)
                ->with('info', 'Anda sudah mengerjakan posttest ini.');
        }

        return view('mitra.test', compact('kursus', 'material', 'testType', 'soalTest', 'durasi'));
    }

    public function submitTest(Request $request, $kursusId, $materialId, $testType)
    {
        Log::info('=== TEST SUBMISSION START ===');
        Log::info('Request Data:', $request->all());

        try {
            // HANYA ambil materi yang AKTIF
            $material = Materials::where('is_active', true)
                                ->findOrFail($materialId);
            $user = Auth::user();

            if ($material->type !== $testType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis test tidak sesuai'
                ], 404);
            }

            // ⚠️ PERBAIKAN: Validasi lebih fleksibel untuk menerima answers kosong
            $validated = $request->validate([
                'answers' => 'sometimes|array', // Gunakan sometimes agar boleh tidak ada
                'answers.*' => 'sometimes|nullable|integer'
            ]);

            // Default answers ke array kosong jika tidak ada
            $userAnswers = $validated['answers'] ?? [];
            
            $score = 0;

            // ⚠️ PERBAIKAN: Tentukan soal yang akan digunakan berdasarkan tipe test
            if ($testType === 'pre_test') {
                $soalTest = $material->soal_pretest;
                $totalQuestions = count($material->soal_pretest ?? []);
            } else {
                $soalTest = $material->soal_posttest;
                $totalQuestions = count($material->soal_posttest ?? []);
            }

            Log::info('Processing answers:', [
                'test_type' => $testType,
                'total_questions' => $totalQuestions,
                'user_answers_count' => count($userAnswers),
                'user_answers' => $userAnswers
            ]);

            // Validasi: pastikan ada soal yang tersedia
            if ($totalQuestions === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada soal yang tersedia untuk test ini'
                ], 400);
            }

            // ⚠️ PERBAIKAN: Hitung score berdasarkan tipe test
            // Jika user tidak menjawab sama sekali, $userAnswers akan kosong
            foreach ($soalTest as $index => $soal) {
                // Cek apakah user menjawab soal ini
                if (isset($userAnswers[$index]) && 
                    $userAnswers[$index] !== null && 
                    $userAnswers[$index] !== '' && 
                    $userAnswers[$index] == ($soal['jawaban_benar'] ?? null)) {
                    $score++;
                }
            }

            $finalScore = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;
            $isPassed = $finalScore >= $material->passing_grade;

            // Log hasil test
            Log::info('Test Results:', [
                'score' => $score,
                'total_questions' => $totalQuestions,
                'final_score' => $finalScore,
                'is_passed' => $isPassed,
                'passing_grade' => $material->passing_grade
            ]);

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
                        'material_id' => $materialId
                    ],
                    $progressData
                );

                // Update enrollment progress
                $this->updateEnrollmentProgress($user->id, $kursusId);
                
                DB::commit();

                Log::info('Test saved successfully:', [
                    'test_type' => $testType,
                    'score' => $finalScore,
                    'is_passed' => $isPassed,
                    'correct' => $score,
                    'total' => $totalQuestions,
                    'progress_id' => $progress->id
                ]);

                return response()->json([
                    'success' => true,
                    'score' => round($finalScore, 2),
                    'is_passed' => $isPassed,
                    'passing_grade' => $material->passing_grade,
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
            
            // ⚠️ PERBAIKAN: Handle array to string conversion
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
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
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
                // Hitung progress berdasarkan materi yang sudah diselesaikan (HANYA YANG AKTIF)
                $totalMaterials = Materials::where('course_id', $kursusId)
                                            ->where('is_active', true)
                                            ->count();
                
                // Hitung materi yang sudah diselesaikan (dengan test atau semua status completed)
                $completedMaterials = MaterialProgress::where('user_id', $userId)
                    ->whereHas('material', function($query) use ($kursusId) {
                        $query->where('course_id', $kursusId)
                              ->where('is_active', true); // HANYA MATERI AKTIF
                    })
                    ->where(function($query) {
                        $query->where('pretest_score', '>=', DB::raw('passing_grade'))
                              ->orWhere('posttest_score', '>=', DB::raw('passing_grade'))
                              ->orWhere(function($q) {
                                  $q->where('attendance_status', 'completed')
                                    ->where('material_status', 'completed')
                                    ->where('video_status', 'completed');
                              });
                    })
                    ->count();

                $progressPercentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;
                
                // Update status enrollment
                $status = ($progressPercentage >= 100) ? 'completed' : 'in_progress';
                
                $enrollment->update([
                    'progress_percentage' => $progressPercentage,
                    'status' => $status,
                    'completed_at' => $status === 'completed' ? now() : null
                ]);

                Log::info('Enrollment progress updated:', [
                    'enrollment_id' => $enrollment->id,
                    'progress_percentage' => $progressPercentage,
                    'status' => $status,
                    'completed_materials' => $completedMaterials,
                    'total_materials' => $totalMaterials
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

    public function showRecap($kursusId, $materialId)
    {
        $kursus = Kursus::findOrFail($kursusId);
        // HANYA ambil materi yang AKTIF
        $material = Materials::where('is_active', true)
                            ->findOrFail($materialId);
        
        $user = Auth::user();

        if ($material->type !== 'recap') {
            abort(404);
        }

        // Get all progress for this course (HANYA untuk materi yang AKTIF)
        $progress = MaterialProgress::where('user_id', $user->id)
            ->whereIn('material_id', $kursus->materials()
                                            ->where('is_active', true)
                                            ->pluck('id'))
            ->with('material')
            ->get();

        return view('mitra.recap', compact('kursus', 'material', 'progress'));
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
            ->where('is_active', true) // HANYA MATERI AKTIF
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