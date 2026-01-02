@php
    use Illuminate\Support\Str;
@endphp


@extends('mitra.layouts.app')

@section('title', 'MOOC BPS - Kursus Tersedia')

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
        margin-top: auto;
        width: 100%;
        min-width: 0;
        display: flex; /* Tambahkan ini */
        justify-content: center; /* Untuk tengah horizontal */
        gap: 10px; /* Jarak antara tombol */
        padding-top: 10px; /* Jarak dari konten di atas */
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
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        white-space: nowrap;
        cursor: pointer;
    }

    /* Modal Styles */
    .modal-image-container {
        text-align: center;
    }

    .modal-course-image {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        object-fit: cover;
    }

    .modal-image-placeholder {
        height: 200px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }

    .info-item {
        margin-bottom: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .info-item strong {
        color: #1e3c72;
        min-width: 150px;
        display: inline-block;
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

    /* Empty State */
    .empty-state-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 300px;
        text-align: center;
        padding: 40px 20px;
        width: 100%;
    }
    
    .kursus-saya-container > div > div:last-child {
        padding: 20px 10px !important;
    }
    
    .kursus-saya-container i.fa-book-open {
        font-size: 2rem !important;
        color: #667eea !important;
        margin-bottom: 15px !important;
    }
    
    .kursus-saya-container h4 {
        font-size: 1.2rem !important;
        color: #1e3c72 !important;
        margin-bottom: 10px !important;
    }
    
    .kursus-saya-container p {
        font-size: 0.9rem !important;
        color: #6c757d !important;
        margin-bottom: 20px !important;
    }
    
    .kursus-saya-container a.btn-empty-state {
        background: #1e3c72 !important;
        color: white !important;
        padding: 8px 20px !important;
        border-radius: 5px !important;
        text-decoration: none !important;
        font-size: 0.9rem !important;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .kursus-saya-container a.btn-empty-state:hover {
        background: #152c5b !important;
        transform: translateY(-2px);
    }

    /* ===== RESPONSIVE DESIGN UNTUK MOBILE KECIL (≤500px) ===== */
    @media (max-width: 500px) {
        /* Header Kursus */
        .main-content {
            padding: 15px 12px !important; /* Tambah kiri-kanan 12px */
            margin: 10px 10px !important; /* Tambah margin kiri-kanan 8px */
        }

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
        
        /* Course Grid - 1 kolom dengan padding lebih kecil */
        .course-grid {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
            margin-bottom: 20px !important;
        }
        
        /* Course Card */
        .modern-course-card {
            border-radius: 10px !important;
        }
        
        /* Course Image */
        .course-image-wrapper {
            height: 120px !important; /* Lebih kecil dari 160px */
        }
        
        .course-badge {
            font-size: 0.6rem !important;
            padding: 3px 6px !important;
            top: 8px !important;
            right: 8px !important;
            border-radius: 10px !important;
        }
        
        /* Course Content */
        .course-content-wrapper {
            padding: 12px !important; /* Lebih kecil dari 20px */
        }
        
        .course-date {
            font-size: 0.7rem !important;
            margin-bottom: 6px !important;
        }
        
        .course-main-title {
            font-size: 0.95rem !important; /* Turun dari 1.1rem */
            height: 2.4em !important; /* Sesuaikan tinggi */
            line-height: 1.2 !important;
            margin-bottom: 6px !important;
        }
        
        .course-category {
            font-size: 0.75rem !important;
            margin-bottom: 8px !important;
        }
        
        .course-description {
            font-size: 0.75rem !important; /* Turun dari 0.8rem */
            line-height: 1.4 !important;
            height: 3.2em !important; /* Sesuaikan tinggi */
            margin-bottom: 10px !important;
        }
        
        /* Meta Information */
        .course-meta-grid {
            grid-template-columns: repeat(2, 1fr) !important; /* Tetap 2 kolom */
            gap: 6px !important;
            margin-bottom: 10px !important;
        }
        
        .meta-card {
            padding: 6px !important;
            border-radius: 4px !important;
        }
        
        .meta-value {
            font-size: 0.8rem !important; /* Turun dari 0.9rem */
            margin-bottom: 1px !important;
        }
        
        .meta-label {
            font-size: 0.65rem !important; /* Turun dari 0.7rem */
        }
        
        .btn-follow-course, 
        .btn-view-course-white {
            padding: 6px 10px !important;
            font-size: 0.7rem !important; /* Lebih kecil */
            flex: 1; /* Membagi ruang sama rata */
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }
        
        /* Modal Responsive */
        .modal-dialog.modal-lg {
            margin: 10px !important;
            max-width: calc(100% - 20px) !important;
        }
        
        .modal-header {
            padding: 10px 12px !important;
            margin: 0 10px !important;
        }
        
        .modal-title {
            font-size: 0.9rem !important;
        }
        
        .modal-body {
            padding: 12px !important;
            margin: 0 10px !important;
            font-size: 0.8rem;
        }
        
        .info-item {
            padding: 6px 0 !important;
            margin-bottom: 8px !important;
        }
        
        .info-item strong {
            font-size: 0.8rem;
            min-width: 120px !important; /* Lebih kecil */
        }
        
        /* Card di modal */
        .card-body {
            padding: 10px !important;
            font-size: 0.8rem;
        }
        
        /* List di modal */
        ul {
            padding-left: 20px !important;
            margin-bottom: 0;
        }
        
        li {
            font-size: 0.8rem;
            margin-bottom: 4px;
        }
        
        /* Modal footer */
        .modal-footer {
            padding: 10px !important;
            margin: 0 10px !important;
        }
        
        .modal-footer .btn {
            padding: 6px 12px !important;
            font-size: 0.8rem !important;
        }
        
        /* Level badges di modal */
        .badge {
            font-size: 0.7rem !important;
            padding: 4px 8px !important;
        }
        
        /* Nonaktifkan efek hover di mobile */
        .modern-course-card:hover {
            transform: none !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;
        }

        /* Ukuran font dasar untuk mobile */
        body {
            font-size: 14px;
        }
        
        /* Headers di modal */
        h5, h6 {
            font-size: 0.9rem;
        }
        
        /* Text di card */
        .card-text {
            font-size: 0.8rem;
            line-height: 1.4;
        }
    }

</style>

<!-- Kursus Header -->
<div class="kursus-header">
    <h1 class="kursus-title">Kursus Tersedia</h1>
    <p class="kursus-subtitle">Pilih dan ikuti kursus yang sesuai dengan minat Anda</p>
</div>

<!-- Course Grid from Database -->
@if(isset($kursus) && $kursus->count() > 0)
    <div class="course-grid">
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
                        @if($item->kategori == 'pemula') level-pemula
                        @elseif($item->kategori == 'menengah') level-menengah
                        @else level-lanjutan @endif">
                        {{ $item->kategori }}
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
                        {{ $item->pelaksana }}
                    </div>

                    <!-- Description -->
                    <p class="course-description">
                        {{ Str::limit($item->deskripsi_kursus, 120) }}
                    </p>

                    <!-- Meta Information -->
                    <div class="course-meta-grid">
                        <div class="meta-card">
                            <div class="meta-value">{{ $item->durasi_jam }} JP</div>
                            <div class="meta-label">Jam Pelajaran</div>
                        </div>
                        <div class="meta-card">
                            <div class="meta-value">{{ $item->kuota_peserta }}</div>
                            <div class="meta-label">Kuota Peserta</div>
                        </div>
                    </div>

                    <!-- Action Row -->
                    <div class="course-action-row">
                     @php
                        $sudahIkut = $item->enrollments
                            ->where('user_id', auth()->id())
                            ->count() > 0;
                    @endphp


                        {{-- PRIORITAS 1: SUDAH IKUT --}}
                        @if($sudahIkut)
                            <a href="{{ route('mitra.kursus.saya') }}" class="btn-follow-course">
                                <i class="fas fa-arrow-right"></i>
                                Lanjutkan
                            </a>

                        {{-- PRIORITAS 2: KUOTA PENUH --}}
                        @elseif($item->isPenuh())
                            <button class="btn-follow-course" disabled
                                style="background:#adb5bd; cursor:not-allowed;">
                                <i class="fas fa-lock"></i>
                                Kuota Penuh
                            </button>

                        {{-- PRIORITAS 3: BISA DAFTAR --}}
                        @else
                            <form action="{{ route('mitra.kursus.enroll', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn-follow-course">
                                    <i class="fas fa-play-circle"></i>
                                    Ikuti Kursus
                                </button>
                            </form>
                        @endif

                        {{-- TOMBOL DETAIL (TETAP ADA) --}}
                        <button type="button"
                            class="btn-view-course-white"
                            data-bs-toggle="modal"
                            data-bs-target="#detailModal{{ $item->id }}">
                            <i class="fas fa-eye"></i>
                            Lihat Kursus
                        </button>
                    </div>

                </div>
            </div>

            <!-- Modal untuk Detail Kursus -->
            <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $item->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" style="margin-right: 20px; margin-left: 20px;">
                            <h5 class="modal-title" id="detailModalLabel{{ $item->id }}">
                                <i class="fas fa-info-circle me-2"></i>
                                Detail Kursus: {{ $item->judul_kursus }}
                            </h5>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                            </button>
                            <!-- <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button> -->
                        </div>
                        <div class="modal-body" style="margin-right: 20px; margin-left: 20px;">
                            <!-- Gambar Kursus jika ada -->
                            @if($item->gambar_kursus)
                            <div class="modal-image-container mb-4">
                                <img src="{{ asset('storage/' . $item->gambar_kursus) }}" 
                                    alt="{{ $item->judul_kursus }}" 
                                    class="modal-course-image"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="modal-image-placeholder" style="display: none;">
                                    <i class="fas fa-book-open"></i>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Informasi Utama -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong><i class="fas fa-user-tie me-2"></i>Pelaksana:</strong>
                                        <span>{{ $item->pelaksana }}</span>
                                    </div>
                                    <div class="info-item">
                                        <strong><i class="fas fa-clock me-2"></i>JP:</strong>
                                        <span>{{ $item->durasi_jam }} JP</span>
                                    </div>
                                    <div class="info-item">
                                        <strong><i class="fas fa-users me-2"></i>Peserta:</strong>
                                        <span>{{ $item->peserta_terdaftar }} / 
                                            @if($item->kuota_peserta)
                                                {{ $item->kuota_peserta }}
                                            @else
                                                Tidak Terbatas
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong><i class="fas fa-chart-line me-2"></i>Kategori:</strong>
                                        <span>
                                            @if($item->kategori == 'pemula')
                                                <span class="badge bg-primary">Pemula</span>
                                            @elseif($item->kategori == 'menengah')
                                                <span class="badge bg-warning">Menengah</span>
                                            @else
                                                <span class="badge bg-danger">Lanjutan</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <strong><i class="fas fa-toggle-on me-2"></i>Status:</strong>
                                        <span>
                                            @if($item->status == 'aktif')
                                                <span class="badge bg-success">Aktif</span>
                                            @elseif($item->status == 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @else
                                                <span class="badge bg-danger">Nonaktif</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if($item->tanggal_mulai && $item->tanggal_selesai)
                                    <div class="info-item">
                                        <strong><i class="fas fa-calendar me-2"></i>Periode:</strong>
                                        <span>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Deskripsi Kursus -->
                            <div class="course-info mb-4">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-align-left me-2"></i>Deskripsi Kursus:
                                </h6>
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text">{{ $item->deskripsi_kursus }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Tambahan -->
                            <div class="row">
                                @if($item->output_pelatihan)
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="fas fa-bullseye me-2"></i>Output Pelatihan:
                                    </h6>
                                    <div class="card">
                                        <div class="card-body">
                                            <ul class="mb-0 ps-3">
                                                @php
                                                    $outputs = array_filter(array_map('trim', explode("\n", $item->output_pelatihan)));
                                                @endphp
                                                @foreach($outputs as $output)
                                                    <li>{{ $output }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($item->persyaratan)
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="fas fa-clipboard-list me-2"></i>Persyaratan:
                                    </h6>
                                    <div class="card">
                                        <div class="card-body">
                                            <ul class="mb-0 ps-3">
                                                @php
                                                    $outputs = array_filter(array_map('trim', explode("\n", $item->persyaratan)));
                                                @endphp
                                                @foreach($outputs as $output)
                                                    <li>{{ $output }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if($item->fasilitas)
                            <div class="mb-3">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-gift me-2"></i>Fasilitas:
                                </h6>
                                <div class="card">
                                    <div class="card-body">
                                        <ul class="mb-0 ps-3">
                                            @php
                                                $outputs = array_filter(array_map('trim', explode("\n", $item->fasilitas)));
                                            @endphp
                                            @foreach($outputs as $output)
                                                <li>{{ $output }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                        
                         @if($item->isPenuh())
                            <button type="button" class="btn btn-secondary" disabled>
                                <i class="fas fa-lock me-2"></i>Kuota Sudah Penuh
                            </button>
                        @else
                    
                        @endif

                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @else
        <!-- Empty State Simple -->
        <div style="text-align: center;">
            <i class="fas fa-book-open" style="font-size: 3rem; color: #667eea; margin-bottom: 15px;"></i>
            <h4 style="color: #1e3c72; margin-bottom: 10px;">Tidak Ada Kursus</h4>
            <p style="color: #6c757d; margin-bottom: 20px;">Lihat kursus pertama Anda</p>
            <a href="{{ route('mitra.kursus.saya') }}" style="
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

<!-- Copyright -->
<!-- Di file layout app.blade.php -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

        // View course button interactions - MODAL
        document.querySelectorAll('.btn-view-course-white').forEach(button => {
            button.addEventListener('click', function(e) {
                const courseTitle = this.closest('.modern-course-card').querySelector('.course-main-title').textContent;
                console.log(`Membuka modal detail kursus: ${courseTitle}`);
            });
        });

        // Handle modal image errors
        document.querySelectorAll('.modal-course-image[src]').forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
                const placeholder = this.nextElementSibling;
                if (placeholder && placeholder.classList.contains('modal-image-placeholder')) {
                    placeholder.style.display = 'flex';
                }
            });
        });
    });
</script>
@endsection