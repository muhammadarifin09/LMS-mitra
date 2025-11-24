<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications ?? [];
        
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        // Logic untuk menandai notifikasi sebagai sudah dibaca
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        // Logic untuk menandai semua notifikasi sebagai sudah dibaca
        return response()->json(['success' => true]);
    }
}