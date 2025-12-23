@extends('mitra.layouts.app')

@section('title', 'Video Materi - ' . $material->title)

@section('content')
<style>
    :root {
        --video-height: 70vh;
    }
    
    .video-container-wrapper {
        position: relative;
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        min-height: 400px;
    }
    
    .video-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, #1a237e, #0d47a1);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 10;
        transition: opacity 0.5s ease;
    }
    
    .video-loading-overlay.hidden {
        opacity: 0;
        pointer-events: none;
    }
    
    .video-container {
        position: relative;
        width: 100%;
        height: var(--video-height);
        min-height: 400px;
    }

    /* Tambahkan CSS ini di bagian style */
.strict-controls-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 20;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    text-align: center;
    padding: 20px;
    pointer-events: auto;
}

.video-locked-message {
    background: rgba(0, 0, 0, 0.9);
    padding: 20px;
    border-radius: 10px;
    max-width: 500px;
    border: 2px solid #ff9800;
}

.lock-icon {
    font-size: 48px;
    color: #ff9800;
    margin-bottom: 15px;
}

.sequential-playback-indicator {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(255, 87, 34, 0.9);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    z-index: 25;
}

.progress-blocker {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 10px;
    background: rgba(255, 87, 34, 0.5);
    z-index: 30;
    pointer-events: none;
}

.video-controls-disabled {
    pointer-events: none;
    opacity: 0.7;
}

.download-prevention-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    z-index: 1000;
    display: none;
    justify-content: center;
    align-items: center;
}

.download-warning {
    background: white;
    padding: 30px;
    border-radius: 10px;
    max-width: 400px;
    text-align: center;
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
}

.download-warning-icon {
    font-size: 48px;
    color: #f44336;
    margin-bottom: 15px;
}
    
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
        background: #000;
    }
    
    .video-fallback {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: #000;
        color: white;
        padding: 20px;
        text-align: center;
    }
    
    .video-controls-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        padding: 20px;
        z-index: 5;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .video-container-wrapper:hover .video-controls-overlay {
        opacity: 1;
    }
    
    .progress-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.25em;
    }
    
    .video-source-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 5;
    }
    
    .troubleshoot-section {
        background: #fff8e1;
        border-left: 4px solid #ff9800;
        padding: 15px;
        margin: 15px 0;
        border-radius: 4px;
    }
    
    .video-alt-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 15px;
    }
    
    /* Optimasi untuk mobile */
    @media (max-width: 768px) {
        :root {
            --video-height: 50vh;
        }
        
        .video-container {
            height: var(--video-height);
            min-height: 300px;
        }
        
        .video-alt-options {
            flex-direction: column;
        }
        
        .video-alt-options .btn {
            width: 100%;
        }
    }
    
    /* Video player adjustments */
    .video-iframe-fix {
        transform: scale(1);
        transform-origin: top left;
        width: 100%;
        height: 100%;
    }
</style>

