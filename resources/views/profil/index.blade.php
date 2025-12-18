{{-- resources/views/profil/index.blade.php --}}
@extends('mitra.layouts.app')

@section('title', 'Profil - MOCC BPS')

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
</style>
    <!-- Header -->
    <div class="kursus-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="kursus-title">Profil Saya</h2>
                <p class="kursus-subtitle">Kelola informasi profil Anda</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Foto Profil & Info Singkat -->
        <div class="col-md-4 mb-4">
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
                    <div class="d-flex justify-content-between mb-2">
                        <span>Kursus Diikuti:</span>
                        <strong>5</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sertifikat:</span>
                        <strong>3</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Progress Rata-rata:</span>
                        <strong>78%</strong>
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">No. Telepon</label>
                            <p class="fw-semibold">{{ $biodata->no_telepon }}</p>
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

<style>
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

.btn-outline-light {
    border: 2px solid white;
    color: white;
    background: transparent;
    transition: all 0.3s ease;
}

.btn-outline-light:hover {
    background: white;
    color: #1e3c72;
    transform: translateY(-2px);
}
</style>

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