{{-- resources/views/mitra/kursus-detail.blade.php --}}
@extends('mitra.layouts.app')

@section('title', 'MOCC BPS - Detail Kursus')

@section('content')
<style>
    /* HIDE SIDEBAR */
    .sidebar {
        display: none !important;
    }
    
    /* Adjust main content to full width */
    .main-content {
        margin-left: 30 !important;
        width: 100% !important;
    }

    .sequential-flow {
        max-width: 3000px;
        margin: 0 auto;
    }

    .flow-step {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-left: 4px solid #e9ecef;
        transition: all 0.3s;
    }

    .flow-step.current {
        border-left-color: #1e3c72;
        background: #f8f9fa;
    }

    .flow-step.completed {
        border-left-color: #28a745;
        background: #f8fff9;
    }

    .flow-step.locked {
        border-left-color: #6c757d;
        background: #f8f9fa;
    }

    .step-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 0;
        cursor: pointer;
        padding: 10px;
        border-radius: 8px;
        transition: background-color 0.3s;
    }

    .step-header:hover {
    background-color: rgba(30, 60, 114, 0.05);
    }

    .step-toggle {
        margin-left: 15px;
        color: #5a6c7d;
        transition: transform 0.3s ease;
    }

    .step-toggle.rotated {
        transform: rotate(180deg);
    }

    .step-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e3c72;
        margin: 0;
    }

    .step-status {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-left: auto;
    }

    .status-locked {
        background: #6c757d;
        color: white;
    }

    .status-current {
        background: #1e3c72;
        color: white;
    }

    .status-completed {
        background: #28a745;
        color: white;
    }

    .sub-tasks {
        background: white;
        border-radius: 8px;
        padding: 0;
        border: 1px solid #e9ecef;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }

    .sub-tasks.expanded {
        max-height: 500px;
        padding: 1rem;
    }

    .sub-task {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1px solid #f1f3f4;
    }

    .sub-task:last-child {
        border-bottom: none;
    }

    .task-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .task-icon.current {
        background: #1e3c72;
        color: white;
    }

    .task-info {
        flex: 1;
    }

    .task-name {
        font-weight: 500;
        margin: 0;
        color: #2c3e50;
    }

    .task-description {
        font-size: 0.8rem;
        color: #6c757d;
        margin: 0;
    }

    .task-action {
        margin-left: 1rem;
    }

    .btn-simple {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        font-size: 0.85rem;
    }

    .btn-primary {
        background: #1e3c72;
        color: white;
    }

    .btn-primary:hover {
        background: #2a5298;
        transform: translateY(-2px);
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn:disabled {
        background: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
    }

    .progress-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 2rem;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.85rem;
        color: #5a6c7d;
        font-weight: 500;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        border-radius: 4px;
    }

    /* Test info badges */
    .test-info {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .info-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    /* Material description */
    .material-description {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 5px;
        line-height: 1.4;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="kursus-title">{{ $kursus->judul_kursus }}</h1>
                    <p class="text-muted mb-0">{{ $kursus->deskripsi_kursus }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="progress-section">
        <div class="progress-info">
            <span><?= $completedMaterials ?> out of <?= $totalMaterials ?> activities completed</span>
            <span><?= $progressPercentage ?>%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $progressPercentage ?>%"></div>
        </div>
    </div>

    <!-- Sequential Flow -->
    <div class="sequential-flow">
        @foreach($materials as $material)
        <div class="flow-step {{ $material['status_class'] }}">
            <!-- Header dengan kondisi yang berbeda -->
            @if($material['type'] == 'material')
            <!-- Header yang bisa diklik untuk material -->
            <div class="step-header" onclick="toggleSubTasks(<?= $material['id'] ?>)">
                <div class="header-content">
                    <h3 class="step-title">
                        {{ $loop->iteration }}. {{ $material['title'] }}
                    </h3>
                    
                    <!-- Tampilkan deskripsi jika ada -->
                    @if($material['description'])
                    <div class="material-description">
                        {{ $material['description'] }}
                    </div>
                    @endif
                </div>

                <span class="step-status status-{{ $material['status'] }}">
                    @if($material['status'] == 'locked') 
                        <span style="font-size: 14px;">
                            <i class="fas fa-lock"></i> Terkunci
                        </span>
                    @elseif($material['status'] == 'current') 
                        Sedang Berjalan
                    @elseif($material['status'] == 'completed') 
                        Selesai
                    @endif
                </span>
                
                <!-- Toggle Icon - hanya untuk material type -->
                <div class="step-toggle" id="toggle{{ $material['id'] }}">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>

            <!-- Sub-tasks untuk material regular -->
            <div class="sub-tasks {{ $material['status'] == 'locked' ? 'locked-content' : '' }}" id="subTasks{{ $material['id'] }}">
                @php
                    $hasContent = false;
                @endphp

                <!-- Kehadiran - hanya tampilkan jika diperlukan -->
                @if($material['attendance_required'] ?? true)
                    @php $hasContent = true; @endphp
                    <div class="sub-task">
                        <div class="task-icon" style="background: <?= (($material['attendance_status'] ?? 'pending') == 'completed') ? '#28a745' : '#e9ecef' ?>; color: <?= (($material['attendance_status'] ?? 'pending') == 'completed') ? 'white' : '#6c757d' ?>;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="task-info">
                            <div class="task-name">Kehadiran</div>
                            <div class="task-description">Konfirmasi kehadiran untuk materi ini</div>
                        </div>
                        <div class="task-action">
                            @if($material['attendance_status'] == 'completed')
                            <span class="btn-simple btn-success">
                                <i class="fas fa-check"></i> Selesai
                            </span>
                            @elseif($material['status'] == 'current')
                            <button class="btn-simple btn-primary" onclick="markAttendance(<?= $material['id'] ?>)">
                                <i class="fas fa-check-circle"></i> Tandai Hadir
                            </button>
                            @else
                            <button class="btn-simple btn-secondary" disabled>
                                <span style="font-size: 14px;">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </button>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Materi Pelatihan - hanya tampilkan jika ada file -->
                @if($material['has_material'] ?? false)
                    @php $hasContent = true; @endphp
                    <div class="sub-task">
                        <div class="task-icon" style="background: <?= (($material['material_status'] ?? 'pending') == 'completed') ? '#28a745' : '#e9ecef' ?>; color: <?= (($material['material_status'] ?? 'pending') == 'completed') ? 'white' : '#6c757d' ?>;">
                            <i class="fas fa-file-download"></i>
                        </div>
                        <div class="task-info">
                            <div class="task-name">Materi Pelatihan</div>
                            <div class="task-description">Download dan pelajari materi PDF/PPT</div>
                        </div>
                        <div class="task-action">
                            @if($material['material_status'] == 'completed')
                            <span class="btn-simple btn-success">
                                <i class="fas fa-check"></i> Selesai
                            </span>
                            @elseif(($material['attendance_status'] == 'completed' || !($material['attendance_required'] ?? true)) && $material['status'] == 'current')
                            <button class="btn-simple btn-primary" onclick="completeMaterial(<?= $material['id'] ?>)">
                                <i class="fas fa-download"></i> Download & Tandai Selesai
                            </button>
                            @else
                            <button class="btn-simple btn-secondary" disabled>
                                <span style="font-size: 14px;">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </button>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Video Pelatihan - hanya tampilkan jika ada video -->
                @if($material['has_video'] ?? false)
                    @php $hasContent = true; @endphp
                    <div class="sub-task">
                        <div class="task-icon" style="background: <?= (($material['video_status'] ?? 'pending') == 'completed') ? '#28a745' : '#e9ecef' ?>; color: <?= (($material['video_status'] ?? 'pending') == 'completed') ? 'white' : '#6c757d' ?>;">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="task-info">
                            <div class="task-name">Video Pelatihan</div>
                            <div class="task-description">Tonton video dengan quiz interaktif</div>
                        </div>
                        <div class="task-action">
                            @if($material['video_status'] == 'completed')
                            <span class="btn-simple btn-success">
                                <i class="fas fa-check"></i> Selesai
                            </span>
                            @elseif(($material['material_status'] == 'completed' || !($material['has_material'] ?? false)) && $material['status'] == 'current')
                            <button class="btn-simple btn-primary" onclick="completeVideo(<?= $material['id'] ?>)">
                                <i class="fas fa-play"></i> Mulai Video
                            </button>
                            @else
                            <button class="btn-simple btn-secondary" disabled>
                                <span style="font-size: 14px;">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </button>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Pesan jika tidak ada konten sama sekali -->
                @if(!$hasContent)
                    <div class="no-content-message">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada aktivitas yang tersedia untuk materi ini.
                    </div>
                @endif
            </div>

            @else
            <!-- Header untuk pretest, posttest, dan recap (TIDAK bisa diklik) -->
            <div class="step-header no-toggle">
                <div class="header-content">
                    <h3 class="step-title">
                        {{ $loop->iteration }}. {{ $material['title'] }}
                    </h3>
                    
                    <!-- Tampilkan info test untuk pretest dan posttest -->
                    @if(in_array($material['type'], ['pre_test', 'post_test']))
                    <div class="test-info">
                        <span class="info-badge badge-warning">
                            <i class="fas fa-clock me-1"></i>
                            {{ $material['type'] == 'pre_test' ? $material['durasi_pretest'] : $material['durasi_posttest'] }} menit
                        </span>
                        <span class="info-badge badge-info">
                            <i class="fas fa-trophy me-1"></i>Passing: {{ $material['passing_grade'] }}%
                        </span>
                        <span class="info-badge badge-success">
                            <i class="fas fa-list-ol me-1"></i>
                            @if($material['type'] == 'pre_test')
                                {{ count($material['soal_pretest'] ?? []) }} soal
                            @else
                                {{ count($material['soal_posttest'] ?? []) }} soal
                            @endif
                        </span>
                    </div>
                    @endif
                </div>

                <span class="step-status status-{{ $material['status'] }}">
                    @if($material['status'] == 'locked') 
                        <span style="font-size: 14px;">
                            <i class="fas fa-lock"></i> Terkunci
                        </span>
                    @elseif($material['status'] == 'current') 
                        Sedang Berjalan
                    @elseif($material['status'] == 'completed') 
                        Selesai
                    @endif
                </span>
            </div>

            <!-- Tombol aksi untuk pretest, posttest, dan recap -->
            <div class="task-action" style="padding: 15px 0 0 0; border-top: 1px solid #f1f3f4; margin-top: 15px;">
                @if($material['type'] == 'pre_test' || $material['type'] == 'post_test')
                    @if($material['status'] == 'locked')
                        <button class="btn-simple btn-secondary" disabled>
                            <span style="font-size: 14px;">
                                <i class="fas fa-lock"></i> Materi Terkunci
                            </span>
                        </button>
                    @elseif($material['is_test_completed'])
                        <span class="btn-simple btn-success">
                            <i class="fas fa-check"></i> Selesai (Nilai: {{ $material['test_score'] }}%)
                        </span>
                    @else
                        <a href="{{ route('mitra.kursus.test.show', [$kursus->id, $material['id'], $material['type']]) }}" 
                           class="btn-simple btn-primary">
                            <i class="fas fa-play"></i> Mulai {{ $material['type'] == 'pre_test' ? 'Pre Test' : 'Post Test' }}
                        </a>
                    @endif

                @elseif($material['type'] == 'recap')
                    @if($material['status'] == 'locked')
                        <button class="btn-simple btn-secondary" disabled>
                            <span style="font-size: 14px;">
                                <i class="fas fa-lock"></i> Materi Terkunci
                            </span>
                        </button>
                    @else
                        <a href="{{ route('mitra.kursus.recap.show', [$kursus->id, $material['id']]) }}" 
                           class="btn-simple btn-primary">
                            <i class="fas fa-chart-bar"></i> Lihat Rekap Nilai
                        </a>
                    @endif
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<script>
function markAttendance(materialId) {
    if(confirm('Apakah Anda yakin ingin menandai kehadiran?')) {
        fetch(`/mitra/material/${materialId}/attendance`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menandai kehadiran: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menandai kehadiran.');
        });
    }
}

function completeMaterial(materialId) {
    if(confirm('Apakah Anda yakin telah mendownload dan mempelajari materi?')) {
        fetch(`/mitra/material/${materialId}/complete-material`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menyelesaikan materi: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyelesaikan materi.');
        });
    }
}

function completeVideo(materialId) {
    if(confirm('Apakah Anda yakin telah menonton video hingga selesai?')) {
        fetch(`/mitra/material/${materialId}/complete-video`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menyelesaikan video: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyelesaikan video.');
        });
    }
}

function toggleSubTasks(materialId) {
    const subTasks = document.getElementById('subTasks' + materialId);
    const toggleIcon = document.getElementById('toggle' + materialId);
    
    if (subTasks && toggleIcon) {
        // Tutup semua sub-tasks lainnya
        document.querySelectorAll('.sub-tasks').forEach(task => {
            if (task.id !== 'subTasks' + materialId) {
                task.classList.remove('expanded');
            }
        });
        
        // Reset semua toggle icon lainnya
        document.querySelectorAll('.step-toggle').forEach(icon => {
            if (icon.id !== 'toggle' + materialId) {
                icon.classList.remove('rotated');
            }
        });
        
        // Toggle yang diklik
        subTasks.classList.toggle('expanded');
        toggleIcon.classList.toggle('rotated');
    }
}

// Auto-expand current material saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Cari material yang statusnya 'current' dan auto expand
    const currentMaterials = document.querySelectorAll('.flow-step.current');
    currentMaterials.forEach(step => {
        const header = step.querySelector('.step-header');
        // Hanya expand jika ini material regular (bukan test/recap)
        if (header && !header.classList.contains('no-toggle')) {
            const onclickAttr = header.getAttribute('onclick');
            if (onclickAttr && onclickAttr.includes('toggleSubTasks')) {
                const materialId = onclickAttr.match(/toggleSubTasks\((\d+)\)/)[1];
                // Tunggu sebentar agar animasi smooth
                setTimeout(() => {
                    toggleSubTasks(materialId);
                }, 300);
            }
        }
    });
});

// Prevent event bubbling untuk semua link dan button dalam task-action
document.querySelectorAll('.task-action a, .task-action button').forEach(element => {
    element.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>
@endsection