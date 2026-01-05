@extends('layouts.admin')

@section('title', 'MOOC BPS - Admin Dashboard')

@section('styles')
<style>
    .admin-badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 15px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.5rem;
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #5a6c7d;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .stat-trend {
        font-size: 0.8rem;
        margin-top: 5px;
        min-height: 16px;
    }

    .section-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 25px;
    }

    .activity-list {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }

    .activity-item {
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        color: #1e3c72;
        margin-bottom: 5px;
    }

    .activity-desc {
        color: #5a6c7d;
        font-size: 0.9rem;
    }

    .activity-time {
        color: #8a9aac;
        font-size: 0.8rem;
        white-space: nowrap;
    }

    .top-courses {
        margin-bottom: 40px;
    }

    .course-list {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }

    .course-item {
        padding: 15px 25px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .course-item:last-child {
        border-bottom: none;
    }

    .course-name {
        font-weight: 600;
        color: #1e3c72;
        flex: 1;
    }

    .course-enrollments {
        background: #f8f9fa;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .course-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-left: 15px;
    }

    .status-aktif {
        background: #d4edda;
        color: #155724;
    }

    .status-draft {
        background: #fff3cd;
        color: #856404;
    }

    .status-nonaktif {
        background: #f8d7da;
        color: #721c24;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .welcome-title {
            font-size: 2rem;
        }
        
        .activity-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .activity-time {
            align-self: flex-end;
        }
    }
</style>
@endsection

@section('content')
<!-- Welcome Section dengan Nama Admin -->
<div class="welcome-section">
    <div class="admin-badge">
        <i class="fas fa-shield-alt me-2"></i>{{ ucfirst(auth()->user()->role) }}
    </div>
    <h1 class="welcome-title">Selamat Datang, {{ auth()->user()->biodata->nama_lengkap ?? auth()->user()->nama }}!</h1>
    <p class="welcome-subtitle">
        Kelola sistem MOOC BPS dengan mudah dan efisien. Pantau aktivitas, kelola pengguna, dan optimalkan pengalaman belajar.
    </p>
</div>

<!-- Stats Grid - Admin -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #3498db; color: white;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-number" style="color: #3498db;">{{ $totalUsers }}</div>
        <div class="stat-label">Total Mitra</div>
        <div class="stat-trend">
            Data real-time dari database
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #2ecc71; color: white;">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-number" style="color: #2ecc71;">{{ $activeCourses }}</div>
        <div class="stat-label">Kursus Aktif</div>
        <div class="stat-trend">
            Status: aktif
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74c3c; color: white;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-number" style="color: #e74c3c;">{{ $completionRate }}%</div>
        <div class="stat-label">Tingkat Penyelesaian</div>
        <div class="stat-trend">
            {{ $completedEnrollments ?? 0 }} dari {{ $totalEnrollments ?? 0 }} enrollment
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #f39c12; color: white;">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="stat-number" style="color: #f39c12;">{{ $newRegistrations }}</div>
        <div class="stat-label">Pendaftar Baru (Bulan Ini)</div>
        <div class="stat-trend">
            Bulan {{ Carbon\Carbon::now()->translatedFormat('F') }}
        </div>
    </div>
</div>

<!-- Top Kursus -->
@if(!empty($courseEnrollments))
<div class="top-courses">
    <h3 class="section-title">Kursus Terpopuler</h3>
    <div class="course-list">
        @foreach($courseEnrollments as $course)
        <div class="course-item">
            <div class="course-name">{{ $course['name'] }}</div>
            <div class="course-enrollments">
                {{ $course['enrollments'] }} peserta
            </div>
            <div class="course-status status-{{ $course['status'] }}">
                {{ ucfirst($course['status']) }}
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recent Activity -->
<div class="recent-activity">
    <h3 class="section-title">Aktivitas Terbaru</h3>
    <div class="activity-list">
        @forelse($recentActivities as $activity)
        <div class="activity-item">
            <div class="activity-icon" style="background: {{ $activity['icon_color'] }};">
                <i class="{{ $activity['icon'] }}"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">{{ $activity['title'] }}</div>
                <div class="activity-desc">{{ $activity['description'] }}</div>
            </div>
            <div class="activity-time">{{ $activity['time'] }}</div>
        </div>
        @empty
        <div class="activity-item">
            <div class="activity-content">
                <div class="activity-title">Tidak ada aktivitas terbaru</div>
                <div class="activity-desc">Belum ada aktivitas dalam 7 hari terakhir</div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Copyright -->
<div class="text-center mt-5 pt-4 border-top">
    <p style="color: #5a6c7d; font-size: 0.9rem;">
        Copyright © 2025 | MOOC BPS - Admin Dashboard
    </p>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard admin loaded dengan data real');
        
        // Auto-refresh setiap 60 detik untuk update data
        setInterval(() => {
            fetch('/admin/dashboard/refresh')
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error refreshing dashboard:', error));
        }, 60000); // 60 detik
    });
</script>
@endsection