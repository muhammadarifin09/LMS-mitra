<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOCC BPS - Edit Biodata Mitra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* NAVIGATION STYLE */
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

        /* SIDEBAR STYLE */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 180px);
        }

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

        /* MAIN CONTENT STYLE */
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        
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

        /* FORM STYLE */
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

        .form-control:read-only {
            background-color: #f8f9fa;
            cursor: not-allowed;
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

        .current-photo {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .current-photo img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            object-fit: cover;
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

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        .debug-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        /* FOOTER STYLE */
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
            padding: 0;
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
            padding: 0;
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

        /* COPYRIGHT SECTION */
        .copyright-section {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #5a6c7d;
            font-size: 0.9rem;
        }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }
        }

        @media (max-width: 768px) {
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
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .form-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- NAVIGATION BAR - SIMPLE VERSION -->
    <nav class="main-nav">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
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
    </nav>

    <!-- DASHBOARD CONTENT DENGAN SIDEBAR -->
    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <div class="sidebar">
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
            </div>

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
            </div>

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

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1 class="welcome-title">Edit Biodata Mitra</h1>
                <p class="welcome-subtitle">
                    Perbarui data biodata mitra BPS. Pastikan semua data diisi dengan benar.
                </p>
            </div>

            <!-- Form Section -->
            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Form Edit Biodata Mitra</h2>
                </div>
                
                <div class="form-content">
                    <!-- DEBUG SECTION -->
                    <!-- <div class="debug-section">
                        <h5>🛠️ Debug Information</h5>
                        <p><strong>Biodata Variable:</strong> {{ isset($biodata) ? '✅ SET' : '❌ NOT SET' }}</p>
                        <p><strong>Biodata Data:</strong> {{ $biodata ? '✅ EXISTS' : '❌ NULL' }}</p>
                        @if($biodata)
                            <p><strong>ID Sobat:</strong> {{ $biodata->id_sobat }}</p>
                            <p><strong>Nama:</strong> {{ $biodata->nama_lengkap }}</p>
                            <p><strong>Kecamatan:</strong> {{ $biodata->kecamatan }}</p>
                        @else
                            <p><strong>Error:</strong> Data tidak tersedia di view</p>
                        @endif
                    </div> -->

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

                    @if(isset($biodata) && $biodata !== null)
                        <form action="{{ route('biodata.update', $biodata->id_sobat) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Data untuk User & Login -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-user-lock me-2"></i>Data Login Mitra
                                </h3>
                                
                                <div class="form-group">
                                    <label class="form-label">ID Sobat</label>
                                    <input type="text" name="id_sobat" class="form-control" value="{{ old('id_sobat', $biodata->id_sobat) }}" readonly>
                                    <div class="form-help">ID Sobat tidak dapat diubah</div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Email/Username</label>
                                    <input type="email" name="username_sobat" class="form-control" value="{{ old('username_sobat', $biodata->username_sobat) }}" readonly>
                                    <div class="form-help">Email/Username tidak dapat diubah</div>
                                </div>
                            </div>

                            <!-- Data Biodata Sesuai Tabel -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-user me-2"></i>Data Pribadi
                                </h3>

                                <div class="form-group">
                                    <label class="form-label">Nama Lengkap *</label>
                                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap Mitra" value="{{ old('nama_lengkap', $biodata->nama_lengkap) }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Kecamatan *</label>
                                            <input type="text" name="kecamatan" class="form-control" placeholder="Kecamatan" value="{{ old('kecamatan', $biodata->kecamatan) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Desa/Kelurahan *</label>
                                            <input type="text" name="desa" class="form-control" placeholder="Desa" value="{{ old('desa', $biodata->desa) }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">No Telepon/HP *</label>
                                            <input type="text" name="no_telepon" class="form-control" placeholder="Contoh: 081234567890" value="{{ old('no_telepon', $biodata->no_telepon) }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Alamat Lengkap *</label>
                                    <textarea name="alamat" class="form-textarea" placeholder="Alamat lengkap tempat tinggal" required>{{ old('alamat', $biodata->alamat) }}</textarea>
                                </div>

                                <!-- Foto Profil Section -->
                                <div class="row mb-4">
                                    <div class="col-12 text-center">
                                        <div class="position-relative d-inline-block mb-2">
                                            <img src="{{ $biodata->foto_profil ? asset('storage/' . $biodata->foto_profil) : asset('img/default-avatar.png') }}" 
                                                alt="Foto Profil" 
                                                class="rounded-circle shadow-sm"
                                                id="foto-preview"
                                                style="width: 180px; height: 180px; object-fit: cover; border: 2px solid #1e3c72;">
                                            <label for="foto_profil" class="btn btn-primary btn-sm rounded-circle position-absolute" 
                                                style="bottom: 0; right: 0; width: 24px; height: 24px; cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-camera" style="font-size: 0.6rem;"></i>
                                            </label>
                                            <input type="file" name="foto_profil" id="foto_profil" class="d-none" accept="image/*">
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-muted">Klik ikon kamera untuk mengubah foto</small><br>
                                            <small class="text-muted">Format: JPG, PNG, GIF (Maks: 2MB)</small>
                                        </div>
                                        @error('foto_profil')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
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
                                    Perbarui Biodata Mitra
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> Data biodata tidak ditemukan atau tidak tersedia!
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('biodata.index') }}" class="btn-kembali">
                                <i class="fas fa-arrow-left"></i>
                                Kembali ke Daftar Biodata
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Copyright -->
            <div class="copyright-section">
                <p>Copyright © 2025 | MOCC BPS - Edit Biodata Mitra</p>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-content">
                <!-- Alamat BPS -->
                <div class="footer-section">
                    <div class="bps-title">BADAN PUSAT STATISTIK</div>
                    <div class="footer-address">
                        <p>Badan Pusat Statistik Kabupaten Tanah Laut</p>
                        <p>Alamat: Jalan A. Syairani No. 9 Pelaihari</p>
                        <p>Kab. Tanah Laut, Prov. Kalimantan Selatan</p>
                        <p>76914 Indonesia</p>
                    </div>
                    <div class="contact-info">
                        <p>Telepon: +62 512 21092</p>
                        <p>Email: bps6301@bps.go.id</p>
                    </div>
                </div>

                <!-- Tentang Kami -->
                <div class="footer-section">
                    <h3>Tentang Kami</h3>
                    <ul class="footer-links">
                        <li><a href="#">Profil BPS</a></li>
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
                        <li><a href="#">Politeknik Statistika STIS</a></li>
                    </ul>
                </div>

                <!-- Berita -->
                <div class="footer-section">
                    <h3>Government Public Relation</h3>
                    <div class="news-item">
                        <div class="news-date">21 October 2025</div>
                        <div class="news-title">Sertifikasi Pemerintah Indonesia: Mendorong 18.805 UMKM</div>
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
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logout Confirmation
        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                document.getElementById('logout-form').submit();
            }
        }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('foto_profil');
        const fotoPreview = document.getElementById('foto-preview');
        
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validasi ukuran file (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB');
                    this.value = '';
                    return;
                }
                
                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file harus JPG, PNG, atau GIF');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    fotoPreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    });
    </script>
</body>
</html>