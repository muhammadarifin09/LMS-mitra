<?php
// app/Http/Controllers/Mitra/KursusController.php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use Illuminate\Http\Request;

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
     * Enroll to course
     */
    public function enroll(Request $request, $id)
    {
        $kursus = Kursus::where('status', 'aktif')
                        ->where('id', $id)
                        ->firstOrFail();

        // Cek kuota jika ada
        if ($kursus->kuota_peserta && 
            $kursus->peserta_terdaftar >= $kursus->kuota_peserta) {
            return redirect()->back()
                           ->with('error', 'Maaf, kuota kursus sudah penuh.');
        }

        // Logic enroll disini
        // Contoh: tambah ke tabel enrollments
        // Enrollment::create([...]);

        // Update jumlah peserta
        $kursus->increment('peserta_terdaftar');

        return redirect()->route('mitra.kursus.show', $id)
                        ->with('success', 'Berhasil mengikuti kursus!');
    }

    /**
     * My courses
     */
    public function myCourses()
    {
        // Ambil kursus yang diikuti user
        // $enrolledCourses = Auth::user()->enrollments()->with('kursus')->get();
        
        return view('mitra.kursus-saya', [
            // 'enrolledCourses' => $enrolledCourses
        ]);
    }
}