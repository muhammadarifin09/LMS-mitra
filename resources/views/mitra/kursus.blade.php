@extends('mitra.layouts.app')

@section('title', 'MOCC BPS - Kursus Tersedia')

@section('content')
<style>
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

    /* Modern Course Card Design */
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

    /* Tombol Ikuti Kursus - Warna biru */
    .btn-follow-course {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        white-space: nowrap;
        cursor: pointer;
    }

    /* Tombol Lihat Kursus - Warna putih dengan border biru */
    .btn-view-course-white {
        background: white;
        color: #1e3c72;
        border: 2px solid #1e3c72;
        padding: 6px 14px;
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
        .course-grid {
            grid-template-columns: 1fr;
        }
        
        .kursus-title {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 576px) {
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
        
        .course-action-row {
            flex-direction: column;
            gap: 10px;
        }
        
        .btn-follow-course, .btn-view-course-white {
            width: 100%;
            justify-content: center;
        }
    }
</style>

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
                        <a href="{{ route('mitra.kursus', $item->id) }}" class="btn-view-course-white">
                            <i class="fas fa-eye"></i>
                            Lihat Kursus
                        </a>
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
                    <i class="fas fa-code"></i>
                </div>
                <div class="course-badge level-badge level-pemula">Pemula</div>
            </div>
            <div class="course-content-wrapper">
                <div class="course-date">01 Nov 2025</div>
                <h3 class="course-main-title">Kursus PYTHON</h3>
                <div class="course-category">Python Engineer</div>
                <p class="course-description">
                    Kursus Python tersedia secara online dan tatap muka dari berbagai platform seperti Udemy, DataCamp, Superprof, dan Agenta...
                </p>
                <div class="course-meta-grid">
                    <div class="meta-card">
                        <div class="meta-value">17h</div>
                        <div class="meta-label">Durasi</div>
                    </div>
                    <div class="meta-card">
                        <div class="meta-value">3</div>
                        <div class="meta-label">Peserta</div>
                    </div>
                </div>
                <div class="course-action-row">
                    <button type="button" class="btn-follow-course">
                        <i class="fas fa-play-circle"></i>
                        Ikuti Kursus
                    </button>
                    <a href="#" class="btn-view-course-white">
                        <i class="fas fa-eye"></i>
                        Lihat Kursus
                    </a>
                </div>
            </div>
        </div>

        <!-- Additional demo courses -->
        <div class="modern-course-card">
            <div class="course-image-wrapper">
                <div class="course-main-image" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                    <i class="fas fa-terminal"></i>
                </div>
                <div class="course-badge level-badge level-menengah">Menengah</div>
            </div>
            <div class="course-content-wrapper">
                <div class="course-date">30 Oct 2025</div>
                <h3 class="course-main-title">Pengenalan Linux</h3>
                <div class="course-category">Tim Digital</div>
                <p class="course-description">
                    Associate Data Scientist menggunakan salah satu program pelatihan Vocational School Graduate Academy Digital Talent...
                </p>
                <div class="course-meta-grid">
                    <div class="meta-card">
                        <div class="meta-value">8h</div>
                        <div class="meta-label">Durasi</div>
                    </div>
                    <div class="meta-card">
                        <div class="meta-value">3</div>
                        <div class="meta-label">Peserta</div>
                    </div>
                </div>
                <div class="course-action-row">
                    <button type="button" class="btn-follow-course">
                        <i class="fas fa-play-circle"></i>
                        Ikuti Kursus
                    </button>
                    <a href="#" class="btn-view-course-white">
                        <i class="fas fa-eye"></i>
                        Lihat Kursus
                    </a>
                </div>
            </div>
        </div>

        <div class="modern-course-card">
            <div class="course-image-wrapper">
                <div class="course-main-image" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="course-badge level-badge level-lanjutan">Lanjutan</div>
            </div>
            <div class="course-content-wrapper">
                <div class="course-date">30 Oct 2025</div>
                <h3 class="course-main-title">Data Sains</h3>
                <div class="course-category">Tim Digital</div>
                <p class="course-description">
                    Sangat bagus untuk pemula yang ingin mempelajari data science dari dasar hingga tingkat lanjut.
                </p>
                <div class="course-meta-grid">
                    <div class="meta-card">
                        <div class="meta-value">30h</div>
                        <div class="meta-label">Durasi</div>
                    </div>
                    <div class="meta-card">
                        <div class="meta-value">5</div>
                        <div class="meta-label">Peserta</div>
                    </div>
                </div>
                <div class="course-action-row">
                    <button type="button" class="btn-follow-course">
                        <i class="fas fa-play-circle"></i>
                        Ikuti Kursus
                    </button>
                    <a href="#" class="btn-view-course-white">
                        <i class="fas fa-eye"></i>
                        Lihat Kursus
                    </a>
                </div>
            </div>
        </div>

        <div class="modern-course-card">
            <div class="course-image-wrapper">
                <div class="course-main-image" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="course-badge level-badge level-menengah">Menengah</div>
            </div>
            <div class="course-content-wrapper">
                <div class="course-date">30 Oct 2025</div>
                <h3 class="course-main-title">Artificial Intelligence</h3>
                <div class="course-category">BPS Talasarabilah</div>
                <p class="course-description">
                    Associate Data Scientist menggunakan salah satu program pelatihan Vocational School Graduate Academy Digital Talent...
                </p>
                <div class="course-meta-grid">
                    <div class="meta-card">
                        <div class="meta-value">8h</div>
                        <div class="meta-label">Durasi</div>
                    </div>
                    <div class="meta-card">
                        <div class="meta-value">4</div>
                        <div class="meta-label">Peserta</div>
                    </div>
                </div>
                <div class="course-action-row">
                    <button type="button" class="btn-follow-course">
                        <i class="fas fa-play-circle"></i>
                        Ikuti Kursus
                    </button>
                    <a href="#" class="btn-view-course-white">
                        <i class="fas fa-eye"></i>
                        Lihat Kursus
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Copyright -->


<script>
    // JavaScript khusus untuk halaman kursus
    document.addEventListener('DOMContentLoaded', function() {
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

        // Course button interactions
        document.querySelectorAll('.btn-follow-course').forEach(button => {
            button.addEventListener('click', function(e) {
                const courseTitle = this.closest('.modern-course-card').querySelector('.course-main-title').textContent;
                console.log(`Mengikuti kursus: ${courseTitle}`);
            });
        });

        // View course button interactions
        document.querySelectorAll('.btn-view-course-white').forEach(button => {
            button.addEventListener('click', function(e) {
                const courseTitle = this.closest('.modern-course-card').querySelector('.course-main-title').textContent;
                console.log(`Melihat detail kursus: ${courseTitle}`);
            });
        });
    });
</script>
@endsection