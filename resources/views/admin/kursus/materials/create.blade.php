@php
    use Illuminate\Support\Str;
@endphp

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
    
    .file-preview {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
    }
    
    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .file-item:last-child {
        border-bottom: none;
    }
    
    .file-actions {
        display: flex;
        gap: 5px;
    }
    
    .file-input-wrapper {
        position: relative;
    }
    
    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: -9999px;
    }
    
    /* Video Section Styles */
    .video-control-section {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .video-question-item {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .player-config-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    
    .config-item {
        background: white;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .config-item label {
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
    }
    
    .config-item small {
        color: #6c757d;
        font-size: 12px;
    }
    
    /* Video Preview */
    .video-preview-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 15px;
    }
    
    .video-preview-header {
        background: #f8f9fa;
        padding: 10px 15px;
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
    }
    
    .video-preview-body {
        padding: 20px;
        background: white;
    }
    
    .ratio-16x9 {
        --bs-aspect-ratio: 56.25%; /* 16:9 Aspect Ratio */
    }
    
    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease;
    }
    
    /* Badges */
    .badge-video {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .badge-hosted {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .badge-external {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .google-drive-info {
        background: #e8f5e9;
        border: 1px solid #c8e6c9;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
    }
    
    .google-drive-info h6 {
        color: #2e7d32;
    }
    
    .upload-progress {
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        display: none;
    }
    
    .progress-bar-google {
        background: linear-gradient(135deg, #4285f4, #34a853, #fbbc05, #ea4335);
        background-size: 400% 400%;
        animation: gradient 3s ease infinite;
    }
    
    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .file-size-info {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
    
    .upload-status {
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 5px;
    }
    
    .status-uploading {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .video-preview-thumbnail {
        width: 100%;
        height: 200px;
        background: #f8f9fa;
        border: 1px dashed #dee2e6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        margin-top: 10px;
    }
    
    .video-preview-thumbnail i {
        font-size: 48px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .player-config-grid {
            grid-template-columns: 1fr;
        }
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
                                <label class="form-label">
                                    Urutan Tampilan 
                                    <i class="mdi mdi-information-outline info-icon ms-1" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Urutan akan disesuaikan otomatis oleh sistem. Urutan dapat diubah nanti melalui fitur sortir materi."></i>
                                </label>
                                <!-- Input hidden untuk order -->
                                <input type="hidden" id="order" name="order" value="{{ $totalMaterials + 1 }}">
                                
                                <div class="order-display">
                                    <i class="mdi mdi-sort-numeric-asc me-2"></i>
                                    Urutan ke-{{ $totalMaterials + 1 }}
                                </div>
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
                                            <small class="text-muted">Upload file materi dalam format PDF, PPT, atau dokumen (disimpan di server lokal)</small>
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
                                            <small class="text-muted">Video akan diupload ke Google Drive dengan kontrol seperti Digitalent</small>
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
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Tambah File Materi</label>
                                                    <div class="file-input-wrapper">
                                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('file_input').click()">
                                                            <i class="mdi mdi-plus me-2"></i> Tambah File
                                                        </button>
                                                        <input type="file" id="file_input" 
                                                               accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png" 
                                                               style="display: none;" multiple
                                                               onchange="handleFileSelection(this.files)">
                                                    </div>
                                                    <small class="text-muted">
                                                        Format: PDF, DOC, PPT, atau gambar. Maksimal 10MB per file. 
                                                        Dapat menambahkan file satu per satu atau multiple.
                                                        <strong>File akan disimpan di server lokal.</strong>
                                                    </small>
                                                    @error('file_path')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <!-- File Preview Area -->
                                                <div id="file-preview" class="file-preview mt-3" style="display: none;">
                                                    <h6 class="mb-3">File yang akan diupload:</h6>
                                                    <div id="file-list"></div>
                                                </div>

                                                <!-- Hidden input untuk menyimpan file paths -->
                                                <div id="file-inputs-container">
                                                    <!-- File inputs akan ditambahkan di sini via JavaScript -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Konten Materi: Video (DIGITALENT STYLE) -->
                        <div id="video-content-section" class="row mb-3" style="display: none;">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="mdi mdi-video text-primary me-2"></i>Materi Video (Digitalent Style)</h6>
                                        
                                        <!-- Informasi Google Drive -->
                                        <div class="google-drive-info">
                                            <h6><i class="mdi mdi-google-drive me-2"></i>Google Drive Storage</h6>
                                            <small class="text-muted">
                                                Video akan diupload ke Google Drive untuk keamanan dan penyimpanan yang lebih baik.
                                                Durasi video akan dideteksi otomatis.
                                            </small>
                                        </div>

                                        <!-- Video Type Selection -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="video_type" class="form-label">Jenis Video <span class="text-danger">*</span></label>
                                                <select class="form-select @error('video_type') is-invalid @enderror" 
                                                        id="video_type" name="video_type" onchange="toggleVideoType()">
                                                    <option value="">Pilih Jenis Video</option>
                                                    <option value="youtube" {{ old('video_type') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                                    <option value="vimeo" {{ old('video_type') == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                                    <option value="hosted" {{ old('video_type') == 'hosted' ? 'selected' : '' }}>Google Drive (Upload Sendiri)</option>
                                                    <option value="external" {{ old('video_type') == 'external' ? 'selected' : '' }}>External (Embed)</option>
                                                </select>
                                                <small class="text-muted mt-1">Pilih "Google Drive" untuk upload video ke cloud storage</small>
                                                @error('video_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Video URL (for YouTube, Vimeo, External) -->
                                        <div class="row mb-3" id="video-url-section" style="display: none;">
                                            <div class="col-12">
                                                <label for="video_url" class="form-label">URL Video</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="mdi mdi-link"></i>
                                                    </span>
                                                    <input type="url" class="form-control @error('video_url') is-invalid @enderror" 
                                                           id="video_url" name="video_url" value="{{ old('video_url') }}" 
                                                           placeholder="Masukkan URL video">
                                                </div>
                                                <small class="text-muted" id="url-help-text">
                                                    <!-- Text help akan diisi oleh JavaScript -->
                                                </small>
                                                @error('video_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Video File Upload (for Google Drive) -->
                                        <div class="row mb-3" id="video-file-section" style="display: none;">
                                            <div class="col-12">
                                                <label for="video_file" class="form-label">Upload Video ke Google Drive</label>
                                                <input type="file" class="form-control @error('video_file') is-invalid @enderror" 
                                                       id="video_file" name="video_file" accept=".mp4,.webm,.avi,.mov,.wmv" 
                                                       onchange="previewVideoFile(this)">
                                                <small class="text-muted">Format: MP4, WebM, AVI, MOV, WMV. Maksimal 100MB</small>
                                                @error('video_file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                
                                                <!-- Video Preview -->
                                                <div id="video-preview" style="display: none;">
                                                    <div class="video-preview-thumbnail mt-3">
                                                        <i class="mdi mdi-video"></i>
                                                    </div>
                                                    <div id="video-file-info" class="file-size-info"></div>
                                                </div>
                                                
                                                <!-- Upload Progress -->
                                                <div id="upload-progress" class="upload-progress">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span>Uploading to Google Drive...</span>
                                                        <span id="upload-percentage">0%</span>
                                                    </div>
                                                    <div class="progress">
                                                        <div id="upload-progress-bar" class="progress-bar progress-bar-google" 
                                                             role="progressbar" style="width: 0%"></div>
                                                    </div>
                                                    <div id="upload-status" class="upload-status mt-2"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Durasi Video (Otomatis) -->
                                        <div class="row mb-3" id="duration-info-section" style="display: none;">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <i class="mdi mdi-timer-sand me-2"></i>
                                                    <strong>Durasi Video:</strong> 
                                                    <span id="duration-display">Akan terdeteksi otomatis setelah upload</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Player Configuration (Digitalent Style) -->
                                        <div class="video-control-section fade-in" id="player-config-section" style="display: none;">
                                            <h6><i class="mdi mdi-cog me-2"></i>Pengaturan Video Player</h6>
                                            <small class="text-muted d-block mb-3">Kontrol seperti platform Digitalent - video tidak bisa di-skip</small>
                                            
                                            <div class="player-config-grid">
                                                <!-- Basic Controls -->
                                                <div class="config-item">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="allow_skip" 
                                                               name="allow_skip" value="1" {{ old('allow_skip') ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold" for="allow_skip">
                                                            Izinkan Skip
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Jika nonaktif, peserta tidak bisa melewati video</small>
                                                </div>

                                                <div class="config-item">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="disable_forward_seek" 
                                                               name="disable_forward_seek" value="1" {{ old('disable_forward_seek', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold" for="disable_forward_seek">
                                                            Nonaktifkan Forward Seek
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Peserta tidak bisa maju ke bagian video yang belum ditonton</small>
                                                </div>

                                                <div class="config-item">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="disable_backward_seek" 
                                                               name="disable_backward_seek" value="1" {{ old('disable_backward_seek') ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold" for="disable_backward_seek">
                                                            Nonaktifkan Backward Seek
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Peserta tidak bisa mundur ke bagian sebelumnya</small>
                                                </div>

                                                <div class="config-item">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="disable_right_click" 
                                                               name="disable_right_click" value="1" {{ old('disable_right_click', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold" for="disable_right_click">
                                                            Nonaktifkan Klik Kanan
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Mencegah download/save video</small>
                                                </div>

                                                <!-- Completion Requirements -->
                                                <div class="config-item">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="require_completion" 
                                                               name="require_completion" value="1" {{ old('require_completion', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold" for="require_completion">
                                                            Wajib Selesai Menonton
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Peserta harus menonton video hingga selesai</small>
                                                </div>

                                                <div class="config-item">
                                                    <label for="min_watch_percentage" class="form-label">Persentase Minimal Tontonan</label>
                                                    <input type="range" class="form-range" id="min_watch_percentage" 
                                                           name="min_watch_percentage" min="50" max="100" step="5" 
                                                           value="{{ old('min_watch_percentage', 90) }}">
                                                    <div class="d-flex justify-content-between">
                                                        <small>50%</small>
                                                        <span id="percentage-value" class="fw-bold">90%</span>
                                                        <small>100%</small>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">Minimal persentase video yang harus ditonton</small>
                                                </div>

                                                <!-- Video Questions -->
                                                <div class="config-item">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="auto_pause_on_question" 
                                                               name="auto_pause_on_question" value="1" {{ old('auto_pause_on_question', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold" for="auto_pause_on_question">
                                                            Auto Pause saat Pertanyaan
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Video otomatis pause saat pertanyaan muncul</small>
                                                </div>

                                                <div class="config-item">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="require_question_completion" 
                                                               name="require_question_completion" value="1" {{ old('require_question_completion') ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold" for="require_question_completion">
                                                            Wajib Jawab Pertanyaan
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Harus menjawab pertanyaan untuk melanjutkan video</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Video Questions -->
                                        <div class="video-control-section fade-in" id="video-questions-section" style="display: none;">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0"><i class="mdi mdi-help-circle me-2"></i>Pertanyaan Video</h6>
                                                <button type="button" class="btn btn-success btn-sm" onclick="addVideoQuestion()">
                                                    <i class="mdi mdi-plus"></i> Tambah Pertanyaan
                                                </button>
                                            </div>
                                            <small class="text-muted d-block mb-3">Tambahkan pertanyaan yang muncul di waktu tertentu dalam video</small>
                                            
                                            <div id="video-questions-container">
                                                <!-- Video questions akan ditambahkan di sini -->
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
                                <div class="col-md-6">
                                    <label for="durasi_pretest" class="form-label">Durasi Pretest (menit) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('durasi_pretest') is-invalid @enderror" 
                                           id="durasi_pretest" name="durasi_pretest" 
                                           value="{{ old('durasi_pretest', 60) }}" min="1">
                                    @error('durasi_pretest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
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
                                <div class="col-md-6">
                                    <label for="durasi_posttest" class="form-label">Durasi Posttest (menit) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('durasi_posttest') is-invalid @enderror" 
                                           id="durasi_posttest" name="durasi_posttest" 
                                           value="{{ old('durasi_posttest', 60) }}" min="1">
                                    @error('durasi_posttest')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
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
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="mdi mdi-content-save me-2"></i> Simpan Materi
                                        </button>
                                        <a href="{{ route('admin.kursus.materials.index', $kursus) }}" class="btn btn-secondary">
                                            <i class="mdi mdi-cancel me-2"></i> Batal
                                        </a>
                                    </div>
                                    <div id="form-status"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Variabel untuk counter soal
let soalPretestCounter = 0;
let soalPosttestCounter = 0;
let videoQuestionCounter = 0;

// Variabel untuk menyimpan file yang dipilih
let selectedFiles = [];
let currentVideoFile = null;

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
        
        // Jika video dipilih, tampilkan config
        if (type === 'video' && isVisible) {
            document.getElementById('player-config-section').style.display = 'block';
            document.getElementById('video-questions-section').style.display = 'block';
            document.getElementById('duration-info-section').style.display = 'block';
        } else if (type === 'video' && !isVisible) {
            document.getElementById('player-config-section').style.display = 'none';
            document.getElementById('video-questions-section').style.display = 'none';
            document.getElementById('duration-info-section').style.display = 'none';
        }
        
        // Jika pretest/posttest dipilih, tambahkan soal default
        if ((type === 'pretest' || type === 'posttest') && isVisible) {
            const container = document.getElementById(`soal-${type}-container`);
            if (container.children.length === 0) {
                addSoal(type);
            }
        }
    }
}

// Toggle video type sections
function toggleVideoType() {
    const videoType = document.getElementById('video_type').value;
    const urlSection = document.getElementById('video-url-section');
    const fileSection = document.getElementById('video-file-section');
    const urlHelp = document.getElementById('url-help-text');
    const videoUrlInput = document.getElementById('video_url');
    
    // Reset
    urlSection.style.display = 'none';
    fileSection.style.display = 'none';
    document.getElementById('video-preview').style.display = 'none';
    document.getElementById('upload-progress').style.display = 'none';
    videoUrlInput.placeholder = 'Masukkan URL video';
    
    // Show appropriate section
    if (videoType === 'youtube') {
        urlSection.style.display = 'block';
        urlHelp.textContent = 'Format: https://youtube.com/watch?v=ID_VIDEO atau https://youtu.be/ID_VIDEO';
        videoUrlInput.placeholder = 'Contoh: https://youtube.com/watch?v=dQw4w9WgXcQ';
        document.getElementById('duration-display').textContent = 'Durasi akan dideteksi dari YouTube';
    } else if (videoType === 'vimeo') {
        urlSection.style.display = 'block';
        urlHelp.textContent = 'Format: https://vimeo.com/ID_VIDEO';
        videoUrlInput.placeholder = 'Contoh: https://vimeo.com/123456789';
        document.getElementById('duration-display').textContent = 'Durasi akan dideteksi dari Vimeo';
    } else if (videoType === 'external') {
        urlSection.style.display = 'block';
        urlHelp.textContent = 'Masukkan URL embed video (iframe src)';
        videoUrlInput.placeholder = 'Contoh: https://example.com/embed/video';
        document.getElementById('duration-display').textContent = 'Masukkan durasi manual jika diperlukan';
    } else if (videoType === 'hosted') {
        fileSection.style.display = 'block';
        document.getElementById('duration-display').textContent = 'Durasi akan dideteksi otomatis setelah upload';
    }
}

// Preview video file sebelum upload
function previewVideoFile(input) {
    const preview = document.getElementById('video-preview');
    const fileInfo = document.getElementById('video-file-info');
    const uploadProgress = document.getElementById('upload-progress');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        currentVideoFile = file;
        
        // Show preview
        preview.style.display = 'block';
        uploadProgress.style.display = 'none';
        
        // Show file info
        const fileSize = (file.size / (1024 * 1024)).toFixed(2);
        fileInfo.innerHTML = `
            <strong>${file.name}</strong><br>
            Size: ${fileSize} MB | Type: ${file.type}<br>
            Video akan diupload ke Google Drive secara otomatis.
        `;
        
        // Reset progress bar
        document.getElementById('upload-progress-bar').style.width = '0%';
        document.getElementById('upload-percentage').textContent = '0%';
        document.getElementById('upload-status').className = 'upload-status';
        document.getElementById('upload-status').innerHTML = '';
    } else {
        preview.style.display = 'none';
        currentVideoFile = null;
    }
}

// Update percentage value display
document.getElementById('min_watch_percentage').addEventListener('input', function() {
    document.getElementById('percentage-value').textContent = this.value + '%';
});

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

// Tambah pertanyaan video baru
function addVideoQuestion() {
    const container = document.getElementById('video-questions-container');
    const questionId = videoQuestionCounter;
    
    const newQuestion = document.createElement('div');
    newQuestion.className = 'video-question-item fade-in';
    newQuestion.innerHTML = `
        <div class="row mb-3">
            <div class="col-11">
                <h6 class="mb-3">Pertanyaan Video #${videoQuestionCounter + 1}</h6>
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeVideoQuestion(this)" title="Hapus Pertanyaan">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Waktu Muncul (detik)</label>
                <input type="number" class="form-control" name="video_questions[${questionId}][time_in_seconds]" 
                       min="0" value="0" placeholder="0">
                <small class="text-muted">Detik ke berapa pertanyaan muncul</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Poin</label>
                <input type="number" class="form-control" name="video_questions[${questionId}][points]" 
                       min="1" max="10" value="1">
                <small class="text-muted">Poin untuk jawaban benar (1-10)</small>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-12">
                <label class="form-label">Pertanyaan</label>
                <textarea class="form-control" name="video_questions[${questionId}][question]" 
                          rows="2" placeholder="Tulis pertanyaan di sini..." required></textarea>
            </div>
        </div>
        
        <div class="row mb-3">
            ${Array.from({length: 4}, (_, i) => `
            <div class="col-md-6 mb-2">
                <div class="input-group">
                    <div class="input-group-text">
                        <input type="radio" name="video_questions[${questionId}][correct_option]" value="${i}" required>
                    </div>
                    <input type="text" class="form-control" name="video_questions[${questionId}][options][]" 
                           placeholder="Pilihan ${String.fromCharCode(65 + i)}" required>
                </div>
            </div>
            `).join('')}
        </div>
        
        <div class="row">
            <div class="col-12">
                <label class="form-label">Penjelasan (Opsional)</label>
                                <textarea class="form-control" name="video_questions[${questionId}][explanation]" 
                          rows="2" placeholder="Penjelasan jawaban yang benar"></textarea>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="required_${questionId}" 
                           name="video_questions[${questionId}][required_to_continue]" value="1" checked>
                    <label class="form-check-label" for="required_${questionId}">
                        Wajib dijawab untuk melanjutkan video
                    </label>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(newQuestion);
    videoQuestionCounter++;
    
    // Update dynamic IDs for checkboxes
    const checkbox = newQuestion.querySelector(`[id^="required_"]`);
    if (checkbox) {
        checkbox.id = `required_${questionId}`;
        checkbox.nextElementSibling.setAttribute('for', `required_${questionId}`);
    }
}

// Hapus soal
function removeSoal(button, type) {
    const container = document.getElementById(`soal-${type}-container`);
    if (container.children.length > 1) {
        button.closest('.soal-item').remove();
        reindexSoal(type);
    } else {
        Swal.fire({
            title: 'Peringatan',
            text: 'Minimal harus ada 1 soal',
            icon: 'warning',
            confirmButtonColor: '#1e3c72'
        });
    }
}

// Hapus pertanyaan video
function removeVideoQuestion(button) {
    const container = document.getElementById('video-questions-container');
    if (container.children.length > 0) {
        button.closest('.video-question-item').remove();
        reindexVideoQuestions();
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

// Reindex video questions
function reindexVideoQuestions() {
    const container = document.getElementById('video-questions-container');
    const questions = container.querySelectorAll('.video-question-item');
    
    questions.forEach((item, index) => {
        // Update title
        const title = item.querySelector('h6');
        if (title) {
            title.textContent = `Pertanyaan Video #${index + 1}`;
        }
        
        // Update all input names
        const inputs = item.querySelectorAll('[name]');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            const newName = name.replace(/video_questions\[\d+\]/, `video_questions[${index}]`);
            input.setAttribute('name', newName);
        });
        
        // Update checkbox ID
        const checkbox = item.querySelector('[id^="required_"]');
        if (checkbox) {
            checkbox.id = `required_${index}`;
            checkbox.nextElementSibling.setAttribute('for', `required_${index}`);
        }
    });
    
    videoQuestionCounter = questions.length;
}

// Handle file selection - tambahkan file baru ke daftar tanpa menghapus yang lama
// GANTI fungsi handleFileSelection yang ada dengan ini:
function handleFileSelection(files) {
    if (files.length > 0) {
        // Clear existing files array
        selectedFiles = [];
        
        // Add all selected files
        Array.from(files).forEach(file => {
            selectedFiles.push(file);
        });
        
        // Update preview
        updateFilePreview();
        
        // Update hidden file inputs untuk form submission
        updateFileInputs();
    }
}

// Update file preview
function updateFilePreview() {
    const previewContainer = document.getElementById('file-preview');
    const fileList = document.getElementById('file-list');
    
    if (selectedFiles.length > 0) {
        fileList.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div class="flex-grow-1">
                    <i class="mdi mdi-file-document me-2"></i>
                    ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                </div>
                <div class="file-actions">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${index})" title="Hapus File">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            `;
            fileList.appendChild(fileItem);
        });
        
        previewContainer.style.display = 'block';
    } else {
        previewContainer.style.display = 'none';
    }
}

// Hapus file dari daftar
function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateFilePreview();
    updateFileInputs();
}

// Update hidden file inputs untuk form submission
// GANTI fungsi updateFileInputs yang ada dengan ini:
function updateFileInputs() {
    const container = document.getElementById('file-inputs-container');
    container.innerHTML = '';
    
    // Buat DataTransfer object untuk multiple files
    const dataTransfer = new DataTransfer();
    
    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });
    
    // Buat input file baru dengan semua file yang dipilih
    const newInput = document.createElement('input');
    newInput.type = 'file';
    newInput.name = 'file_path[]';
    newInput.multiple = true;
    newInput.style.display = 'none';
    
    // Set files menggunakan DataTransfer
    newInput.files = dataTransfer.files;
    
    container.appendChild(newInput);
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
            
            // Jika video sudah dipilih, init video type
            if (type === 'video') {
                setTimeout(() => {
                    const videoType = document.getElementById('video_type').value;
                    if (videoType) {
                        toggleVideoType();
                    }
                }, 100);
            }
        }
    });

    // Initialize percentage display
    const percentageSlider = document.getElementById('min_watch_percentage');
    if (percentageSlider) {
        document.getElementById('percentage-value').textContent = percentageSlider.value + '%';
    }

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
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

// Validasi form sebelum submit
document.getElementById('materialForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const contentTypes = Array.from(document.querySelectorAll('input[name="content_types[]"]:checked'))
        .map(cb => cb.value);
    
    if (contentTypes.length === 0) {
        Swal.fire({
            title: 'Peringatan',
            text: 'Pilih minimal satu jenis konten materi',
            icon: 'warning',
            confirmButtonColor: '#1e3c72'
        });
        return false;
    }
    
    // Validasi untuk setiap konten yang dipilih
    let warnings = [];
    
    if (contentTypes.includes('file') && selectedFiles.length === 0) {
        warnings.push('File materi untuk konten PDF/PPT belum dipilih');
    }
    
    if (contentTypes.includes('video')) {
        const videoType = document.getElementById('video_type').value;
        if (!videoType) {
            warnings.push('Jenis video harus dipilih');
        } else {
            if (videoType === 'hosted') {
                const videoFile = document.getElementById('video_file').value;
                if (!videoFile) {
                    warnings.push('File video harus diupload untuk Google Drive');
                }
            } else {
                const videoUrl = document.getElementById('video_url').value;
                if (!videoUrl) {
                    warnings.push('URL video harus diisi');
                } else if (!isValidUrl(videoUrl)) {
                    warnings.push('URL video tidak valid');
                }
            }
        }
    }
    
    if (contentTypes.includes('pretest')) {
        const soalPretestContainer = document.getElementById('soal-pretest-container');
        if (soalPretestContainer.children.length === 0) {
            warnings.push('Pretest harus memiliki minimal 1 soal');
        }
        
        const durasiPretest = document.getElementById('durasi_pretest').value;
        if (!durasiPretest || durasiPretest < 1) {
            warnings.push('Durasi pretest harus diisi (minimal 1 menit)');
        }
    }
    
    if (contentTypes.includes('posttest')) {
        const soalPosttestContainer = document.getElementById('soal-posttest-container');
        if (soalPosttestContainer.children.length === 0) {
            warnings.push('Posttest harus memiliki minimal 1 soal');
        }
        
        const durasiPosttest = document.getElementById('durasi_posttest').value;
        if (!durasiPosttest || durasiPosttest < 1) {
            warnings.push('Durasi posttest harus diisi (minimal 1 menit)');
        }
    }
    
    // Validasi tidak boleh pilih pretest dan posttest bersamaan
    if (contentTypes.includes('pretest') && contentTypes.includes('posttest')) {
        warnings.push('Tidak dapat memilih pretest dan posttest bersamaan dalam satu materi');
    }
    
    // Jika ada warnings, tampilkan konfirmasi
    if (warnings.length > 0) {
        const warningMessage = 'Perhatikan hal berikut:\n\n' + warnings.join('\n');
        
        Swal.fire({
            title: 'Data Belum Lengkap',
            text: warningMessage,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1e3c72',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Perbaiki Data'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user memilih untuk lanjut, submit form
                submitForm();
            }
        });
    } else {
        // Jika tidak ada warning, langsung submit
        submitForm();
    }
});

// Function untuk submit form
function submitForm() {
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Disable button dan ubah text
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-2"></i> Menyimpan...';
    
    // Jika ada video hosted, tampilkan progress
    const videoType = document.getElementById('video_type').value;
    if (videoType === 'hosted' && currentVideoFile) {
        document.getElementById('upload-progress').style.display = 'block';
        document.getElementById('upload-status').className = 'upload-status status-uploading';
        document.getElementById('upload-status').innerHTML = 'Mengupload ke Google Drive...';
        
        // Simulasi progress (di backend sebenarnya dihandle oleh Laravel)
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += 10;
            document.getElementById('upload-progress-bar').style.width = progress + '%';
            document.getElementById('upload-percentage').textContent = progress + '%';
            
            if (progress >= 100) {
                clearInterval(progressInterval);
                document.getElementById('upload-status').className = 'upload-status status-success';
                document.getElementById('upload-status').innerHTML = 'Upload berhasil!';
            }
        }, 200);
    }
    
    // Submit form
    document.getElementById('materialForm').submit();
}
</script>
@endsection