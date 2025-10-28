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
        
        // Return yang sangat eksplisit
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
            'username_sobat' => 'required|email|unique:users,username',
            'nama_lengkap' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required',
            'no_telepon' => 'required',
            'pekerjaan' => 'required',
            'instansi' => 'required',
            'pendidikan_terakhir' => 'required|in:SD,SMP,SMA,D3,S1,S2,S3',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 1. BUAT USER BARU
        $user = M_User::create([
            'username' => $request->username_sobat,
            'password' => Hash::make($request->id_sobat),
            'role' => 'mitra',
        ]);

        // 2. BUAT BIODATA
        $biodataData = [
            'id_sobat' => $request->id_sobat,
            'user_id' => $user->id,
            'nama_lengkap' => $request->nama_lengkap,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'username_sobat' => $request->username_sobat,
            'pekerjaan' => $request->pekerjaan,
            'instansi' => $request->instansi,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
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
    }
}