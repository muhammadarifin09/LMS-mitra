@php
    use Illuminate\Support\Str;
@endphp

@extends('mitra.layouts.app')

@section('title', 'MOOC BPS - Kursus Tersedia')

@section('content')
<style>
    /* ===== GAYA UNTUK NOTIFIKASI ERROR ENROLL CODE ===== */
    .enroll-error-notification {
        background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        border: 2px solid #fc8181;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(252, 129, 129, 0.2);
        animation: slideDown 0.5s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .enroll-error-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .enroll-error-icon {
        background: #e53e3e;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.2rem;
    }

    .enroll-error-title {
        color: #c53030;
        font-weight: 700;
        font-size: 1.2rem;
        margin: 0;
    }

    .enroll-error-body {
        color: #742a2a;
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .enroll-error-code {
        background: #fed7d7;
        border: 1px solid #fc8181;
        border-radius: 6px;
        padding: 8px 12px;
        font-family: monospace;
        font-weight: 600;
        color: #c53030;
        margin: 5px 0 15px 0;
        display: inline-block;
    }

    .enroll-error-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-enroll-retry {
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
        cursor: pointer;
    }

    .btn-enroll-retry:hover {
        background: linear-gradient(135deg, #152c5b, #1e3c72);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
    }

    .btn-enroll-ok {
        background: linear-gradient(135deg, #38a169, #2f855a);
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
        cursor: pointer;
    }

    .btn-enroll-ok:hover {
        background: linear-gradient(135deg, #2f855a, #276749);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3);
    }

    /* ===== GAYA UNTUK NOTIFIKASI SUKSES ===== */
    .enroll-success-notification {
        background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
        border: 2px solid #9ae6b4;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(154, 230, 180, 0.2);
        animation: slideDown 0.5s ease;
    }

    .enroll-success-icon {
        background: #38a169;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.2rem;
    }

    .enroll-success-title {
        color: #276749;
        font-weight: 700;
        font-size: 1.2rem;
        margin: 0;
    }

    /* ===== GAYA UNTUK MODAL (TANPA AUTO-OPEN) ===== */
    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    /* ===== GAYA UNTUK KURSUS (YANG SUDAH ADA) ===== */
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
        display: flex;
        justify-content: center;
        gap: 10px;
        padding-top: 10px;
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
    
    /* ===== RESPONSIVE DESIGN UNTUK MOBILE ===== */
    @media (max-width: 500px) {
        /* Notifikasi Error */
        .enroll-error-notification,
        .enroll-success-notification {
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .enroll-error-header {
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .enroll-error-icon,
        .enroll-success-icon {
            margin-bottom: 10px;
            margin-right: 0;
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
        
        .enroll-error-title,
        .enroll-success-title {
            font-size: 1rem;
        }
        
        .enroll-error-body {
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .enroll-error-actions {
            flex-direction: column;
            width: 100%;
        }
        
        .btn-enroll-retry,
        .btn-enroll-ok {
            width: 100%;
            justify-content: center;
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        /* Header Kursus */
        .main-content {
            padding: 15px 12px !important;
            margin: 10px 10px !important;
        }

        .kursus-header {
            margin-bottom: 20px !important;
            text-align: center;
        }
        
        .kursus-title {
            font-size: 1.5rem !important;
            margin-bottom: 8px !important;
        }
        
        .kursus-subtitle {
            font-size: 0.9rem !important;
            line-height: 1.4;
        }
        
        /* Course Grid - 1 kolom */
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
            height: 120px !important;
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
            padding: 12px !important;
        }
        
        .course-date {
            font-size: 0.7rem !important;
            margin-bottom: 6px !important;
        }
        
        .course-main-title {
            font-size: 0.95rem !important;
            height: 2.4em !important;
            line-height: 1.2 !important;
            margin-bottom: 6px !important;
        }
        
        .course-category {
            font-size: 0.75rem !important;
            margin-bottom: 8px !important;
        }
        
        .course-description {
            font-size: 0.75rem !important;
            line-height: 1.4 !important;
            height: 3.2em !important;
            margin-bottom: 10px !important;
        }
        
        /* Meta Information */
        .course-meta-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 6px !important;
            margin-bottom: 10px !important;
        }
        
        .meta-card {
            padding: 6px !important;
            border-radius: 4px !important;
        }
        
        .meta-value {
            font-size: 0.8rem !important;
            margin-bottom: 1px !important;
        }
        
        .meta-label {
            font-size: 0.65rem !important;
        }
        
        .btn-follow-course, 
        .btn-view-course-white {
            padding: 6px 10px !important;
            font-size: 0.7rem !important;
            flex: 1;
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
            min-width: 120px !important;
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

        body {
            font-size: 14px;
        }
    }

</style>

<!-- ===== NOTIFIKASI ERROR ENROLL CODE (DI ATAS HALAMAN) ===== -->
@if(session('error_type') == 'enroll_code' && session('enroll_course_id'))
    @php
        $errorCourseId = session('enroll_course_id');
        $attemptedCode = session('attempted_code', '');
        $errorCourse = $kursus->firstWhere('id', $errorCourseId);
    @endphp
    
    @if($errorCourse)
    <div class="enroll-error-notification" id="enrollErrorNotification">
        <div class="enroll-error-header">
            <div class="enroll-error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h4 class="enroll-error-title">Kode Enroll Salah!</h4>
        </div>
        
        <div class="enroll-error-body">
            <p>Kode enroll yang Anda masukkan untuk kursus <strong>"{{ $errorCourse->judul_kursus }}"</strong> tidak sesuai.</p>
            
            <!-- @if($attemptedCode)
                <p>Kode yang Anda coba: <span class="enroll-error-code">{{ $attemptedCode }}</span></p>
            @endif -->
            
            <p>Silakan masukkan kembali kode yang benar.</p>
        </div>
        
        <div class="enroll-error-actions">
            <button type="button" 
                    class="btn-enroll-retry" 
                    onclick="openEnrollModal({{ $errorCourseId }})">
                <i class="fas fa-redo me-1"></i>
                Coba Lagi
            </button>
            
            <button type="button" 
                    class="btn-enroll-ok" 
                    onclick="dismissEnrollError()">
                <i class="fas fa-close me-1"></i>
                Tutup
            </button>
        </div>
    </div>
    @endif
@endif

<!-- ===== NOTIFIKASI ERROR LAINNYA ===== -->
@if(session('error') && session('error_type') != 'enroll_code')
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 25px;">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- ===== NOTIFIKASI SUKSES ===== -->
@if(session('success'))
    <div class="enroll-success-notification">
        <div class="enroll-error-header">
            <div class="enroll-success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h4 class="enroll-success-title">Berhasil!</h4>
        </div>
        
        <div class="enroll-error-body">
            <p>{{ session('success') }}</p>
        </div>
        
        <button type="button" 
                class="btn-enroll-ok" 
                onclick="dismissSuccessNotification()">
            <i class="fas fa-check me-1"></i>
            Oke
        </button>
    </div>
@endif

<!-- ===== KURSUS HEADER ===== -->
<div class="kursus-header">
    <h1 class="kursus-title">Kursus Tersedia</h1>
    <p class="kursus-subtitle">Pilih dan ikuti kursus yang sesuai dengan minat Anda</p>
</div>

<!-- ===== COURSE GRID FROM DATABASE ===== -->
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

                            $pakaiEnrollCode = !empty($item->enroll_code);
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
                            {{-- 🔐 JIKA ADA ENROLL CODE → POPUP --}}
                            @if($pakaiEnrollCode)
                                <button type="button"
                                    class="btn-follow-course"
                                    data-bs-toggle="modal"
                                    data-bs-target="#enrollModal{{ $item->id }}">
                                    <i class="fas fa-play-circle"></i>
                                    Ikuti Kursus
                                </button>
                            {{-- 🔓 TANPA ENROLL CODE → LANGSUNG --}}
                            @else
                                <form action="{{ route('mitra.kursus.enroll', $item->id) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-follow-course">
                                        <i class="fas fa-play-circle"></i>
                                        Ikuti kursus
                                    </button>
                                </form>
                            @endif
                        @endif

                        {{-- DETAIL --}}
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🔐 MODAL ENROLL CODE (TANPA AUTO-OPEN) -->
            @if(!empty($item->enroll_code))
            <div class="modal fade" id="enrollModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-key me-2"></i>
                                Masukkan Kode Enroll
                            </h5>
                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    onclick="clearEnrollError()"></button>
                        </div>

                        <form action="{{ route('mitra.kursus.enroll', $item->id) }}" method="POST" id="enrollForm{{ $item->id }}">
                            @csrf
                            <div class="modal-body">
                                <p class="text-muted mb-4">
                                    Kursus ini hanya untuk mitra tertentu.
                                    Masukkan kode enroll yang diberikan.
                                </p>

                                <div class="mb-3">
                                    <label for="enroll_code{{ $item->id }}" class="form-label">
                                        <i class="fas fa-lock me-1"></i>Kode Enroll
                                    </label>
                                    <input type="text"
                                        id="enroll_code{{ $item->id }}"
                                        name="enroll_code"
                                        class="form-control"
                                        placeholder="Contoh: BPS2025"
                                        value="{{ old('enroll_code') }}"
                                        required
                                        autofocus>
                                    <div class="form-text">Masukkan kode dengan benar (case-sensitive)</div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal"
                                        onclick="clearEnrollError()">
                                    <i class="fas fa-times me-1"></i>Batal
                                </button>

                                <button type="submit"
                                        class="btn btn-primary">
                                    <i class="fas fa-check me-1"></i>
                                    Ikuti Kursus
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            @endif
        @endforeach
    </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===== FUNGSI UTAMA =====

// Fungsi untuk membuka modal enroll (hanya ketika tombol "Coba Lagi" ditekan)
function openEnrollModal(courseId) {
    const modalElement = document.querySelector(`#enrollModal${courseId}`);
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Auto-focus ke input field setelah modal terbuka
        setTimeout(() => {
            const inputField = modalElement.querySelector('input[name="enroll_code"]');
            if (inputField) {
                inputField.focus();
                // Kosongkan nilai sebelumnya
                inputField.value = '';
            }
        }, 300);
    }
}

// Fungsi untuk menutup notifikasi error enroll
function dismissEnrollError() {
    const errorNotification = document.querySelector('.enroll-error-notification');
    if (errorNotification) {
        // Animasi fade out
        errorNotification.style.animation = 'slideUp 0.5s ease';
        errorNotification.style.transform = 'translateY(-20px)';
        errorNotification.style.opacity = '0';
        
        // Hapus elemen setelah animasi selesai
        setTimeout(() => {
            errorNotification.remove();
            // Hapus session data dari URL (optional)
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('error');
                url.searchParams.delete('error_type');
                url.searchParams.delete('enroll_course_id');
                window.history.replaceState({}, '', url);
            }
        }, 500);
    }
}

// Fungsi untuk membersihkan error saat modal ditutup
function clearEnrollError() {
    // Hapus notifikasi error jika masih ada
    dismissEnrollError();
}

// Fungsi untuk menutup notifikasi sukses
function dismissSuccessNotification() {
    const successNotification = document.querySelector('.enroll-success-notification');
    if (successNotification) {
        // Animasi fade out
        successNotification.style.animation = 'slideUp 0.5s ease';
        successNotification.style.transform = 'translateY(-20px)';
        successNotification.style.opacity = '0';
        
        // Hapus elemen setelah animasi selesai
        setTimeout(() => {
            successNotification.remove();
        }, 500);
    }
}

// Tambahkan CSS untuk animasi slideUp
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
`;
document.head.appendChild(style);

// ===== EVENT LISTENERS =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('Kursus page loaded - Modal will NOT auto-open');

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

    // Validasi form enroll sebelum submit
    document.querySelectorAll('form[action*="enroll"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const enrollCodeInput = this.querySelector('input[name="enroll_code"]');
            if (enrollCodeInput) {
                const code = enrollCodeInput.value.trim();
                if (!code) {
                    e.preventDefault();
                    // Tambahkan styling error
                    enrollCodeInput.classList.add('is-invalid');
                    
                    // Buat pesan error
                    if (!enrollCodeInput.nextElementSibling || !enrollCodeInput.nextElementSibling.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'Harap masukkan kode enroll';
                        enrollCodeInput.parentNode.appendChild(errorDiv);
                    }
                    
                    enrollCodeInput.focus();
                }
            }
        });
        
        // Hapus error saat user mulai mengetik
        const input = form.querySelector('input[name="enroll_code"]');
        if (input) {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const errorDiv = this.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv.remove();
                }
            });
        }
    });

    // Reset form saat modal ditutup
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            // Reset form inputs
            const forms = this.querySelectorAll('form');
            forms.forEach(form => {
                form.reset();
                // Hapus kelas invalid
                const invalidInputs = form.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => {
                    input.classList.remove('is-invalid');
                });
                
                // Hapus invalid feedback
                const invalidFeedbacks = form.querySelectorAll('.invalid-feedback');
                invalidFeedbacks.forEach(feedback => {
                    feedback.remove();
                });
            });
        });
    });

    // Tutup notifikasi dengan tombol Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dismissEnrollError();
            dismissSuccessNotification();
        }
    });

    // Auto focus ke input saat modal terbuka
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const input = this.querySelector('input[name="enroll_code"]');
            if (input) {
                setTimeout(() => {
                    input.focus();
                }, 100);
            }
        });
    });
});

// Logging untuk debugging
document.querySelectorAll('.btn-follow-course').forEach(button => {
    button.addEventListener('click', function(e) {
        if (!this.disabled) {
            const courseTitle = this.closest('.modern-course-card')?.querySelector('.course-main-title')?.textContent;
            console.log(`Mengikuti kursus: ${courseTitle || 'Unknown'}`);
        }
    });
});

document.querySelectorAll('.btn-view-course-white').forEach(button => {
    button.addEventListener('click', function(e) {
        const courseTitle = this.closest('.modern-course-card')?.querySelector('.course-main-title')?.textContent;
        console.log(`Membuka modal detail kursus: ${courseTitle || 'Unknown'}`);
    });
});
</script>
@endsection