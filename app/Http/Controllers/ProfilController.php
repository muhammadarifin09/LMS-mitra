<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $biodata = Biodata::where('user_id', $user->id)->first();

        return view('profil.index', compact('user', 'biodata'));
    }

    public function edit()
    {
        $user = Auth::user();
        $biodata = Biodata::where('user_id', $user->id)->first();

        return view('profil.edit', compact('user', 'biodata'));
    }

    public function update(Request $request)
{
    $user = Auth::user();
    
    $request->validate([
        'nama_lengkap' => 'required|string|max:255',
        'tempat_lahir' => 'required|string|max:255',
        'tanggal_lahir' => 'required|date',
        'jenis_kelamin' => 'required|in:L,P',
        'alamat' => 'required|string',
        'no_telepon' => 'required|string|max:15',
        'pekerjaan' => 'required|string|max:255',
        'instansi' => 'required|string|max:255',
        'pendidikan_terakhir' => 'required|string|max:255',
        'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $biodata = Biodata::where('user_id', $user->id)->first();
    
    if (!$biodata) {
        $biodata = new Biodata();
        $biodata->user_id = $user->id;
    }

    // Update data
    $biodata->nama_lengkap = $request->nama_lengkap;
    $biodata->tempat_lahir = $request->tempat_lahir;
    $biodata->tanggal_lahir = $request->tanggal_lahir;
    $biodata->jenis_kelamin = $request->jenis_kelamin;
    $biodata->alamat = $request->alamat;
    $biodata->no_telepon = $request->no_telepon;
    $biodata->pekerjaan = $request->pekerjaan;
    $biodata->instansi = $request->instansi;
    $biodata->pendidikan_terakhir = $request->pendidikan_terakhir;

    // Handle upload foto profil
    if ($request->hasFile('foto_profil')) {
        // Hapus foto lama jika ada
        if ($biodata->foto_profil) {
            $oldPhotoPath = str_replace('storage/', 'public/', $biodata->foto_profil);
            Storage::delete($oldPhotoPath);
        }
        
        // Simpan foto baru
        $path = $request->file('foto_profil')->store('foto_profil', 'public');
        $biodata->foto_profil = $path; // Simpan path relatif
    }

    $biodata->save();

    return redirect()->route('profil.index')
        ->with('success', 'Profil berhasil diperbarui!');
}

    public function hapusFoto(Request $request)
{
    $user = Auth::user();
    $biodata = Biodata::where('user_id', $user->id)->first();

    if ($biodata && $biodata->foto_profil) {
        // Hapus file dari storage
        $photoPath = str_replace('storage/', 'public/', $biodata->foto_profil);
        if (Storage::exists($photoPath)) {
            Storage::delete($photoPath);
        }
        
        // Hapus path dari database
        $biodata->foto_profil = null;
        $biodata->save();
    }

    return redirect()->route('profil.index')
        ->with('success', 'Foto profil berhasil dihapus!');
}
}