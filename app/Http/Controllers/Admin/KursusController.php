<?php
// app/Http/Controllers/Admin/KursusController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KursusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kursus = Kursus::latest()->get();
        
        return view('admin.kursus.index', compact('kursus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tingkatKesulitan = [
            'pemula' => 'Pemula',
            'menengah' => 'Menengah', 
            'lanjutan' => 'Lanjutan'
        ];

        return view('admin.kursus.create', compact('tingkatKesulitan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_kursus' => 'required|string|max:255',
            'deskripsi_kursus' => 'required|string',
            'pelaksana' => 'required|string|max:255',
            'kategori' => 'required|in:pemula,menengah,lanjutan',
            'gambar_kursus' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'durasi_jam' => 'required|integer|min:0',
            'status' => 'required|in:draft,aktif,nonaktif',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'output_pelatihan' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'fasilitas' => 'nullable|string',
            'kuota_peserta' => 'nullable|integer|min:1'
        ]);

        // Upload gambar jika ada
        if ($request->hasFile('gambar_kursus')) {
            $gambarPath = $request->file('gambar_kursus')->store('kursus', 'public');
            $validated['gambar_kursus'] = $gambarPath;
        }

        // Set default nilai
        $validated['peserta_terdaftar'] = 0;

        Kursus::create($validated);

        return redirect()->route('admin.kursus.index')
                        ->with('success', 'Kursus berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kursus $kursus)
    {
        return view('admin.kursus.show', compact('kursus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kursus $kursus)
    {
        $tingkatKesulitan = [
            'pemula' => 'Pemula',
            'menengah' => 'Menengah',
            'lanjutan' => 'Lanjutan'
        ];

        return view('admin.kursus.edit', compact('kursus', 'tingkatKesulitan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kursus $kursus)
    {
        $validated = $request->validate([
            'judul_kursus' => 'required|string|max:255',
            'deskripsi_kursus' => 'required|string',
            'pelaksana' => 'required|string|max:255',
            'kategori' => 'required|in:pemula,menengah,lanjutan',
            'gambar_kursus' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'durasi_jam' => 'required|integer|min:0',
            'status' => 'required|in:draft,aktif,nonaktif',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'output_pelatihan' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'fasilitas' => 'nullable|string',
            'kuota_peserta' => 'nullable|integer|min:1'
        ]);

        // Upload gambar baru jika ada
        if ($request->hasFile('gambar_kursus')) {
            // Hapus gambar lama jika ada
            if ($kursus->gambar_kursus) {
                Storage::disk('public')->delete($kursus->gambar_kursus);
            }
            
            $gambarPath = $request->file('gambar_kursus')->store('kursus', 'public');
            $validated['gambar_kursus'] = $gambarPath;
        }

        $kursus->update($validated);

        return redirect()->route('admin.kursus.index')
                        ->with('success', 'Kursus berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kursus $kursus)
    {
        // Hapus gambar jika ada
        if ($kursus->gambar_kursus) {
            Storage::disk('public')->delete($kursus->gambar_kursus);
        }

        $kursus->delete();

        return redirect()->route('admin.kursus.index')
                        ->with('success', 'Kursus berhasil dihapus!');
    }

    /**
     * Update status kursus
     */
    public function updateStatus(Request $request, Kursus $kursus)
    {
        $request->validate([
            'status' => 'required|in:draft,aktif,nonaktif'
        ]);

        $kursus->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status kursus berhasil diperbarui!'
        ]);
    }
    public function materials(Kursus $kursus)
{
    return redirect()->route('admin.kursus.materials.index', $kursus);
}
}