@extends('layouts.dashboard')

@section('title', 'Edit Profil - MOCC BPS')

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="kursus-header text-center mb-4">
        <h1 class="kursus-title">Edit Profil</h1>
        <p class="kursus-subtitle">Perbarui informasi profil Anda</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" class="form-control" 
                                       value="{{ old('tempat_lahir', $biodata->tempat_lahir ?? '') }}" 
                                       placeholder="Masukkan tempat lahir" required>
                                @error('tempat_lahir')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control" 
                                       value="{{ old('tanggal_lahir', $biodata ? (\Carbon\Carbon::parse($biodata->tanggal_lahir)->format('Y-m-d')) : '') }}" required>
                                @error('tanggal_lahir')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin', $biodata->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $biodata->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
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

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                                <input type="text" name="pekerjaan" class="form-control" 
                                       value="{{ old('pekerjaan', $biodata->pekerjaan ?? '') }}" 
                                       placeholder="Contoh: PNS, Swasta, Mahasiswa" required>
                                @error('pekerjaan')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Instansi <span class="text-danger">*</span></label>
                                <input type="text" name="instansi" class="form-control" 
                                       value="{{ old('instansi', $biodata->instansi ?? '') }}" 
                                       placeholder="Contoh: BPS, Universitas, Perusahaan" required>
                                @error('instansi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <select name="pendidikan_terakhir" class="form-control" required>
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="SD" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'SD' ? 'selected' : '' }}>SD / Sederajat</option>
                                    <option value="SMP" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'SMP' ? 'selected' : '' }}>SMP / Sederajat</option>
                                    <option value="SMA" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'SMA' ? 'selected' : '' }}>SMA / Sederajat</option>
                                    <option value="D3" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'D3' ? 'selected' : '' }}>Diploma (D3)</option>
                                    <option value="S1" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'S1' ? 'selected' : '' }}>Sarjana (S1)</option>
                                    <option value="S2" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'S2' ? 'selected' : '' }}>Magister (S2)</option>
                                    <option value="S3" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'S3' ? 'selected' : '' }}>Doktor (S3)</option>
                                </select>
                                @error('pendidikan_terakhir')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
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
</div>

<style>
.main-content {
    padding: 20px;
}

.card {
    border-radius: 10px;
    border: none;
}

.card-body {
    padding: 1.5rem;
}

.form-control {
    border-radius: 6px;
    border: 1px solid #ddd;
    padding: 8px 12px;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #1e3c72;
    box-shadow: 0 0 0 0.1rem rgba(30, 60, 114, 0.15);
}

.form-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.btn {
    border-radius: 6px;
    font-weight: 500;
    padding: 8px 16px;
    font-size: 0.85rem;
}

.btn-primary {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2a5298, #1e3c72);
    transform: translateY(-1px);
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

.btn-sm {
    padding: 4px 8px;
    font-size: 0.7rem;
}

.kursus-title {
    color: white;
    font-weight: 600;
    font-size: 1.5rem;
    margin-bottom: 0.3rem;
}

.kursus-subtitle {
    color: white;
    font-size: 0.9rem;
    opacity: 0.9;
}

#profile-preview {
    transition: all 0.3s ease;
}

.text-muted {
    font-size: 0.75rem;
}

.text-danger {
    font-size: 0.75rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .kursus-title {
        font-size: 1.3rem;
    }
    
    .kursus-subtitle {
        font-size: 0.8rem;
    }
    
    .btn {
        padding: 6px 12px;
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .main-content {
        padding: 10px;
    }
    
    .card-body {
        padding: 0.8rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 8px;
    }
    
    .btn {
        width: 100%;
    }
    
    .row > .col-md-6 {
        margin-bottom: 0.8rem;
    }
}
</style>

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