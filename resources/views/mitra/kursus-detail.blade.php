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
        margin-bottom: 0; /* Hapus margin bottom karena ada toggle */
        cursor: pointer; /* Tambah cursor pointer */
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
        padding: 0; /* Ubah padding jadi 0 dulu */
        border: 1px solid #e9ecef;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }

    .sub-tasks.expanded {
        max-height: 500px; /* Sesuaikan dengan konten */
        padding: 1rem; /* Tambah padding saat expanded */
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
            <!-- Header yang bisa diklik -->
            <div class="step-header" onclick="toggleSubTasks(<?= $material['id'] ?>)">
                <h3 class="step-title">
                    {{ $loop->iteration }}. {{ $material['title'] }}
                </h3>
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
                
                <!-- Toggle Icon - selalu show untuk material type -->
                @if($material['type'] == 'material')
                <div class="step-toggle" id="toggle{{ $material['id'] }}">
                    <i class="fas fa-chevron-down"></i>
                </div>
                @endif
            </div>

            @if($material['type'] == 'material')
            <!-- Sub-tasks - hidden by default -->
            <div class="sub-tasks {{ $material['status'] == 'locked' ? 'locked-content' : '' }}" id="subTasks{{ $material['id'] }}">
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

// Update function toggleSubTasks - hanya toggle, tidak navigate otomatis
function toggleSubTasks(materialId) {
    const subTasks = document.getElementById('subTasks' + materialId);
    const toggleIcon = document.getElementById('toggle' + materialId);
    
    if (subTasks && toggleIcon) {
        // SELALU allow toggle untuk material type, regardless of status
        subTasks.classList.toggle('expanded');
        toggleIcon.classList.toggle('rotated');
    }
    // HAPUS bagian else-nya yang navigate otomatis
}

// Function untuk navigate ke halaman material (hanya dipanggil manual)
function navigateToMaterial(materialId) {
    // Cari material berdasarkan ID
    const material = <?= json_encode($materials) ?>.find(m => m.id == materialId);
    
    if (material && material.status !== 'locked') {
        switch(material.type) {
            case 'pre_test':
                window.location.href = '/mitra/pre-test/' + materialId;
                break;
            case 'post_test':
                window.location.href = '/mitra/post-test/' + materialId;
                break;
            case 'recap':
                window.location.href = '/mitra/rekap-nilai/' + materialId;
                break;
            default:
                console.log('Navigating to material:', materialId);
        }
    } else {
        alert('Materi ini masih terkunci. Selesaikan materi sebelumnya terlebih dahulu.');
    }
}

// HAPUS atau COMMENT bagian auto-expand ini:
/*
// Optional: Auto-expand current material
document.addEventListener('DOMContentLoaded', function() {
    // Cari material yang statusnya 'current' dan auto expand
    const currentMaterials = document.querySelectorAll('.flow-step.current');
    currentMaterials.forEach(step => {
        const header = step.querySelector('.step-header');
        if (header) {
            const onclickAttr = header.getAttribute('onclick');
            if (onclickAttr && onclickAttr.includes('toggleSubTasks')) {
                // Execute the toggle function
                const materialId = onclickAttr.match(/toggleSubTasks\((\d+)\)/)[1];
                toggleSubTasks(materialId);
            }
        }
    });
});
*/

// Optional: Auto-expand current material
document.addEventListener('DOMContentLoaded', function() {
    // Cari material yang statusnya 'current' dan auto expand
    const currentMaterials = document.querySelectorAll('.flow-step.current');
    currentMaterials.forEach(step => {
        const header = step.querySelector('.step-header');
        if (header) {
            const onclickAttr = header.getAttribute('onclick');
            if (onclickAttr && onclickAttr.includes('toggleSubTasks')) {
                // Execute the toggle function
                const materialId = onclickAttr.match(/toggleSubTasks\((\d+)\)/)[1];
                toggleSubTasks(materialId);
            }
        }
    });
});
</script>
@endsection