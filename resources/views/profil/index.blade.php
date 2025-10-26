{{-- resources/views/profil/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Profil - MOCC BPS')

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="kursus-header">
        <h1 class="kursus-title">Profil Saya</h1>
        <p class="kursus-subtitle">Kelola informasi profil Anda</p>
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
                            <label class="form-label text-muted">Tempat, Tanggal Lahir</label>
                            <p class="fw-semibold">{{ $biodata->tempat_lahir }}, {{ $biodata->tanggal_lahir->format('d F Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Jenis Kelamin</label>
                            <p class="fw-semibold">{{ $biodata->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Alamat</label>
                            <p class="fw-semibold">{{ $biodata->alamat }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">No. Telepon</label>
                            <p class="fw-semibold">{{ $biodata->no_telepon }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Pekerjaan</label>
                            <p class="fw-semibold">{{ $biodata->pekerjaan }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Instansi</label>
                            <p class="fw-semibold">{{ $biodata->instansi }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Pendidikan Terakhir</label>
                            <p class="fw-semibold">{{ $biodata->pendidikan_terakhir }}</p>
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
</style>
@endsection