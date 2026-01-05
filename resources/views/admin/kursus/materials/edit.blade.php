@php
    use Illuminate\Support\Str;
@endphp


@extends('layouts.admin')

@section('title', 'MOOC BPS - Edit Materi')

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
    
    .order-display {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px 15px;
        font-weight: 600;
        color: #1e3c72;
    }
    
    .info-icon {
        cursor: pointer;
        color: #6c757d;
        transition: color 0.3s ease;
    }
    
    .info-icon:hover {
        color: #1e3c72;
    }
    
    .tooltip-inner {
        max-width: 300px;
        padding: 12px;
        background: #1e3c72;
        color: white;
        border-radius: 8px;
        text-align: left;
    }
    
    .bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow::before,
    .bs-tooltip-top .tooltip-arrow::before {
        border-top-color: #1e3c72;
    }
    
    .form-check-label {
        display: flex;
        align-items: center;
    }
    
    .current-file {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 6px;
        padding: 10px;
        margin-top: 8px;
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
                        <h4 class="mb-1">Edit Materi: {{ $material->title }}</h4>
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
                                <li class="breadcrumb-item active">Edit Materi</li>
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
                <h4 class="page-title">Edit Materi untuk: {{ $kursus->judul_kursus }}</h4>
                <p class="mb-0 text-white-50">Perbarui informasi materi berikut</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.kursus.materials.update', [$kursus, $material]) }}" method="POST" enctype="multipart/form-data" id="materialForm">
                        @csrf
                        @method('PUT')

                        <!-- Informasi Dasar Materi -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label">Judul Materi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $material->title) }}" required
                                       placeholder="Contoh: Materi 1 - Pengenalan Dasar">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    Urutan Tampilan 
                                    <i class="mdi mdi-information-outline info-icon ms-1" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Urutan akan disesuaikan otomatis oleh sistem. Urutan dapat diubah nanti melalui fitur sortir materi."></i>
                                </label>
                                <!-- Input hidden untuk order -->
                                <input type="hidden" id="order" name="order" value="{{ $material->order }}">
                                
                                <div class="order-display">
                                    <i class="mdi mdi-sort-numeric-asc me-2"></i>
                                    Urutan ke-{{ $material->order }}
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="description" class="form-label">Deskripsi Materi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3"
                                          placeholder="Deskripsi singkat tentang materi ini">{{ old('description', $material->description) }}</textarea>
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
                                
                                @php
                                    $contentTypes = [];
                                    if ($material->file_path) $contentTypes[] = 'file';
                                    if ($material->video_url) $contentTypes[] = 'video';
                                    if ($material->soal_pretest) $contentTypes[] = 'pretest';
                                    if ($material->soal_posttest) $contentTypes[] = 'posttest';
                                @endphp
                                
                                <div class="row">
                                    <!-- Materi PDF/PPT -->
                                    <div class="col-md-6 mb-3">
                                        <div class="content-type-option" onclick="toggleContentType('file')">
                                            <div class="form-check">
                                                <input class="form-check-input content-type-checkbox" type="checkbox" 
                                                       id="content_type_file" name="content_types[]" value="file"
                                                       {{ in_array('file', old('content_types', $contentTypes)) ? 'checked' : '' }}>
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
                                                       {{ in_array('video', old('content_types', $contentTypes)) ? 'checked' : '' }}>
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
                                                       {{ in_array('pretest', old('content_types', $contentTypes)) ? 'checked' : '' }}>
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
                                                       {{ in_array('posttest', old('content_types', $contentTypes)) ? 'checked' : '' }}>
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
                                           name="attendance_required" value="1" {{ old('attendance_required', $material->attendance_required) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="attendance_required">
                                        Wajib Kehadiran
                                        <i class="mdi mdi-information-outline info-icon ms-1" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Jika diaktifkan, peserta wajib mengkonfirmasi kehadiran untuk materi ini sebelum dapat melanjutkan ke materi berikutnya."></i>
                                    </label>
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
                                        
                                        @if($material->file_path)
                                        <div class="current-file mb-3">
                                            <strong>File Saat Ini:</strong>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span>
                                                    <i class="mdi mdi-file-document me-2"></i>
                                                    {{ basename($material->file_path) }}
                                                </span>
                                                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="mdi mdi-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="file_path" class="form-label">
                                                    {{ $material->file_path ? 'Ganti File Materi' : 'Upload File Materi' }}
                                                <small class="text-muted">(Opsional - biarkan kosong jika tidak ingin mengubah)</small>
                                                </label>
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
                                                       id="duration_file" name="duration_file" value="{{ old('duration_file', $material->duration_file) }}" min="1"
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
                                                       id="video_url" name="video_url" value="{{ old('video_url', $material->video_url) }}" 
                                                       placeholder="https://youtube.com/embed/...">
                                                <small class="text-muted">Link embed video YouTube atau platform lainnya</small>
                                                @error('video_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="duration_video" class="form-label">Durasi Video (menit)</label>
                                                <input type="number" class="form-control @error('duration_video') is-invalid @enderror" 
                                                       id="duration_video" name="duration_video" value="{{ old('duration_video', $material->duration_video) }}" min="1"
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
                                           value="{{ old('durasi_pretest', $material->durasi_pretest ?? 60) }}" min="1">
                                    @error('durasi_pretest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="is_pretest" 
                                               name="is_pretest" value="1" {{ old('is_pretest', $material->type == 'pre_test') ? 'checked' : '' }}>
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
                                           value="{{ old('durasi_posttest', $material->durasi_posttest ?? 60) }}" min="1">
                                    @error('durasi_posttest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" class="form-check-input" id="is_posttest" 
                                               name="is_posttest" value="1" {{ old('is_posttest', $material->type == 'post_test') ? 'checked' : '' }}>
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
                                           name="is_active" value="1" {{ old('is_active', $material->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">
                                        Aktifkan materi ini
                                        <i class="mdi mdi-information-outline info-icon ms-1" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Materi yang nonaktif tidak akan ditampilkan ke peserta dan tidak dapat diakses. Gunakan fitur ini untuk menyembunyikan materi sementara."></i>
                                    </label>
                                </div>
                                <small class="text-muted">Materi yang nonaktif tidak akan ditampilkan ke peserta</small>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-content-save me-2"></i> Update Materi
                                        </button>
                                        <a href="{{ route('admin.kursus.materials.index', $kursus) }}" class="btn btn-secondary">
                                            <i class="mdi mdi-cancel me-2"></i> Batal
                                        </a>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                            <i class="mdi mdi-delete me-2"></i> Hapus Materi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Form untuk Hapus -->
                    <form id="deleteForm" action="{{ route('admin.kursus.materials.destroy', [$kursus, $material]) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
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

// Data soal yang sudah ada
const existingPretest = @json($material->soal_pretest ?? []);
const existingPosttest = @json($material->soal_posttest ?? []);

// Flag untuk menandai apakah soal sudah dimuat
let pretestLoaded = false;
let posttestLoaded = false;

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
        
        // Jika pretest/posttest dipilih dan belum ada soal, load existing soal HANYA JIKA BELUM DIMUAT
        if ((type === 'pretest' || type === 'posttest') && isVisible) {
            const container = document.getElementById(`soal-${type}-container`);
            const isLoaded = type === 'pretest' ? pretestLoaded : posttestLoaded;
            
            if (container.children.length === 0 && !isLoaded) {
                loadExistingSoal(type);
                
                // Tandai sudah dimuat
                if (type === 'pretest') {
                    pretestLoaded = true;
                } else {
                    posttestLoaded = true;
                }
            }
        }
    }
}

// Load existing soal - PERBAIKAN: Hanya load jika belum ada
function loadExistingSoal(type) {
    const existingSoal = type === 'pretest' ? existingPretest : existingPosttest;
    const container = document.getElementById(`soal-${type}-container`);
    
    // Clear container terlebih dahulu untuk memastikan tidak ada duplikasi
    container.innerHTML = '';
    
    if (existingSoal && existingSoal.length > 0) {
        existingSoal.forEach((soal, index) => {
            addSoal(type, soal, index);
        });
        
        // Update counter berdasarkan jumlah soal yang ada
        if (type === 'pretest') {
            soalPretestCounter = existingSoal.length;
        } else {
            soalPosttestCounter = existingSoal.length;
        }
    } else {
        // Jika tidak ada soal existing, tambahkan satu soal kosong
        addSoal(type);
    }
}

// Tambah soal baru atau dengan data existing - PERBAIKAN: Handle counter dengan benar
function addSoal(type, existingData = null, index = null) {
    const container = document.getElementById(`soal-${type}-container`);
    
    // Jika index tidak diberikan, gunakan counter yang sesuai
    let counter;
    if (index !== null) {
        counter = index;
    } else {
        counter = (type === 'pretest' ? soalPretestCounter : soalPosttestCounter);
    }
    
    const soalId = counter + 1;
    
    const newSoal = document.createElement('div');
    newSoal.className = 'soal-item card mb-3';
    newSoal.innerHTML = `
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-11">
                    <label class="form-label">Pertanyaan ${soalId}</label>
                    <textarea class="form-control" name="${type}_soal[${counter}][pertanyaan]" 
                              placeholder="Tulis pertanyaan di sini..." required>${existingData ? existingData.pertanyaan : ''}</textarea>
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeSoal(this, '${type}')" title="Hapus Soal">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            </div>
            
            <div class="row">
                ${Array.from({length: 4}, (_, i) => {
                    const pilihan = existingData ? existingData.pilihan[i] : '';
                    const isChecked = existingData && existingData.jawaban_benar == i ? 'checked' : '';
                    return `
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="radio" name="${type}_soal[${counter}][jawaban_benar]" value="${i}" ${isChecked} required>
                            </div>
                            <input type="text" class="form-control" name="${type}_soal[${counter}][pilihan][]" 
                                   value="${pilihan || ''}" placeholder="Pilihan ${String.fromCharCode(65 + i)}" required>
                        </div>
                    </div>
                    `;
                }).join('')}
            </div>
            
            <small class="text-muted mt-2">Pilih jawaban yang benar dengan mencentang radio button</small>
        </div>
    `;
    container.appendChild(newSoal);
    
    // Update counter HANYA jika ini adalah soal baru (bukan dari existing data)
    if (index === null) {
        if (type === 'pretest') {
            soalPretestCounter++;
        } else {
            soalPosttestCounter++;
        }
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
    
    // Reset counter
    if (type === 'pretest') {
        soalPretestCounter = 0;
    } else {
        soalPosttestCounter = 0;
    }
    
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
        
        // Increment counter
        if (type === 'pretest') {
            soalPretestCounter++;
        } else {
            soalPosttestCounter++;
        }
    });
}

// Initialize form berdasarkan nilai yang sudah dipilih sebelumnya - PERBAIKAN: Load soal hanya sekali
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

    // Load existing soal HANYA JIKA section sudah visible (tidak perlu panggil loadExistingSoal lagi)
    // Karena sudah ditangani oleh toggleContentSection

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
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

// Konfirmasi hapus materi
function confirmDelete() {
    Swal.fire({
        title: 'Hapus Materi?',
        text: "Anda tidak dapat mengembalikan materi yang sudah dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
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
    
    if (contentTypes.includes('file') && !document.getElementById('file_path').value && !@json($material->file_path)) {
        warnings.push('File materi untuk konten PDF/PPT belum diupload');
    }
    
    if (contentTypes.includes('video')) {
        const videoUrl = document.getElementById('video_url').value;
        if (!videoUrl && !@json($material->video_url)) {
            warnings.push('URL video untuk konten video belum diisi');
        } else if (videoUrl && !isValidUrl(videoUrl)) {
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