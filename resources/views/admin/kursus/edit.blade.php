<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOCC BPS - Edit Kursus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* CSS yang sama dengan halaman create */
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

        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 100px);
            flex: 1;
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
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
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

        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-back:hover {
            background: #5a6268;
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }

        .form-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #1e3c72;
            margin-bottom: 8px;
            display: block;
        }

        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
        }

        .form-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .required::after {
            content: " *";
            color: #e74c3c;
        }

        .image-preview {
            max-width: 200px;
            max-height: 150px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 10px;
            display: block;
            margin-top: 10px;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 4px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .col-md-6 {
            flex: 0 0 50%;
            padding: 0 10px;
        }

        /* PERBAIKAN: Menambahkan margin untuk form-group yang memiliki gambar */
        .form-group-with-image {
            margin-bottom: 30px;
        }

        /* PERBAIKAN: Mengatur tinggi maksimum untuk preview gambar */
        .image-preview-container {
            max-height: 200px;
            overflow: hidden;
            margin-bottom: 15px;
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

        /* Responsif */
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

            .col-md-6 {
                flex: 0 0 100%;
                padding: 0;
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

            .form-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation - Sticky -->
    <nav class="main-nav">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="#" class="nav-brand">
                    <img src="{{ asset('img/Logo_E-Learning.png') }}" alt="MOCC BPS Logo" class="logo-image">
                </a>
                <div class="nav-menu ms-5">
                    <a href="/admin/dashboard" class="nav-item">Dashboard</a>
                    <a href="/users" class="nav-item">Manajemen User</a>
                    <a href="/admin/kursus" class="nav-item active">Manajemen Kursus</a>
                    <a href="/admin/reports" class="nav-item">Laporan</a>
                    <a href="/admin/settings" class="nav-item">Pengaturan</a>
                </div>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="nav-icon me-3">
                    <i class="fas fa-globe"></i>
                </div>
                
                <div class="nav-icon me-4">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <div class="user-profile">
                    <div class="user-avatar">
                        <div class="avatar-initials">AD</div>
                    </div>
                    <div class="user-info">
                        <div class="user-name">Administrator</div>
                        <div class="user-status">
                            <span class="status-dot"></span>
                            Admin
                        </div>
                    </div>
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
                <a href="/users" class="sidebar-item">
                    <i class="fas fa-users"></i>
                    <span>Manajemen User</span>
                </a>
                <a href="/biodata" class="sidebar-item">
                    <i class="fas fa-id-card"></i>
                    <span>Manajemen Biodata</span>
                </a>
                <a href="/admin/kursus" class="sidebar-item active">
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
                <a href="#" class="sidebar-item text-danger" onclick="confirmLogout()">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- WELCOME SECTION -->
            <div class="welcome-section">
                <h1 class="welcome-title">Edit Kursus</h1>
                <p class="welcome-subtitle">
                    Perbarui informasi kursus yang sudah ada di platform MOCC BPS.
                </p>
            </div>

            <!-- FORM SECTION -->
            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Form Edit Kursus</h2>
                    <a href="{{ route('admin.kursus.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
                
                <div class="form-body">
                    <form action="{{ route('admin.kursus.update', $kursus->id) }}" method="POST" enctype="multipart/form-data" id="kursusForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <!-- Judul Kursus -->
                                <div class="form-group">
                                    <label for="judul_kursus" class="form-label required">Judul Kursus</label>
                                    <input type="text" class="form-control" id="judul_kursus" name="judul_kursus" 
                                           value="{{ old('judul_kursus', $kursus->judul_kursus) }}" required 
                                           placeholder="Masukkan judul kursus">
                                    @error('judul_kursus')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Penerbit -->
                                <div class="form-group">
                                    <label for="penerbit" class="form-label required">Penerbit</label>
                                    <input type="text" class="form-control" id="penerbit" name="penerbit" 
                                           value="{{ old('penerbit', $kursus->penerbit) }}" required 
                                           placeholder="Masukkan nama penerbit">
                                    @error('penerbit')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tingkat Kesulitan -->
                                <div class="form-group">
                                    <label for="tingkat_kesulitan" class="form-label required">Tingkat Kesulitan</label>
                                    <select class="form-select" id="tingkat_kesulitan" name="tingkat_kesulitan" required>
                                        <option value="">Pilih Tingkat Kesulitan</option>
                                        <option value="pemula" {{ old('tingkat_kesulitan', $kursus->tingkat_kesulitan) == 'pemula' ? 'selected' : '' }}>Pemula</option>
                                        <option value="menengah" {{ old('tingkat_kesulitan', $kursus->tingkat_kesulitan) == 'menengah' ? 'selected' : '' }}>Menengah</option>
                                        <option value="lanjutan" {{ old('tingkat_kesulitan', $kursus->tingkat_kesulitan) == 'lanjutan' ? 'selected' : '' }}>Lanjutan</option>
                                    </select>
                                    @error('tingkat_kesulitan')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Durasi -->
                                <div class="form-group">
                                    <label for="durasi_jam" class="form-label required">Durasi (Jam)</label>
                                    <input type="number" class="form-control" id="durasi_jam" name="durasi_jam" 
                                           value="{{ old('durasi_jam', $kursus->durasi_jam) }}" min="0" required 
                                           placeholder="Masukkan durasi dalam jam">
                                    <div class="form-text">Durasi total kursus dalam jam</div>
                                    @error('durasi_jam')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- PERBAIKAN: Kuota Peserta dipindah ke kolom kiri -->
                                <div class="form-group">
                                    <label for="kuota_peserta" class="form-label">Kuota Peserta</label>
                                    <input type="number" class="form-control" id="kuota_peserta" name="kuota_peserta" 
                                           value="{{ old('kuota_peserta', $kursus->kuota_peserta) }}" min="1" 
                                           placeholder="Kosongkan untuk tidak terbatas">
                                    <div class="form-text">Biarkan kosong jika kuota tidak terbatas</div>
                                    @error('kuota_peserta')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tanggal Mulai & Selesai -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                                                   value="{{ old('tanggal_mulai', $kursus->tanggal_mulai ? $kursus->tanggal_mulai->format('Y-m-d') : '') }}">
                                            @error('tanggal_mulai')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" 
                                                   value="{{ old('tanggal_selesai', $kursus->tanggal_selesai ? $kursus->tanggal_selesai->format('Y-m-d') : '') }}">
                                            @error('tanggal_selesai')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <!-- Status -->
                                <div class="form-group">
                                    <label for="status" class="form-label required">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="draft" {{ old('status', $kursus->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="aktif" {{ old('status', $kursus->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="nonaktif" {{ old('status', $kursus->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- PERBAIKAN: Gambar Kursus dengan container yang lebih baik -->
                                <div class="form-group form-group-with-image">
                                    <label for="gambar_kursus" class="form-label">Gambar Kursus</label>
                                    <input type="file" class="form-control" id="gambar_kursus" name="gambar_kursus" 
                                           accept="image/*" onchange="previewImage(this)">
                                    <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB</div>
                                    <div class="image-preview-container">
                                        <div class="image-preview" id="imagePreview">
                                            @if($kursus->gambar_kursus)
                                                <img src="{{ asset('storage/' . $kursus->gambar_kursus) }}" alt="Preview Gambar" style="max-height: 150px;">
                                            @else
                                                <img src="" alt="Preview Gambar" style="display: none; max-height: 150px;">
                                                <div class="text-muted text-center">Tidak ada gambar</div>
                                            @endif
                                        </div>
                                    </div>
                                    @error('gambar_kursus')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi Kursus -->
                        <div class="form-group">
                            <label for="deskripsi_kursus" class="form-label required">Deskripsi Kursus</label>
                            <textarea class="form-control" id="deskripsi_kursus" name="deskripsi_kursus" 
                                      rows="4" required placeholder="Masukkan deskripsi lengkap kursus">{{ old('deskripsi_kursus', $kursus->deskripsi_kursus) }}</textarea>
                            @error('deskripsi_kursus')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Output Pelatihan -->
                        <div class="form-group">
                            <label for="output_pelatihan" class="form-label">Output Pelatihan</label>
                            <textarea class="form-control" id="output_pelatihan" name="output_pelatihan" 
                                      rows="3" placeholder="Masukkan output yang akan didapat peserta (pisahkan dengan enter)">{{ old('output_pelatihan', $kursus->output_pelatihan) }}</textarea>
                            <div class="form-text">Pisahkan setiap poin dengan enter (baris baru)</div>
                            @error('output_pelatihan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Persyaratan -->
                        <div class="form-group">
                            <label for="persyaratan" class="form-label">Persyaratan</label>
                            <textarea class="form-control" id="persyaratan" name="persyaratan" 
                                      rows="3" placeholder="Masukkan persyaratan untuk mengikuti kursus">{{ old('persyaratan', $kursus->persyaratan) }}</textarea>
                            @error('persyaratan')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fasilitas -->
                        <div class="form-group">
                            <label for="fasilitas" class="form-label">Fasilitas</label>
                            <textarea class="form-control" id="fasilitas" name="fasilitas" 
                                      rows="3" placeholder="Masukkan fasilitas yang didapat peserta">{{ old('fasilitas', $kursus->fasilitas) }}</textarea>
                            @error('fasilitas')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Perbarui Kursus
                            </button>
                        </div>
                    </form>
                </div>
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

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Logout Confirmation
        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                document.getElementById('logout-form').submit();
            }
        }

        // Image Preview Function
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.style.display = 'block';
                    const img = preview.querySelector('img');
                    img.src = e.target.result;
                    img.style.display = 'block';
                    const noImageText = preview.querySelector('.text-muted');
                    if (noImageText) noImageText.style.display = 'none';
                }
                
                reader.readAsDataURL(file);
            }
        }

        // Form Validation
        document.getElementById('kursusForm').addEventListener('submit', function(e) {
            const judul = document.getElementById('judul_kursus').value.trim();
            const penerbit = document.getElementById('penerbit').value.trim();
            const tingkat = document.getElementById('tingkat_kesulitan').value;
            const status = document.getElementById('status').value;
            
            if (!judul || !penerbit || !tingkat || !status) {
                e.preventDefault();
                Swal.fire({
                    title: 'Data Belum Lengkap!',
                    text: 'Harap isi semua field yang wajib diisi.',
                    icon: 'warning',
                    confirmButtonColor: '#1e3c72'
                });
            }
        });

        // Initialize image preview on page load
        document.addEventListener('DOMContentLoaded', function() {
            const imagePreview = document.getElementById('imagePreview');
            const img = imagePreview.querySelector('img');
            if (img && img.src && !img.src.includes('data:')) {
                img.style.display = 'block';
                const noImageText = imagePreview.querySelector('.text-muted');
                if (noImageText) noImageText.style.display = 'none';
            }
        });
    </script>   
</body>
</html>