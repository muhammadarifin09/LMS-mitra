<?php
// app/Http/Controllers/Mitra/KursusController.php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Enrollment;
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
                        ->firstOrFail();
        
        return view('mitra.kursus-detail', compact('kursus'));
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
}