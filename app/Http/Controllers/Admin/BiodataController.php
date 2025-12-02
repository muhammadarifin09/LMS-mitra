<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Biodata;
use App\Models\M_User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class BiodataController extends Controller
{
    public function index()
    {
        $biodata = Biodata::with('user')->get();
        return view('admin.biodata.index', ['biodata' => $biodata]);
    }

    public function create()
    {
        return view('admin.biodata.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'id_sobat' => 'required|unique:biodata',
        'nama_lengkap' => 'required',
        'kecamatan' => 'required',
        'desa' => 'required',
        'alamat' => 'required',
        'no_telepon' => 'required',
        'username_sobat' => 'required|email|unique:users,username',
        'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    try {
        // 1. BUAT USER BARU
        $user = M_User::create([
            'username' => $request->username_sobat,
            'password' => Hash::make($request->id_sobat),
            'nama' => $request->nama_lengkap,
            'role' => 'mitra',
        ]);

        // 2. BUAT BIODATA
        $biodataData = [
            'id_sobat' => $request->id_sobat,
            'user_id' => $user->id, // Pastikan user_id tidak null
            'nama_lengkap' => $request->nama_lengkap,
            'kecamatan' => $request->kecamatan,
            'desa' => $request->desa,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'username_sobat' => $request->username_sobat,
        ];

        // Handle upload foto profil - KONSISTEN DENGAN UPDATE
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('foto_profil', 'public');
            $biodataData['foto_profil'] = $path; // Simpan path relatif
        }

        Biodata::create($biodataData);

        return redirect()->route('admin.biodata.index')->with('success', 'Biodata dan akun mitra berhasil ditambahkan!');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menambahkan biodata: ' . $e->getMessage());
    }
}
   
    public function edit($id_sobat)
    {
        // dd($id_sobat);
        $biodata = Biodata::where('id_sobat', (string) $id_sobat)->firstOrFail();
        
        return view('admin.biodata.edit', compact('biodata'));
    }


    public function update(Request $request, $id_sobat)
    {
        // Gunakan findOrFail karena id_sobat adalah primary key
        $biodata = Biodata::where('id_sobat', (string) $id_sobat)->firstOrFail();

        // Validasi data
        $request->validate([
            'nama_lengkap' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'alamat' => 'required',
            'no_telepon' => 'required',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update data
        $updateData = [
            'nama_lengkap' => $request->nama_lengkap,
            'kecamatan' => $request->kecamatan,
            'desa' => $request->desa,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
        ];

        // Handle upload foto profil jika ada - KONSISTEN DENGAN STORE()
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($biodata->foto_profil) {
                Storage::disk('public')->delete($biodata->foto_profil);
            }
            
            // Simpan foto baru dengan cara yang konsisten
            $path = $request->file('foto_profil')->store('foto_profil', 'public');
            $updateData['foto_profil'] = $path;
        }

        $biodata->update($updateData);

        return redirect()->route('admin.biodata.index')->with('success', 'Biodata berhasil diperbarui!');
    }

    public function destroy($id_sobat)
    {
        try {
            // Cari biodata berdasarkan id_sobat
            $biodata = Biodata::findOrFail($id_sobat);
            
            // Simpan user_id sebelum menghapus biodata
            $user_id = $biodata->user_id;
            
            // Hapus biodata
            $biodata->delete();
            
            // Hapus user terkait jika ada
            if ($user_id) {
                M_User::where('id', $user_id)->delete();
            }
            
            return redirect()->route('admin.biodata.index')->with('success', 'Biodata dan akun mitra berhasil dihapus!');
            
        } catch (\Exception $e) {
            return redirect()->route('admin.biodata.index')->with('error', 'Gagal menghapus biodata: ' . $e->getMessage());
        }
    }
}