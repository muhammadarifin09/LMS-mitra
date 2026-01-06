@extends('layouts.admin')

@section('title', 'MOOC BPS - Materi Kursus: ' . $kursus->judul_kursus)

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
<!-- TAMBAHKAN SORTABLE JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.css">
<style>
    .page-title-box {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #1e3c72;
        font-weight: 600;
    }

    .material-actions {
        display: flex;
        gap: 8px;
        flex-wrap: nowrap;
    }

    .btn-sm-action {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: none;
        transition: all 0.3s ease;
    }

    .material-content {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
    }

    .content-item {
        margin-bottom: 10px;
        padding: 10px;
        background: white;
        border-radius: 6px;
        border-left: 4px solid #1e3c72;
    }

    .stats-item {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 4px solid;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* TAMBAHKAN/UPDATE STYLE INI DI BAGIAN CSS */

/* ANIMASI DRAG & DROP YANG LEBIH BAGUS */
.sortable-ghost {
    opacity: 0.4;
    background: linear-gradient(135deg, rgba(30, 60, 114, 0.1) 0%, rgba(42, 82, 152, 0.1) 100%);
    border: 2px dashed #1e3c72 !important;
    transform: scale(0.98);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.sortable-dragging {
    opacity: 0.9 !important;
    transform: rotate(0deg) scale(1.02) !important;
    box-shadow: 0 15px 30px rgba(30, 60, 114, 0.2), 
                0 5px 15px rgba(0, 0, 0, 0.1) !important;
    z-index: 9999 !important;
    border: 2px solid #1e3c72 !important;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    animation: pulse-drag 2s infinite alternate;
}

@keyframes pulse-drag {
    0% {
        box-shadow: 0 15px 30px rgba(30, 60, 114, 0.2), 
                    0 5px 15px rgba(0, 0, 0, 0.1);
    }
    100% {
        box-shadow: 0 20px 40px rgba(30, 60, 114, 0.3), 
                    0 8px 20px rgba(0, 0, 0, 0.15),
                    0 0 0 3px rgba(30, 60, 114, 0.1);
    }
}

.sortable-placeholder {
    background: linear-gradient(135deg, rgba(30, 60, 114, 0.05) 0%, rgba(42, 82, 152, 0.05) 100%) !important;
    border: 3px dashed #1e3c72 !important;
    margin: 8px 0 !important;
    border-radius: 12px !important;
    min-height: 85px !important;
    animation: placeholder-pulse 2s ease-in-out infinite;
}

@keyframes placeholder-pulse {
    0%, 100% {
        border-color: #1e3c72;
        background: linear-gradient(135deg, rgba(30, 60, 114, 0.05) 0%, rgba(42, 82, 152, 0.05) 100%);
    }
    50% {
        border-color: #2a5298;
        background: linear-gradient(135deg, rgba(30, 60, 114, 0.1) 0%, rgba(42, 82, 152, 0.1) 100%);
    }
}

/* Handle drag yang lebih menarik */
.sortable-handle {
    cursor: move !important;
    color: #6c757d;
    padding: 0 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    background: rgba(0, 0, 0, 0.03);
}

.sortable-handle:hover {
    color: #1e3c72 !important;
    background: rgba(30, 60, 114, 0.1);
    transform: scale(1.1);
}

.sortable-handle i {
    font-size: 20px;
}

/* Efek saat mode drag aktif */
.accordion-item.sortable-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin: 5px 0;
}

.accordion-item.sortable-item:hover:not(.sortable-dragging) {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Order badge yang lebih menarik */
.order-badge {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    font-weight: bold;
    min-width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 14px;
    box-shadow: 0 4px 8px rgba(30, 60, 114, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.sortable-dragging .order-badge {
    transform: scale(1.2);
    box-shadow: 0 6px 12px rgba(30, 60, 114, 0.4);
    animation: badge-pulse 1.5s infinite alternate;
}

@keyframes badge-pulse {
    0% {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }
    100% {
        background: linear-gradient(135deg, #2a5298 0%, #3a6fd8 100%);
    }
}

/* Panel kontrol yang lebih menarik */
.sort-control-panel {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    border: 2px solid #1e3c72;
    box-shadow: 0 8px 25px rgba(30, 60, 114, 0.15);
    animation: panel-appear 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes panel-appear {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.sort-info {
    background: white;
    padding: 15px;
    border-radius: 10px;
    font-size: 0.95rem;
    color: #495057;
    border-left: 4px solid #1e3c72;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}


@keyframes danger-pulse {
    0% {
        border-color: #dc3545;
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
    }
    70% {
        border-color: #dc3545;
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
    100% {
        border-color: #dc3545;
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

/* Smooth transition untuk semua elemen */
.sortable-item * {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* Efek saat drag dimulai */
.sortable-drag-start {
    opacity: 0.8;
    cursor: grabbing !important;
}

/* Feedback visual saat berhasil disimpan */
.save-success {
    animation: save-success 2s ease-out;
}

@keyframes save-success {
    0% {
        background-color: #d4edda;
        transform: scale(1);
    }
    50% {
        background-color: #c3e6cb;
        transform: scale(1.02);
    }
    100% {
        background-color: #d4edda;
        transform: scale(1);
    }
}

    .stats-item.attendance {
        border-left-color: #28a745;
    }

    .stats-item.material {
        border-left-color: #17a2b8;
    }

    .stats-item.video {
        border-left-color: #ffc107;
    }

    .stats-item.pretest {
        border-left-color: #6f42c1;
    }

    .stats-item.posttest {
        border-left-color: #e83e8c;
    }

    .stats-number {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stats-label {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .progress-container {
        margin-top: 8px;
    }

    .progress {
        height: 6px;
        margin-bottom: 5px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .test-results {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        border: 1px solid #e9ecef;
    }

    .result-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .result-item:last-child {
        border-bottom: none;
    }

    .score-badge {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .score-passed {
        background: #d4edda;
        color: #155724;
    }

    .score-failed {
        background: #f8d7da;
        color: #721c24;
    }
    
    /* STYLE UNTUK DRAG & DROP */
    .sortable-handle {
        cursor: move;
        color: #6c757d;
        padding: 0 10px;
        transition: color 0.3s;
    }
    
    .sortable-handle:hover {
        color: #1e3c72;
    }
    
    .accordion-button .drag-indicator {
        margin-right: 10px;
        opacity: 0.6;
        transition: opacity 0.3s;
    }
    
    .accordion-button:hover .drag-indicator {
        opacity: 1;
    }
    
    .sortable-placeholder {
        border: 2px dashed #1e3c72;
        background-color: rgba(30, 60, 114, 0.1);
        margin: 5px 0;
        border-radius: 8px;
        min-height: 80px;
    }
    
    .sortable-dragging {
        opacity: 0.8;
        transform: rotate(2deg);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .order-badge {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        font-weight: bold;
        min-width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }
    
    .swal2-popup {
        border-radius: 12px !important;
        padding: 20px !important;
    }
    
    .swal2-title {
        font-size: 1.3rem !important;
        color: #1e3c72 !important;
    }
    
    .swal2-html-container {
        font-size: 1rem !important;
        color: #6c757d !important;
    }
    
    .swal2-confirm {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
        border: none !important;
        padding: 10px 25px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
    }
    
    .swal2-cancel {
        background: #6c757d !important;
        border: none !important;
        padding: 10px 25px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
    }
    
    .status-badge {
        font-size: 0.8rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .status-active {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-inactive {
        background-color: #e2e3e5;
        color: #383d41;
        border: 1px solid #d6d8db;
    }
    
    /* Control Panel untuk Sortable */
    .sort-control-panel {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    
    .sort-info {
        background: white;
        padding: 10px;
        border-radius: 6px;
        font-size: 0.9rem;
        color: #6c757d;
        border-left: 3px solid #1e3c72;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="page-title mb-0">
                            <i class="mdi mdi-book-open-variant me-2"></i>
                            Materi Kursus: {{ $kursus->judul_kursus }}
                        </h4>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="{{ route('admin.kursus.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Kursus
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Action Bar -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <a href="{{ route('admin.kursus.materials.create', $kursus) }}" class="btn btn-primary">
                                <i class="mdi mdi-plus-circle me-2"></i> Tambah Materi Baru
                            </a>
                            
                            <!-- TOMBOL UNTUK MENGUBAH MODE DRAG & DROP -->
                            <button type="button" id="toggleSortMode" class="btn btn-outline-primary ms-2">
                                <i class="mdi mdi-drag me-1"></i> Ubah Urutan (Drag & Drop)
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="stats-container">
                                <span class="badge bg-primary">
                                    <i class="mdi mdi-book-multiple me-1"></i>
                                    Total: {{ $materials->count() }}
                                </span>
                                <span class="badge bg-success">
                                    <i class="mdi mdi-check-circle me-1"></i>
                                    Aktif: {{ $materials->where('is_active', true)->count() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- PANEL KONTROL SORTABLE (HIDDEN DEFAULT) -->
                    <div id="sortControlPanel" class="sort-control-panel" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="sort-info">
                                    <i class="mdi mdi-information-outline me-2"></i>
                                    <strong>Mode Drag & Drop Aktif</strong> - Seret materi untuk mengubah urutan. Klik "Simpan Urutan" untuk menyimpan perubahan.
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" id="saveOrder" class="btn btn-success">
                                    <i class="mdi mdi-content-save me-1"></i> Simpan Urutan
                                </button>
                                <button type="button" id="cancelSort" class="btn btn-secondary ms-2">
                                    <i class="mdi mdi-close me-1"></i> Batal
                                </button>
                            </div>
                        </div>
                    </div>

                    @if($materials->count() > 0)
                    <!-- Materials Accordion -->
                    <div id="materialsAccordion" class="accordion">
                        @foreach($materials->sortBy('order') as $material)
                        @php
                            // Hitung statistik progress untuk materi ini
                            $totalPeserta = $kursus->enrollments->count();
                            $progressData = $material->progress ?? collect();
                            
                            $jumlahHadir = $progressData->where('attendance_status', 'completed')->count();
                            $jumlahDownload = $progressData->where('material_status', 'completed')->count();
                            $jumlahTonton = $progressData->where('video_status', 'completed')->count();
                            
                            // Hitung statistik test
                            $jumlahPretest = $progressData->whereNotNull('pretest_score')->count();
                            $jumlahPosttest = $progressData->whereNotNull('posttest_score')->count();
                            
                            $pretestLulus = $progressData->where('pretest_score', '>=', $material->passing_grade ?? 70)->count();
                            $posttestLulus = $progressData->where('posttest_score', '>=', $material->passing_grade ?? 70)->count();
                            
                            // Hitung persentase
                            $persentaseHadir = $totalPeserta > 0 ? round(($jumlahHadir / $totalPeserta) * 100) : 0;
                            $persentaseDownload = $totalPeserta > 0 ? round(($jumlahDownload / $totalPeserta) * 100) : 0;
                            $persentaseTonton = $totalPeserta > 0 ? round(($jumlahTonton / $totalPeserta) * 100) : 0;
                            $persentasePretest = $totalPeserta > 0 ? round(($jumlahPretest / $totalPeserta) * 100) : 0;
                            $persentasePosttest = $totalPeserta > 0 ? round(($jumlahPosttest / $totalPeserta) * 100) : 0;
                            
                            // Rata-rata nilai
                            $rataRataPretest = $progressData->whereNotNull('pretest_score')->avg('pretest_score');
                            $rataRataPosttest = $progressData->whereNotNull('posttest_score')->avg('posttest_score');
                        @endphp
                        
                        <div class="accordion-item sortable-item" data-id="{{ $material->id }}" data-order="{{ $material->order }}">
                            <h2 class="accordion-header" id="heading{{ $material->id }}">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $material->id }}" 
                                        aria-expanded="false" 
                                        aria-controls="collapse{{ $material->id }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div class="d-flex align-items-center">
                                            <!-- HANDLE UNTUK DRAG & DROP -->
                                            <span class="sortable-handle me-2" style="display: none;">
                                                <i class="mdi mdi-drag-vertical"></i>
                                            </span>
                                            
                                            <!-- BADGE NOMOR URUT -->
                                            <span class="order-badge">
                                                {{ $material->order }}
                                            </span>
                                            
                                            <div>
                                                <h6 class="mb-0">{{ $material->title }}</h6>
                                                <small class="text-muted">
                                                    Tipe: 
                                                    @if($material->type == 'pre_test')
                                                        <span class="badge bg-warning">Pretest</span>
                                                    @elseif($material->type == 'post_test')
                                                        <span class="badge bg-info">Posttest</span>
                                                    @else
                                                        <span class="badge bg-success">Materi</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            @if($material->attendance_required)
                                            <span class="badge bg-warning me-2">
                                                <i class="mdi mdi-clipboard-check me-1"></i>Wajib Hadir
                                            </span>
                                            @endif
                                            <span class="badge bg-{{ $material->is_active ? 'success' : 'secondary' }} me-2">
                                                {{ $material->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $material->id }}" class="accordion-collapse collapse" 
                                 aria-labelledby="heading{{ $material->id }}" 
                                 data-bs-parent="#materialsAccordion">
                                <div class="accordion-body">
                                    <!-- Statistik Progress Peserta -->
                                    <div class="stats-grid">
                                        <!-- Statistik Kehadiran -->
                                        @if($material->attendance_required)
                                        <div class="stats-item attendance">
                                            <div class="stats-number text-success">
                                                {{ $jumlahHadir }}/{{ $totalPeserta }}
                                            </div>
                                            <div class="stats-label">
                                                <i class="mdi mdi-clipboard-check me-1"></i>
                                                Peserta Sudah Hadir
                                            </div>
                                            <div class="progress-container">
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ $persentaseHadir }}%"
                                                         aria-valuenow="{{ $persentaseHadir }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $persentaseHadir }}%</small>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Statistik Download Materi -->
                                        @if($material->file_path)
                                        <div class="stats-item material">
                                            <div class="stats-number text-info">
                                                {{ $jumlahDownload }}/{{ $totalPeserta }}
                                            </div>
                                            <div class="stats-label">
                                                <i class="mdi mdi-download me-1"></i>
                                                Peserta Sudah Download Materi
                                            </div>
                                            <div class="progress-container">
                                                <div class="progress">
                                                    <div class="progress-bar bg-info" role="progressbar" 
                                                         style="width: {{ $persentaseDownload }}%"
                                                         aria-valuenow="{{ $persentaseDownload }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $persentaseDownload }}%</small>
                                            </div>
                                            @if($material->file_path)
                                            <small class="text-muted d-block mt-1">
                                                File: {{ basename($material->file_path) }}
                                            </small>
                                            @endif
                                        </div>
                                        @endif

                                        <!-- Statistik Menonton Video -->
                                        @if($material->video_url)
                                        <div class="stats-item video">
                                            <div class="stats-number text-warning">
                                                {{ $jumlahTonton }}/{{ $totalPeserta }}
                                            </div>
                                            <div class="stats-label">
                                                <i class="mdi mdi-play-circle me-1"></i>
                                                Peserta Sudah Menonton Video
                                            </div>
                                            <div class="progress-container">
                                                <div class="progress">
                                                    <div class="progress-bar bg-warning" role="progressbar" 
                                                         style="width: {{ $persentaseTonton }}%"
                                                         aria-valuenow="{{ $persentaseTonton }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $persentaseTonton }}%</small>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Statistik Pretest -->
                                        @if(is_array($material->soal_pretest) && count($material->soal_pretest) > 0)
                                        <div class="stats-item pretest">
                                            <div class="stats-number text-purple">
                                                {{ $jumlahPretest }}/{{ $totalPeserta }}
                                            </div>
                                            <div class="stats-label">
                                                <i class="mdi mdi-clipboard-text me-1"></i>
                                                Peserta Sudah Mengerjakan Pretest
                                            </div>
                                            <div class="progress-container">
                                                <div class="progress">
                                                    <div class="progress-bar bg-purple" role="progressbar" 
                                                         style="width: {{ $persentasePretest }}%"
                                                         aria-valuenow="{{ $persentasePretest }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $persentasePretest }}%</small>
                                            </div>
                                            @if($jumlahPretest > 0)
                                            <small class="text-muted d-block mt-1">
                                                Lulus: {{ $pretestLulus }} | Rata-rata: {{ round($rataRataPretest, 1) }}%
                                            </small>
                                            @endif
                                        </div>
                                        @endif

                                        <!-- Statistik Posttest -->
                                        @if($material->soal_posttest && count($material->soal_posttest) > 0)
                                        <div class="stats-item posttest">
                                            <div class="stats-number text-pink">
                                                {{ $jumlahPosttest }}/{{ $totalPeserta }}
                                            </div>
                                            <div class="stats-label">
                                                <i class="mdi mdi-clipboard-check me-1"></i>
                                                Peserta Sudah Mengerjakan Posttest
                                            </div>
                                            <div class="progress-container">
                                                <div class="progress">
                                                    <div class="progress-bar bg-pink" role="progressbar" 
                                                         style="width: {{ $persentasePosttest }}%"
                                                         aria-valuenow="{{ $persentasePosttest }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $persentasePosttest }}%</small>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Detail Hasil Test -->
                                    @if(($material->soal_pretest && $jumlahPretest > 0) || ($material->soal_posttest && $jumlahPosttest > 0))
                                    <div class="test-results">
                                        <h6 class="mb-3"><i class="mdi mdi-chart-bar me-2"></i>Detail Hasil Test</h6>
                                        
                                        @if($material->soal_pretest && $jumlahPretest > 0)
                                        <div class="result-item">
                                            <div>
                                                <strong>Pretest</strong>
                                                <small class="text-muted d-block">
                                                    {{ $pretestLulus }} dari {{ $jumlahPretest }} peserta lulus
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="score-badge {{ $pretestLulus >= $jumlahPretest * 0.7 ? 'score-passed' : 'score-failed' }}">
                                                    {{ $persentasePretest }}% Selesai
                                                </span>
                                                <div class="text-muted small mt-1">
                                                    Rata-rata: {{ round($rataRataPretest, 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($material->soal_posttest && $jumlahPosttest > 0)
                                        <div class="result-item">
                                            <div>
                                                <strong>Posttest</strong>
                                            </div>
                                            <div class="text-end">
                                                <span class="score-badge {{ $posttestLulus >= $jumlahPosttest * 0.7 ? 'score-passed' : 'score-failed' }}">
                                                    {{ $persentasePosttest }}% Selesai
                                                </span>
                                                <div class="text-muted small mt-1">
                                                    Rata-rata: {{ round($rataRataPosttest, 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($material->passing_grade)
                                        <div class="mt-2 text-center">
                                            <small class="text-muted">
                                                <i class="mdi mdi-trophy me-1"></i>
                                                Passing Grade: {{ $material->passing_grade }}%
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Informasi Tambahan Materi -->
                                    <div class="material-content mt-3">
                                        <div class="row text-muted small">
                                            @if($material->duration_file)
                                            <div class="col-md-4 mb-2">
                                                <i class="mdi mdi-clock me-1"></i>
                                                Durasi File: {{ $material->duration_file }} menit
                                            </div>
                                            @endif
                                            @if($material->duration_video)
                                            <div class="col-md-4 mb-2">
                                                <i class="mdi mdi-video me-1"></i>
                                                Durasi Video: {{ $material->duration_video }} menit
                                            </div>
                                            @endif
                                            @if($material->durasi_pretest)
                                            <div class="col-md-4 mb-2">
                                                <i class="mdi mdi-timer me-1"></i>
                                                Durasi Test: {{ $material->durasi_pretest }} menit
                                            </div>
                                            @endif
                
                                        </div>

                                        <!-- Informasi Soal Test -->
                                        @if(is_array($material->soal_pretest) && count($material->soal_pretest) > 0)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="mdi mdi-clipboard-text me-1"></i>
                                                Jumlah Soal Pretest: {{ count($material->soal_pretest) }}
                                            </small>
                                        </div>
                                        @endif

                                        @if($material->soal_posttest && count($material->soal_posttest) > 0)
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <i class="mdi mdi-clipboard-check me-1"></i>
                                                Jumlah Soal Posttest: {{ count($material->soal_posttest) }}
                                            </small>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                                        <div class="material-actions">
                                            <a href="{{ route('admin.kursus.materials.edit', [$kursus, $material]) }}" 
                                               class="btn btn-warning btn-sm">
                                                <i class="mdi mdi-pencil me-1"></i> Edit
                                            </a>
                                            
                                            <!-- Tombol Status yang sudah diperbaiki -->
                                            <form action="{{ route('admin.kursus.materials.status', [$kursus, $material]) }}" 
                                                  method="POST" class="d-inline status-form">
                                                @csrf
                                                <input type="hidden" name="is_active" value="{{ $material->is_active ? 0 : 1 }}">
                                                <button type="submit" class="btn btn-{{ $material->is_active ? 'secondary' : 'success' }} btn-sm status-button">
                                                    <i class="mdi mdi-{{ $material->is_active ? 'eye-off' : 'eye' }} me-1"></i>
                                                    {{ $material->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <form action="{{ route('admin.kursus.materials.destroy', [$kursus, $material]) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirmDelete(event, {{ $material->id }})">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="mdi mdi-delete me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="mdi mdi-book-open-variant mdi-48px text-muted mb-3"></i>
                        <h4 class="text-muted">Belum Ada Materi</h4>
                        <p class="text-muted mb-4">
                            Mulai dengan menambahkan materi pertama untuk kursus ini.
                        </p>
                        <a href="{{ route('admin.kursus.materials.create', $kursus) }}" class="btn btn-primary">
                            <i class="mdi mdi-plus-circle me-2"></i> Tambah Materi Pertama
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- TAMBAHKAN SORTABLE JS LIBRARY -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi confirmDelete yang benar
    function confirmDelete(event, materialId) {
        event.preventDefault();
        console.log('Deleting material ID:', materialId);
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Materi akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                const submitBtn = event.target.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menghapus...';
                submitBtn.disabled = true;
                
                event.target.submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert for status changes with AJAX
        const statusForms = document.querySelectorAll('form[action*="status"]');
        statusForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const url = this.action;
                const method = 'POST';
                const button = this.querySelector('button[type="submit"]');
                const originalText = button.innerHTML;
                
                // Get current status for confirmation message
                const currentStatus = this.querySelector('input[name="is_active"]').value == 1 ? 'Aktif' : 'Nonaktif';
                const newStatus = currentStatus === 'Aktif' ? 'Nonaktif' : 'Aktif';
                
                Swal.fire({
                    title: 'Konfirmasi Perubahan Status',
                    html: `Apakah Anda yakin ingin mengubah status materi dari <strong>${currentStatus}</strong> menjadi <strong>${newStatus}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal',
                    showLoaderOnConfirm: true,
                    preConfirm: async () => {
                        try {
                            const response = await fetch(url, {
                                method: method,
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                }
                            });
                            
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            
                            return await response.json();
                        } catch (error) {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        }
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const response = result.value;
                        
                        if (response.success) {
                            // Tampilkan notifikasi sukses
                            Swal.fire({
                                title: 'Berhasil!',
                                html: `<strong>${response.message}</strong><br>Status berhasil diubah menjadi <span class="badge bg-${response.new_status === 'Aktif' ? 'success' : 'secondary'}">${response.new_status}</span>`,
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                timer: 3000,
                                timerProgressBar: true,
                                didClose: () => {
                                    // Update UI tanpa reload
                                    const badge = form.closest('.accordion-item').querySelector('.badge.bg-success, .badge.bg-secondary');
                                    const button = form.querySelector('button[type="submit"]');
                                    const statusInput = form.querySelector('input[name="is_active"]');
                                    
                                    // Update badge
                                    if (response.new_status === 'Aktif') {
                                        badge.className = 'badge bg-success me-2';
                                        badge.textContent = 'Aktif';
                                        button.className = 'btn btn-secondary btn-sm';
                                        button.innerHTML = '<i class="mdi mdi-eye-off me-1"></i> Nonaktifkan';
                                        statusInput.value = 0;
                                    } else {
                                        badge.className = 'badge bg-secondary me-2';
                                        badge.textContent = 'Nonaktif';
                                        button.className = 'btn btn-success btn-sm';
                                        button.innerHTML = '<i class="mdi mdi-eye me-1"></i> Aktifkan';
                                        statusInput.value = 1;
                                    }
                                }
                            });
                        } else {
                            // Tampilkan error
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat mengubah status',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    }
                });
            });
        });
        
        // ============================================
        // KODE UNTUK DRAG & DROP FUNCTIONALITY
        // ============================================
        
        let sortable = null;
        let isSortMode = false;
        const accordion = document.getElementById('materialsAccordion');
        const sortControlPanel = document.getElementById('sortControlPanel');
        const toggleSortModeBtn = document.getElementById('toggleSortMode');
        const saveOrderBtn = document.getElementById('saveOrder');
        const cancelSortBtn = document.getElementById('cancelSort');
        
        // Toggle sort mode
        toggleSortModeBtn.addEventListener('click', function() {
            if (!isSortMode) {
                enableSortMode();
            } else {
                disableSortMode();
            }
        });
        
        // Save order
        saveOrderBtn.addEventListener('click', saveOrder);
        
        // Cancel sort
        cancelSortBtn.addEventListener('click', disableSortMode);
        
        function enableSortMode() {
            isSortMode = true;
            
            // Show control panel
            sortControlPanel.style.display = 'block';
            
            // Change button text
            toggleSortModeBtn.innerHTML = '<i class="mdi mdi-close me-1"></i> Keluar Mode Drag & Drop';
            toggleSortModeBtn.className = 'btn btn-outline-danger ms-2';
            
            // Show drag handles
            document.querySelectorAll('.sortable-handle').forEach(handle => {
                handle.style.display = 'inline-block';
            });
            
            // Collapse all accordions
            const collapses = document.querySelectorAll('.accordion-collapse');
            collapses.forEach(collapse => {
                if (collapse.classList.contains('show')) {
                    collapse.classList.remove('show');
                }
            });
            
            // Disable all action buttons
            document.querySelectorAll('.material-actions a, .material-actions button, form button[type="submit"]').forEach(btn => {
                if (!btn.classList.contains('sortable-handle')) {
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.6';
                }
            });
            
            // Initialize Sortable
            sortable = new Sortable(accordion, {
                animation: 150,
                handle: '.sortable-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-dragging',
                dragClass: 'sortable-drag',
                filter: '.accordion-collapse',
                preventOnFilter: false,
                onStart: function() {
                    // Update all badges when dragging starts
                    updateOrderBadges();
                },
                onEnd: function() {
                    // Update badges after dragging
                    updateOrderBadges();
                }
            });
        }
        
        function disableSortMode() {
            if (sortable) {
                sortable.destroy();
                sortable = null;
            }
            
            isSortMode = false;
            
            // Hide control panel
            sortControlPanel.style.display = 'none';
            
            // Reset button
            toggleSortModeBtn.innerHTML = '<i class="mdi mdi-drag me-1"></i> Ubah Urutan (Drag & Drop)';
            toggleSortModeBtn.className = 'btn btn-outline-primary ms-2';
            
            // Hide drag handles
            document.querySelectorAll('.sortable-handle').forEach(handle => {
                handle.style.display = 'none';
            });
            
            // Enable all action buttons
            document.querySelectorAll('.material-actions a, .material-actions button, form button[type="submit"]').forEach(btn => {
                btn.style.pointerEvents = '';
                btn.style.opacity = '';
            });
            
            // Reset to original order (reload page or reset from stored data)
            // For simplicity, we'll just reload the page
            location.reload();
        }

        function updateAllOrderNumbers() {
    const items = document.querySelectorAll('.sortable-item');
    
    items.forEach((item, index) => {
        const badge = item.querySelector('.order-badge');
        const orderInput = item.querySelector('input[name="order"]');
        
        if (badge) {
            badge.textContent = index + 1;
        }
        
        if (orderInput) {
            orderInput.value = index + 1;
        }
        
        // Update data attribute
        item.setAttribute('data-order', index + 1);
    });
}
        
        function updateOrderBadges() {
            const items = document.querySelectorAll('.sortable-item');
            items.forEach((item, index) => {
                const badge = item.querySelector('.order-badge');
                if (badge) {
                    badge.textContent = index + 1;
                }
                // Update data-order attribute
                item.setAttribute('data-order', index + 1);
            });
        }
        
        async function saveOrder() {
    const items = document.querySelectorAll('.sortable-item');
    const materials = [];
    
    // Update UI terlebih dahulu
    updateAllOrderNumbers();
    
    items.forEach((item, index) => {
        const id = item.getAttribute('data-id');
        const order = index + 1;
        
        materials.push({
            id: id,
            order: order
        });
    });
            
            // Show loading
            const originalText = saveOrderBtn.innerHTML;
            saveOrderBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...';
            saveOrderBtn.disabled = true;
            
            try {
                const response = await fetch('{{ route("admin.kursus.materials.update-order", $kursus) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ materials: materials })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show success message
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        timer: 2000,
                        timerProgressBar: true,
                        didClose: () => {
                            // Exit sort mode and reload
                            disableSortMode();
                            location.reload();
                        }
                    });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
                
            } catch (error) {
                console.error('Error saving order:', error);
                
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan urutan: ' + error.message,
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
                
                // Reset button
                saveOrderBtn.innerHTML = originalText;
                saveOrderBtn.disabled = false;
            }
        }
    });
</script>
@endsection