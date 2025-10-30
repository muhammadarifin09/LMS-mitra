<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOCC BPS - Manajemen Biodata Mitra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* CSS yang sudah work dari sebelumnya */
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
        
        /* Navigation styles (sudah work) */
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

        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .table-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e3c72;
            margin: 0;
        }

        .btn-tambah {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
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

        .btn-tambah:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-edit {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }

        .btn-edit:hover {
            background: #3498db;
            color: white;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        .btn-delete:hover {
            background: #e74c3c;
            color: white;
            transform: translateY(-2px);
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
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
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
                <a href="#" class="nav-brand">
                    <img src="{{ asset('img/Logo_E-Learning.png') }}" alt="MOCC BPS Logo" class="logo-image">
                </a>
                <div class="nav-menu ms-5">
                    <a href="/admin/dashboard" class="nav-item">Dashboard</a>
                    <a href="/users" class="nav-item">Manajemen User</a>
                    <a href="/admin/courses" class="nav-item">Manajemen Kursus</a>
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
                    <span class="notification-badge">5</span>
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
                <h1 class="welcome-title">Manajemen Biodata Mitra</h1>
                <p class="welcome-subtitle">
                    Kelola data biodata mitra BPS dengan mudah. Lihat, edit, atau hapus data mitra sesuai kebutuhan.
                </p>
            </div>

            <!-- TABLE SECTION -->
            <div class="table-container">
                <div class="table-header">
                    <h2 class="table-title">Daftar Biodata Mitra</h2>
                    <a href="/biodata/create" class="btn-tambah">
                        <i class="fas fa-plus-circle"></i>
                        Tambah Biodata
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Sobat</th>
                                <th>Nama</th>
                                <th>Kecamatan</th>
                                <th>Desa/Kelurahan</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($biodata) && $biodata->count() > 0)
                                @foreach($biodata as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->id_sobat }}</td>
                                    <td>{{ $item->nama_lengkap }}</td>
                                    <td>{{ $item->kecamatan }}</td>
                                    <td>{{ $item->desa }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($item->alamat, 50) }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="/biodata/{{ $item->id_sobat }}/edit" class="btn-action btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="/biodata/{{ $item->id_sobat }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-delete" title="Hapus" 
                                                        onclick="confirmDelete(event, this.closest('form'))">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-database me-2"></i>
                                        @if(!isset($biodata))
                                            Variabel $biodata tidak terdefinisi
                                        @else
                                            Tidak ada data biodata mitra
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
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

         // Alternative logout function jika route logout tidak tersedia
         function logout() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                // Redirect langsung ke route logout
                window.location.href = "{{ route('logout') }}";
            }
        }

        // Delete Confirmation dengan SweetAlert
        function confirmDelete(event, form) {
            event.preventDefault();
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data biodata dan akun mitra akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>   
</body>
</html>