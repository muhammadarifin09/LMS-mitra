<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung notifikasi belum dibaca
        $unreadCount = 3; // Ganti dengan query database nanti
        
        return view('admin.dashboard', compact('unreadCount'));
    }
}