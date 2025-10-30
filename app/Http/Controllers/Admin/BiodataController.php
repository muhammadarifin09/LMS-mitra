<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Biodata;
use App\Models\M_User;
use Illuminate\Support\Facades\Hash;

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

        // Handle upload foto profil
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/foto_profil', $filename);
            $biodataData['foto_profil'] = 'foto_profil/' . $filename;
        }

        Biodata::create($biodataData);

        return redirect()->route('biodata.index')->with('success', 'Biodata dan akun mitra berhasil ditambahkan!');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menambahkan biodata: ' . $e->getMessage());
    }
}
    public function edit($id)
    {
        $biodata = Biodata::with('user')->where('id_sobat', $id)->firstOrFail();
        return view('admin.biodata.edit', compact('biodata'));
    }

    public function update(Request $request, $id)
    {
        $biodata = Biodata::where('id_sobat', $id)->firstOrFail();

        $request->validate([
            'nama_lengkap' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'alamat' => 'required',
            'no_telepon' => 'required',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'nama_lengkap' => $request->nama_lengkap,
            'kecamatan' => $request->kecamatan,
            'desa' => $request->desa,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
        ];

        // Handle upload foto profil
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/foto_profil', $filename);
            $updateData['foto_profil'] = 'foto_profil/' . $filename;
        }

        $biodata->update($updateData);

        // Update user jika ada relasi
        if ($biodata->user) {
            $biodata->user->update([
                'nama' => $request->nama_lengkap
            ]);
        }

        return redirect()->route('biodata.index')->with('success', 'Biodata berhasil diupdate!');
    }

    public function destroy($id)
    {
        $biodata = Biodata::where('id_sobat', $id)->firstOrFail();
        
        // Cek jika biodata memiliki user terkait
        if ($biodata->user) {
            return redirect()->route('biodata.index')->with('error', 'Tidak dapat menghapus biodata karena memiliki user terkait! Hapus user terlebih dahulu.');
        }
        
        $biodata->delete();

        return redirect()->route('biodata.index')->with('success', 'Biodata berhasil dihapus!');
    }
}