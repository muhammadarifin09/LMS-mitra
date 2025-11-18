<?php
// app/Http/Controllers/Mitra/KursusController.php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Enrollment;
use App\Models\Materials;
use App\Models\MaterialProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KursusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil hanya kursus dengan status aktif
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
                        ->with(['materials'])
                        ->firstOrFail();

        $user = Auth::user();
        
        // Cek enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
                            ->where('kursus_id', $id)
                            ->firstOrFail();

        // Process materials dengan status
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

    private function getMaterialsWithStatus($materials, $user)
    {
        $processedMaterials = [];
        $foundCurrent = false;
        
        foreach ($materials->sortBy('order') as $material) {
            $progress = MaterialProgress::where('user_id', $user->id)
                                    ->where('material_id', $material->id)
                                    ->first();
            
            // Determine status
            if ($progress && $this->isMaterialCompleted($progress)) {
                $status = 'completed';
                $statusClass = 'completed';
            } elseif (!$foundCurrent) {
                $status = 'current';
                $statusClass = 'current';
                $foundCurrent = true;
            } else {
                $status = 'locked';
                $statusClass = 'locked';
            }
            
            $processedMaterials[] = [
                'id' => $material->id,
                'title' => $material->title,
                'type' => $material->type,
                'order' => $material->order,
                'status' => $status,
                'status_class' => $statusClass,
                'attendance_status' => $progress->attendance_status ?? 'pending',
                'material_status' => $progress->material_status ?? 'pending',
                'video_status' => $progress->video_status ?? 'pending',
            ];
        }
        
        return $processedMaterials;
    }

    private function isMaterialCompleted($progress)
    {
        return $progress->attendance_status == 'completed' &&
            $progress->material_status == 'completed' &&
            $progress->video_status == 'completed';
    }

    /**
     * Enroll to course - VERSI LEBIH AMAN
     */
    
    public function enroll(Request $request, $id)
    {
        $kursus = Kursus::where('status', 'aktif')
                        ->where('id', $id)
                        ->firstOrFail();

        $user = Auth::user();
        
        // Cara lebih aman: Cek langsung di tabel enrollments
        $alreadyEnrolled = Enrollment::where('user_id', $user->id)
                                   ->where('kursus_id', $id)
                                   ->exists();

        if ($alreadyEnrolled) {
            return redirect()->back()
                           ->with('error', 'Anda sudah mengikuti kursus ini.');
        }

        // Cek kuota jika ada
        if ($kursus->kuota_peserta && 
            $kursus->peserta_terdaftar >= $kursus->kuota_peserta) {
            return redirect()->back()
                           ->with('error', 'Maaf, kuota kursus sudah penuh.');
        }

        try {
            // Create enrollment
            Enrollment::create([
                'user_id' => $user->id,
                'kursus_id' => $id,
                'total_activities' => 3, // Default value
                'enrolled_at' => now()
            ]);

            // Update jumlah peserta
            $kursus->increment('peserta_terdaftar');

            return redirect()->route('mitra.kursus.saya')
                            ->with('success', 'Berhasil mengikuti kursus!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * My courses - VERSI LEBIH AMAN
     */
    public function myCourses()
    {
        $user = Auth::user();
        
        // Ambil enrollments dengan data kursus
        $enrolledCourses = Enrollment::with('kursus')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mitra.kursus-saya', compact('enrolledCourses'));
    }

    public function markAttendance($materialId)
    {
        $user = Auth::user();
        $material = Materials::findOrFail($materialId);
        
        // Pastikan user enrolled di kursus ini
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

    public function markMaterialDownloaded($materialId)
    {
        $user = Auth::user();
        $material = Materials::findOrFail($materialId);
        
        // Pastikan user enrolled di kursus ini
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
}