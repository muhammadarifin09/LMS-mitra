@extends('layouts.admin')

@section('title', 'MOCC BPS - Materi Kursus: ' . $kursus->judul_kursus)

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

                    @if($materials->count() > 0)
                    <!-- Materials Accordion -->
                    <div class="accordion" id="materialsAccordion">
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
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $material->id }}">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $material->id }}" 
                                        aria-expanded="false" 
                                        aria-controls="collapse{{ $material->id }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-book-open-page-variant me-3 text-primary"></i>
                                            <div>
                                                <h6 class="mb-0">{{ $material->title }}</h6>
                                                <small class="text-muted">
                                                    Urutan: {{ $material->order }} | 
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
                                        @if($material->soal_pretest && count($material->soal_pretest) > 0)
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
                                        @if($material->soal_pretest && count($material->soal_pretest) > 0)
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
        
        <form action="{{ route('admin.kursus.materials.status', [$kursus, $material]) }}" 
              method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="is_active" value="{{ $material->is_active ? 0 : 1 }}">
            <button type="submit" class="btn btn-{{ $material->is_active ? 'secondary' : 'success' }} btn-sm">
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
        // SweetAlert for status changes
        const statusForms = document.querySelectorAll('form[action*="status"]');
        statusForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Konfirmasi',
                    text: "Apakah Anda yakin ingin mengubah status materi?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>
@endsection