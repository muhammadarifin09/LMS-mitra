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
        opacity: 0.6;
    }

    .step-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1rem;
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
        padding: 1rem;
        border: 1px solid #e9ecef;
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
    }

    .btn-primary {
        background: #1e3c72;
        color: white;
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
            <span><?= $completedMaterials ?> out of <?= $totalMaterials ?> materials completed</span>
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
            <div class="step-header">
                <h3 class="step-title">
                    {{ $loop->iteration }}. {{ $material['title'] }}
                </h3>
                <span class="step-status status-{{ $material['status'] }}">
                    @if($material['status'] == 'locked') Terkunci
                    @elseif($material['status'] == 'current') Sedang Berjalan
                    @elseif($material['status'] == 'completed') Selesai
                    @endif
                </span>
            </div>

            @if($material['type'] == 'material')
            <!-- Sub-tasks untuk materi pembelajaran -->
            <div class="sub-tasks">
                <!-- Kehadiran -->
                <div class="sub-task">
                    <div class="task-icon" style="background: <?= $material['attendance_status'] == 'completed' ? '#28a745' : '#e9ecef' ?>; color: <?= $material['attendance_status'] == 'completed' ? 'white' : '#6c757d' ?>;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="task-info">
                        <div class="task-name">Kehadiran</div>
                        <div class="task-description">Konfirmasi kehadiran untuk materi ini</div>
                    </div>
                    <div class="task-action">
                        <?php if($material['attendance_status'] == 'completed'): ?>
                        <span class="btn-simple btn-success">
                            <i class="fas fa-check"></i> Selesai
                        </span>
                        <?php elseif($material['status'] == 'current'): ?>
                        <button class="btn-simple btn-primary" onclick="markAttendance(<?= $material['id'] ?>)">
                            <i class="fas fa-check-circle"></i> Tandai Hadir
                        </button>
                        <?php else: ?>
                        <button class="btn-simple btn-secondary" disabled>
                            <span style="font-size: 14px;">
                                <i class="fas fa-lock"></i>
                            </span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Materi Pelatihan -->
                <div class="sub-task">
                    <div class="task-icon" style="background: <?= $material['material_status'] == 'completed' ? '#28a745' : '#e9ecef' ?>; color: <?= $material['material_status'] == 'completed' ? 'white' : '#6c757d' ?>;">
                        <i class="fas fa-file-download"></i>
                    </div>
                    <div class="task-info">
                        <div class="task-name">Materi Pelatihan</div>
                        <div class="task-description">Download dan pelajari materi PDF/PPT</div>
                    </div>
                    <div class="task-action">
                        <?php if($material['material_status'] == 'completed'): ?>
                        <span class="btn-simple btn-success">
                            <i class="fas fa-check"></i> Selesai
                        </span>
                        <?php elseif($material['attendance_status'] == 'completed' && $material['status'] == 'current'): ?>
                        <button class="btn-simple btn-primary" onclick="downloadMaterial(<?= $material['id'] ?>)">
                            <i class="fas fa-download"></i> Download & Tandai Selesai
                        </button>
                        <?php else: ?>
                        <button class="btn-simple btn-secondary" disabled>
                            <span style="font-size: 14px;">
                                <i class="fas fa-lock"></i>
                            </span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Video Pelatihan -->
                <div class="sub-task">
                    <div class="task-icon" style="background: <?= $material['video_status'] == 'completed' ? '#28a745' : '#e9ecef' ?>; color: <?= $material['video_status'] == 'completed' ? 'white' : '#6c757d' ?>;">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="task-info">
                        <div class="task-name">Video Pelatihan</div>
                        <div class="task-description">Tonton video dengan quiz interaktif</div>
                    </div>
                    <div class="task-action">
                        <?php if($material['video_status'] == 'completed'): ?>
                        <span class="btn-simple btn-success">
                            <i class="fas fa-check"></i> Selesai
                        </span>
                        <?php elseif($material['material_status'] == 'completed' && $material['status'] == 'current'): ?>
                        <button class="btn-simple btn-primary" onclick="startVideo(<?= $material['id'] ?>)">
                            <i class="fas fa-play"></i> Mulai Video
                        </button>
                        <?php else: ?>
                        <button class="btn-simple btn-secondary" disabled>
                            <span style="font-size: 14px;">
                                <i class="fas fa-lock"></i>
                            </span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            @else
            <!-- Untuk pre-test, post-test, recap -->
            <div class="text-center">
                @if($material['status'] == 'completed')
                <span class="btn-simple btn-success">
                    <i class="fas fa-check"></i> Sudah Diselesaikan
                </span>
                @elseif($material['status'] == 'current')
                <button class="btn-simple btn-primary">
                    <i class="fas fa-play"></i> Mulai {{ $material['type'] == 'pre_test' ? 'Pre Test' : ($material['type'] == 'post_test' ? 'Post Test' : 'Rekap Nilai') }}
                </button>
                @else
                <button class="btn-simple btn-secondary" disabled>
                    <span style="font-size: 14px;">
                        <i class="fas fa-lock"></i>
                    </span>
                </button>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<script>
function markAttendance(materialId) {
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
        }
    });
}

function downloadMaterial(materialId) {
    // Simulasi download
    alert('Materi akan didownload...');
    
    fetch(`/mitra/material/${materialId}/download`, {
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
        }
    });
}

function startVideo(materialId) {
    alert('Video player akan dibuka untuk material ID: ' + materialId);
    // Implement video player dengan quiz nanti
}
</script>
@endsection