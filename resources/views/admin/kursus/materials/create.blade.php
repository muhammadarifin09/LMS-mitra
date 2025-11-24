@extends('layouts.admin')

@section('title', 'MOCC BPS - Tambah Materi')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
<style>
    .page-title-box {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        border: none;
        border-radius: 8px;
        font-weight: 600;
        padding: 12px 24px;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        border: none;
        border-radius: 8px;
        font-weight: 600;
        padding: 12px 24px;
    }
    
    .soal-item {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: #f8f9fa;
    }
    
    .pretest-section, .posttest-section {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .posttest-section {
        background: #d1ecf1;
        border: 1px solid #bee5eb;
    }
    
    .content-type-option {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .content-type-option:hover {
        border-color: #1e3c72;
    }
    
    .content-type-option.selected {
        border-color: #1e3c72;
        background-color: #f8f9fa;
    }
    
    .content-type-checkbox {
        display: none;
    }
    
    .order-info {
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 8px;
        padding: 10px 15px;
        margin-top: 5px;
    }
    
    .navigation-header {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid #1e3c72;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Navigation Header dengan Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="navigation-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Tambah Materi Baru</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.kursus.index') }}">Kursus</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.kursus.show', $kursus) }}">{{ Str::limit($kursus->judul_kursus, 30) }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.kursus.materials.index', $kursus) }}">Materi</a>
                                </li>
                                <li class="breadcrumb-item active">Tambah Materi</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="{{ route('admin.kursus.materials.index', $kursus) }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-2"></i> Kembali ke Daftar Materi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <!-- Kosongkan bagian ini atau tambahkan elemen lain jika perlu -->
                </div>
                <h4 class="page-title">Tambah Materi untuk: {{ $kursus->judul_kursus }}</h4>
                <p class="mb-0 text-white-50">Lengkapi form berikut untuk menambahkan materi baru</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.kursus.materials.store', $kursus) }}" method="POST" enctype="multipart/form-data" id="materialForm">
                        @csrf

                        <!-- Informasi Dasar Materi -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label">Judul Materi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required
                                       placeholder="Contoh: Materi 1 - Pengenalan Dasar">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="order" class="form-label">Urutan Tampilan <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                       id="order" name="order" value="{{ old('order', $nextOrder) }}" min="1" required
                                       onchange="updateOrderInfo()">
                                
                                <div class="order-info">
                                    <small class="text-muted">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        <span id="order-info-text">
                                            @if($nextOrder == 1)
                                                Ini akan menjadi materi pertama
                                            @else
                                                Urutan saat ini: {{ $nextOrder }} dari {{ $totalMaterials + 1 }} materi
                                            @endif
                                        </span>
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="mdi mdi-lightbulb-on-outline me-1"></i>
                                        Urutan akan disesuaikan otomatis
                                    </small>
                                </div>
                                
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="description" class="form-label">Deskripsi Materi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3"
                                          placeholder="Deskripsi singkat tentang materi ini">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tipe Konten Materi (Multiple Selection) -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Jenis Konten Materi <span class="text-danger">*</span></label>
                                <small class="text-muted d-block mb-2">Pilih satu atau lebih jenis konten yang akan dimasukkan dalam materi ini</small>
                                
                                <div class="row">
                                    <!-- Materi PDF/PPT -->
                                    <div class="col-md-6 mb-3">
                                        <div class="content-type-option" onclick="toggleContentType('file')">
                                            <div class="form-check">
                                                <input class="form-check-input content-type-checkbox" type="checkbox" 
                                                       id="content_type_file" name="content_types[]" value="file"
                                                       {{ in_array('file', old('content_types', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="content_type_file">
                                                    <i class="mdi mdi-file-pdf-box mdi-24px text-danger me-2"></i>
                                                    Materi PDF/PPT
                                                </label>
                                            </div>
                                            <small class="text-muted">Upload file materi dalam format PDF, PPT, atau dokumen</small>
                                        </div>
                                    </div>

                                    <!-- Video -->
                                    <div class="col-md-6 mb-3">
                                        <div class="content-type-option" onclick="toggleContentType('video')">
                                            <div class="form-check">
                                                <input class="form-check-input content-type-checkbox" type="checkbox" 
                                                       id="content_type_video" name="content_types[]" value="video"
                                                       {{ in_array('video', old('content_types', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="content_type_video">
                                                    <i class="mdi mdi-video mdi-24px text-primary me-2"></i>
                                                    Video Pembelajaran
                                                </label>
                                            </div>
                                            <small class="text-muted">Tautan video YouTube atau platform video lainnya</small>
                                        </div>
                                    </div>

                                    <!-- Pretest -->
                                    <div class="col-md-6 mb-3">
                                        <div class="content-type-option" onclick="toggleContentType('pretest')">
                                            <div class="form-check">
                                                <input class="form-check-input content-type-checkbox" type="checkbox" 
                                                       id="content_type_pretest" name="content_types[]" value="pretest"
                                                       {{ in_array('pretest', old('content_types', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="content_type_pretest">
                                                    <i class="mdi mdi-clipboard-text mdi-24px text-warning me-2"></i>
                                                    Pretest
                                                </label>
                                            </div>
                                            <small class="text-muted">Tes awal untuk mengukur pengetahuan sebelum belajar</small>
                                        </div>
                                    </div>

                                    <!-- Posttest -->
                                    <div class="col-md-6 mb-3">
                                        <div class="content-type-option" onclick="toggleContentType('posttest')">
                                            <div class="form-check">
                                                <input class="form-check-input content-type-checkbox" type="checkbox" 
                                                       id="content_type_posttest" name="content_types[]" value="posttest"
                                                       {{ in_array('posttest', old('content_types', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="content_type_posttest">
                                                    <i class="mdi mdi-clipboard-check mdi-24px text-success me-2"></i>
                                                    Posttest
                                                </label>
                                            </div>
                                            <small class="text-muted">Tes akhir untuk mengukur pemahaman setelah belajar</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kehadiran -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="attendance_required" 
                                           name="attendance_required" value="1" {{ old('attendance_required') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="attendance_required">Wajib Kehadiran</label>
                                </div>
                                <small class="text-muted">Centang jika peserta wajib mengkonfirmasi kehadiran untuk materi ini</small>
                            </div>
                        </div>

                        <!-- Konten Materi: File PDF/PPT -->
                        <div id="file-content-section" class="row mb-3" style="display: none;">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="mdi mdi-file-pdf-box text-danger me-2"></i>Materi File</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="file_path" class="form-label">Upload File Materi</label>
                                                <input type="file" class="form-control @error('file_path') is-invalid @enderror" 
                                                       id="file_path" name="file_path" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png">
                                                <small class="text-muted">Format: PDF, DOC, PPT, atau gambar. Maksimal 10MB</small>
                                                @error('file_path')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="duration_file" class="form-label">Durasi Belajar File (menit)</label>
                                                <input type="number" class="form-control @error('duration_file') is-invalid @enderror" 
                                                       id="duration_file" name="duration_file" value="{{ old('duration_file') }}" min="1"
                                                       placeholder="Contoh: 30">
                                                <small class="text-muted">Perkiraan waktu menyelesaikan materi file</small>
                                                @error('duration_file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Konten Materi: Video -->
                        <div id="video-content-section" class="row mb-3" style="display: none;">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="mdi mdi-video text-primary me-2"></i>Materi Video</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="video_url" class="form-label">URL Video</label>
                                                <input type="url" class="form-control @error('video_url') is-invalid @enderror" 
                                                       id="video_url" name="video_url" value="{{ old('video_url') }}" 
                                                       placeholder="https://youtube.com/embed/...">
                                                <small class="text-muted">Link embed video YouTube atau platform lainnya</small>
                                                @error('video_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="duration_video" class="form-label">Durasi Video (menit)</label>
                                                <input type="number" class="form-control @error('duration_video') is-invalid @enderror" 
                                                       id="duration_video" name="duration_video" value="{{ old('duration_video') }}" min="1"
                                                       placeholder="Contoh: 45">
                                                <small class="text-muted">Durasi video dalam menit</small>
                                                @error('duration_video')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FORM PRETEST -->
                        <div id="pretest-content-section" class="pretest-section" style="display: none;">
                            <h5 class="mb-3 text-warning"><i class="mdi mdi-clipboard-text me-2"></i>Pengaturan Pretest</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="durasi_pretest" class="form-label">Durasi Pretest (menit)</label>
                                    <input type="number" class="form-control @error('durasi_pretest') is-invalid @enderror" 
                                           id="durasi_pretest" name="durasi_pretest" 
                                           value="{{ old('durasi_pretest', 60) }}" min="1">
                                    @error('durasi_pretest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="passing_grade_pretest" class="form-label">Passing Grade (%)</label>
                                    <input type="number" class="form-control @error('passing_grade_pretest') is-invalid @enderror" 
                                           id="passing_grade_pretest" name="passing_grade_pretest" 
                                           value="{{ old('passing_grade_pretest', 70) }}" min="1" max="100">
                                    <small class="text-muted">Nilai minimal untuk lulus pretest</small>
                                    @error('passing_grade_pretest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="is_pretest" 
                                               name="is_pretest" value="1" {{ old('is_pretest', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_pretest">Tandai sebagai Pretest</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Soal Pretest -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Soal Pretest</h6>
                                    <button type="button" class="btn btn-success btn-sm" onclick="addSoal('pretest')">
                                        <i class="mdi mdi-plus"></i> Tambah Soal
                                    </button>
                                </div>
                            </div>

                            <div id="soal-pretest-container">
                                <!-- Soal pretest akan ditambahkan di sini via JavaScript -->
                            </div>
                        </div>

                        <!-- FORM POSTTEST -->
                        <div id="posttest-content-section" class="posttest-section" style="display: none;">
                            <h5 class="mb-3 text-success"><i class="mdi mdi-clipboard-check me-2"></i>Pengaturan Posttest</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="durasi_posttest" class="form-label">Durasi Posttest (menit)</label>
                                    <input type="number" class="form-control @error('durasi_posttest') is-invalid @enderror" 
                                           id="durasi_posttest" name="durasi_posttest" 
                                           value="{{ old('durasi_posttest', 60) }}" min="1">
                                    @error('durasi_posttest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="passing_grade_posttest" class="form-label">Passing Grade (%)</label>
                                    <input type="number" class="form-control @error('passing_grade_posttest') is-invalid @enderror" 
                                           id="passing_grade_posttest" name="passing_grade_posttest" 
                                           value="{{ old('passing_grade_posttest', 70) }}" min="1" max="100">
                                    <small class="text-muted">Nilai minimal untuk lulus posttest</small>
                                    @error('passing_grade_posttest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="is_posttest" 
                                               name="is_posttest" value="1" {{ old('is_posttest', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_posttest">Tandai sebagai Posttest</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Soal Posttest -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Soal Posttest</h6>
                                    <button type="button" class="btn btn-success btn-sm" onclick="addSoal('posttest')">
                                        <i class="mdi mdi-plus"></i> Tambah Soal
                                    </button>
                                </div>
                            </div>

                            <div id="soal-posttest-container">
                                <!-- Soal posttest akan ditambahkan di sini via JavaScript -->
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_active" 
                                           name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">Aktifkan materi ini</label>
                                </div>
                                <small class="text-muted">Materi yang nonaktif tidak akan ditampilkan ke peserta</small>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-content-save me-2"></i> Simpan Materi
                                        </button>
                                        <a href="{{ route('admin.kursus.materials.index', $kursus) }}" class="btn btn-secondary">
                                            <i class="mdi mdi-cancel me-2"></i> Batal
                                        </a>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            <i class="mdi mdi-information-outline me-1"></i>
                                            Urutan akan disesuaikan otomatis
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variabel untuk counter soal
let soalPretestCounter = 0;
let soalPosttestCounter = 0;

// Toggle content type selection
function toggleContentType(type) {
    const checkbox = document.getElementById(`content_type_${type}`);
    const option = checkbox.closest('.content-type-option');
    
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        option.classList.add('selected');
    } else {
        option.classList.remove('selected');
    }
    
    // Tampilkan/sembunyikan section yang sesuai
    toggleContentSection(type, checkbox.checked);
}

// Toggle content section berdasarkan pilihan
function toggleContentSection(type, isVisible) {
    const section = document.getElementById(`${type}-content-section`);
    
    if (section) {
        section.style.display = isVisible ? 'block' : 'none';
        
        // Jika pretest/posttest dipilih, tambahkan soal default
        if ((type === 'pretest' || type === 'posttest') && isVisible) {
            const container = document.getElementById(`soal-${type}-container`);
            if (container.children.length === 0) {
                addSoal(type);
            }
        }
    }
}

// Tambah soal baru
function addSoal(type) {
    const container = document.getElementById(`soal-${type}-container`);
    const counter = type === 'pretest' ? soalPretestCounter : soalPosttestCounter;
    const soalId = counter + 1;
    
    const newSoal = document.createElement('div');
    newSoal.className = 'soal-item card mb-3';
    newSoal.innerHTML = `
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-11">
                    <label class="form-label">Pertanyaan ${soalId}</label>
                    <textarea class="form-control" name="${type}_soal[${counter}][pertanyaan]" 
                              placeholder="Tulis pertanyaan di sini..." required></textarea>
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeSoal(this, '${type}')" title="Hapus Soal">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            </div>
            
            <div class="row">
                ${Array.from({length: 4}, (_, i) => `
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <div class="input-group-text">
                            <input type="radio" name="${type}_soal[${counter}][jawaban_benar]" value="${i}" required>
                        </div>
                        <input type="text" class="form-control" name="${type}_soal[${counter}][pilihan][]" 
                               placeholder="Pilihan ${String.fromCharCode(65 + i)}" required>
                    </div>
                </div>
                `).join('')}
            </div>
            
            <small class="text-muted mt-2">Pilih jawaban yang benar dengan mencentang radio button</small>
        </div>
    `;
    container.appendChild(newSoal);
    
    if (type === 'pretest') {
        soalPretestCounter++;
    } else {
        soalPosttestCounter++;
    }
}

// Hapus soal
function removeSoal(button, type) {
    const container = document.getElementById(`soal-${type}-container`);
    if (container.children.length > 1) {
        button.closest('.soal-item').remove();
        reindexSoal(type);
    } else {
        alert('Minimal harus ada 1 soal');
    }
}

// Reindex soal setelah hapus
function reindexSoal(type) {
    const container = document.getElementById(`soal-${type}-container`);
    const soalItems = container.querySelectorAll('.soal-item');
    
    soalItems.forEach((item, index) => {
        // Update label pertanyaan
        item.querySelector('label').textContent = `Pertanyaan ${index + 1}`;
        
        // Update semua input names
        const inputs = item.querySelectorAll('[name]');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            const newName = name.replace(new RegExp(`${type}_soal\\[\\d+\\]`), `${type}_soal[${index}]`);
            input.setAttribute('name', newName);
        });
    });
    
    if (type === 'pretest') {
        soalPretestCounter = soalItems.length;
    } else {
        soalPosttestCounter = soalItems.length;
    }
}

// Update informasi urutan
function updateOrderInfo() {
    const orderInput = document.getElementById('order');
    const orderValue = parseInt(orderInput.value);
    const totalMaterials = {{ $totalMaterials }};
    const nextOrder = {{ $nextOrder }};
    
    let infoText = '';
    
    if (orderValue === nextOrder) {
        infoText = 'Urutan saat ini: ' + orderValue + ' dari ' + (totalMaterials + 1) + ' materi';
    } else if (orderValue < nextOrder) {
        infoText = 'Materi akan ditempatkan di urutan ' + orderValue + '. Materi setelahnya akan bergeser.';
    } else {
        infoText = 'Materi akan ditempatkan di urutan ' + orderValue + '. Urutan melebihi jumlah materi saat ini.';
    }
    
    document.getElementById('order-info-text').textContent = infoText;
}

// Initialize form berdasarkan nilai yang sudah dipilih sebelumnya
document.addEventListener('DOMContentLoaded', function() {
    // Initialize selected options
    const contentTypes = ['file', 'video', 'pretest', 'posttest'];
    contentTypes.forEach(type => {
        const checkbox = document.getElementById(`content_type_${type}`);
        if (checkbox.checked) {
            checkbox.closest('.content-type-option').classList.add('selected');
            toggleContentSection(type, true);
        }
    });
    
    // Initialize order info
    updateOrderInfo();
});

// Fungsi untuk menampilkan SweetAlert yang lebih user friendly
function showAlert(title, message, type = 'error') {
    Swal.fire({
        title: title,
        text: message,
        icon: type,
        confirmButtonColor: '#1e3c72'
    });
}

// Validasi form sebelum submit (non-blocking, hanya warning)
document.getElementById('materialForm').addEventListener('submit', function(e) {
    const contentTypes = Array.from(document.querySelectorAll('input[name="content_types[]"]:checked'))
        .map(cb => cb.value);
    
    if (contentTypes.length === 0) {
        e.preventDefault();
        showAlert('Peringatan', 'Pilih minimal satu jenis konten materi');
        return false;
    }
    
    // Validasi untuk setiap konten yang dipilih (hanya warning, tidak block)
    let warnings = [];
    
    if (contentTypes.includes('file') && !document.getElementById('file_path').value) {
        warnings.push('File materi untuk konten PDF/PPT belum diupload');
    }
    
    if (contentTypes.includes('video')) {
        const videoUrl = document.getElementById('video_url').value;
        if (!videoUrl) {
            warnings.push('URL video untuk konten video belum diisi');
        } else if (!isValidUrl(videoUrl)) {
            warnings.push('URL video tidak valid');
        }
    }
    
    if (contentTypes.includes('pretest')) {
        const soalPretestContainer = document.getElementById('soal-pretest-container');
        if (soalPretestContainer.children.length === 0) {
            warnings.push('Pretest harus memiliki minimal 1 soal');
        }
    }
    
    if (contentTypes.includes('posttest')) {
        const soalPosttestContainer = document.getElementById('soal-posttest-container');
        if (soalPosttestContainer.children.length === 0) {
            warnings.push('Posttest harus memiliki minimal 1 soal');
        }
    }
    
    // Jika ada warnings, tampilkan konfirmasi
    if (warnings.length > 0) {
        e.preventDefault();
        const warningMessage = 'Perhatikan hal berikut:\n\n' + warnings.join('\n') + '\n\nLanjutkan menyimpan?';
        
        Swal.fire({
            title: 'Data Belum Lengkap',
            text: warningMessage,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1e3c72',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user memilih untuk lanjut, submit form
                document.getElementById('materialForm').submit();
            }
        });
    }
});

// Fungsi untuk validasi URL sederhana
function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}
</script>
@endsection