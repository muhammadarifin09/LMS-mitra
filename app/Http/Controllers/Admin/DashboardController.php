<?php
// app/Http\Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\M_User;
use App\Models\Kursus;
use App\Models\Enrollment;
use App\Models\Materials;
use App\Models\Biodata;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Data statistik
        $totalUsers = M_User::where('role', 'mitra')->count();
        $activeCourses = Kursus::where('status', 'aktif')->count();
        
        // Hitung tingkat penyelesaian
        $completedEnrollments = Enrollment::where('status', 'completed')->count();
        $totalEnrollments = Enrollment::count();
        $completionRate = $totalEnrollments > 0 
            ? round(($completedEnrollments / $totalEnrollments) * 100, 2)
            : 0;
        
        // Pendaftar bulan ini
        $newRegistrations = M_User::where('role', 'mitra')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Aktivitas terbaru
        $recentActivities = [];
        
        // 1. User baru mendaftar (7 hari terakhir)
        $newUsers = M_User::with('biodata')
            ->where('role', 'mitra')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
        
        foreach ($newUsers as $user) {
            $recentActivities[] = [
                'type' => 'user_registered',
                'title' => 'Mitra Baru Terdaftar',
                'description' => ($user->biodata->nama_lengkap ?? $user->nama) . ' bergabung sebagai mitra',
                'icon' => 'fas fa-user-plus',
                'icon_color' => '#3498db',
                'time' => $user->created_at->diffForHumans(),
                'timestamp' => $user->created_at
            ];
        }
        
        // 2. Kursus baru dibuat (7 hari terakhir)
        $newCourses = Kursus::where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();
        
        foreach ($newCourses as $course) {
            $recentActivities[] = [
                'type' => 'course_created',
                'title' => 'Kursus Baru Ditambahkan',
                'description' => 'Kursus "' . $course->judul_kursus . '" telah ditambahkan',
                'icon' => 'fas fa-book',
                'icon_color' => '#2ecc71',
                'time' => $course->created_at->diffForHumans(),
                'timestamp' => $course->created_at
            ];
        }
        
        // 3. Kursus yang diselesaikan (7 hari terakhir)
        $recentCompletions = Enrollment::with(['user.biodata', 'kursus'])
            ->where('status', 'completed')
            ->where('completed_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('completed_at', 'desc')
            ->limit(2)
            ->get();
        
        foreach ($recentCompletions as $enrollment) {
            $recentActivities[] = [
                'type' => 'course_completed',
                'title' => 'Kursus Diselesaikan',
                'description' => ($enrollment->user->biodata->nama_lengkap ?? $enrollment->user->nama) . 
                               ' menyelesaikan kursus "' . $enrollment->kursus->judul_kursus . '"',
                'icon' => 'fas fa-graduation-cap',
                'icon_color' => '#e74c3c',
                'time' => $enrollment->completed_at ? $enrollment->completed_at->diffForHumans() : 'Baru saja',
                'timestamp' => $enrollment->completed_at
            ];
        }
        
        // 4. Material baru ditambahkan (7 hari terakhir) - FIXED
        $newMaterials = Materials::with(['kursus' => function($query) {
            $query->select('id', 'judul_kursus'); // Hanya ambil kolom yang ada
        }])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();
        
        foreach ($newMaterials as $material) {
            $recentActivities[] = [
                'type' => 'material_added',
                'title' => 'Materi Baru Ditambahkan',
                'description' => 'Materi "' . $material->title . '" ditambahkan ke kursus "' . 
                               ($material->kursus->judul_kursus ?? 'Tidak diketahui') . '"',
                'icon' => 'fas fa-file-alt',
                'icon_color' => '#f39c12',
                'time' => $material->created_at->diffForHumans(),
                'timestamp' => $material->created_at
            ];
        }
        
        // Urutkan aktivitas berdasarkan timestamp terbaru
        usort($recentActivities, function($a, $b) {
            $timeA = isset($a['timestamp']) ? strtotime($a['timestamp']) : 0;
            $timeB = isset($b['timestamp']) ? strtotime($b['timestamp']) : 0;
            return $timeB - $timeA;
        });
        
        // Ambil hanya 4 aktivitas terbaru
        $recentActivities = array_slice($recentActivities, 0, 4);
        
        // Data untuk statistik kursus terpopuler - FIXED
        $courseEnrollments = Kursus::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($course) {
                return [
                    'name' => $course->judul_kursus,
                    'enrollments' => $course->enrollments_count,
                    'status' => $course->status,
                    'id' => $course->id
                ];
            })
            ->toArray();
        
        // Data untuk statistik bulanan - FIXED
        $monthlyRegistrations = M_User::where('role', 'mitra')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'month' => Carbon::create()->month($item->month)->format('M'),
                    'count' => $item->count
                ];
            })
            ->toArray();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeCourses',
            'completionRate',
            'newRegistrations',
            'recentActivities',
            'courseEnrollments',
            'monthlyRegistrations',
            'completedEnrollments',
            'totalEnrollments'
        ));
    }
    
    private function getMonthlyRegistrations()
    {
        // Data pendaftaran per bulan (6 bulan terakhir)
        return M_User::where('role', 'mitra')  // UBAH: M_User bukan User
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
    }
    
    private function getCourseEnrollmentStats()
    {
        // Statistik enrollment per kursus
        return Kursus::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($course) {
                return [
                    'name' => $course->judul_kursus,
                    'enrollments' => $course->enrollments_count,
                    'status' => $course->status
                ];
            })
            ->toArray();
    }
    
    // Method untuk refresh data AJAX
    public function refresh()
    {
        $data = [
            'totalUsers' => M_User::where('role', 'mitra')->count(),
            'activeCourses' => Kursus::where('status', 'aktif')->count(),
            'success' => true,
            'timestamp' => Carbon::now()->format('H:i:s')
        ];
        
        return response()->json($data);
    }
}