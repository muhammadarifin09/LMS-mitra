<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOCC BPS - Beranda</title>
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
    height: 60px; /* Tinggi garis */
    width: 1.5px; /* Lebar garis */
    background: linear-gradient(to bottom,  
        rgba(42, 82, 152, 0.7));
    border-radius: 2px;
}
        
        .nav-brand span {
            color: #2a5298;
        }
        
        /* Logo MOCC BPS sebagai gambar - DIPERBESAR */
        .logo-image {
            height: 50px; /* Diperbesar dari 100px */
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

/* Untuk responsif di mobile */
@media (max-width: 768px) {
    .nav-icon {
        width: 45px;
        height: 45px;
        font-size: 1rem;
    }
    
    .nav-icon.me-3 {
        margin-right: 15px !important;
    }
    
    .nav-icon.me-4 {
        margin-right: 20px !important;
    }
    
    .notification-badge {
        width: 16px;
        height: 16px;
        font-size: 0.6rem;
    }
}
        
        /* Perbesar ukuran teks navigasi */
        .nav-item {
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            color: #1e3c72;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.1rem; /* Ukuran teks lebih besar */
        }
        
        .nav-item:hover, .nav-item.active {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            transform: translateY(-2px);
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 30px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        /* Slider Styles - Tanpa Border Kotak */
        .slider-container {
            position: flex;
            max-width: 1900px;
            margin: 0 auto;
            overflow: hidden;
            margin-bottom: 100px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2); /* Hanya pembatas bawah */
        }
        
        .slider {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        
        .slide {
            min-width: 100%;
            display: flex;
            flex-direction: column;
            padding: 60px 80px;
            background: transparent; /* Background transparan */
        }
        
        .slide-content {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 80px;
            align-items: start;
        }
        
        .text-content {
            text-align: left;
            padding-top: 50px;
        }
        
        .image-content {
            text-align: right;
            position: relative;
        }
        
        .welcome-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 30px;
            line-height: 1.2;
        }
        
        .welcome-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 40px;
            line-height: 1.7;
        }
        
        .divider {
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
            margin: 35px 0;
            max-width: 180px;
        }
        
        .signature {
            font-style: italic;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            font-size: 1.3rem;
        }
        
        .mocc-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Profile Image - Larger and Right Aligned */
        .profile-image-container {
            position: relative;
            display: inline-block;
            margin-right: -50px; /* Geser lebih ke kanan */
        }
        
        .profile-image {
            width: 500px; /* Lebih besar */
            height: 600px; /* Lebih tinggi */
            border-radius: 25px;
            object-fit: cover;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            border: 8px solid rgba(255, 255, 255, 0.25);
            transition: transform 0.3s ease;
        }
        
        .profile-image:hover {
            transform: scale(1.02);
        }
        
        .image-decoration {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 3px solid rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            top: 20px;
            right: -20px; /* Sesuaikan dengan pergeseran ke kanan */
            z-index: -1;
        }
        
        /* Slider Navigation */
        .slider-nav {
            position: absolute;
            bottom: 30px;
            left: -50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
            z-index: 10;
        }
        
        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .slider-dot.active {
            background: white;
            transform: scale(1.2);
        }
        
        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            backdrop-filter: blur(10px);
            
        }
        
        .slider-arrow:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(1.1);
        }
        
        .slider-arrow.prev {
            left: 250px;
        }
        
        .slider-arrow.next {
            right: 250px;
        }
        
        /* Stats Mini */
        .stats-mini {
            display: flex;
            gap: 25px;
            margin-top: 40px;
            justify-content: flex-start;
        }
        
        .stat-mini-item {
            background: rgba(255, 255, 255, 0.15);
            padding: 20px 25px;
            border-radius: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            min-width: 120px;
        }
        
        .stat-mini-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            display: block;
        }
        
        .stat-mini-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 5px;
        }
        
        /* Floating Elements */
        .floating-elements {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: -1;
        }
        
        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        
        .floating-element:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 15%;
            left: 5%;
            animation-delay: 0s;
        }
        
        .floating-element:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 75%;
            left: 85%;
            animation-delay: 2s;
        }
        
        .floating-element:nth-child(3) {
            width: 80px;
            height: 80px;
            top: 25%;
            left: 90%;
            animation-delay: 4s;
        }
        
        .floating-element:nth-child(4) {
            width: 70px;
            height: 70px;
            top: 60%;
            left: 10%;
            animation-delay: 6s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-25px) rotate(180deg);
            }
        }
        
        /* Additional Content untuk Scroll */
        .additional-content {
            max-width: 1900px;
            margin: 0 auto;
            color: white;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 80px;
        }
        
        .content-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }
        
        .content-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
        }
        
        .content-icon {
            font-size: 3rem;
            color: white;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        .content-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: white;
        }
        
        .content-description {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 50px;
            color: white;
        }
        
        /* Footer */
        /* Footer */