<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <!-- Navigasi Kembali -->
            <div class="mb-3">
                <a href="{{ route('mitra.kursus.show', $kursus) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Materi
                </a>
            </div>
            
            <!-- Video Player Section -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="fas fa-play-circle me-2"></i>
                                {{ $material->title }}
                            </h4>
                            <small class="opacity-75">{{ $material->description }}</small>
                        </div>
                        <div>
                            <span class="badge bg-light text-primary">
                                <i class="fas fa-clock me-1"></i>
                                {{ $material->duration ? ceil($material->duration / 60) . ' menit' : 'Video' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <!-- Video Container -->
                    <div class="video-container-wrapper" id="video-wrapper">
                        <!-- Loading Overlay -->
                        <div class="video-loading-overlay" id="video-loading">
                            <div class="spinner-border text-light mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5 class="text-white mb-2">Memuat Video...</h5>
                            <p class="text-white-50 mb-0">Mohon tunggu sebentar</p>
                            <div class="mt-3">
                                <div class="progress" style="width: 200px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         id="loading-progress" 
                                         style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Video Source Badge -->
                        <div class="video-source-badge">
                            @if($videoData['type'] === 'hosted')
                                <i class="fas fa-hdd me-1"></i> Google Drive
                            @elseif($videoData['type'] === 'youtube')
                                <i class="fab fa-youtube me-1"></i> YouTube
                            @elseif($videoData['type'] === 'vimeo')
                                <i class="fab fa-vimeo-v me-1"></i> Vimeo
                            @else
                                <i class="fas fa-external-link-alt me-1"></i> Eksternal
                            @endif
                        </div>
                        
                        <!-- Video Container -->
                        <div class="video-container">
                            @if($videoData['embed_url'])
                                @if($videoData['type'] === 'hosted')
                                    <!-- Google Drive dengan format fix -->
                                    <iframe 
                                        src="{{ $videoData['embed_url'] }}" 
                                        allow="autoplay; fullscreen; picture-in-picture; encrypted-media; accelerometer; gyroscope"
                                        allowfullscreen
                                        id="video-player"
                                        title="{{ $material->title }}"
                                        loading="lazy"
                                        referrerpolicy="no-referrer"
                                        sandbox="allow-scripts allow-same-origin allow-presentation allow-popups"
                                        class="video-iframe-fix"
                                        onload="videoLoaded()"
                                        style="width: 100%; height: 100%; border: none;">
                                    </iframe>
                                @else
                                    <!-- YouTube, Vimeo, dll -->
                                    <iframe 
                                        src="{{ $videoData['embed_url'] }}" 
                                        allow="autoplay; fullscreen; picture-in-picture; encrypted-media"
                                        allowfullscreen
                                        id="video-player"
                                        title="{{ $material->title }}"
                                        loading="eager"
                                        onload="videoLoaded()">
                                    </iframe>
                                @endif
                            @else
                                <!-- Fallback Message -->
                                <div class="video-fallback">
                                    <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                                    <h5>Video tidak dapat dimuat</h5>
                                    <p class="mb-3">URL video tidak tersedia atau tidak valid.</p>
                                    
                                    <!-- Alternate Options -->
                                    <div class="video-alt-options">
                                        @if($videoData['direct_link'])
                                            <a href="{{ $videoData['direct_link'] }}" 
                                               class="btn btn-primary" 
                                               target="_blank"
                                               download>
                                                <i class="fas fa-download me-1"></i> Download Video
                                            </a>
                                            
                                            <a href="{{ $videoData['direct_link'] }}" 
                                               class="btn btn-outline-primary" 
                                               target="_blank">
                                                <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru
                                            </a>
                                        @endif
                                        
                                        <button class="btn btn-outline-secondary" onclick="window.location.reload()">
                                            <i class="fas fa-redo me-1"></i> Refresh Halaman
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Controls Overlay -->
                        <div class="video-controls-overlay">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button class="btn btn-sm btn-light me-2" onclick="reloadVideo()" id="reload-btn" title="Muat ulang video">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light me-2" onclick="openInNewTab()" title="Buka di tab baru">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light" onclick="toggleFullscreen()" title="Mode layar penuh">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                </div>
                                <div class="text-white">
                                    <small id="video-status-text">Video siap diputar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Troubleshooting Section (Tampilkan jika Google Drive) -->
                    @if($videoData['type'] === 'hosted')
                        <div class="troubleshoot-section" id="troubleshoot-section">
                            <h6><i class="fas fa-info-circle me-2 text-warning"></i>Tips Jika Video Tidak Muncul:</h6>
                            <ul class="mb-2">
                                <li>Klik tombol <strong>"Buka di Tab Baru"</strong> di atas</li>
                                <li>Nonaktifkan AdBlock/extension browser</li>
                                <li>Gunakan browser Chrome atau Firefox</li>
                                <li>Clear cache browser (Ctrl+Shift+Del)</li>
                            </ul>
                            <div class="video-alt-options">
                                @if($videoData['direct_link'])
                                    <a href="{{ $videoData['direct_link'] }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       target="_blank"
                                       download>
                                        <i class="fas fa-download me-1"></i> Download Video
                                    </a>
                                @endif
                                <button class="btn btn-sm btn-outline-info" onclick="testVideoUrl()">
                                    <i class="fas fa-wrench me-1"></i> Test Koneksi Video
                                </button>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Progress Tracking -->
                    <div class="progress-section">
                        <h6 class="mb-3">
                            <i class="fas fa-chart-line me-2 text-primary"></i>
                            Progress Pembelajaran
                        </h6>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="text-muted">Progress Video</span>
                                <span class="badge bg-primary ms-2" id="current-status">
                                    @if($progress && $progress->video_status === 'completed')
                                        <i class="fas fa-check-circle me-1"></i> Selesai
                                    @else
                                        <i class="fas fa-play-circle me-1"></i> Dalam Proses
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-muted" id="progress-percentage-text">
                                    {{ $progress->video_progress ?? 0 }}%
                                </span>
                            </div>
                        </div>
                        
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success" 
                                 id="progress-bar" 
                                 role="progressbar" 
                                 style="width: {{ $progress->video_progress ?? 0 }}%"
                                 aria-valuenow="{{ $progress->video_progress ?? 0 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        
                        <!-- Auto-save Status -->
                        <div class="alert alert-info py-2 mb-0 d-flex justify-content-between align-items-center" id="auto-save-status">
                            <div>
                                <i class="fas fa-sync fa-spin me-2"></i>
                                <small>Progress otomatis disimpan</small>
                            </div>
                            <div>
                                <small id="last-saved">Belum disimpan</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('mitra.kursus.show', $kursus) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Materi
                                </a>
                                
                                @if($progress && $progress->video_status !== 'completed')
                                    <button class="btn btn-success" onclick="markAsCompleted()" id="mark-complete-btn">
                                        <i class="fas fa-check-circle me-1"></i> Tandai Selesai
                                    </button>
                                @elseif($progress && $progress->video_status === 'completed')
                                    <button class="btn btn-success" disabled>
                                        <i class="fas fa-check-circle me-1"></i> Sudah Selesai
                                    </button>
                                @endif
                                
                                <button class="btn btn-outline-secondary" onclick="showVideoInfo()">
                                    <i class="fas fa-info-circle me-1"></i> Info Video
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                @if($videoData['type'] === 'hosted')
                                    Video dari Google Drive
                                @else
                                    Video eksternal
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Video Questions -->
            @if($videoQuestions && count($videoQuestions) > 0)
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            Pertanyaan Video
                            <span class="badge bg-light text-info ms-2">{{ count($videoQuestions) }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="videoQuestionsAccordion">
                            @foreach($videoQuestions as $index => $question)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $index }}">
                                            Pertanyaan {{ $index + 1 }} 
                                            <span class="badge bg-primary ms-2">
                                                {{ $question->time_in_seconds }} detik
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" 
                                         class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                         data-bs-parent="#videoQuestionsAccordion">
                                        <div class="accordion-body">
                                            <p class="fw-bold">{{ $question->question }}</p>
                                            
                                            @php
                                                $options = json_decode($question->options, true);
                                            @endphp
                                            
                                            @if(is_array($options))
                                                <div class="list-group">
                                                    @foreach($options as $optIndex => $option)
                                                        <div class="list-group-item">
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="radio" 
                                                                       disabled
                                                                       {{ $optIndex == $question->correct_option ? 'checked' : '' }}>
                                                                <label class="form-check-label">
                                                                    {{ $option }}
                                                                    @if($optIndex == $question->correct_option)
                                                                        <span class="badge bg-success ms-2">
                                                                            <i class="fas fa-check"></i> Jawaban Benar
                                                                        </span>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            @if($question->explanation)
                                                <div class="alert alert-light mt-3">
                                                    <strong>Penjelasan:</strong> {{ $question->explanation }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal untuk Info Video -->
<div class="modal fade" id="videoInfoModal" tabindex="-1" aria-labelledby="videoInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoInfoModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Informasi Video
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <tr>
                        <td width="40%"><strong>Tipe Video</strong></td>
                        <td>
                            @if($videoData['type'] === 'hosted')
                                <span class="badge bg-primary">Google Drive</span>
                            @elseif($videoData['type'] === 'youtube')
                                <span class="badge bg-danger">YouTube</span>
                            @elseif($videoData['type'] === 'vimeo')
                                <span class="badge bg-success">Vimeo</span>
                            @else
                                <span class="badge bg-secondary">Eksternal</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>URL Video</strong></td>
                        <td>
                            <small>
                                <a href="{{ $videoData['url'] }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
                                    {{ $videoData['url'] }}
                                </a>
                            </small>
                        </td>
                    </tr>
                    @if($videoData['direct_link'])
                        <tr>
                            <td><strong>Direct Link</strong></td>
                            <td>
                                <small>
                                    <a href="{{ $videoData['direct_link'] }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
                                        {{ $videoData['direct_link'] }}
                                    </a>
                                </small>
                            </td>
                        </tr>
                    @endif
                    @if($material->duration)
                        <tr>
                            <td><strong>Durasi</strong></td>
                            <td>{{ ceil($material->duration / 60) }} menit</td>
                        </tr>
                    @endif
                    <tr>
                        <td><strong>Status Progress</strong></td>
                        <td>{{ $progress->video_progress ?? 0 }}%</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="copyVideoInfo()">
                    <i class="fas fa-copy me-1"></i> Copy Info
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk video player yang dioptimasi -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoWrapper = document.getElementById('video-wrapper');
    const videoPlayer = document.getElementById('video-player');
    const videoLoading = document.getElementById('video-loading');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-percentage-text');
    const currentStatus = document.getElementById('current-status');
    const loadingProgress = document.getElementById('loading-progress');
    const autoSaveStatus = document.getElementById('auto-save-status');
    const lastSaved = document.getElementById('last-saved');
    const videoStatusText = document.getElementById('video-status-text');
    const markCompleteBtn = document.getElementById('mark-complete-btn');
    const troubleshootSection = document.getElementById('troubleshoot-section');
    
    let videoProgress = {{ $progress->video_progress ?? 0 }};
    let isVideoCompleted = {{ $progress && $progress->video_status === 'completed' ? 'true' : 'false' }};
    let updateInterval;
    let saveInterval;
    let loadingInterval;
    let loadingProgressValue = 0;
    let lastSaveTime = null;
    let videoLoadAttempts = 0;
    const MAX_LOAD_ATTEMPTS = 3;
    
    // Konfigurasi
    const KURSUS_ID = {{ $kursus->id }};
    const MATERIAL_ID = {{ $material->id }};
    const UPDATE_FREQUENCY = 15000; // 15 detik
    const SAVE_FREQUENCY = 30000; // 30 detik
    const MIN_WATCH_PERCENTAGE = {{ $videoData['player_config']['min_watch_percentage'] ?? 90 }};
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const VIDEO_TYPE = '{{ $videoData["type"] }}';
    const EMBED_URL = '{{ $videoData["embed_url"] }}';
    const DIRECT_LINK = '{{ $videoData["direct_link"] }}';
    
    // Inisialisasi
    initVideoPlayer();
    
    function initVideoPlayer() {
        // Simulasi loading progress
        simulateLoading();
        
        // Setup event listeners untuk video player
        if (videoPlayer) {
            // Google Drive memerlukan penanganan khusus
            if (VIDEO_TYPE === 'hosted') {
                setupGoogleDrivePlayer();
            } else {
                setupStandardPlayer();
            }
        } else {
            // Jika tidak ada video player, sembunyikan loading
            hideLoading();
        }
        
        // Mulai tracking progress
        startProgressTracking();
    }
    
    function setupGoogleDrivePlayer() {
        console.log('Setting up Google Drive player...');
        
        // Google Drive seringkali membutuhkan waktu lebih lama
        setTimeout(hideLoading, 5000);
        
        // Tambahkan event listener untuk error
        videoPlayer.addEventListener('error', function(e) {
            console.error('Video player error:', e);
            videoLoadAttempts++;
            
            if (videoLoadAttempts < MAX_LOAD_ATTEMPTS) {
                videoStatusText.textContent = `Mencoba memuat ulang (${videoLoadAttempts}/${MAX_LOAD_ATTEMPTS})...`;
                setTimeout(reloadVideo, 2000 * videoLoadAttempts);
            } else {
                videoStatusText.textContent = 'Gagal memuat video. Coba alternatif.';
                showFallbackOptions();
            }
        });
        
        // Check jika iframe sudah load
        videoPlayer.addEventListener('load', function() {
            console.log('Google Drive iframe loaded');
            hideLoading();
            videoStatusText.textContent = 'Video siap diputar';
        });
        
        // Fallback: sembunyikan loading setelah timeout
        setTimeout(function() {
            if (videoLoading && !videoLoading.classList.contains('hidden')) {
                hideLoading();
                videoStatusText.textContent = 'Video mungkin memerlukan waktu loading lebih lama';
                
                // Tampilkan troubleshooting section
                if (troubleshootSection) {
                    troubleshootSection.style.display = 'block';
                }
            }
        }, 10000);
    }
    
    function setupStandardPlayer() {
        console.log('Setting up standard video player...');
        
        videoPlayer.addEventListener('load', function() {
            console.log('Standard video player loaded');
            hideLoading();
            videoStatusText.textContent = 'Video siap diputar';
        });
        
        videoPlayer.addEventListener('error', function() {
            console.error('Standard video player error');
            videoStatusText.textContent = 'Error memuat video';
            showFallbackOptions();
        });
        
        // Fallback timeout
        setTimeout(hideLoading, 8000);
    }
    
    function simulateLoading() {
        loadingInterval = setInterval(() => {
            loadingProgressValue += 2;
            if (loadingProgressValue > 80) {
                loadingProgressValue = 80;
                clearInterval(loadingInterval);
            }
            loadingProgress.style.width = loadingProgressValue + '%';
        }, 100);
    }
    
    function hideLoading() {
        if (videoLoading && !videoLoading.classList.contains('hidden')) {
            videoLoading.classList.add('hidden');
            clearInterval(loadingInterval);
            loadingProgress.style.width = '100%';
            
            setTimeout(() => {
                videoLoading.style.display = 'none';
            }, 500);
        }
    }
    
    // Video loaded callback
    window.videoLoaded = function() {
        console.log('Video loaded successfully');
        hideLoading();
        videoStatusText.textContent = 'Video berhasil dimuat';
    };
    
    // Mulai tracking progress
    function startProgressTracking() {
        if (isVideoCompleted) return;
        
        // Update progress setiap interval
        updateInterval = setInterval(() => {
            if (!isVideoCompleted && videoProgress < MIN_WATCH_PERCENTAGE) {
                // Tingkatkan progress otomatis (simulasi)
                videoProgress += Math.min(15, MIN_WATCH_PERCENTAGE - videoProgress);
                
                // Update UI
                updateProgressUI(videoProgress);
                
                // Kirim ke server
                sendProgressToServer(videoProgress);
            }
        }, UPDATE_FREQUENCY);
        
        // Auto-save progress
        saveInterval = setInterval(() => {
            if (!isVideoCompleted && videoProgress > 0) {
                sendProgressToServer(videoProgress);
                showAutoSaveStatus();
            }
        }, SAVE_FREQUENCY);
    }
    
    // Update UI progress
    function updateProgressUI(percentage) {
        if (percentage > 100) percentage = 100;
        
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute('aria-valuenow', percentage);
        progressText.textContent = Math.round(percentage) + '%';
        
        // Jika mencapai minimal persentase, tandai sebagai selesai
        if (percentage >= MIN_WATCH_PERCENTAGE && !isVideoCompleted) {
            markVideoAsCompleted();
        }
    }
    
    // Kirim progress ke server
    function sendProgressToServer(percentage) {
        fetch(`/mitra/kursus/${KURSUS_ID}/material/${MATERIAL_ID}/video/progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({
                progress_percentage: percentage,
                current_time: 0,
                duration: 0
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Progress saved:', percentage + '%');
                lastSaveTime = new Date().toLocaleTimeString();
                lastSaved.textContent = `Terakhir: ${lastSaveTime}`;
                
                if (data.is_completed && !isVideoCompleted) {
                    markVideoAsCompleted();
                }
            }
        })
        .catch(error => {
            console.error('Error saving progress:', error);
        });
    }
    
    // Tandai video sebagai selesai
    function markVideoAsCompleted() {
        if (isVideoCompleted) return;
        
        fetch(`/mitra/kursus/${KURSUS_ID}/material/${MATERIAL_ID}/video/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                isVideoCompleted = true;
                videoProgress = 100;
                updateProgressUI(100);
                
                currentStatus.innerHTML = '<i class="fas fa-check-circle me-1"></i> Selesai';
                currentStatus.className = 'badge bg-success ms-2';
                
                // Hentikan interval tracking
                if (updateInterval) clearInterval(updateInterval);
                if (saveInterval) clearInterval(saveInterval);
                
                // Update tombol
                if (markCompleteBtn) {
                    markCompleteBtn.disabled = true;
                    markCompleteBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Sudah Selesai';
                }
                
                videoStatusText.textContent = 'Video telah selesai ditonton';
                
                // Tampilkan notifikasi
                showNotification('Video berhasil diselesaikan!', 'success');
            }
        })
        .catch(error => {
            console.error('Error completing video:', error);
            showNotification('Gagal menandai video selesai', 'error');
        });
    }
    
    // Tampilkan status auto-save
    function showAutoSaveStatus() {
        autoSaveStatus.innerHTML = `
            <div>
                <i class="fas fa-check me-2 text-success"></i>
                <small>Progress berhasil disimpan</small>
            </div>
            <div>
                <small id="last-saved">Terakhir: ${new Date().toLocaleTimeString()}</small>
            </div>
        `;
        autoSaveStatus.className = 'alert alert-success py-2 mb-0 d-flex justify-content-between align-items-center';
        
        setTimeout(() => {
            autoSaveStatus.innerHTML = `
                <div>
                    <i class="fas fa-sync fa-spin me-2"></i>
                    <small>Progress otomatis disimpan</small>
                </div>
                <div>
                    <small id="last-saved">${lastSaveTime ? 'Terakhir: ' + lastSaveTime : 'Belum disimpan'}</small>
                </div>
            `;
            autoSaveStatus.className = 'alert alert-info py-2 mb-0 d-flex justify-content-between align-items-center';
        }, 3000);
    }
    
    // Tampilkan fallback options
    function showFallbackOptions() {
        const fallbackHTML = `
            <div class="alert alert-warning mt-3">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Video Tidak Dapat Dimuat</h6>
                <p class="mb-2">Silakan coba salah satu alternatif berikut:</p>
                <div class="d-flex gap-2 flex-wrap">
                    ${DIRECT_LINK ? `
                        <a href="${DIRECT_LINK}" class="btn btn-sm btn-primary" target="_blank" download>
                            <i class="fas fa-download me-1"></i> Download Video
                        </a>
                        <a href="${DIRECT_LINK}" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru
                        </a>
                    ` : ''}
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()">
                        <i class="fas fa-redo me-1"></i> Refresh Halaman
                    </button>
                </div>
            </div>
        `;
        
        const progressSection = document.querySelector('.progress-section');
        if (progressSection) {
            progressSection.insertAdjacentHTML('beforebegin', fallbackHTML);
        }
    }
    
    // Tampilkan notifikasi
    function showNotification(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        const icon = type === 'success' ? 'fa-check-circle' : 
                    type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
        
        const alertHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3" 
                 style="z-index: 1060; max-width: 300px;" role="alert">
                <i class="fas ${icon} me-2"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', alertHTML);
        
        // Hapus otomatis setelah 5 detik
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.parentNode) alert.parentNode.removeChild(alert);
            });
        }, 5000);
    }
    
    // Fungsi helper
    window.reloadVideo = function() {
        if (videoPlayer) {
            const currentSrc = videoPlayer.src;
            videoPlayer.src = '';
            setTimeout(() => {
                videoPlayer.src = currentSrc;
                if (videoLoading) {
                    videoLoading.style.display = 'flex';
                    videoLoading.classList.remove('hidden');
                    loadingProgressValue = 0;
                    loadingProgress.style.width = '0%';
                    simulateLoading();
                }
                videoStatusText.textContent = 'Memuat ulang video...';
            }, 100);
        } else {
            window.location.reload();
        }
    };
    
    window.openInNewTab = function() {
        const url = EMBED_URL || DIRECT_LINK || '{{ $videoData["url"] }}';
        if (url) {
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };
    
    window.toggleFullscreen = function() {
        const elem = videoWrapper;
        if (!document.fullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    };
    
    window.testVideoUrl = function() {
        const url = EMBED_URL || DIRECT_LINK;
        if (url) {
            window.open(url, '_blank', 'noopener,noreferrer');
            showNotification('Membuka video di tab baru...', 'info');
        }
    };
    
    window.showVideoInfo = function() {
        const modal = new bootstrap.Modal(document.getElementById('videoInfoModal'));
        modal.show();
    };
    
    window.copyVideoInfo = function() {
        const info = `Video Info:
Judul: {{ $material->title }}
Tipe: {{ $videoData['type'] === 'hosted' ? 'Google Drive' : ucfirst($videoData['type']) }}
URL: {{ $videoData['url'] }}
Direct Link: {{ $videoData['direct_link'] ?? 'Tidak tersedia' }}`;
        
        navigator.clipboard.writeText(info)
            .then(() => showNotification('Informasi video disalin!', 'success'))
            .catch(() => showNotification('Gagal menyalin informasi', 'error'));
    };
    
    // Fungsi untuk tombol "Tandai Selesai"
    window.markAsCompleted = function() {
        if (confirm('Apakah Anda yakin ingin menandai video ini sebagai selesai?\n\nPastikan Anda sudah menonton video minimal ' + MIN_WATCH_PERCENTAGE + '%.')) {
            videoProgress = 100;
            updateProgressUI(100);
            markVideoAsCompleted();
        }
    };
    
    // Deteksi visibilitas halaman
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Tab tidak aktif, simpan progress terakhir
            sendProgressToServer(videoProgress);
        }
    });
    
    // Simpan progress saat halaman ditutup
    window.addEventListener('beforeunload', function() {
        if (!isVideoCompleted && videoProgress > 0) {
            // Gunakan sendBeacon untuk mengirim data sebelum halaman ditutup
            const data = JSON.stringify({
                progress_percentage: videoProgress,
                current_time: 0,
                duration: 0,
                _token: CSRF_TOKEN
            });
            
            navigator.sendBeacon(
                `/mitra/kursus/${KURSUS_ID}/material/${MATERIAL_ID}/video/progress`,
                data
            );
        }
    });
    
    // Fallback: jika video tidak load dalam 15 detik
    setTimeout(() => {
        if (videoLoading && !videoLoading.classList.contains('hidden')) {
            videoLoading.innerHTML = `
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h5 class="text-white">Video lambat dimuat</h5>
                <p class="text-white-50 mb-3">Silakan coba metode alternatif:</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-light" onclick="reloadVideo()">
                        <i class="fas fa-redo me-1"></i> Muat Ulang
                    </button>
                    ${DIRECT_LINK ? `
                        <a href="${DIRECT_LINK}" class="btn btn-light" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i> Buka Langsung
                        </a>
                    ` : ''}
                </div>
            `;
        }
    }, 15000);
});
</script>

<!-- Tambahkan Font Awesome untuk icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Tambahkan Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection