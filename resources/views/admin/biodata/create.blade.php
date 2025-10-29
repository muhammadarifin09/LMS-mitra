<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOCC BPS - Tambah Biodata Mitra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* COPY STYLE DARI HALAMAN BIODATA */
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
            padding: 20px 60px;
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
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e3c72;
            font-size: 1.2rem;
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
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            color: #1e3c72;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.1rem;
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
            gap: 12px;
            padding: 8px 15px;
            border-radius: 25px;
            background: rgba(30, 60, 114, 0.05);
            transition: all 0.3s ease;
            margin-left: 20px;
            cursor: pointer;
            position: relative;
        }

        .user-profile:hover {
            background: rgba(30, 60, 114, 0.1);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .avatar-initials {
            color: white;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .user-name {
            font-weight: 700;
            color: #1e3c72;
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        .user-status {
            font-size: 0.75rem;
            color: #5a6c7d;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background: #28a745;
            border-radius: 50%;
            display: inline-block;
        }

        /* User Dropdown Menu */
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            z-index: 1000;
            display: none;
            margin-top: 10px;
            border: 1px solid #e9ecef;
        }

        .user-dropdown.show {
            display: block;
        }

        .dropdown-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .dropdown-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #5a6c7d;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .dropdown-item:hover {
            background: rgba(30, 60, 114, 0.05);
            color: #1e3c72;
            border-left-color: #1e3c72;
        }

        .dropdown-item.logout {
            color: #e74c3c;
            border-top: 1px solid #e9ecef;
        }

        .dropdown-item.logout:hover {
            background: rgba(231, 76, 60, 0.05);
            color: #e74c3c;
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

        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .welcome-subtitle {
            font-size: 1.2rem;
            line-height: 1.6;
            opacity: 0.9;
        }

        /* Form Styles */
        .form-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e3c72;
            margin: 0;
        }

        .btn-kembali {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-kembali:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }

        .form-content {
            padding: 30px;
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #1e3c72;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
            outline: none;
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fff;
            cursor: pointer;
        }

        .form-select:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
            outline: none;
        }

        .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fff;
            resize: vertical;
            min-height: 100px;
        }

        .form-textarea:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
            outline: none;
        }

        .form-file {
            width: 100%;
            padding: 12px 15px;
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-file:hover {
            border-color: #1e3c72;
            background: #f0f4f8;
        }

        .form-help {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert li {
            margin-bottom: 5px;
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

        /* CSS untuk Fallback Image */
        .avatar-image[src=""],
        .avatar-image:not([src]) {
            opacity: 0;
        }

        .avatar-image:not([src]) + .avatar-initials,
        .avatar-image[src=""] + .avatar-initials {
            display: flex !important;
        }

        /* Responsif */
        @media (max-width: 1200px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }
        }

        @media (max-width: 768px) {
            .main-nav {
                padding: 12px 20px;
            }
            
            .nav-menu {
                gap: 5px;
            }
            
            .nav-item {
                padding: 8px 15px;
                font-size: 1rem;
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
            
            .welcome-title {
                font-size: 2rem;
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

            /* Responsif untuk User Profile */
            .user-profile {
                margin-left: 10px;
                padding: 6px 12px;
            }
            
            .user-avatar {
                width: 40px;
                height: 40px;
            }
            
            .avatar-initials {
                font-size: 14px;
            }
            
            .user-name {
                font-size: 0.85rem;
                max-width: 120px;
            }
            
            .user-status {
                font-size: 0.7rem;
            }

            .user-dropdown {
                position: fixed;
                top: auto;
                bottom: 0;
                left: 0;
                right: 0;
                border-radius: 12px 12px 0 0;
                margin-top: 0;
            }

            .form-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .form-title {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 576px) {
            .user-info {
                display: none;
            }
            
            .user-profile {
                padding: 8px;
                background: transparent;
            }
            
            .user-profile:hover {
                background: rgba(30, 60, 114, 0.1);
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
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
                    <a href="/admin/dashboard" class="nav-item">Dashboard</a>
                    <a href="/admin/users" class="nav-item">Manajemen User</a>
                    <a href="/admin/courses" class="nav-item">Manajemen Kursus</a>
                    <a href="/admin/reports" class="nav-item">Laporan</a>
                    <a href="/admin/settings" class="nav-item">Pengaturan</a>
                </div>
            </div>
            
            <!-- Tambahkan bagian ikon di sini -->
            <div class="d-flex align-items-center">
                <!-- Ikon Bahasa -->
                <div class="nav-icon me-3">
                    <i class="fas fa-globe"></i>
                </div>
                
                <!-- Ikon Notifikasi -->
                <div class="nav-icon me-4">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </div>
                
                <!-- User Profile Admin dengan Data Dinamis -->
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">
                        @auth
                            @php
                                $user = Auth::user();
                                $biodata = $user->biodata ?? null;
                                // Generate initials from user name
                                $nameParts = explode(' ', $user->name);
                                $initials = '';
                                if (count($nameParts) >= 2) {
                                    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
                                } else {
                                    $initials = strtoupper(substr($user->name, 0, 2));
                                }
                            @endphp
                            
                            @if($biodata && $biodata->foto_profil)
                                <img src="{{ asset('storage/' . $biodata->foto_profil) }}" 
                                     alt="Foto Profil Admin" 
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
                            {{ ucfirst(Auth::user()->role) }}
                        </div>
                    </div>
                    <i class="fas fa-chevron-down ms-2" style="color: #5a6c7d; font-size: 0.8rem;"></i>
                </div>

                <!-- User Dropdown Menu -->
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="user-name">{{ Auth::user()->biodata->nama_lengkap ?? Auth::user()->name }}</div>
                        <div class="user-status">
                            <span class="status-dot"></span>
                            {{ ucfirst(Auth::user()->role) }}
                        </div>
                    </div>
                    <a href="/admin/profile" class="dropdown-item">
                        <i class="fas fa-user-cog"></i>
                        <span>Profil Saya</span>
                    </a>
                    <a href="/admin/settings" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Pengaturan</span>
                    </a>
                    <a href="#" class="dropdown-item logout" onclick="event.preventDefault(); confirmLogout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <!-- Sidebar Admin -->
        <div class="sidebar">
            <!-- Admin Menu Section -->
            <div class="sidebar-section">
                <div class="sidebar-title">Menu Admin</div>
                <a href="/admin/dashboard" class="sidebar-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/admin/users" class="sidebar-item">
                    <i class="fas fa-users"></i>
                    <span>Manajemen User</span>
                </a>
                <a href="/biodata" class="sidebar-item active">
                    <i class="fas fa-id-card"></i>
                    <span>Manajemen Biodata</span>
                </a>
                <a href="/admin/courses" class="sidebar-item">
                    <i class="fas fa-book"></i>
                    <span>Manajemen Kursus</span>
                </a>
                <a href="/admin/categories" class="sidebar-item">
                    <i class="fas fa-tags"></i>
                    <span>Kategori Kursus</span>
                </a>
            </div>

            <!-- System Section -->
            <div class="sidebar-section">
                <div class="sidebar-title">Sistem</div>
                <a href="/admin/reports" class="sidebar-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan & Analitik</span>
                </a>
                <a href="/admin/settings" class="sidebar-item">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan Sistem</span>
                </a>
                <a href="/admin/backup" class="sidebar-item">
                    <i class="fas fa-database"></i>
                    <span>Backup Data</span>
                </a>
            </div>

            <!-- Account Section -->
            <div class="sidebar-section">
                <a href="/admin/profile" class="sidebar-item">
                    <i class="fas fa-user-cog"></i>
                    <span>Profil Admin</span>
                </a>
                <a href="#" class="sidebar-item text-danger" onclick="event.preventDefault(); confirmLogout()">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1 class="welcome-title">Tambah Biodata Mitra</h1>
                <p class="welcome-subtitle">
                    Tambahkan data biodata mitra BPS baru. Pastikan semua data diisi dengan benar.
                </p>
            </div>

            <!-- Form Section -->
            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Form Tambah Biodata Mitra</h2>
                    <a href="{{ route('biodata.index') }}" class="btn-kembali">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Daftar
                    </a>
                </div>
                
                <div class="form-content">
                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mt-2 mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('biodata.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Data untuk User & Login -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-user-lock me-2"></i>Data Login Mitra
                            </h3>
                            
                            <div class="form-group">
                                <label class="form-label">ID Sobat *</label>
                                <input type="text" name="id_sobat" class="form-control" placeholder="Contoh: mitra_001" value="{{ old('id_sobat') }}" required>
                                <div class="form-help">ID Sobat akan menjadi password default untuk login mitra</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email/Username *</label>
                                <input type="email" name="username_sobat" class="form-control" placeholder="Contoh: mitra@example.com" value="{{ old('username_sobat') }}" required>
                                <div class="form-help">Email akan menjadi username untuk login mitra</div>
                            </div>
                        </div>

                        <!-- Data Biodata Sesuai Tabel -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-user me-2"></i>Data Pribadi
                            </h3>

                            <div class="form-group">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap Mitra" value="{{ old('nama_lengkap') }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Tempat Lahir *</label>
                                        <input type="text" name="tempat_lahir" class="form-control" placeholder="Kota/Kabupaten Lahir" value="{{ old('tempat_lahir') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Tanggal Lahir *</label>
                                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Jenis Kelamin *</label>
                                        <select name="jenis_kelamin" class="form-select" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">No Telepon/HP *</label>
                                        <input type="text" name="no_telepon" class="form-control" placeholder="Contoh: 081234567890" value="{{ old('no_telepon') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Alamat Lengkap *</label>
                                <textarea name="alamat" class="form-textarea" placeholder="Alamat lengkap tempat tinggal" required>{{ old('alamat') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Foto Profil</label>
                                <input type="file" name="foto_profil" class="form-file" accept="image/*">
                                <div class="form-help">Format: JPG, PNG, GIF (Maksimal 2MB)</div>
                            </div>
                        </div>

                        <!-- Data Profesional -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-briefcase me-2"></i>Data Profesional
                            </h3>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Pekerjaan *</label>
                                        <input type="text" name="pekerjaan" class="form-control" placeholder="Contoh: Mahasiswa, PNS, Wiraswasta" value="{{ old('pekerjaan') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Instansi *</label>
                                        <input type="text" name="instansi" class="form-control" placeholder="Contoh: Universitas, Perusahaan, BPS" value="{{ old('instansi') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Pendidikan Terakhir *</label>
                                <select name="pendidikan_terakhir" class="form-select" required>
                                    <option value="">Pilih Pendidikan Terakhir</option>
                                    <option value="SD" {{ old('pendidikan_terakhir') == 'SD' ? 'selected' : '' }}>SD</option>
                                    <option value="SMP" {{ old('pendidikan_terakhir') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                    <option value="SMA" {{ old('pendidikan_terakhir') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                    <option value="D3" {{ old('pendidikan_terakhir') == 'D3' ? 'selected' : '' }}>D3</option>
                                    <option value="S1" {{ old('pendidikan_terakhir') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('pendidikan_terakhir') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('pendidikan_terakhir') == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('biodata.index') }}" class="btn-kembali">
                                <i class="fas fa-arrow-left"></i>
                                Kembali
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save"></i>
                                Simpan Biodata Mitra
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center mt-5 pt-4 border-top">
                <p style="color: #5a6c7d; font-size: 0.9rem;">
                    Copyright © 2025 | MOCC BPS - Tambah Biodata Mitra
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
                        <li><a href="https://ppid.bps.go.id/app/konten/6301/Profil-BPS.html?_gl=1*15t609r*_ga*MjQxOTY0MDAzLjE3NjEyNzM4MzU.*_ga_XXTTVXWHDB*czE3NjEyNzM4MzQkbzEkZzAkdTE3NjEyNzM4MzQkajYwJGwwJGgw">Profil BPS</a></li>
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

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // User Profile Dropdown Toggle
        document.getElementById('userProfile').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('userDropdown').classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!document.getElementById('userProfile').contains(e.target)) {
                document.getElementById('userDropdown').classList.remove('show');
            }
        });

        // Logout Confirmation
        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                document.getElementById('logout-form').submit();
            }
        }

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
        document.querySelectorAll('.avatar-image').forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
                const initials = this.nextElementSibling;
                if (initials && initials.classList.contains('avatar-initials')) {
                    initials.style.display = 'flex';
                }
            });
        });

        // Form validation enhancement
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '#e9ecef';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Harap lengkapi semua field yang wajib diisi!');
            }
        });
    </script>
</body>
</html>