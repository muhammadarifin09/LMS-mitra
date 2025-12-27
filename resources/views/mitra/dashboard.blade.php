@extends('mitra.layouts.app')

@section('title', 'MOOC BPS - Dashboard Mitra')

@section('content')
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

    /* Dashboard Layout */
    .dashboard-container {
        display: flex;
        min-height: calc(100vh - 100px);
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
        text-align: center;
    }

    .welcome-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .welcome-subtitle {
        font-size: 1.2rem;
        line-height: 1.6;
        max-width: 800px;
        margin: 0 auto;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 8px;
    }

    .stat-label {
        color: #5a6c7d;
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Features Section */
    .features-section {
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 25px;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }

    .feature-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        transition: transform 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .feature-card h5 {
        color: #1e3c72;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .feature-card p {
        color: #5a6c7d;
        line-height: 1.6;
    }

    /* ===== RESPONSIVE DESIGN UNTUK MOBILE (≤500px) ===== */
    @media (max-width: 500px) {
        /* Layout utama */
        .main-content {
            padding: 15px !important;
            margin: 10px !important;
            border-radius: 15px !important;
        }
        
        /* Welcome Section */
        .welcome-section {
            padding: 20px !important;
            margin-bottom: 20px !important;
            border-radius: 12px !important;
        }
        
        .welcome-title {
            font-size: 1.5rem !important; /* Turun dari 2.5rem */
            margin-bottom: 15px !important;
        }
        
        .welcome-subtitle {
            font-size: 0.9rem !important; /* Turun dari 1.2rem */
            line-height: 1.4 !important;
            text-align: justify;
            hyphens: auto;
        }
        
        /* Stats Grid */
        .stats-grid {
            grid-template-columns: 1fr !important; /* Satu kolom saja */
            gap: 15px !important;
            margin-bottom: 25px !important;
        }
        
        .stat-card {
            padding: 15px !important;
            border-radius: 10px !important;
        }
        
        .stat-number {
            font-size: 1.8rem !important; /* Turun dari 2.5rem */
            margin-bottom: 5px !important;
        }
        
        .stat-label {
            font-size: 0.8rem !important; /* Turun dari 0.9rem */
        }
        
        /* Features Section */
        .features-section {
            margin-bottom: 25px !important;
        }
        
        .section-title {
            font-size: 1.3rem !important; /* Turun dari 1.8rem */
            margin-bottom: 15px !important;
            text-align: center;
        }
        
        .features-grid {
            grid-template-columns: 1fr !important; /* Satu kolom saja */
            gap: 15px !important;
        }
        
        .feature-card {
            padding: 15px !important;
            border-radius: 10px !important;
        }
        
        .feature-icon {
            width: 45px !important; /* Kecilkan icon */
            height: 45px !important;
            font-size: 1rem !important;
            margin-bottom: 10px !important;
            border-radius: 8px !important;
        }
        
        .feature-card h5 {
            font-size: 1rem !important;
            margin-bottom: 8px !important;
        }
        
        .feature-card p {
            font-size: 0.85rem !important;
            line-height: 1.4 !important;
        }
        
        /* Efek hover di mobile dinonaktifkan */
        .feature-card:hover {
            transform: none !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08) !important;
        }
    }
</style>

    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1 class="welcome-title">Selamat Datang di MOOC BPS!</h1>
        <p class="welcome-subtitle">
            Platform pembelajaran mandiri bagi Mitra Statistik untuk mempersiapkan diri sebelum pelatihan tatap muka dan penugasan di lapangan dengan sistem belajar adaptif.
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $pesertaAktif }}</div>
            <div class="stat-label">Peserta Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $kursusTersedia }}</div>
            <div class="stat-label">Kursus Tersedia</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $materiOnline }}</div>
            <div class="stat-label">Materi Online</div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="features-section">
        <h3 class="section-title">Apa Yang Mitra Dapatkan?</h3>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h5>Materi Terarah</h5>
                <p>Akses materi pembelajaran yang dirancang sesuai kebutuhan kompetensi mitra sebelum turun ke lapangan.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h5>Evaluasi Terstandar</h5>
                <p>Selesaikan evaluasi terstruktur sebagai prasyarat resmi mengikuti pelatihan dan penugasan berikutnya.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h5>Progress Terpantau</h5>
                <p>Setiap mitra dapat memantau perkembangan belajar secara mandiri dari kursus yang diikuti melalui menu Kursus Saya.</p>
            </div>
        </div>

    <!-- Copyright -->

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
</script>
@endsection