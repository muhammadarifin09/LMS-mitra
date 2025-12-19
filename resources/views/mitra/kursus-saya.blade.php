@extends('mitra.layouts.app')

@section('title', 'MOOC BPS - Kursus Saya')

@section('content')

<style>
    /* Reset dan base styles */
    .kursus-saya-container {
        min-height: calc(100vh - 200px);
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

    /* Filter Section */
    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        margin-left: 2px;
        margin-right: 2px;
    }

    .filter-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: white;
        color: #666;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.9rem;
    }

    .filter-btn.active {
        background: #1e3c72;
        color: white;
        border-color: #1e3c72;
    }

    .sort-select, .search-input {
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        min-width: 150px;
    }

    /* Course Grid Container */
    .courses-main-content {
        padding: 0 2px;
    }

    /* Modern Course Card Design - DISEDERHANAKAN */
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

    .course-content-wrapper {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 15px;
        position: relative; /* ← WAJIB AGAR STATUS MUNCUL */
    }

    .course-main-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e3c72;
        margin: 0;
        line-height: 1.3;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    /* Progress Section - DISEDERHANAKAN */
    .progress-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin: 0;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.85rem;
        color: #5a6c7d;
        font-weight: 500;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        transition: width 0.3s ease;
        border-radius: 4px;
    }

    /* Status Badge */
    .status-container {
        display: block;   /* mencegah flex dari parent memaksa ukuran */
        width: 100%;
    }

    .status-badge {
        display: inline-block;
        width: auto;
        flex: 0 0 auto;       /* penting kalau parent flex */
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        backdrop-filter: blur(10px);
    }

    .status-in_progress {
        background: rgba(52, 152, 219, 0.9);
        color: white;
    }

    .status-finished {
        background: rgba(39, 174, 96, 0.9);
        color: white;
    }

    /* Action Row */
    .course-action-row {
        margin-top: auto;
    }

    .btn-view-course-blue {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        width: 100%;
        justify-content: center;
    }

    .btn-view-course-blue:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(30, 60, 114, 0.3);
        color: white;
    }

    /* Course Grid Layout */
    .course-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-bottom: 30px;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .course-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .course-grid {
            grid-template-columns: 1fr;
        }
        
        .course-image-wrapper {
            height: 140px;
        }
        
        .course-content-wrapper {
            padding: 15px;
        }
    }

    /* ===== RESPONSIVE DESIGN UNTUK MOBILE KECIL (≤400px) ===== */
    @media (max-width: 400px) {
        /* Container utama */
        .main-content {
            padding: 15px 12px !important; /* Tambah kiri-kanan 12px */
            margin: 10px 10px !important; /* Tambah margin kiri-kanan 8px */
        }
        
        /* Header Kursus */
        .kursus-header {
            margin-bottom: 20px !important;
            text-align: center;
        }
        
        .kursus-title {
            font-size: 1.5rem !important; /* Turun dari 2.2rem */
            margin-bottom: 8px !important;
        }
        
        .kursus-subtitle {
            font-size: 0.9rem !important; /* Turun dari 1.1rem */
            line-height: 1.4;
        }
        
        /* Filter Section */
        .filter-section {
            padding: 0rem !important;
            margin-bottom: 0.5rem !important;
            border-radius: 10px !important;
        }
        
        .filter-buttons {
            gap: 0.3rem !important;
            margin-bottom: 15px;
            justify-content: center;
        }
        
        .filter-btn {
            padding: 0.4rem 0.8rem !important;
            font-size: 0.6rem !important; /* Lebih kecil */
            border-radius: 5px !important;
        }
        
        /* Search box */
        .search-input {
            padding: 0.4rem 1rem !important; /* Lebih besar sedikit */
            font-size: 0.6rem !important; /* Font lebih besar agar terbaca */
            width: 100% !important;
            min-width: 188px !important; /* Lebar minimal lebih panjang */
            max-width: 288px !important; /* Maksimal panjang */
            border-radius: 6px !important;
            border: 1px solid #ccc !important;
            box-sizing: border-box !important;
        }
        
        /* Layout filter */
        .filter-section .d-flex {
            flex-direction: column !important;
            gap: 5px !important;
        }
        
        .search-box {
            width: 100% !important;
            text-align: center;
            margin: 0 auto;
            padding: 0 5px; /* Beri sedikit padding samping */
        }
        
        /* Main Content */
        .courses-main-content {
            padding: 0 !important;
        }
        
        /* Course Grid */
        .course-grid {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
            margin-bottom: 20px !important;
        }
        
        /* Course Card */
        .modern-course-card {
            border-radius: 10px !important;
            margin: 0 2px;
        }
        
        /* Course Image */
        .course-image-wrapper {
            height: 120px !important; /* Turun dari 160px */
        }
        
        /* Course Content */
        .course-content-wrapper {
            padding: 12px !important; /* Turun dari 20px */
            gap: 10px !important; /* Kurangi gap */
        }
        
        /* Status Badge */
        .status-container {
            text-align: left;
            margin-bottom: 5px;
        }
        
        .status-badge {
            padding: 4px 10px !important;
            font-size: 0.65rem !important; /* Lebih kecil */
            border-radius: 10px !important;
            width: auto;
            display: inline-block;
        }
        
        /* Course Title */
        .course-main-title {
            font-size: 0.95rem !important; /* Turun dari 1.1rem */
            line-height: 1.2 !important;
            text-align: center;
            -webkit-line-clamp: 2 !important;
        }
        
        /* Progress Section */
        .progress-section {
            padding: 10px !important;
            border-radius: 6px !important;
        }
        
        .progress-info {
            flex-direction: column !important;
            gap: 5px !important;
            margin-bottom: 8px !important;
            font-size: 0.75rem !important;
            text-align: center;
        }
        
        .progress-info span:first-child {
            font-size: 0.7rem !important;
        }
        
        .progress-bar {
            height: 6px !important;
            border-radius: 3px !important;
            margin: 4px 0;
        }
        
        /* Action Button */
        .btn-view-course-blue {
            padding: 8px 15px !important;
            font-size: 0.8rem !important; /* Turun dari 0.9rem */
            gap: 6px !important;
            border-radius: 5px !important;
        }
        
        /* Empty State */
        .kursus-saya-container > div > div:last-child {
            padding: 20px 10px !important;
        }
        
        .kursus-saya-container i.fa-book-open {
            font-size: 2rem !important;
        }
        
        .kursus-saya-container h4 {
            font-size: 1.2rem !important;
        }
        
        .kursus-saya-container p {
            font-size: 0.9rem !important;
        }
        
        .kursus-saya-container a[style*="background: #1e3c72"] {
            padding: 6px 16px !important;
            font-size: 0.9rem !important;
        }
        
        /* Nonaktifkan efek hover di mobile */
        .modern-course-card:hover {
            transform: none !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;
        }
        
        .btn-view-course-blue:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    }

    /* ===== OPTIMASI GLOBAL UNTUK MOBILE ===== */
    @media (max-width: 400px) {
        /* Pastikan tidak ada overflow */
        * {
            max-width: 100%;
            box-sizing: border-box;
        }
        
        /* Perbaikan layout filter section */
        .filter-section .d-flex.justify-content-between {
            flex-wrap: wrap;
        }
        
        /* Text alignment untuk mobile */
        .kursus-header,
        .status-container,
        .course-main-title,
        .progress-info {
            text-align: center;
        }
        
        /* Word wrap untuk teks panjang */
        .course-main-title,
        .progress-info span {
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }
        
        /* Progress bar responsif */
        .progress-fill {
            min-width: 5% !important; /* Pastikan progress minimal terlihat */
        }
    }

    /* ===== PERBAIKAN MARGIN DAN PADDING ===== */
    @media (max-width: 400px) {
        /* Tambah padding untuk container utama */
        body .main-content {
            padding: 15px 12px !important; /* Tambah kiri-kanan 12px */
            margin: 10px 10px !important; /* Tambah margin kiri-kanan 8px */
        }
        
        /* Kurangi gap di grid */
        .course-grid {
            margin-left: -2px;
            margin-right: -2px;
            padding: 0 2px;
        }
    }
