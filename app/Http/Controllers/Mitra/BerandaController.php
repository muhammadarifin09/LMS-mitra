<?php

namespace App\Http\Controllers\Mitra;

use Illuminate\Http\Request;
use App\Models\Biodata;
use App\Models\Kursus;
use App\Models\Materials;
use App\Http\Controllers\Controller;

class BerandaController extends Controller
{
    public function index()
    {
        // Hitung jumlah peserta aktif (dari tabel biodata)
        $pesertaAktif = Biodata::count();
        
        // Hitung jumlah kursus tersedia (status aktif dan belum penuh)
        $kursusTersedia = Kursus::where('status', 'aktif')->count();
        
        // Hitung jumlah materi online (type material)
        $materiOnline = Materials::where('type', 'material')->count();
        
        return view('mitra.beranda', compact('pesertaAktif', 'kursusTersedia', 'materiOnline'));
    }
}