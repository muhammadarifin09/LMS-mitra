@extends('layouts.admin')

@section('title', 'MOOC BPS - Admin Dashboard')

@section('styles')
<style>
    /* Additional CSS khusus untuk dashboard */
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

    /* Stats Grid - Admin */
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
    }

    .trend-up {
        color: #28a745;
    }

    .trend-down {
        color: #e74c3c;
    }

    /* Quick Actions */
    .quick-actions {
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 25px;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .action-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
    }

    .action-card:hover .action-icon {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .action-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin: 0 auto 15px;
        transition: all 0.3s ease;
    }

    .action-card h5 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .action-card p {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    /* Recent Activity */
    .recent-activity {
        margin-bottom: 40px;
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
    }

    /* Responsif untuk komponen dashboard */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .actions-grid {
            grid-template-columns: 1fr;
        }
        
        .welcome-title {
            font-size: 2rem;
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
    <h1 class="welcome-title">Selamat Datang, {{ auth()->user()->biodata->nama_lengkap ?? auth()->user()->name }}!</h1>
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
        <div class="stat-number" style="color: #3498db;">2,847</div>
        <div class="stat-label">Total Pengguna</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-arrow-up me-1"></i>12% dari bulan lalu
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #2ecc71; color: white;">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-number" style="color: #2ecc71;">24</div>
        <div class="stat-label">Kursus Aktif</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-arrow-up me-1"></i>3 kursus baru
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74c3c; color: white;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-number" style="color: #e74c3c;">94%</div>
        <div class="stat-label">Tingkat Penyelesaian</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-arrow-up me-1"></i>5% peningkatan
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #f39c12; color: white;">
            <i class="fas fa-comments"></i>
        </div>
        <div class="stat-number" style="color: #f39c12;">156</div>
        <div class="stat-label">Diskusi Aktif</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-arrow-up me-1"></i>8 diskusi baru
        </div>
    </div>
</div>

<!-- Quick Actions -->


<!-- Recent Activity -->
<div class="recent-activity">
    <h3 class="section-title">Aktivitas Terbaru</h3>
    <div class="activity-list">
        <div class="activity-item">
            <div class="activity-icon" style="background: #3498db;">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">User Baru Terdaftar</div>
                <div class="activity-desc">Budi Santoso mendaftar sebagai mitra BPS</div>
            </div>
            <div class="activity-time">5 menit lalu</div>
        </div>
        <div class="activity-item">
            <div class="activity-icon" style="background: #2ecc71;">
                <i class="fas fa-book"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">Kursus Diselesaikan</div>
                <div class="activity-desc">Siti Rahayu menyelesaikan kursus Data Analysis</div>
            </div>
            <div class="activity-time">1 jam lalu</div>
        </div>
        <div class="activity-item">
            <div class="activity-icon" style="background: #e74c3c;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">Peringatan Sistem</div>
                <div class="activity-desc">Backup otomatis berhasil dilakukan</div>
            </div>
            <div class="activity-time">2 jam lalu</div>
        </div>
        <div class="activity-item">
            <div class="activity-icon" style="background: #f39c12;">
                <i class="fas fa-comment"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">Diskusi Baru</div>
                <div class="activity-desc">Pertanyaan baru di forum Statistik Dasar</div>
            </div>
            <div class="activity-time">3 jam lalu</div>
        </div>
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
    // Script khusus untuk dashboard
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard admin loaded');
        
        // Animasi untuk stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
        
        // Update real-time data (contoh)
        setInterval(() => {
            // Di sini bisa ditambahkan logika untuk update data real-time
            console.log('Checking for updates...');
        }, 30000); // Setiap 30 detik
    });
</script>
@endsection