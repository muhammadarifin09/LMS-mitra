@extends('mitra.layouts.app')

@section('title', 'Edit Profil - MOOC BPS')

<style>
    /* Header Edit Profil - FIXED: RATA KIRI seperti halaman lain */
    .edit-profil-header {
        margin-bottom: 30px;
        text-align: left !important; /* PERUBAHAN: dari center ke left */
    }

    .edit-profil-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 10px;
        text-align: left !important; /* PERUBAHAN: dari default ke left */
    }

    .edit-profil-subtitle {
        font-size: 1.1rem;
        color: #5a6c7d;
        text-align: left !important; /* PERUBAHAN: dari default ke left */
    }

    /* PERUBAHAN: Container form lebih lebar */
    .row.justify-content-center {
        margin-left: -10px !important;
        margin-right: -10px !important;
    }
    
    .col-lg-8, .col-md-10 {
        padding-left: 5px !important;
        padding-right: 5px !important;
        max-width: 100% !important;
        width: 100% !important;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        width: 100% !important; /* PERUBAHAN: Full width */
        margin: 0 !important; /* PERUBAHAN: Hilangkan margin */
    }

    /* PERUBAHAN: Card body padding lebih kecil untuk lebih lebar */
    .card-body.p-4 {
        padding: 30px 25px !important; /* Kurangi horizontal padding */
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #5a6c7d;
        margin-bottom: 5px;
    }

    /* PERUBAHAN: Form control lebih lebar */
    .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 10px 12px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        color: #1e3c72;
        width: 100% !important; /* Pastikan full width */
    }

    .form-control:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.15);
    }

    .btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 20px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(30, 60, 114, 0.3);
    }

    .btn-outline-secondary {
        border: 1px solid #6c757d;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    /* Foto Profil Preview */
    #profile-preview {
        transition: all 0.3s ease;
        border: 3
        px solid #1e3c72;
    }

    .text-muted {
        font-size: 0.8rem;
        color: #5a6c7d !important;
    }

    .text-danger {
        font-size: 0.8rem;
    }

    /* Required star */
    .text-danger[title="required"] {
        color: #e74c3c;
        margin-left: 2px;
    }

    /* ===== RESPONSIVE DESIGN UNTUK MOBILE (≤400px) ===== */
    @media (max-width: 400px) {                      
        /* Container utama - SAMA DENGAN HALAMAN PROFIL */
        .main-content {
            padding: 15px 12px !important;
            margin: 10px 10px !important;
            border-radius: 15px !important;
        }
        
        /* Pastikan styling dalam .main-content TIDAK keluar */
        .main-content .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        
        /* Header Edit Profil - konsisten dengan halaman profil */
        .edit-profil-header {
            margin-bottom: 20px !important;
            padding: 0 !important;
            text-align: center !important;
        }
        
        .edit-profil-title {
            font-size: 1.5rem !important;
            margin-bottom: 5px !important;
            color: #1e3c72 !important;
            font-weight: 700 !important;
            text-align: center !important;
        }
        
        .edit-profil-subtitle {
            font-size: 0.9rem !important;
            line-height: 1.4 !important;
            color: #5a6c7d !important;
            text-align: center !important;
        }
        
        .col-lg-8, .col-md-10 {
            padding: 0px !important; /* Sedikit padding */
            width: 100% !important;
        }
        
        /* Cards - FIX: Hilangkan margin-left yang membuat tidak center */
        .card {
            border-radius: 12px !important;
            box-shadow: 0 4px 4px rgba(0,0,0,0.08) !important;
            border: 1px solid #e9ecef !important;
            max-width: 100% !important;
            overflow: hidden !important;
            margin: 0 auto 15px auto !important; /* Center dengan auto */
            width: calc(100% - 4px) !important; /* Sedikit kurangi untuk padding */
        }
        
        /* Card body */
        .card-body.p-4 {
            padding: 20px 5px !important;
        }
        
        /* Foto Profil Section */
        #profile-preview {
            width: 120px !important;
            height: 120px !important;
            border-width: 3px !important;
            border-color: #1e3c72 !important;
        }
        
        /* Tombol kamera kecil */
        .btn-primary.btn-sm.rounded-circle {
            width: 30px !important;
            height: 30px !important;
            bottom: 5px !important;
            right: 5px !important;
            font-size: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 50% !important;
            border: 2px solid white !important;
        }
        
        /* Form controls */
        .form-control {
            padding: 10px 12px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
            width: 100% !important;
        }
        
        /* Form labels */
        .form-label {
            font-size: 0.85rem !important;
            margin-bottom: 5px !important;
            color: #5a6c7d !important;
            font-weight: 500 !important;
            display: block !important;
        }
        
        .d-flex.justify-content-between {
            gap: 15px !important;
            align-items: stretch !important;
            width: 100% !important;
            padding: 0 5px !important;
        }
        
        .d-flex.justify-content-between .btn {
            width: 100% !important;
            padding: 12px 15px !important;
            font-size: 0.9rem !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
        }
    }