.main-footer {
    background: #1a365d;
    color: white;
    padding: 50px 0 25px;
    margin-top: 100px;
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

/* Style untuk gambar BerAKHLAK */
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
@media (max-width: 1200px) {
    .slide-content {
        grid-template-columns: 1fr;
        gap: 50px;
        text-align: center;
    }
    
    .text-content {
        text-align: center;
        padding-top: 0;
    }
    
    .image-content {
        text-align: center;
    }
    
    .profile-image {
        width: 400px;
        height: 500px;
    }
    
    .stats-mini {
        justify-content: center;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
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
    
    .slide {
        padding: 40px 30px;
    }
    
    .profile-image {
        width: 300px;
        height: 400px;
    }
    
    .welcome-title {
        font-size: 2.5rem;
    }
    
    .stats-mini {
        flex-direction: column;
        align-items: center;
    }
    
    .stat-mini-item {
        width: 200px;
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
    </style>
</head>
<body>
    <!-- Navigation - Sticky -->
    <nav class="main-nav">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <!-- Logo MOCC BPS sebagai gambar -->
            <a href="#" class="nav-brand">
                <img src="img/Logo_E-Learning.png" alt="MOCC BPS Logo" class="logo-image">
            </a>
            <div class="nav-menu ms-5">
                <a href="#" class="nav-item active">Beranda</a>
                <a href="#" class="nav-item">Dashboard</a>
                <a href="#" class="nav-item">Kursus</a>
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
            <div class="nav-icon me-4">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </div>
            
            <!-- User Profile (yang sudah ada) -->
            <div class="user-profile">
                <div class="user-avatar">MP</div>
                <div>
                    <div style="font-weight: 600; color: #1e3c72;">Mitra BPS</div>
                    <div style="font-size: 0.8rem; color: #5a6c7d;">Online</div>
                </div>
            </div>
        </div>
    </div>
</nav>

    <!-- Floating Elements -->
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <!-- Main Content - Enhanced with Scroll -->
    <main class="main-content">
        <!-- Slider Container - Tanpa Border Kotak -->
        <div class="slider-container">
            <div class="slider">
                <!-- Slide 1 - Welcome -->
                <div class="slide">
                    <div class="slide-content">
                        <!-- Text Content -->
                        <div class="text-content">
                            <div class="mocc-badge">
                                MOCC BPS - Massive Online Coaching Course
                            </div>
                            
                            <h1 class="welcome-title">
                                Selamat Datang, di MOCC BPS!
                            </h1>
                            
                            <p class="welcome-subtitle">
                                Dengan Memadukan Media Digital Dan Teknologi Daring, Keterbatasan Mitra BPS Dalam Mengembangkan Kompetensi Statistik Karena Jarak Dan Waktu Dapat Diminimalkan.
                            </p>
                            
                            <div class="divider"></div>
                            

                            <!-- Mini Stats -->
                            <div class="stats-mini">
                                <div class="stat-mini-item">
                                    <span class="stat-mini-number">2,847</span>
                                    <span class="stat-mini-label">Peserta Aktif</span>
                                </div>
                                <div class="stat-mini-item">
                                    <span class="stat-mini-number">15+</span>
                                    <span class="stat-mini-label">Kursus Tersedia</span>
                                </div>
                                <div class="stat-mini-item">
                                    <span class="stat-mini-number">98%</span>
                                    <span class="stat-mini-label">Tingkat Kepuasan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Image Content - Larger and Right Aligned -->
                        <div class="image-content">
                            <div class="profile-image-container">
                                <div class="image-decoration"></div>
                                <!-- GANTI PATH GAMBAR DI BAWAH INI -->
                                <img src="/img/foto.png" alt="Mentor MOCC BPS" class="profile-image">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 2 - Course Features -->
                <div class="slide">
                    <div class="slide-content">
                        <!-- Text Content -->
                        <div class="text-content">
                            <div class="mocc-badge">
                                Fitur Unggulan MOCC BPS
                            </div>
                            
                            <h1 class="welcome-title">
                                Pengalaman Belajar Terbaik
                            </h1>
                            
                            <p class="welcome-subtitle">
                                MOCC BPS menyediakan berbagai fitur unggulan yang dirancang khusus untuk memberikan pengalaman belajar terbaik bagi mitra BPS di seluruh Indonesia.
                            </p>
                            
                            <div class="divider"></div>
                            
                            <div class="signature">
                                Tim Pengembangan MOCC BPS
                            </div>

                            <!-- Mini Stats -->
                            <div class="stats-mini">
                                <div class="stat-mini-item">
                                    <span class="stat-mini-number">24/7</span>
                                    <span class="stat-mini-label">Akses Materi</span>
                                </div>
                                <div class="stat-mini-item">
                                    <span class="stat-mini-number">50+</span>
                                    <span class="stat-mini-label">Mentor Ahli</span>
                                </div>
                                <div class="stat-mini-item">
                                    <span class="stat-mini-number">100%</span>
                                    <span class="stat-mini-label">Online</span>
                                </div>
                            </div>
                        </div>

                        <!-- Image Content - Larger and Right Aligned -->
                        <div class="image-content">
                            <div class="profile-image-container">
                                <div class="image-decoration"></div>
                                <!-- GANTI PATH GAMBAR DI BAWAH INI -->
                                <img src="/img/foto1.png" alt="Fitur MOCC BPS" class="profile-image">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 3 - Community -->
                <div class="slide">
                    <div class="slide-content">
                        <!-- Text Content -->
                        <div class="text-content">
                            <div class="mocc-badge">
                                Komunitas MOCC BPS
                            </div>
                            
                            <h1 class="welcome-title">
                                Bergabung dengan Komunitas
                            </h1>
                            
                            <p class="welcome-subtitle">
                                Dapatkan manfaat maksimal dengan bergabung dalam komunitas MOCC BPS yang aktif dan saling mendukung dalam pengembangan kompetensi statistik.
                            </p>
                            
                            <div class="divider"></div>
                            
                            <div class="signature">
                                Komunitas MOCC BPS
                            </div>

                            <!-- Mini Stats -->
                            <div class="stats-mini">
                                <div class="stat-mini-item">
                                    <span class="stat-mini-number">5,000+</span>
                                    <span class="stat-mini-label">Anggota</span>
                                </div>
                                <div class="stat-mini-item">
                                    <span class="stat-mini-number">100+</span>
                                    <span class="stat-mini-label">Diskusi/Minggu</span>
                                </div>
                                <div class="stat-mini-item">
                                    <span class="stat-mini-number">95%</span>
                                    <span class="stat-mini-label">Respon Cepat</span>
                                </div>
                            </div>
                        </div>

                        <!-- Image Content - Larger and Right Aligned -->
                        <div class="image-content">
                            <div class="profile-image-container">
                                <div class="image-decoration"></div>
                                <!-- GANTI PATH GAMBAR DI BAWAH INI -->
                                <img src="img/foto_dua.png" alt="Komunitas MOCC BPS" class="profile-image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slider Navigation -->
            <div class="slider-arrow prev">
                <i class="fas fa-chevron-left"></i>
            </div>
            <div class="slider-arrow next">
                <i class="fas fa-chevron-right"></i>
            </div>
            
            <div class="slider-nav">
                <div class="slider-dot active"></div>
                <div class="slider-dot"></div>
                <div class="slider-dot"></div>
            </div>
        </div>

        <!-- Additional Content untuk Scroll -->
        <div class="additional-content">
            <h2 class="section-title">Mengapa Memilih MOCC BPS?</h2>
            
            <div class="content-grid">
                <div class="content-card">
                    <div class="content-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="content-title">Coach Berpengalaman</h3>
                    <p class="content-description">
                        Dibimbing oleh mentor dan coach yang berpengalaman di bidang statistik dan data analysis dari BPS.
                    </p>
                </div>
                
                <div class="content-card">
                    <div class="content-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3 class="content-title">Sertifikat Resmi</h3>
                    <p class="content-description">
                        Dapatkan sertifikat resmi yang diakui secara nasional setelah menyelesaikan program pelatihan.
                    </p>
                </div>
                
                <div class="content-card">
                    <div class="content-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="content-title">Komunitas Aktif</h3>
                    <p class="content-description">
                        Bergabung dengan komunitas mitra BPS untuk berdiskusi dan berkolaborasi dalam proyek statistik.
                    </p>
                </div>
            </div>
        </div>
    </main>

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
                    <img src="img/cover.jpg" alt="BerAKHLAK" class="berakhlak-image">
                    <ul class="berakhlak-links">
                        <li><a href="#">Manual S&K Daftar Tarakan</a></li>
                    </ul>
                </div>
            </div>

            <!-- Tentang Kami -->
            <div class="footer-section">
                <h3>Tentang Kami</h3>
                <ul class="footer-links">
                    <li><a href="https://ppid.bps.go.id/app/konten/6301/Profil-BPS.html?_gl=1*15t609r*_ga*MjQxOTY0MDAzLjE3NjEyNzM4MzU.*_ga_XXTTVXWHDB*czE3NjEyNzM4MzQkbzEkZzAkdDE3NjEyNzM4MzQkajYwJGwwJGgw">Profil BPS</a></li>
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

    <script>
        // Slider functionality
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.querySelector('.slider');
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.slider-dot');
            const prevBtn = document.querySelector('.slider-arrow.prev');
            const nextBtn = document.querySelector('.slider-arrow.next');
            
            let currentSlide = 0;
            const slideCount = slides.length;
            
            // Function to update slider position
            function updateSlider() {
                slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                
                // Update active dot
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
            }
            
            // Next slide
            function nextSlide() {
                currentSlide = (currentSlide + 1) % slideCount;
                updateSlider();
            }
            
            // Previous slide
            function prevSlide() {
                currentSlide = (currentSlide - 1 + slideCount) % slideCount;
                updateSlider();
            }
            
            // Event listeners
            nextBtn.addEventListener('click', nextSlide);
            prevBtn.addEventListener('click', prevSlide);
            
            // Dot navigation
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    currentSlide = index;
                    updateSlider();
                });
            });
            
            // Auto slide (optional)
            // setInterval(nextSlide, 5000);
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>