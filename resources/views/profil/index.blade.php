{{-- resources/views/profil/index.blade.php --}}
@extends('mitra.layouts.app')

@section('title', 'Profil - MOOC BPS')

@section('content')

<style>
    /* Profil Header */
    .profil-header {
        margin-bottom: 30px;
    }

    .profil-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 10px;
    }

    .profil-subtitle {
        font-size: 1.1rem;
        color: #5a6c7d;
    }

    .card {
        border-radius: 12px;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
    }

    .fw-semibold {
        font-weight: 600;
        color: #1e3c72;
    }

    /* ===== RESPONSIVE DESIGN UNTUK MOBILE (≤500px) ===== */
    @media (max-width: 500px) {
        /* Layout utama - konsisten dengan halaman lain */
        .main-content {
            padding: 15px 12px !important;
            margin: 10px 10px !important;
            border-radius: 15px !important;
        }
        
        /* Header Section - serasi dengan halaman kursus */
        .profil-header {
            padding: 0 !important;
            text-align: center !important;
        }
        
        .profil-title {
            font-size: 1.5rem !important; /* Sama dengan halaman kursus */
            margin-bottom: 5px !important;
            color: #1e3c72 !important;
            font-weight: 700 !important;
            text-align: center !important;
        }
        
        .profil-subtitle {
            font-size: 0.9rem !important; /* Sama dengan halaman kursus */
            line-height: 1.4 !important;
            color: #5a6c7d !important;
            text-align: center !important;
        }
        
        /* Row layout - satu kolom seperti halaman lain */
        .row {
            margin: 0 !important;
            flex-direction: column !important;
            gap: 15px !important; /* Konsisten dengan gap halaman lain */
        }
        
        .col-md-4, .col-md-8, .col-md-6, .col-12 {
            padding: 0 !important;
            width: 100% !important;
            margin-bottom: 0 !important;
        }
        
        /* Cards - serasi dengan card di halaman kursus */
        .card {
            border-radius: 12px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;
            border: 1px solid #e9ecef !important;
        }
        
        /* Foto Profil Card */
        .card.shadow-sm.border-0 {
            padding: 0 !important;
        }
        
        .card-body.text-center {
            padding: 20px 15px !important;
        }
        
        .card-body.text-center img {
            width: 120px !important;
            height: 120px !important;
            border-width: 3px !important;
            border-color: #1e3c72 !important;
        }
        
        .position-relative.d-inline-block .btn-danger {
            width: 28px !important;
            height: 28px !important;
            padding: 0 !important;
            bottom: 5px !important;
            right: 5px !important;
            font-size: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 50% !important;
        }
        
        /* Profile Info */
        .card-body.text-center h4 {
            font-size: 1.2rem !important;
            margin-bottom: 5px !important;
            color: #1e3c72 !important;
            font-weight: 600 !important;
        }
        
        .card-body.text-center p.text-muted {
            font-size: 0.9rem !important;
            margin-bottom: 15px !important;
            color: #5a6c7d !important;
        }
        
        /* Buttons - serasi dengan tombol di halaman lain */
        .d-grid.gap-2 .btn {
            padding: 12px 15px !important;
            font-size: 0.95rem !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
        }
        
        .d-grid.gap-2 .btn-primary {
            background: linear-gradient(135deg, #1e3c72, #2a5298) !important;
            border: none !important;
        }
        
        .d-grid.gap-2 .btn i {
            margin-right: 8px !important;
            font-size: 0.9rem !important;
        }
        
        /* Statistik Card */
        
        .card.shadow-sm.border-0.mt-3 .card-body {
            padding: 15px !important;
        }
        
        .card.shadow-sm.border-0.mt-3 h6 {
            font-size: 1rem !important;
            text-align: center !important;
            color: #1e3c72 !important;
            font-weight: 600 !important;
        }
        
        .d-flex.justify-content-between {
            font-size: 0.9rem !important;
            padding: 0 5px !important;
            color: #5a6c7d !important;
        }
        
        .d-flex.justify-content-between strong {
            color: #1e3c72 !important;
            font-weight: 600 !important;
        }
        
        /* Detail Profil Card Header */
        .card-header {
            padding: 15px !important;
            background-color: transparent !important;
            border-bottom: 1px solid #e9ecef !important;
        }
        
        .card-header h5 {
            font-size: 1.1rem !important;
            margin-bottom: 0 !important;
            text-align: center !important;
            color: #1e3c72 !important;
            font-weight: 700 !important;
        }
        
        /* Informasi Pribadi Grid */
        .card-body .row {
            margin: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 15px !important;
        }
        
        .col-md-6.mb-3, .col-12.mb-3 {
            padding: 0 !important;
            margin-bottom: 0 !important;
            width: 100% !important;
        }
        
        .form-label.text-muted {
            font-size: 0.85rem !important;
            display: block !important;
            color: #5a6c7d !important;
            font-weight: 500 !important;
        }
        
        .fw-semibold {
            font-size: 0.95rem !important;
            font-weight: 600 !important;
            margin-bottom: 0 !important;
            word-break: break-word !important;
            color: #1e3c72 !important;
            line-height: 1.4 !important;
        }
        
        /* Profil Belum Lengkap Section */
        .text-center.py-4 {
            padding: 20px 15px !important;
        }
        
        .text-center.py-4 i {
            font-size: 2.5rem !important;
            margin-bottom: 15px !important;
            color: #5a6c7d !important;
        }
        
        .text-center.py-4 h5 {
            font-size: 1.1rem !important;
            margin-bottom: 8px !important;
            color: #5a6c7d !important;
        }
        
        .text-center.py-4 p {
            font-size: 0.9rem !important;
            margin-bottom: 15px !important;
            line-height: 1.4 !important;
            color: #5a6c7d !important;
        }
        
        /* Badges - serasi dengan badge di halaman lain */
        .badge {
            font-size: 0.75rem !important;
            padding: 5px 10px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
        }
        
        .badge.bg-primary {
            background: linear-gradient(135deg, #1e3c72, #2a5298) !important;
        }
        
        .badge.bg-success {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
        }
        
        /* Mobile Typography Enhancement - konsisten */
        body {
            font-size: 14px !important;
            line-height: 1.4 !important;
        }
        
        /* Nonaktifkan efek hover di mobile - sama dengan halaman lain */
        .btn:hover {
            transform: none !important;
            box-shadow: none !important;
        }
        
        /* Konsistensi margin/padding */
        .mb-3 {
            margin-bottom: 15px !important;
        }
        
        .mb-4 {
            margin-bottom: 20px !important;
        }

        /* Container khusus untuk konten halaman (tidak termasuk navbar) */
        .main-content {
            max-width: 100% !important;
            overflow: hidden !important;
        }
        
        /* Pastikan elemen dalam .main-content tidak menyebabkan overflow */
        .main-content > * {
            max-width: 100% !important;
        }
        
        /* Perbaikan khusus untuk elemen yang mungkin melebar */
        .card,
        .card-body,
        .card-header,
        .card-footer {
            max-width: 100% !important;
            overflow: hidden !important;
        }

        /* Tombol hapus tetap lingkaran */
        .position-relative.d-inline-block .btn-danger {
            width: 30px !important; /* Lebih besar sedikit */
            height: 30px !important;
            padding: 0 !important;
            border-radius: 50% !important; /* Ini yang bikin lingkaran */
            bottom: 5px !important;
            right: 5px !important;
            font-size: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 30px !important; /* Tambahkan ini */
            min-height: 30px !important; /* Tambahkan ini */
        }
        
        /* Override inline style */
        .position-relative.d-inline-block .btn-danger[style*="bottom: 10px"] {
            bottom: 5px !important;
            right: 5px !important;
        }
    }
</style>
<div>
    <h2 class="profil-title">Profil Saya</h2>
    <p class="profil-subtitle">Kelola informasi profil Anda</p>
</div>

<div class="row">
    <!-- Foto Profil & Info Singkat -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block">
                    <img src="{{ $biodata && $biodata->foto_profil ? asset('storage/' . $biodata->foto_profil) : asset('img/default-avatar.png') }}" 
                            alt="Foto Profil" 
                            class="rounded-circle mb-3"
                            style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #1e3c72;">
                    @if($biodata && $biodata->foto_profil)
                    <form action="{{ route('profil.hapus-foto') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger position-absolute" 
                                style="bottom: 10px; right: 10px;"
                                onclick="return confirm('Hapus foto profil?')">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                    @endif
                </div>
                
                <h4 class="mb-2">{{ $biodata ? $biodata->nama_lengkap : $user->name }}</h4>
                <p class="text-muted mb-3">{{ $biodata ? $biodata->pekerjaan : 'Mitra BPS' }}</p>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('profil.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Profil
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistik Singkat -->
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body">
                <h6 class="card-title mb-3">Aktivitas</h6>

                <div class="d-flex justify-content-between mb-2" style="color: #5a6c7d !important; font-weight: 500 !important;">
                    <span>Kursus Diikuti:</span>
                    <strong>{{ $totalKursus }}</strong>
                </div>

                <div class="d-flex justify-content-between mb-2"  style="color: #5a6c7d !important; font-weight: 500 !important;">
                    <span>Sertifikat:</span>
                    <strong>{{ $totalSertifikat }}</strong>
                </div>

                <div class="d-flex justify-content-between"  style="color: #5a6c7d !important; font-weight: 500 !important;">
                    <span>Progress Rata-rata:</span>
                    <strong>{{ $avgProgress }}%</strong>
                </div>
            </div>
        </div>

    </div>

    <!-- Detail Profil -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Informasi Pribadi</h5>
            </div>
            <div class="card-body">
                @if($biodata)

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nama Lengkap</label>
                            <p class="fw-semibold">{{ $biodata->nama_lengkap }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="fw-semibold">{{ $user->username }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tempat, Tanggal Lahir (Umur)</label>
                            <p class="fw-semibold">{{ $biodata->tempat_tanggal_lahir ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">No. Telepon</label>
                            <p class="fw-semibold">{{ $biodata->no_telepon }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Posisi</label>
                            <p class="fw-semibold">{{ $biodata->posisi ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Posisi Saat Daftar</label>
                            <p class="fw-semibold">{{ $biodata->posisi_daftar ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Jenis Kelamin</label>
                            <p class="fw-semibold">{{ $biodata->jenis_kelamin ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Pendidikan</label>
                            <p class="fw-semibold">{{ $biodata->pendidikan ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Pekerjaan</label>
                            <p class="fw-semibold">{{ $biodata->pekerjaan ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Deskripsi Pekerjaan Lain</label>
                            <p class="fw-semibold">{{ $biodata->deskripsi_pekerjaan_lain ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Provinsi</label>
                            <p class="fw-semibold">{{ $biodata->alamat_prov ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Kabupaten / Kota</label>
                            <p class="fw-semibold">{{ $biodata->alamat_kab ?? '-' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Kecamatan</label>
                            <p class="fw-semibold">{{ $biodata->kecamatan }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Desa/Kelurahan</label>
                            <p class="fw-semibold">{{ $biodata->desa }}</p>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Alamat</label>
                            <p class="fw-semibold">{{ $biodata->alamat }}</p>
                        </div>

                    </div>

                @else

                    <div class="text-center py-4">
                        <i class="fas fa-user-circle fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Profil Belum Lengkap</h5>
                        <p class="text-muted">Lengkapi profil Anda untuk pengalaman yang lebih baik</p>
                        <a href="{{ route('profil.edit') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Lengkapi Profil
                        </a>
                    </div>

                @endif
            </div>

        </div>

        <!-- Informasi Akun -->
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">Informasi Akun</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Username</label>
                        <p class="fw-semibold">{{ $user->username }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Role</label>
                        <p class="fw-semibold">
                            <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Terdaftar Sejak</label>
                        <p class="fw-semibold">{{ $user->created_at->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Status</label>
                        <p class="fw-semibold">
                            <span class="badge bg-success">Aktif</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Nav item active state
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        
        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.getAttribute('href') === currentPath) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
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
    });
</script>
@endsection