</style>

@section('content')
    <!-- Header -->
    <div class="edit-profil-header">
        <h1 class="edit-profil-title">Edit Profil</h1>
        <p class="edit-profil-subtitle">Perbarui informasi profil Anda</p>
    </div>

    <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Tambahkan field hidden untuk username_sobat -->
                    <input type="hidden" name="username_sobat" value="{{ old('username_sobat', $biodata->username_sobat ?? $user->username ?? '') }}">

                    <!-- Foto Profil Section -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <div class="position-relative d-inline-block mb-2">
                                <img src="{{ $biodata && $biodata->foto_profil ? asset('storage/' . $biodata->foto_profil) : asset('img/default-avatar.png') }}" 
                                     alt="Foto Profil" 
                                     class="rounded-circle shadow-sm"
                                     id="profile-preview"
                                     style="width: 180px; height: 180px; object-fit: cover; border: 2px solid #1e3c72;">
                                <label for="foto_profil" class="btn btn-primary btn-sm rounded-circle position-absolute" 
                                       style="bottom: 0; right: 0; width: 24px; height: 24px; cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-camera" style="font-size: 0.6rem;"></i>
                                </label>
                                <input type="file" name="foto_profil" id="foto_profil" class="d-none" accept="image/*">
                            </div>
                            <div class="mt-1">
                                <small class="text-muted">Klik ikon kamera untuk mengubah foto</small><br>
                                <small class="text-muted">Format: JPG, PNG, GIF (Maks: 2MB)</small>
                            </div>
                            @error('foto_profil')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Informasi Pribadi -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" 
                                   value="{{ old('nama_lengkap', $biodata->nama_lengkap ?? '') }}" 
                                   placeholder="Masukkan nama lengkap" required>
                            @error('nama_lengkap')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->username }}" disabled>
                            <small class="text-muted">Email tidak dapat diubah</small>
                        </div>

                        <!-- Tambahkan field untuk username_sobat jika ingin bisa diubah -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username Sobat <span class="text-danger">*</span></label>
                            <input type="text" name="username_sobat" class="form-control" 
                                   value="{{ old('username_sobat', $biodata->username_sobat ?? $user->username ?? '') }}" 
                                   placeholder="Masukkan username" required>
                            @error('username_sobat')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                            <input type="text" name="kecamatan" class="form-control" 
                                   value="{{ old('kecamatan', $biodata->kecamatan ?? '') }}" 
                                   placeholder="Masukkan kecamatan" required>
                            @error('kecamatan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                            <input type="text" name="desa" class="form-control" 
                                   value="{{ old('desa', $biodata->desa ?? '') }}" 
                                   placeholder="Masukkan desa/kelurahan" required>
                            @error('desa')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="no_telepon" class="form-control" 
                                   value="{{ old('no_telepon', $biodata->no_telepon ?? '') }}" 
                                   placeholder="Contoh: 081234567890" required>
                            @error('no_telepon')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" rows="3" 
                                      placeholder="Masukkan alamat lengkap" required>{{ old('alamat', $biodata->alamat ?? '') }}</textarea>
                            @error('alamat')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('profil.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('foto_profil');
    const profilePreview = document.getElementById('profile-preview');
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validasi ukuran file (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Debug: Cek nilai tanggal lahir
    const tanggalLahirInput = document.querySelector('input[name="tanggal_lahir"]');
    console.log('Tanggal Lahir Value:', tanggalLahirInput.value);
});
</script>
@endsection