</style>

<div class="kursus-saya-container">
    <!-- Kursus Header -->
    <div class="kursus-header">
        <h1 class="kursus-title">Kursus Saya</h1>
        <p class="kursus-subtitle">Lihat progress dan lanjutkan belajar</p>
    </div>

    @if(isset($filter))
    <!-- Filter Section -->
    <div class="filter-section">
        <div class="d-flex justify-content-between align-items-center">
            <div class="filter-buttons">
                <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" 
                class="filter-btn {{ $filter == 'all' ? 'active' : '' }}" 
                data-filter="all">All</a>

                <a href="{{ request()->fullUrlWithQuery(['filter' => 'in_progress']) }}" 
                class="filter-btn {{ $filter == 'in_progress' ? 'active' : '' }}" 
                data-filter="in_progress">In progress</a>

                <a href="{{ request()->fullUrlWithQuery(['filter' => 'completed']) }}" 
                class="filter-btn {{ $filter == 'completed' ? 'active' : '' }}" 
                data-filter="finished">Finished</a> <!-- Perhatikan: data-filter harus match dengan status di card -->
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="search-box">
                    <input type="text" placeholder="Search" class="search-input">
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Area -->
    <div class="courses-main-content">
        @if($enrollments->count() > 0)
            <div class="course-grid">
                @foreach($enrollments as $enrollment)
                    @php 
                        $course = $enrollment->kursus;
                        if (!$course) continue;
                        $progressWidth = $enrollment->progress_percentage . '%';
                        $status = $enrollment->progress_percentage == 100 ? 'Finished' : 'In Progress';
                    @endphp
                <div class="modern-course-card" data-status="{{ strtolower(str_replace(' ', '_', $status)) }}" data-title="{{ strtolower($course->judul_kursus) }}">
                    <!-- Course Image -->
                    <div class="course-image-wrapper">
                        @if($course->gambar_kursus)
                            <img src="{{ asset('storage/' . $course->gambar_kursus) }}" 
                                alt="{{ $course->judul_kursus }}" 
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
                    
                </div>

                <div class="course-content-wrapper">
                    <!-- Status Badge -->
                    <div class="status-container">
                        <div class="status-badge status-{{ strtolower(str_replace(' ', '_', $status)) }}">
                            {{ $status }}
                        </div>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="course-main-title">{{ $course->judul_kursus }}</h3>

                    <!-- Progress Section - VERSI PALING AMAN -->
                    <div class="progress-info" style="justify-content: flex-start; flex-direction: column; gap: 6px;">
                        <span>{{ $enrollment->completed_activities }} out of {{ $enrollment->total_activities }} activities completed</span>

                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $enrollment->progress_percentage ?>%"></div>
                        </div>

                        <span>{{ $enrollment->progress_percentage }}% Course Completed</span>
                    </div>

                    <!-- Action Row -->
                    <div class="course-action-row">
                        <a href="{{ route('mitra.kursus.show', $course->id) }}" class="btn-view-course-blue">
                            <i class="fas fa-play-circle"></i>
                            {{ $status == 'Finished' ? 'Lihat Kembali' : 'Lanjutkan Belajar' }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State Simple -->
        <div style="text-align: center; padding: 3px 2px;">
            <i class="fas fa-book-open" style="font-size: 3rem; color: #667eea; margin-bottom: 15px;"></i>
            <h4 style="color: #1e3c72; margin-bottom: 10px;">Belum Ada Kursus</h4>
            <p style="color: #6c757d; margin-bottom: 20px;">Ikuti kursus pertama Anda</p>
            <a href="{{ route('mitra.kursus.index') }}" style="
                background: #1e3c72; 
                color: white; 
                padding: 8px 20px; 
                border-radius: 5px; 
                text-decoration: none;
            ">
                Lihat Kursus
            </a>
        </div>
        @endif
    </div>
</div>

<script>
    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Course image error handling
    document.addEventListener('DOMContentLoaded', function() {
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
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const courseCards = document.querySelectorAll('.modern-course-card');
    const searchInput = document.querySelector('.search-input');

    // Filter by status
    filterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');
            const url = this.getAttribute('href');
            
            // Redirect ke URL filter
            window.location.href = url;
        });
    });

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
        
        courseCards.forEach(card => {
            const cardTitle = card.getAttribute('data-title');
            const cardStatus = card.getAttribute('data-status');
            
            const matchesSearch = cardTitle.includes(searchTerm);
            const matchesFilter = activeFilter === 'all' || cardStatus === activeFilter;

            
            if (matchesSearch && matchesFilter) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
@endsection