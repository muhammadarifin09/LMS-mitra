<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\M_User;
use App\Models\Biodata;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   // app/Http\Controllers/Admin\UserController.php
public function index()
{
    // PASTIKAN MENGGUNAKAN paginate() BUKAN get() atau all()
    $users = \App\Models\M_User::paginate(10);
    return view('admin.users.index', compact('users'));
}

    public function create()
    {
        // Ambil biodata yang belum memiliki user
        $availableBiodata = Biodata::whereNull('user_id')->get();
        return view('admin.users.create', compact('availableBiodata'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|email|unique:users,username',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,mitra,instruktur,moderator',
        ]);

        $user = M_User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama' => $request->nama,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = M_User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = M_User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|email|unique:users,username,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|in:admin,mitra,instruktur,moderator',
        ]);

        $updateData = [
            'nama' => $request->nama,
            'username' => $request->username,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate!');
    }
    public function destroy($id)
{
    $currentUser = auth()->user();
    $userToDelete = M_User::findOrFail($id);

    // Cek jika user yang akan dihapus adalah user yang sedang login
    if ($currentUser->id == $userToDelete->id) {
        return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus akun sendiri!');
    }

    try {
        // Putus relasi dengan biodata (set user_id menjadi null)
        if ($userToDelete->biodata) {
            $userToDelete->biodata->update(['user_id' => null]);
        }

        $userToDelete->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus! Data biodata tetap tersimpan.');
        
    } catch (\Exception $e) {
        return redirect()->route('admin.users.index')->with('error', 'Gagal menghapus user: ' . $e->getMessage());
    }
}

    public function show($id)
    {
        $user = M_User::with('biodata')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    // Method khusus untuk membuat user dari biodata yang sudah ada
    public function createFromBiodata($biodataId)
    {
        $biodata = Biodata::where('id_sobat', $biodataId)->firstOrFail();
        
        // Cek apakah biodata sudah memiliki user
        if ($biodata->user_id) {
            return redirect()->route('users.index')->with('error', 'Biodata sudah memiliki user!');
        }

        return view('admin.users.create-from-biodata', compact('biodata'));
    }
}