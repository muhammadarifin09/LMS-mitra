<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\M_User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = M_User::all();
        return view('admin.users.index', ['users' => $users]);
    }

    public function create()
    {
        return view('admin.users.create');
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
        $user = M_User::findOrFail($id);
        
        if ($user->biodata) {
            return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus user karena memiliki data biodata terkait!');
        }
        
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }

    public function show($id)
    {
        $user = M_User::with('biodata')->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }
}