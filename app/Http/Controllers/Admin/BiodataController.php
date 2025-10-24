<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\M_Biodata;

class BiodataController extends Controller
{
    public function index()
    {
        $biodata = M_Biodata::all();
        return view('biodata.index', compact('biodata'));
    }

    public function create()
    {
        return view('admin.biodata.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_sobat' => 'required|unique:biodata',
            'nama' => 'required',
            'username_sobat' => 'required|email|unique:biodata',
            'no_hp' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'alamat' => 'required',
        ]);

        M_Biodata::create($request->all());

        return redirect()->route('biodata.index')->with('success', 'Biodata dan akun mitra berhasil ditambahkan!');
    }
}
