@extends('layouts.admin')

@section('title', 'Tambah Kursus Baru - MOOC BPS')

@section('styles')
<style>
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    .form-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3c72;
        margin: 0;
    }

    .btn-back {
        background: #6c757d;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back:hover {
        background: #5a6268;
        color: white;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
    }

    .form-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #1e3c72;
        margin-bottom: 8px;
        display: block;
    }

    .form-control, .form-select {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
    }

    .form-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }

    .required::after {
        content: " *";
        color: #e74c3c;
    }

    .image-preview {
        max-width: 200px;
        max-height: 150px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 10px;
        display: none;
        margin-top: 10px;
    }

    .image-preview img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 4px;
    }

    .form-group-with-image {
        margin-bottom: 30px;
    }

    .image-preview-container {
        max-height: 200px;
        overflow: hidden;
        margin-bottom: 15px;
    }

    /* Responsif */
    @media (max-width: 768px) {
        .form-body {
            padding: 20px;
        }

        .form-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .form-header .d-flex {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>
@endsection

@section('content')
<!-- WELCOME SECTION -->
<div class="welcome-section">
    <h1 class="welcome-title">Tambah Kursus Baru</h1>
    <p class="welcome-subtitle">
        Isi form berikut untuk menambahkan kursus baru ke platform MOOC BPS.
    </p>
</div>

<!-- FORM SECTION -->
<div class="form-container">
    <div class="form-header">
        <h2 class="form-title">Form Tambah Kursus</h2>
        <a href="{{ route('admin.kursus.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>
    
    <div class="form-body">
        <form action="{{ route('admin.kursus.store') }}" method="POST" enctype="multipart/form-data" id="kursusForm">
            @csrf
            
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <!-- Judul Kursus -->
                    <div class="form-group">
                        <label for="judul_kursus" class="form-label required">Judul Kursus</label>
                        <input type="text" class="form-control" id="judul_kursus" name="judul_kursus" 
                               value="{{ old('judul_kursus') }}" required 
                               placeholder="Masukkan judul kursus">
                        @error('judul_kursus')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- pelaksana -->
                    <div class="form-group">
                        <label for="pelaksana" class="form-label required">Pelaksana</label>
                        <input type="text" class="form-control" id="pelaksana" name="pelaksana" 
                               value="{{ old('pelaksana') }}" required 
                               placeholder="Masukkan nama pelaksana">
                        @error('pelaksana')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- kategori -->
                    <div class="form-group">
                        <label for="kategori" class="form-label required">Kategori</label>
                        <select class="form-select" id="kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="pemula" {{ old('kategori') == 'pemula' ? 'selected' : '' }}>Pemula</option>
                            <option value="menengah" {{ old('kategori') == 'menengah' ? 'selected' : '' }}>Menengah</option>
                            <option value="lanjutan" {{ old('kategori') == 'lanjutan' ? 'selected' : '' }}>Lanjutan</option>
                        </select>
                        @error('kategori')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Durasi -->
                    <div class="form-group">
                        <label for="durasi_jam" class="form-label required">Jam Pelajaran (JP) </label>
                        <input type="number" class="form-control" id="durasi_jam" name="durasi_jam" 
                               value="{{ old('durasi_jam', 0) }}" min="0" required 
                               placeholder="Masukkan durasi dalam jam">
                        <div class="form-text">1 JP adalah 45 menit</div>
                        @error('durasi_jam')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kuota Peserta -->
                    <div class="form-group">
                        <label for="kuota_peserta" class="form-label">Kuota Peserta</label>
                        <input type="number" class="form-control" id="kuota_peserta" name="kuota_peserta" 
                               value="{{ old('kuota_peserta') }}" min="1" 
                               placeholder="Kosongkan untuk tidak terbatas">
                        <div class="form-text">Biarkan kosong jika kuota tidak terbatas</div>
                        @error('kuota_peserta')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kode Enroll Kursus -->
                    <div class="form-group">
                        <label for="enroll_code" class="form-label">
                            Kode Enroll Kursus
                            <span class="text-muted">(opsional)</span>
                        </label>
                        <input type="text"
                            class="form-control"
                            id="enroll_code"
                            name="enroll_code"
                            value="{{ old('enroll_code') }}"
                            placeholder="Contoh: BPS-TALA-2025">
                        <div class="form-text">
                            Isi jika kursus hanya boleh diikuti mitra tertentu.
                            Kosongkan jika kursus terbuka untuk semua.
                        </div>
                        @error('enroll_code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                  <!-- Tanggal Mulai & Selesai -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_mulai" class="form-label">
                                    <i class="fas fa-calendar-plus me-1"></i>Tanggal Mulai
                                </label>
                                <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                    id="tanggal_mulai" name="tanggal_mulai" 
                                    value="{{ old('tanggal_mulai') }}"
                                    onchange="validateDates()">
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>Pilih tanggal mulai kursus
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_selesai" class="form-label">
                                    <i class="fas fa-calendar-check me-1"></i>Tanggal Selesai
                                </label>
                                <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                    id="tanggal_selesai" name="tanggal_selesai" 
                                    value="{{ old('tanggal_selesai') }}"
                                    onchange="validateDates()">
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>Pilih tanggal selesai kursus
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Container untuk pesan error custom -->
                    <div id="dateValidationError" class="alert alert-danger alert-dismissible fade show d-none mt-2" role="alert">
                        <i class="fas fa-calendar-times me-2"></i>
                        <span id="dateErrorText"></span>
                        <button type="button" class="btn-close" onclick="dismissDateError()"></button>
                    </div>

                    <!-- Container untuk pesan info -->
                    <div id="dateInfo" class="alert alert-info alert-dismissible fade show d-none mt-2" role="alert">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span id="dateInfoText"></span>
                    </div>

                    <style>
                        /* Styling untuk input tanggal yang error */
                        .form-control.is-invalid {
                            border-color: #dc3545;
                            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
                            background-repeat: no-repeat;
                            background-position: right calc(0.375em + 0.1875rem) center;
                            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
                        }

                        .form-control.is-invalid:focus {
                            border-color: #dc3545;
                            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
                        }

                        /* Styling untuk pesan error dan info */
                        .alert {
                            border-radius: 8px;
                            border: 1px solid transparent;
                            padding: 12px 16px;
                        }

                        .alert-danger {
                            background-color: #f8d7da;
                            border-color: #f5c6cb;
                            color: #721c24;
                        }

                        .alert-info {
                            background-color: #d1ecf1;
                            border-color: #bee5eb;
                            color: #0c5460;
                        }

                        /* Animasi untuk munculnya pesan */
                        @keyframes slideDown {
                            from {
                                opacity: 0;
                                transform: translateY(-10px);
                            }
                            to {
                                opacity: 1;
                                transform: translateY(0);
                            }
                        }

                        .fade.show {
                            animation: slideDown 0.3s ease;
                        }

                        /* Styling untuk label */
                        .form-label {
                            font-weight: 600;
                            color: #495057;
                            margin-bottom: 8px;
                        }

                        /* Styling untuk helper text */
                        .form-text {
                            font-size: 0.85rem;
                            margin-top: 4px;
                        }
                    </style>

                    <script>
                    // Fungsi untuk validasi tanggal
                    function validateDates() {
                        const startDateInput = document.getElementById('tanggal_mulai');
                        const endDateInput = document.getElementById('tanggal_selesai');
                        const errorDiv = document.getElementById('dateValidationError');
                        const errorText = document.getElementById('dateErrorText');
                        const infoDiv = document.getElementById('dateInfo');
                        const infoText = document.getElementById('dateInfoText');
                        
                        // Reset semua status
                        startDateInput.classList.remove('is-invalid');
                        endDateInput.classList.remove('is-invalid');
                        errorDiv.classList.add('d-none');
                        infoDiv.classList.add('d-none');
                        
                        // Ambil nilai tanggal
                        const startDate = startDateInput.value;
                        const endDate = endDateInput.value;
                        
                        // Jika kedua tanggal terisi
                        if (startDate && endDate) {
                            const start = new Date(startDate);
                            const end = new Date(endDate);
                            
                            // Validasi: Tanggal mulai tidak boleh setelah tanggal selesai
                            if (start > end) {
                                // Tampilkan pesan error
                                errorText.textContent = 'Tanggal mulai harus lebih awal dari tanggal selesai. Periksa kembali urutan tanggal.';
                                errorDiv.classList.remove('d-none');
                                
                                // Tambahkan kelas error pada input
                                startDateInput.classList.add('is-invalid');
                                endDateInput.classList.add('is-invalid');
                                
                                // Scroll ke error message
                                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                
                                return false;
                            }
                            
                            // Validasi: Tanggal selesai tidak boleh sebelum tanggal mulai
                            if (end < start) {
                                errorText.textContent = 'Tanggal selesai tidak boleh sebelum tanggal mulai. Pastikan urutan tanggal benar.';
                                errorDiv.classList.remove('d-none');
                                endDateInput.classList.add('is-invalid');
                                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                return false;
                            }
                            
                            // Hitung durasi kursus
                            const diffTime = Math.abs(end - start);
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                            
                            // Tampilkan info durasi kursus
                            if (diffDays === 1) {
                                infoText.textContent = 'Kursus berlangsung selama 1 hari.';
                            } else {
                                infoText.textContent = `Kursus berlangsung selama ${diffDays} hari.`;
                            }
                            infoDiv.classList.remove('d-none');
                            
                            return true;
                        }
                        
                        // Validasi jika hanya satu tanggal yang terisi
                        if (startDate && !endDate) {
                            infoText.textContent = 'Silakan pilih tanggal selesai untuk menghitung durasi kursus.';
                            infoDiv.classList.remove('d-none');
                        } else if (!startDate && endDate) {
                            infoText.textContent = 'Silakan pilih tanggal mulai terlebih dahulu.';
                            infoDiv.classList.remove('d-none');
                        }
                        
                        return null; // Validasi belum lengkap
                    }

                    // Fungsi untuk menutup pesan error tanggal
                    function dismissDateError() {
                        const errorDiv = document.getElementById('dateValidationError');
                        errorDiv.classList.add('d-none');
                    }

                    // Fungsi untuk validasi sebelum submit form
                    function validateFormBeforeSubmit(event) {
                        const startDateInput = document.getElementById('tanggal_mulai');
                        const endDateInput = document.getElementById('tanggal_selesai');
                        
                        const startDate = startDateInput.value;
                        const endDate = endDateInput.value;
                        
                        // Jika kedua tanggal terisi, lakukan validasi
                        if (startDate && endDate) {
                            const start = new Date(startDate);
                            const end = new Date(endDate);
                            
                            if (start > end) {
                                // Tampilkan pesan error
                                const errorDiv = document.getElementById('dateValidationError');
                                const errorText = document.getElementById('dateErrorText');
                                
                                errorText.textContent = 'Tanggal mulai tidak boleh lebih dari tanggal selesai. Silakan perbaiki tanggal sebelum melanjutkan.';
                                errorDiv.classList.remove('d-none');
                                
                                // Tambahkan kelas error
                                startDateInput.classList.add('is-invalid');
                                endDateInput.classList.add('is-invalid');
                                
                                // Scroll ke error
                                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                
                                // Cegah submit
                                event.preventDefault();
                                return false;
                            }
                        }
                        
                        return true;
                    }

                    // Validasi real-time saat user mengubah input
                    document.addEventListener('DOMContentLoaded', function() {
                        const startDateInput = document.getElementById('tanggal_mulai');
                        const endDateInput = document.getElementById('tanggal_selesai');
                        
                        // Tambahkan event listener untuk input real-time
                        if (startDateInput) {
                            startDateInput.addEventListener('change', validateDates);
                            startDateInput.addEventListener('input', function() {
                                this.classList.remove('is-invalid');
                            });
                        }
                        
                        if (endDateInput) {
                            endDateInput.addEventListener('change', validateDates);
                            endDateInput.addEventListener('input', function() {
                                this.classList.remove('is-invalid');
                            });
                        }
                        
                        // Validasi saat form di-submit
                        const form = startDateInput?.closest('form');
                        if (form) {
                            form.addEventListener('submit', validateFormBeforeSubmit);
                        }
                        
                        // Validasi saat halaman pertama kali dimuat (jika ada data lama)
                        setTimeout(validateDates, 100);
                    });

                    // Fungsi untuk reset validasi tanggal
                    function resetDateValidation() {
                        const startDateInput = document.getElementById('tanggal_mulai');
                        const endDateInput = document.getElementById('tanggal_selesai');
                        const errorDiv = document.getElementById('dateValidationError');
                        const infoDiv = document.getElementById('dateInfo');
                        
                        startDateInput?.classList.remove('is-invalid');
                        endDateInput?.classList.remove('is-invalid');
                        errorDiv?.classList.add('d-none');
                        infoDiv?.classList.add('d-none');
                    }

                    // Fungsi untuk menetapkan tanggal (dari external source)
                    function setCourseDates(startDate, endDate) {
                        const startDateInput = document.getElementById('tanggal_mulai');
                        const endDateInput = document.getElementById('tanggal_selesai');
                        
                        if (startDateInput) startDateInput.value = startDate;
                        if (endDateInput) endDateInput.value = endDate;
                        
                        // Jalankan validasi setelah mengatur tanggal
                        setTimeout(validateDates, 50);
                    }
                    </script>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <!-- Status -->
                    <div class="form-group">
                        <label for="status" class="form-label required">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Gambar Kursus -->
                    <div class="form-group form-group-with-image">
                        <label for="gambar_kursus" class="form-label">Gambar Kursus</label>
                        <input type="file" class="form-control" id="gambar_kursus" name="gambar_kursus" 
                               accept="image/*" onchange="previewImage(this)">
                        <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB</div>
                        <div class="image-preview-container">
                            <div class="image-preview" id="imagePreview">
                                <img src="" alt="Preview Gambar" style="max-height: 150px;">
                            </div>
                        </div>
                        @error('gambar_kursus')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Deskripsi Kursus -->
            <div class="form-group">
                <label for="deskripsi_kursus" class="form-label required">Deskripsi Kursus</label>
                <textarea class="form-control" id="deskripsi_kursus" name="deskripsi_kursus" 
                          rows="4" required placeholder="Masukkan deskripsi lengkap kursus">{{ old('deskripsi_kursus') }}</textarea>
                @error('deskripsi_kursus')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Output Pelatihan -->
            <div class="form-group">
                <label for="output_pelatihan" class="form-label">Output Pelatihan</label>
                <textarea class="form-control" id="output_pelatihan" name="output_pelatihan" 
                          rows="3" placeholder="Masukkan output yang akan didapat peserta (pisahkan dengan enter)">{{ old('output_pelatihan') }}</textarea>
                <div class="form-text">Pisahkan setiap poin dengan enter (baris baru)</div>
                @error('output_pelatihan')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Persyaratan -->
            <div class="form-group">
                <label for="persyaratan" class="form-label">Persyaratan</label>
                <textarea class="form-control" id="persyaratan" name="persyaratan" 
                          rows="3" placeholder="Masukkan persyaratan untuk mengikuti kursus">{{ old('persyaratan') }}</textarea>
                @error('persyaratan')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Fasilitas -->
            <div class="form-group">
                <label for="fasilitas" class="form-label">Fasilitas</label>
                <textarea class="form-control" id="fasilitas" name="fasilitas" 
                          rows="3" placeholder="Masukkan fasilitas yang didapat peserta">{{ old('fasilitas') }}</textarea>
                @error('fasilitas')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="form-group text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Kursus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Image Preview Function
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.style.display = 'block';
                preview.querySelector('img').src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            preview.querySelector('img').src = '';
        }
    }

    // Form Validation
    document.getElementById('kursusForm').addEventListener('submit', function(e) {
        const judul = document.getElementById('judul_kursus').value.trim();
        const pelaksana = document.getElementById('pelaksana').value.trim();
        const kategori = document.getElementById('kategori').value;
        const status = document.getElementById('status').value;
        
        if (!judul || !pelaksana || !kategori || !status) {
            e.preventDefault();
            Swal.fire({
                title: 'Data Belum Lengkap!',
                text: 'Harap isi semua field yang wajib diisi.',
                icon: 'warning',
                confirmButtonColor: '#1e3c72'
            });
        }
    });
</script>
@endsection