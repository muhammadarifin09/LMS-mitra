<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Biodata;
use App\Models\Kursus;
use App\Models\Materials;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data yang SAMA PERSIS dengan BerandaController
        $pesertaAktif = Biodata::count();
        $kursusTersedia = Kursus::where('status', 'aktif')->count();
        $materiOnline = Materials::where('type', 'material')->count();
        
        return view('mitra.dashboard', compact('pesertaAktif', 'kursusTersedia', 'materiOnline'));
    }
}