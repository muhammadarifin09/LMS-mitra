@extends('layouts.dashboard')

@section('title', 'Edit Profil - MOCC BPS')

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="kursus-header">
        <h1 class="kursus-title">Edit Profil</h1>
        <p class="kursus-subtitle">Perbarui informasi profil Anda</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Foto Profil -->
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <div class="position-relative d-inline-block">
                                    <img src="{{ $biodata && $biodata->foto_profil ? asset('storage/' . $biodata->foto_profil) : asset('img/default-avatar.png') }}" 
                                         alt="Foto Profil" 
                                         class="rounded-circle mb-3"
                                         style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #1e3c72;">
                                </div>
                                <div class="mt-2">
                                    <input type="file" name="foto_profil" class="form-control form-control-sm" accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG, GIF (Max: 2MB)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" class="form-control" 
                                       value="{{ old('nama_lengkap', $biodata->nama_lengkap ?? '') }}" required>
                                @error('nama_lengkap')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $user->username }}" disabled>
                                <small class="text-muted">Email tidak dapat diubah</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir *</label>
                                <input type="text" name="tempat_lahir" class="form-control" 
                                       value="{{ old('tempat_lahir', $biodata->tempat_lahir ?? '') }}" required>
                                @error('tempat_lahir')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" class="form-control" 
                                       value="{{ old('tanggal_lahir', $biodata->tanggal_lahir ?? '') }}" required>
                                @error('tanggal_lahir')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin', $biodata->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $biodata->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon *</label>
                                <input type="text" name="no_telepon" class="form-control" 
                                       value="{{ old('no_telepon', $biodata->no_telepon ?? '') }}" required>
                                @error('no_telepon')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Alamat *</label>
                                <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat', $biodata->alamat ?? '') }}</textarea>
                                @error('alamat')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pekerjaan *</label>
                                <input type="text" name="pekerjaan" class="form-control" 
                                       value="{{ old('pekerjaan', $biodata->pekerjaan ?? '') }}" required>
                                @error('pekerjaan')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Instansi *</label>
                                <input type="text" name="instansi" class="form-control" 
                                       value="{{ old('instansi', $biodata->instansi ?? '') }}" required>
                                @error('instansi')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pendidikan Terakhir *</label>
                                <select name="pendidikan_terakhir" class="form-control" required>
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="SD" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'SD' ? 'selected' : '' }}>SD</option>
                                    <option value="SMP" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                    <option value="SMA" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                    <option value="D3" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'D3' ? 'selected' : '' }}>D3</option>
                                    <option value="S1" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('pendidikan_terakhir', $biodata->pendidikan_terakhir ?? '') == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                                @error('pendidikan_terakhir')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('profil.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection