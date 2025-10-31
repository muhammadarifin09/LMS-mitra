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

        /* User Profile & Avatar Styles - DIPERBAIKI */
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

        /* CSS untuk Fallback Image */
        .avatar-image[src=""],
        .avatar-image:not([src]) {
            opacity: 0;
        }

        .avatar-image:not([src]) + .avatar-initials,
        .avatar-image[src=""] + .avatar-initials {
            display: flex !important;
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

        /* Messages Section */
        .messages-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 4px solid #1e3c72;
        }

        .messages-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1e3c72;
            margin-bottom: 15px;
        }

        .profile-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
        }

        .profile-text {
            font-weight: 500;
            color: #1e3c72;
        }

        /* Performance Section */
        .performance-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 4px solid #28a745;
        }

        .performance-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1e3c72;
            margin-bottom: 15px;
        }

        .performance-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .progress-container {
            margin-top: 10px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: #5a6c7d;
        }

        .progress-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }

        /* Improved Course Image Styles */
        .course-image-container {
            position: relative;
            width: 100%;
            height: 280px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .course-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .course-image:hover {
            transform: scale(1.05);
        }

        .course-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 3rem;
        }

        /* Course Card Layout Improvements */
        .course-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 15px;
        }

        .course-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .course-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #5a6c7d;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .meta-item i {
            color: #1e3c72;
            width: 16px;
        }

        /* Badge Improvements */
        .level-badge, .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
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

        .status-aktif {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
        }

        /* Course Content Layout */
        .course-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 25px;
        }

        .course-description {
            color: #5a6c7d;
            line-height: 1.7;
            font-size: 1rem;
            margin-bottom: 0;
        }

        .course-requirements {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #1e3c72;
        }

        .requirements-title {
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .requirements-list {
            list-style: none;
            padding-left: 0;
        }

        .requirements-list li {
            padding: 8px 0;
            color: #5a6c7d;
            position: relative;
            padding-left: 25px;
            line-height: 1.5;
        }

        .requirements-list li:before {
            content: "✓";
            color: #27ae60;
            font-weight: bold;
            position: absolute;
            left: 0;
            font-size: 1rem;
        }

        /* Progress and Action Section */
        .course-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #f1f3f4;
        }

        .progress-container {
            flex: 1;
            margin-right: 30px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.95rem;
            color: #5a6c7d;
            font-weight: 500;
        }

        .progress-bar {
            height: 10px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .btn-ikuti {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
        }

        .btn-ikuti:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
            color: white;
        }

        /* Footer */
        .main-footer {
            background: #1a365d;
            color: white;
            padding: 50px 0 25px;
            margin-top: auto;
            width: 100%;
            font-size: 14px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
            border-bottom: 2px solid #2d74da;
            padding-bottom: 8px;
        }

        .footer-section h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: white;
        }

        .footer-address {
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .footer-address p {
            margin-bottom: 8px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 14px;
        }

        .footer-links a:hover {
            color: white;
            text-decoration: underline;
        }

        .news-item {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .news-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 5px;
        }

        .news-title {
            font-weight: 500;
            line-height: 1.4;
        }

        .footer-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
            margin: 30px 0;
        }

        .footer-bottom {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .bps-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: white;
        }

        .contact-info {
            margin-top: 15px;
        }

        .contact-info p {
            margin-bottom: 5px;
        }

        .berakhlak-container {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .berakhlak-image {
            max-width: 200px;
            margin-bottom: 15px;
        }

        .berakhlak-links {
            list-style: none;
            margin-top: 10px;
        }

        .berakhlak-links li {
            margin-bottom: 8px;
        }

        .berakhlak-links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 14px;
        }

        .berakhlak-links a:hover {
            color: white;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .course-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .course-image-container {
                height: 250px;
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
            
            .course-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .course-action {
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
            }
            
            .progress-container {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .course-image-container {
                height: 220px;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .footer-container {
                padding: 0 15px;
            }
            
            .berakhlak-image {
                max-width: 150px;
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

            .course-card {
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .course-title {
                font-size: 1.3rem;
            }
            
            .course-meta {
                flex-direction: column;
                gap: 8px;
            }
            
            .course-image-container {
                height: 200px;
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
                
                <!-- User Profile dengan Foto - VERSI DIPERBAIKI -->
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

            <!-- Messages Section -->
            <div class="messages-section">
                <h3 class="messages-title">Informasi Penting</h3>
                <div class="profile-item">
                    <div class="profile-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="profile-text">Selamat datang di platform kursus MOCC BPS</div>
                </div>
            </div>

            <!-- Performance Section -->
            <div class="performance-section">
                <h3 class="performance-title">Progress Belajar</h3>
                <div class="performance-item">
                    <div class="profile-text">Kursus yang Diikuti</div>
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Total Progress</span>
                            <span>45%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 45%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Cards dari Database -->
            @if(isset($kursus) && $kursus->count() > 0)
                @foreach($kursus as $item)
                    @if($item->status == 'aktif')
                    <div class="course-card">
                        <!-- Gambar Kursus dengan Container -->
                        <div class="course-image-container">
                            @if($item->gambar_kursus)
                                <img src="{{ asset('storage/' . $item->gambar_kursus) }}" 
                                     alt="{{ $item->judul_kursus }}" 
                                     class="course-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="course-image-placeholder" style="display: none;">
                                    <i class="fas fa-book-open"></i>
                                </div>
                            @else
                                <div class="course-image-placeholder">
                                    <i class="fas fa-book-open"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="course-header">
                            <div style="flex: 1;">
                                <h2 class="course-title">{{ $item->judul_kursus }}</h2>
                                <div class="course-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-user-tie"></i>
                                        {{ $item->penerbit }}
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        {{ $item->durasi_jam }} jam
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-users"></i>
                                        {{ $item->peserta_terdaftar }} peserta
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                @if($item->tingkat_kesulitan == 'pemula')
                                    <span class="level-badge level-pemula">Pemula</span>
                                @elseif($item->tingkat_kesulitan == 'menengah')
                                    <span class="level-badge level-menengah">Menengah</span>
                                @else
                                    <span class="level-badge level-lanjutan">Lanjutan</span>
                                @endif
                                <span class="status-badge status-aktif">Aktif</span>
                            </div>
                        </div>
                        
                        <div class="course-content">
                            <div>
                                <p class="course-description">
                                    {{ $item->deskripsi_kursus }}
                                </p>
                            </div>
                            
                            <div>
                                @if($item->output_pelatihan)
                                <div class="course-requirements">
                                    <h4 class="requirements-title">Output Pelatihan:</h4>
                                    <ul class="requirements-list">
                                        @foreach(explode("\n", $item->output_pelatihan) as $output)
                                            @if(trim($output))
                                                <li>{{ trim($output) }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($item->persyaratan)
                        <div class="course-requirements">
                            <h4 class="requirements-title">Persyaratan:</h4>
                            <ul class="requirements-list">
                                @foreach(explode("\n", $item->persyaratan) as $syarat)
                                    @if(trim($syarat))
                                        <li>{{ trim($syarat) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if($item->fasilitas)
                        <div class="course-requirements">
                            <h4 class="requirements-title">Fasilitas:</h4>
                            <ul class="requirements-list">
                                @foreach(explode("\n", $item->fasilitas) as $fasilitas)
                                    @if(trim($fasilitas))
                                        <li>{{ trim($fasilitas) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="course-action">
                            <div class="progress-container">
                                <div class="progress-label">
                                    <span>Kuota Tersedia</span>
                                    <span>
                                        @if($item->kuota_peserta)
                                            {{ $item->kuota_peserta - $item->peserta_terdaftar }} / {{ $item->kuota_peserta }}
                                        @else
                                            Tidak Terbatas
                                        @endif
                                    </span>
                                </div>
                                <div class="progress-bar">
                                    @if($item->kuota_peserta)
                                        <div class="progress-fill" 
                                             style="width: {{ ($item->peserta_terdaftar / $item->kuota_peserta) * 100 }}%">
                                        </div>
                                    @else
                                        <div class="progress-fill" style="width: 100%"></div>
                                    @endif
                                </div>
                            </div>
                            <!-- Di file mitra/kursus.blade.php -->
                            <form action="{{ route('mitra.kursus.enroll', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn-ikuti">
                                    <i class="fas fa-play-circle"></i>
                                    Ikuti Kursus
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                @endforeach
            @else
                <div class="course-card text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">Belum Ada Kursus Tersedia</h3>
                    <p class="text-muted">Silakan hubungi administrator untuk informasi lebih lanjut.</p>
                </div>
            @endif

            <!-- Copyright -->
            <div class="text-center mt-5 pt-4 border-top">
                <p style="color: #5a6c7d; font-size: 0.9rem;">
                    Copyright © 2024 | Kementerian Komunikasi dan Informatika
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-content">
                <!-- Alamat BPS + BerAKHLAK -->
                <div class="footer-section">
                    <div class="bps-title">BADAN PUSAT STATISTIK</div>
                    <div class="footer-address">
                        <p>Badan Pusat Statistik Kabupaten Tanah Laut (BPS-Statistics of Tanah Laut Regency)</p>
                        <p>Alamat: Jalan A. Syairani No. 9 Pelaihari Kab. Tanah Laut</p>
                        <p>Prov. Kalimantan Selatan</p>
                        <p>76914</p>
                        <p>Indonesia</p>
                    </div>
                    <div class="contact-info">
                        <p>Telepon: +62 512 21092</p>
                        <p>Fax: +62 512 3113</p>
                        <p>Email: bps6301@bps.go.id</p>
                        <p>bps6301@gmail.com</p>
                    </div>
                    
                    <!-- Gambar BerAKHLAK dan Manual S&K Daftar Tarakan -->
                    <div class="berakhlak-container">
                        <img src="{{ asset('img/cover.jpg') }}" alt="BerAKHLAK" class="berakhlak-image">
                        <ul class="berakhlak-links">
                            <li><a href="#">Manual S&K Daftar Tarakan</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Tentang Kami -->
                <div class="footer-section">
                    <h3>Tentang Kami</h3>
                    <ul class="footer-links">
                        <li><a href="https://ppid.bps.go.id/app/konten/6301/Profil-BPS.html?_gl=1*15t609r*_ga*MjQxOTY0MAAzLjE3NjEyNzM4MzU.*_ga_XXTTVXWHDB*czE3NjEyNzM4MzQkbzEkZzAkdTE3NjEyNzM4MzQkajYwJGwwJGgw">Profil BPS</a></li>
                        <li><a href="#">PPID</a></li>
                        <li><a href="#">Kebijakan Diseminasi</a></li>
                    </ul>
                </div>

                <!-- Tautan Lainnya -->
                <div class="footer-section">
                    <h3>Tautan Lainnya</h3>
                    <ul class="footer-links">
                        <li><a href="#">ASEAN Stats</a></li>
                        <li><a href="#">Forum Masyarakat Statistik</a></li>
                        <li><a href="#">Reformasi Birokrasi</a></li>
                        <li><a href="#">Layanan Pengaduan Secara Elektronik</a></li>
                        <li><a href="#">Politeknik Statistika STIS</a></li>
                        <li><a href="#">Pusdiklat BPS</a></li>
                        <li><a href="#">JDIH BPS</a></li>
                    </ul>
                </div>

                <!-- Government Public Relation -->
                <div class="footer-section">
                    <h3>Government Public Relation</h3>
                    <div class="news-item">
                        <div class="news-date">21 October 2025, 19:23 WEB</div>
                        <div class="news-title">Sertifikasi Pemerintah Indonesia: Mendorong 18.805 UMKM dan Sektor Tenaga Kerja</div>
                    </div>
                    <div class="news-item">
                        <div class="news-date">21 October 2025, 19:22 WEB</div>
                        <div class="news-title">Sertifikasi Pemerintah Indonesia: Capai Swasembada 225 Ribu Hektar, Target 480 Ribu Hektar Tahun Depan</div>
                    </div>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-bottom">
                <div class="copyright">
                    Hak Cipta © 2023 Badan Pusat Statistik
                </div>
            </div>
        </div>
    </footer>

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
        });

        // Ikuti kursus button
        document.querySelectorAll('.btn-ikuti').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const courseTitle = this.closest('.course-card').querySelector('.course-title').textContent;
                if (confirm(`Apakah Anda yakin ingin mengikuti kursus "${courseTitle}"?`)) {
                    this.closest('form').submit();
                }
            });
        });
    </script>
</body>
</html>