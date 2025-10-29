{{-- resources/views/profil/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Profil - MOCC BPS')

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="kursus-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="kursus-title" style="color: white;">Profil Saya</h1>
                <p class="kursus-subtitle" style="color: white;">Kelola informasi profil Anda</p>
            </div>
            <a href="{{ route('mitra.dashboard') }}" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
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
    height: 60px;
    width: 1.5px;
    background: linear-gradient(to bottom, rgba(42, 82, 152, 0.7));
    border-radius: 2px;
}

.nav-brand span {
    color: #2a5298;
}

/* Logo MOCC BPS sebagai gambar */
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

/* Perbesar ukuran teks navigasi */
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

/* User Profile & Avatar Styles - PERBAIKAN */
.user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 15px;
    border-radius: 25px;
    background: rgba(30, 60, 114, 0.05);
    transition: all 0.3s ease;
    margin-left: 20px;
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

    .kursus-header .d-flex {
        flex-direction: column;
        gap: 15px;
        text-align: center;
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

    .btn-outline-light {
        width: 100%;
    }
}
</style>

@section('navbar')
<!-- Navigation - Sticky -->
<nav class="main-nav">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <!-- Logo MOCC BPS sebagai gambar -->
            <a href="#" class="nav-brand">
                <img src="{{ asset('img/Logo_E-Learning.png') }}" alt="MOCC BPS Logo" class="logo-image">
            </a>
            <div class="nav-menu ms-5">
                <a href="/beranda" class="nav-item">Beranda</a>
                <a href="/dashboard" class="nav-item">Dashboard</a>
                <a href="/kursus" class="nav-item">Kursus</a>
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
            
            <!-- User Profile dengan Foto - VERSI DIPERBAIKI -->
            <div class="user-profile">
                <div class="user-avatar">
                    @auth
                        @php
                            $user = Auth::user();
                            $biodata = $user->biodata ?? null;
                            $initials = strtoupper(substr($user->name, 0, 2));
                        @endphp
                        
                        @if($biodata && $biodata->foto_profil)
                            <img src="{{ asset('storage/' . $biodata->foto_profil) }}" 
                                 alt="Foto Profil" 
                                 class="avatar-image"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="avatar-initials" style="display: none;">{{ $initials }}</div>
                        @else
                            <div class="avatar-initials">{{ $initials }}</div>
                        @endif
                    @endauth
                </div>
                <div class="user-info">
                    <div class="user-name">
                        {{ Auth::user()->biodata->nama_lengkap ?? Auth::user()->name }}
                    </div>
                    <div class="user-status">
                        <span class="status-dot"></span>
                        Online
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
@endsection

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