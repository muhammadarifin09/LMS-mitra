<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOCC BPS - Kursus Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
        }
        
        /* Navigation - Sticky dengan teks besar */

        /* Tombol Ikuti Kursus - Warna biru */
.btn-follow-course {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    white-space: nowrap;
}

/* Tombol Lihat Kursus - Warna putih dengan border biru */
.btn-view-course-white {
    background: white;
    color: #1e3c72;
    border: 2px solid #1e3c72;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    white-space: nowrap;
}

.btn-view-course-white:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(30, 60, 114, 0.2);
    background: #f8f9fa;
    color: #1e3c72;
    border-color: #1e3c72;
}

.btn-follow-course:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(30, 60, 114, 0.3);
    color: white;
}

/* Tombol Lihat Kursus - Warna putih */
.btn-view-course {
    background: white;
    color: #1e3c72;
    border: 1px solid #1e3c72;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    white-space: nowrap;
}

.btn-view-course:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(30, 60, 114, 0.2);
    background: #f8f9fa;
    color: #1e3c72;
}
        .main-nav {
            background: rgba(255, 255, 255, 0.98);
            padding: 15px 60px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid #1e3c72;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }
        
        .nav-brand {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e3c72;
            text-decoration: none;
            position: relative;
            padding-right: 25px;
        }

        .nav-brand::after {
            content: "";
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 60px;
            width: 1.5px;
            background: linear-gradient(to bottom, rgba(42, 82, 152, 0.7));
            border-radius: 2px;
        }
        
        .nav-brand span {
            color: #2a5298;
        }
        
        /* Logo MOCC BPS sebagai gambar */
        .logo-image {
            height: 50px;
            width: auto;
            transition: transform 0.3s ease;
        }
        
        .logo-image:hover {
            transform: scale(1.05);
        }
        
        .nav-menu {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Style untuk ikon navigasi */
        .nav-icon {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e3c72;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(30, 60, 114, 0.1);
        }

        .nav-icon:hover {
            background: rgba(30, 60, 114, 0.2);
            color: #2a5298;
            transform: scale(1.1);
        }

        /* Badge notifikasi */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        /* Perbesar ukuran teks navigasi */
        .nav-item {
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            color: #1e3c72;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .nav-item:hover, .nav-item.active {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            transform: translateY(-2px);
        }

        /* User Profile & Avatar Styles */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            border-radius: 20px;
            background: rgba(30, 60, 114, 0.05);
            transition: all 0.3s ease;
            margin-left: 15px;
            text-decoration: none;
            border: 1px solid rgba(30, 60, 114, 0.1);
        }

        .user-profile:hover {
            background: rgba(30, 60, 114, 0.1);
            text-decoration: none;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid white;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .avatar-initials {
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .user-name {
            font-weight: 600;
            color: #1e3c72;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
            line-height: 1.2;
        }

        .user-status {
            font-size: 0.7rem;
            color: #5a6c7d;
            display: flex;
            align-items: center;
            gap: 4px;
            line-height: 1.2;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background: #28a745;
            border-radius: 50%;
            display: inline-block;
        }

        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 100px);
        }

        /* Sidebar Styles */
        .sidebar {
            width: 300px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-section {
            margin-bottom: 30px;
        }

        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e3c72;
            padding: 0 25px 15px 25px;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 15px;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #5a6c7d;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-item:hover {
            background: rgba(30, 60, 114, 0.1);
            color: #1e3c72;
            border-left-color: #1e3c72;
        }

        .sidebar-item.active {
            background: rgba(30, 60, 114, 0.15);
            color: #1e3c72;
            border-left-color: #1e3c72;
            font-weight: 600;
        }

        .sidebar-item i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            margin: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* Kursus Header */
        .kursus-header {
            margin-bottom: 30px;
        }

        .kursus-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 10px;
        }

        .kursus-subtitle {
            font-size: 1.1rem;
            color: #5a6c7d;
        }

        /* Course Grid Layout - 4 cards per row */
        .course-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Modern Course Card Design - Updated for database content */
        .modern-course-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .modern-course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .course-image-wrapper {
            position: relative;
            width: 100%;
            height: 160px;
            overflow: hidden;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .course-main-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .course-image-wrapper:hover .course-main-image {
            transform: scale(1.05);
        }

        .course-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(30, 60, 114, 0.9);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .course-content-wrapper {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-date {
            font-size: 0.75rem;
            color: #5a6c7d;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .course-main-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 8px;
            line-height: 1.3;
            height: 2.6em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .course-category {
            font-size: 0.8rem;
            color: #5a6c7d;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .course-description {
            color: #5a6c7d;
            line-height: 1.5;
            font-size: 0.8rem;
            margin-bottom: 15px;
            flex: 1;
            height: 3.6em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .course-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }

        .meta-card {
            background: #f8f9fa;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
        }

        .meta-value {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 2px;
        }

        .meta-label {
            font-size: 0.7rem;
            color: #5a6c7d;
            font-weight: 500;
        }

        .course-action-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }

        .lessons-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: #5a6c7d;
        }

        .btn-view-course {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            white-space: nowrap;
        }

        .btn-view-course:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(30, 60, 114, 0.3);
            color: white;
        }

        /* Level Badges */
        .level-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .level-pemula {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .level-menengah {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }

        .level-lanjutan {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        /* Status Badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-aktif {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
        }

        .status-draft {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 1400px) {
            .course-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1200px) {
            .course-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .main-nav {
                padding: 12px 20px;
            }
            
            .user-profile {
                margin-left: 10px;
                padding: 5px 10px;
            }
            
            .user-avatar {
                width: 35px;
                height: 35px;
            }
            
            .avatar-initials {
                font-size: 0.75rem;
            }
            
            .user-name {
                font-size: 0.8rem;
                max-width: 100px;
            }
            
            .user-status {
                font-size: 0.65rem;
            }

            .nav-item {
                padding: 8px 15px;
                font-size: 0.9rem;
            }

            .nav-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }

            .nav-menu {
                gap: 5px;
            }
            
            .logo-image {
                height: 60px;
            }
            
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                order: 2;
            }
            
            .main-content {
                order: 1;
                margin: 10px;
                padding: 20px;
            }
            
            .kursus-title {
                font-size: 1.8rem;
            }
            
            .course-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .user-info {
                display: none;
            }
            
            .user-profile {
                padding: 6px;
                background: transparent;
                border: none;
            }
            
            .user-profile:hover {
                background: rgba(30, 60, 114, 0.1);
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .course-content-wrapper {
                padding: 15px;
            }
            
            .course-main-title {
                font-size: 1rem;
            }
            
            .course-meta-grid {
                grid-template-columns: 1fr;
            }
            
            .course-image-wrapper {
                height: 140px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation - Sticky -->
    <nav class="main-nav">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <!-- Logo MOCC BPS sebagai gambar -->
                <a href="#" class="nav-brand">
                    <img src="{{ asset('img/Logo_E-Learning.png') }}" alt="MOCC BPS Logo" class="logo-image">
                </a>
                <div class="nav-menu ms-5">
                    <a href="/beranda" class="nav-item">Beranda</a>
                    <a href="/dashboard" class="nav-item">Dashboard</a>
                    <a href="/kursus" class="nav-item active">Kursus</a>
                    <a href="#" class="nav-item">Kursus Saya</a>
                </div>
            </div>
            
            <!-- Tambahkan bagian ikon di sini -->
            <div class="d-flex align-items-center">
                <!-- Ikon Bahasa -->
                <div class="nav-icon me-3">
                    <i class="fas fa-globe"></i>
                </div>
                
                <!-- Ikon Notifikasi -->
                <div class="nav-icon me-3">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <!-- User Profile dengan Foto -->
                <a href="{{ route('profil.index') }}" class="user-profile">
                    <div class="user-avatar">
                        @auth
                            @php
                                $user = Auth::user();
                                $biodata = $user->biodata ?? null;
                                $initials = strtoupper(substr($user->name, 0, 2));
                            @endphp
                            
                            @if($biodata && $biodata->foto_profil)
                                <img src="{{ asset('storage/' . $biodata->foto_profil) }}" 
                                     alt="Foto Profil" 
                                     class="avatar-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="avatar-initials" style="display: none;">{{ $initials }}</div>
                            @else
                                <div class="avatar-initials">{{ $initials }}</div>
                            @endif
                        @endauth
                    </div>
                    <div class="user-info">
                        <div class="user-name">
                            {{ Auth::user()->biodata->nama_lengkap ?? Auth::user()->name }}
                        </div>
                        <div class="user-status">
                            <span class="status-dot"></span>
                            Online
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Data Section -->
            <div class="sidebar-section">
                <div class="sidebar-title">Dashboard</div>
                <a href="{{ route('profil.index') }}" class="sidebar-item {{ request()->routeIs('profil.*') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span>Profil</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Nilai</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-flag"></i>
                    <span>Laporan</span>
                </a>
                <a href="#" class="sidebar-item text-danger" onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin keluar?')) document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        <!-- Main Content - Kursus -->
        <div class="main-content">
            <!-- Kursus Header -->
            <div class="kursus-header">
                <h1 class="kursus-title">Kursus Tersedia</h1>
                <p class="kursus-subtitle">Pilih dan ikuti kursus yang sesuai dengan minat Anda</p>
            </div>

            <!-- Course Grid from Database -->
            <div class="course-grid">
                @if(isset($kursus) && $kursus->count() > 0)
                    @foreach($kursus as $item)
                        @if($item->status == 'aktif')
                        <div class="modern-course-card">
                            <!-- Course Image -->
                            <div class="course-image-wrapper">
                                @if($item->gambar_kursus)
                                    <img src="{{ asset('storage/' . $item->gambar_kursus) }}" 
                                         alt="{{ $item->judul_kursus }}" 
                                         class="course-main-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="course-main-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: none; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                @else
                                    <div class="course-main-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                @endif
                                
                                <!-- Level Badge -->
                                <div class="course-badge level-badge 
                                    @if($item->tingkat_kesulitan == 'pemula') level-pemula
                                    @elseif($item->tingkat_kesulitan == 'menengah') level-menengah
                                    @else level-lanjutan @endif">
                                    {{ $item->tingkat_kesulitan }}
                                </div>
                            </div>

                            <div class="course-content-wrapper">
                                <!-- Date -->
                                <div class="course-date">
                                    @if($item->tanggal_mulai)
                                        {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                    @endif
                                </div>

                                <!-- Title -->
                                <h3 class="course-main-title">{{ $item->judul_kursus }}</h3>

                                <!-- Category/Publisher -->
                                <div class="course-category">
                                    {{ $item->penerbit }}
                                </div>

                                <!-- Description -->
                                <p class="course-description">
                                    {{ Str::limit($item->deskripsi_kursus, 120) }}
                                </p>

                                <!-- Meta Information -->
                                <div class="course-meta-grid">
                                    <div class="meta-card">
                                        <div class="meta-value">{{ $item->durasi_jam }}h</div>
                                        <div class="meta-label">Durasi</div>
                                    </div>
                                    <div class="meta-card">
                                        <div class="meta-value">{{ $item->peserta_terdaftar }}</div>
                                        <div class="meta-label">Peserta</div>
                                    </div>
                                </div>

                                <!-- Action Row -->
                                <div class="course-action-row">
                                <form action="{{ route('mitra.kursus.enroll', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-follow-course">
                                        <i class="fas fa-play-circle"></i>
                                        Ikuti Kursus
                                    </button>
                                </form>
                                <form action="{{ route('mitra.kursus.enroll', $item->id) }}" method="GET" class="d-inline">
                                    <button type="submit" class="btn-view-course-white">
                                        <i class="fas fa-eye"></i>
                                        Lihat Kursus
                                    </button>
                                </form>
                            </div>

                            </div>
                        </div>
                        @endif
                    @endforeach
                @else
                    <!-- Fallback demo courses if no data from database -->
                    <div class="modern-course-card">
                        <div class="course-image-wrapper">
                            <div class="course-main-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="course-badge level-badge level-pemula">Pemula</div>
                        </div>
                        <div class="course-content-wrapper">
                            <div class="course-date">03 Nov 2025</div>
                            <h3 class="course-main-title">WEB PERFORMANCE OPTIMIZATION</h3>
                            <div class="course-category">Knowledge Center</div>
                            <p class="course-description">
                                Knowledge Sharing Web Performance Analysis dan Optimasi untuk meningkatkan kecepatan website.
                            </p>
                            <div class="course-meta-grid">
                                <div class="meta-card">
                                    <div class="meta-value">4h</div>
                                    <div class="meta-label">Durasi</div>
                                </div>
                                <div class="meta-card">
                                    <div class="meta-value">156</div>
                                    <div class="meta-label">Peserta</div>
                                </div>
                            </div>
                            <div class="course-action-row">
                                <div class="lessons-info">
                                    <i class="fas fa-play-circle"></i>
                                    4 lessons
                                </div>
                                <button class="btn-view-course">
                                    <i class="fas fa-eye"></i>
                                    View Course
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Additional demo courses -->
                    <div class="modern-course-card">
                        <div class="course-image-wrapper">
                            <div class="course-main-image" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="course-badge level-badge level-menengah">Menengah</div>
                        </div>
                        <div class="course-content-wrapper">
                            <div class="course-date">05 Nov 2025</div>
                            <h3 class="course-main-title">DATA ANALYSIS FUNDAMENTALS</h3>
                            <div class="course-category">Statistics Department</div>
                            <p class="course-description">
                                Pelatihan dasar analisis data menggunakan tools statistik modern untuk pemula.
                            </p>
                            <div class="course-meta-grid">
                                <div class="meta-card">
                                    <div class="meta-value">8h</div>
                                    <div class="meta-label">Durasi</div>
                                </div>
                                <div class="meta-card">
                                    <div class="meta-value">89</div>
                                    <div class="meta-label">Peserta</div>
                                </div>
                            </div>
                            <div class="course-action-row">
                                <div class="lessons-info">
                                    <i class="fas fa-play-circle"></i>
                                    8 lessons
                                </div>
                                <button class="btn-view-course">
                                    <i class="fas fa-eye"></i>
                                    View Course
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Copyright -->
            <div class="text-center mt-5 pt-4 border-top">
                <p style="color: #5a6c7d; font-size: 0.9rem;">
                    Copyright © 2024 | Kementerian Komunikasi dan Informatika
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar item active state
        document.querySelectorAll('.sidebar-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Nav item active state
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Handle image loading errors
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.avatar-image').forEach(img => {
                img.addEventListener('error', function() {
                    this.style.display = 'none';
                    const initials = this.nextElementSibling;
                    if (initials && initials.classList.contains('avatar-initials')) {
                        initials.style.display = 'flex';
                    }
                });
            });

            // Handle course image errors
            document.querySelectorAll('.course-main-image[src]').forEach(img => {
                img.addEventListener('error', function() {
                    this.style.display = 'none';
                    const placeholder = this.nextElementSibling;
                    if (placeholder && placeholder.classList.contains('course-main-image')) {
                        placeholder.style.display = 'flex';
                    }
                });
            });
        });

        // View course button interactions
        document.querySelectorAll('.btn-view-course').forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.closest('form')) {
                    // If it's inside a form, let the form submit normally
                    return;
                }
                
                e.preventDefault();
                const courseTitle = this.closest('.modern-course-card').querySelector('.course-main-title').textContent;
                alert(`Membuka kursus: ${courseTitle}`);
            });
        });
    </script>
</body>
</html>