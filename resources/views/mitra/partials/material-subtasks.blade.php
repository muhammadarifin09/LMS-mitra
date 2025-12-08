@php
    $filePaths = json_decode($material['file_path'], true) ?? [$material['file_path']];
    $fileCount = count($filePaths);
    $isSingleFile = $fileCount === 1;
    $canDownload = $material['status'] == 'current' && 
                  ($material['attendance_status'] == 'completed' || !($material['attendance_required'] ?? true));
@endphp

@if($material['attendance_required'] ?? true)
<div class="sub-task">
    <div class="task-icon" id="attendance-icon-{{ $material['id'] }}" 
         style="background: {{ $material['attendance_status'] == 'completed' ? '#28a745' : '#e9ecef' }}; 
                color: {{ $material['attendance_status'] == 'completed' ? 'white' : '#6c757d' }};">
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
        <button class="btn-simple btn-primary" onclick="markAttendance({{ $material['id'] }})">
            <i class="fas fa-check-circle"></i> Tandai Hadir
        </button>
        @else
        <button class="btn-simple btn-secondary" disabled>
            <i class="fas fa-lock"></i>
        </button>
        @endif
    </div>
</div>
@endif

@if($material['has_material'] ?? false)
<div class="sub-task">
    <div class="task-icon" id="material-icon-{{ $material['id'] }}"
         style="background: {{ $material['material_status'] == 'completed' ? '#28a745' : '#e9ecef' }}; 
                color: {{ $material['material_status'] == 'completed' ? 'white' : '#6c757d' }};">
        <i class="fas fa-file-download"></i>
    </div>
    <div class="task-info">
        <div class="task-name">Materi Pelatihan</div>
        <div class="task-description">
            @if($fileCount > 1)
                {{ $fileCount }} file tersedia
            @else
                Download dan pelajari materi
            @endif
        </div>
        
        @if($material['file_path'])
        <div class="file-list">
            @foreach($filePaths as $index => $filePath)
                @php
                    $fileName = basename($filePath);
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $fileIcon = getFileIcon($fileExtension);
                @endphp
                
                <div class="file-item">
                    <div class="file-icon">
                        <i class="{{ $fileIcon }}"></i>
                    </div>
                    <div class="file-name">
                        {{ $fileName }}
                    </div>
                    <div class="file-status" id="file-status-{{ $material['id'] }}-{{ $index }}">
                        @if($material['material_status'] == 'completed')
                        <i class="fas fa-check text-success"></i>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
    <div class="task-action">
        @if($material['material_status'] == 'completed')
        <span class="btn-simple btn-success" id="material-button-{{ $material['id'] }}">
            <i class="fas fa-check"></i> Selesai
        </span>
        @elseif($canDownload)
        <div class="download-options">
            @if($isSingleFile)
                <!-- Single file -->
                <a href="{{ route('mitra.kursus.material.download', ['kursus' => $kursus->id, 'material' => $material['id']]) }}" 
                   class="btn-simple btn-primary"
                   id="download-button-{{ $material['id'] }}"
                   onclick="handleSingleDownload(event, {{ $material['id'] }}, {{ $kursus->id }})">
                    <i class="fas fa-download"></i> Download File
                </a>
            @else
                <!-- Multiple files -->
                <div class="dropdown">
                    <button class="btn-simple btn-primary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" id="dropdownMenuButton{{ $material['id'] }}">
                        <i class="fas fa-download"></i> Download File
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $material['id'] }}">
                        <li>
                            <a class="dropdown-item" href="#"
                               onclick="handleZipDownload(event, {{ $material['id'] }}, {{ $kursus->id }})">
                                <i class="fas fa-file-archive"></i> Download Semua (ZIP)
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @foreach($filePaths as $index => $filePath)
                            @php
                                $fileName = basename($filePath);
                                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                                $fileIcon = getFileIcon($fileExtension);
                            @endphp
                            <li>
                                <a class="dropdown-item" href="#"
                                   onclick="handleSpecificDownload(event, {{ $material['id'] }}, {{ $kursus->id }}, {{ $index }})">
                                    <i class="fas {{ $fileIcon }} me-2"></i> {{ $fileName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        @else
        <button class="btn-simple btn-secondary" disabled id="locked-material-{{ $material['id'] }}">
            <i class="fas fa-lock"></i>
        </button>
        @endif
    </div>
</div>
@endif

@if($material['has_video'] ?? false)
@php 
    $canWatchVideo = $material['status'] == 'current' && 
                    ($material['material_status'] == 'completed' || !($material['has_material'] ?? false)) &&
                    ($material['attendance_status'] == 'completed' || !($material['attendance_required'] ?? true));
@endphp
<div class="sub-task">
    <div class="task-icon" id="video-icon-{{ $material['id'] }}"
         style="background: {{ $material['video_status'] == 'completed' ? '#28a745' : '#e9ecef' }}; 
                color: {{ $material['video_status'] == 'completed' ? 'white' : '#6c757d' }};">
        <i class="fas fa-play-circle"></i>
    </div>
    <div class="task-info">
        <div class="task-name">Video Pelatihan</div>
        <div class="task-description">Tonton video materi</div>
        @if($material['video_url'])
        <div class="mt-2">
            <small class="text-muted">
                <i class="fas fa-link me-1"></i>
                URL Video: 
                <a href="{{ $material['video_url'] }}" target="_blank" class="text-primary">
                    {{ \Illuminate\Support\Str::limit($material['video_url'], 40) }}
                </a>
            </small>
        </div>
        @endif
    </div>
    <div class="task-action">
        @if($material['video_status'] == 'completed')
        <span class="btn-simple btn-success" id="video-button-{{ $material['id'] }}">
            <i class="fas fa-check"></i> Selesai
        </span>
        @elseif($canWatchVideo)
        <a href="{{ route('mitra.kursus.material.video.view', ['kursus' => $kursus->id, 'material' => $material['id']]) }}" 
           class="btn-simple btn-primary"
           id="video-link-{{ $material['id'] }}"
           target="_blank"
           onclick="handleVideoWatch(event, {{ $material['id'] }}, {{ $kursus->id }})">
            <i class="fas fa-play"></i> Tonton Video
        </a>
        @else
        <button class="btn-simple btn-secondary" disabled id="locked-video-{{ $material['id'] }}">
            <i class="fas fa-lock"></i>
        </button>
        @endif
    </div>
</div>
@endif