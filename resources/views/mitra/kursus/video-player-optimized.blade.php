@extends('mitra.layouts.app')

@section('title', 'Video Materi - ' . $material->title)

@section('content')
@php
// Helper function untuk memotong teks
function shortenText($text, $length = 50) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Validasi data video
$videoData = $videoData ?? [];

// Tentukan tipe video dan data yang tersedia
$videoType = $videoData['type'] ?? 'unknown';
$isAvailable = $videoData['is_available'] ?? false;
$embedUrl = $videoData['embed_url'] ?? '';
$directLink = $videoData['direct_link'] ?? '';
$videoUrl = $videoData['url'] ?? $directLink ?? $embedUrl ?? '';
$playerType = $videoData['player_type'] ?? 'iframe';
$isLocalVideo = $videoType === 'local';
$isYouTube = $videoType === 'youtube';
$isHosted = $videoType === 'hosted';

// Untuk video lokal
$localVideoToken = $videoData['video_token'] ?? '';
$localVideoPath = $videoData['path'] ?? '';

// Tentukan min watch percentage
$minWatchPercentage = $videoData['player_config']['min_watch_percentage'] ?? 90;

// ==============================================
// FIX UTAMA: PERBAIKI URL VIDEO LOKAL
// ==============================================
if ($isLocalVideo && !empty($directLink)) {
    // Fix 1: Jika URL tanpa port, tambahkan :8000
    if (strpos($directLink, 'http://localhost/storage/') === 0 && 
        strpos($directLink, ':8000') === false) {
        $directLink = str_replace('http://localhost/storage/', 'http://localhost:8000/storage/', $directLink);
        $videoUrl = $directLink;
    }
    
    // Fix 2: Jika URL dimulai dengan /storage/, tambahkan base URL
    elseif (strpos($directLink, '/storage/') === 0) {
        $directLink = 'http://localhost:8000' . $directLink;
        $videoUrl = $directLink;
    }
}

// Fallback jika video tidak tersedia
if ($isLocalVideo && empty($directLink)) {
    // Coba cari file video di storage
    $videoFiles = Storage::files('videos');
    foreach ($videoFiles as $file) {
        $filename = basename($file);
        if (strpos($filename, 'mp4') !== false || strpos($filename, 'mov') !== false) {
            $directLink = url(Storage::url($file));
            $videoUrl = $directLink;
            $isAvailable = true;
            break;
        }
    }
}
@endphp

<style>
    :root {
        --video-height: 70vh;
        --controls-height: 60px;
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
    
    .video-container-wrapper {
        position: relative;
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        background: #000;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        cursor: pointer;
        user-select: none;
    }
    
    .video-container {
        position: relative;
        width: 100%;
        height: var(--video-height);
        min-height: 400px;
        background: #000;
    }
    
    /* Container khusus untuk video lokal */
    .local-video-wrapper {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }
    
    /* Style untuk video element */
    #main-video-player {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        background: #000;
        display: block;
        margin: 0 auto;
    }
    
    /* Untuk video dengan aspect ratio 16:9 */
    .aspect-ratio-16-9 {
        position: relative;
        width: 100%;
        padding-top: 56.25%; /* 16:9 Aspect Ratio */
    }
    
    .aspect-ratio-16-9 .video-content {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    /* Center Play/Pause Button */
    .center-play-button {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.7);
        border: 4px solid rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 30;
        opacity: 0;
        transition: opacity 0.3s, transform 0.2s;
        backdrop-filter: blur(5px);
    }
    
    .center-play-button:hover {
        background: rgba(0, 0, 0, 0.9);
        border-color: #ffffff;
        transform: translate(-50%, -50%) scale(1.05);
    }
    
    .center-play-button i {
        font-size: 40px;
        color: white;
        margin-left: 5px; /* Untuk centering icon play */
    }
    
    .center-play-button.playing i {
        margin-left: 0;
    }
    
    .video-container-wrapper:hover .center-play-button {
        opacity: 1;
    }
    
    /* Video Controls */
    .video-controls-container {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.9));
        padding: 15px 20px 10px;
        z-index: 20;
        opacity: 0;
        transition: opacity 0.3s ease;
        transform: translateY(100%);
    }
    
    .video-container-wrapper:hover .video-controls-container,
    .video-container-wrapper.controls-visible .video-controls-container {
        opacity: 1;
        transform: translateY(0);
    }
    
    .controls-top {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
    }
    
    .progress-container {
        flex: 1;
        height: 6px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .progress-bar {
        height: 100%;
        background: #2196F3;
        border-radius: 3px;
        width: 0%;
        position: relative;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        right: -6px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        background: #2196F3;
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .progress-container:hover .progress-bar::after {
        opacity: 1;
    }
    
    .controls-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 5px;
    }
    
    .left-controls, .right-controls {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .control-button {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        transition: background 0.2s, transform 0.2s;
    }
    
    .control-button:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: scale(1.1);
    }
    
    .control-button:active {
        transform: scale(0.95);
    }
    
    .time-display {
        color: white;
        font-size: 14px;
        font-family: monospace;
        min-width: 100px;
        text-align: center;
    }
    
    /* Volume Control */
    .volume-control {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .volume-slider {
        width: 80px;
        height: 4px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
        position: relative;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .volume-control:hover .volume-slider {
        opacity: 1;
    }
    
    .volume-slider-fill {
        position: absolute;
        height: 100%;
        background: #2196F3;
        border-radius: 2px;
        width: 100%;
    }
    
    /* Playback Rate Menu */
    .playback-rate-menu {
        position: absolute;
        bottom: 50px;
        background: rgba(0, 0, 0, 0.9);
        border-radius: 6px;
        padding: 10px 0;
        min-width: 120px;
        display: none;
        z-index: 100;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .playback-rate-menu.show {
        display: block;
    }
    
    .playback-rate-item {
        padding: 8px 20px;
        color: white;
        cursor: pointer;
        transition: background 0.2s;
        font-size: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .playback-rate-item:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    
    .playback-rate-item.active {
        background: #2196F3;
        color: white;
    }
    
    /* Settings Menu */
    .settings-menu {
        position: absolute;
        bottom: 50px;
        right: 0;
        background: rgba(0, 0, 0, 0.9);
        border-radius: 6px;
        padding: 10px 0;
        min-width: 150px;
        display: none;
        z-index: 100;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .settings-menu.show {
        display: block;
    }
    
    .settings-item {
        padding: 10px 15px;
        color: white;
        cursor: pointer;
        transition: background 0.2s;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .settings-item:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    
    .settings-item i {
        width: 20px;
        text-align: center;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .video-container {
            height: 50vh;
            min-height: 300px;
        }
        
        .center-play-button {
            width: 60px;
            height: 60px;
        }
        
        .center-play-button i {
            font-size: 30px;
        }
        
        .controls-bottom {
            flex-wrap: wrap;
        }
        
        .time-display {
            min-width: 80px;
            font-size: 12px;
        }
        
        .volume-slider {
            display: none;
        }
    }
    
    /* Animations */
    .video-container-wrapper {
        transition: transform 0.3s;
    }
    
    .video-container-wrapper.scaled {
        transform: scale(0.98);
    }
    
    /* Hide cursor and controls after inactivity */
    .video-container-wrapper.inactive {
        cursor: none;
    }
    
    .video-container-wrapper.inactive .video-controls-container,
    .video-container-wrapper.inactive .center-play-button {
        opacity: 0;
        pointer-events: none;
    }
    
    /* Loading animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .pulse {
        animation: pulse 1.5s infinite;
    }
    
    /* Video Source Badge */
    .video-source-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 25;
        backdrop-filter: blur(5px);
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
        z-index: 5;
    }
    
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.25em;
    }
</style>

<script>
console.log('=== VIDEO VIEW DEBUG ===');
console.log('videoType:', '{{ $videoType }}');
console.log('isLocalVideo:', {{ $isLocalVideo ? 'true' : 'false' }});
console.log('isAvailable:', {{ $isAvailable ? 'true' : 'false' }});
console.log('directLink:', '{{ $directLink }}');
console.log('videoUrl:', '{{ $videoUrl }}');

function fixVideoUrl(url) {
    if (!url) return '';
    
    if (url.includes('http://localhost/storage/') && !url.includes(':8000')) {
        return url.replace('http://localhost/storage/', 'http://localhost:8000/storage/');
    }
    
    if (url.startsWith('/storage/')) {
        return 'http://localhost:8000' + url;
    }
    
    return url;
}
</script>

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
                        </div>
                        
                        <!-- Video Source Badge -->
                        <div class="video-source-badge">
                            @if($isHosted)
                                <i class="fas fa-hdd me-1"></i> Google Drive
                            @elseif($isYouTube)
                                <i class="fab fa-youtube me-1"></i> YouTube
                            @elseif($isLocalVideo)
                                <i class="fas fa-file-video me-1"></i> Video Lokal
                            @else
                                <i class="fas fa-video me-1"></i> Video
                            @endif
                        </div>
                        
                        <!-- Center Play/Pause Button -->
                        <div class="center-play-button" id="center-play-button">
                            <i class="fas fa-play"></i>
                        </div>

                        <!-- Video Container -->
                        <div class="video-container" id="video-container">
                            @if($isAvailable && !empty($embedUrl) && !$isLocalVideo)
                                <!-- YouTube atau Google Drive Player -->
                                <div class="aspect-ratio-16-9">
                                    <div class="video-content">
                                        <iframe 
                                            src="{{ $embedUrl }}" 
                                            allow="autoplay; fullscreen; picture-in-picture; encrypted-media"
                                            allowfullscreen
                                            id="video-iframe"
                                            title="{{ $material->title }}"
                                            loading="lazy"
                                            style="width: 100%; height: 100%; border: none;">
                                        </iframe>
                                    </div>
                                </div>
                            
                            @elseif($isLocalVideo && !empty($directLink))
                                <!-- Video Player untuk Video Lokal -->
                                <?php $videoSrc = $directLink; ?>
                                
                                <div class="local-video-wrapper" id="local-video-wrapper">
                                    <video 
                                        id="main-video-player"
                                        preload="metadata"
                                        playsinline
                                        poster="{{ asset('img/video-poster.jpg') }}"
                                        onloadedmetadata="handleVideoMetadataLoaded(event)"
                                        onloadeddata="handleVideoLoadedSuccess()"
                                        onerror="handleVideoPlayerError(event)"
                                        style="max-width: 100%; max-height: 100%;">
                                        <source src="{{ $videoSrc }}" type="video/mp4">
                                        <p class="text-white p-3">
                                            Browser Anda tidak mendukung pemutaran video HTML5.<br>
                                            <a href="{{ $videoSrc }}" class="text-primary" download>Download video</a>
                                        </p>
                                    </video>
                                </div>
                                
                            @else
                                <!-- Fallback Message -->
                                <div class="video-fallback">
                                    <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                                    <h5>Video tidak tersedia</h5>
                                    <p class="mb-3">
                                        @if($isLocalVideo && empty($directLink))
                                            URL video tidak ditemukan.
                                        @elseif(!$isAvailable)
                                            Video belum diunggah atau tidak dapat diakses.
                                        @else
                                            Terjadi kesalahan dalam memuat video.
                                        @endif
                                    </p>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-secondary" onclick="window.location.reload()">
                                            <i class="fas fa-redo me-1"></i> Refresh
                                        </button>
                                        <a href="{{ route('mitra.kursus.show', $kursus) }}" class="btn btn-outline-warning">
                                            <i class="fas fa-arrow-left me-1"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Video Controls -->
                        <div class="video-controls-container" id="video-controls">
                            <!-- Progress Bar -->
                            <div class="controls-top">
                                <div class="progress-container" id="progress-container">
                                    <div class="progress-bar" id="progress-bar"></div>
                                </div>
                            </div>
                            
                            <!-- Bottom Controls -->
                            <div class="controls-bottom">
                                <!-- Left Controls -->
                                <div class="left-controls">
                                    <button class="control-button" id="play-pause-btn" title="Play/Pause (Space)">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    
                                    <button class="control-button" id="skip-backward-btn" title="Mundur 10 detik (←)">
                                        <i class="fas fa-backward"></i>
                                    </button>
                                    
                                    <button class="control-button" id="skip-forward-btn" title="Maju 10 detik (→)">
                                        <i class="fas fa-forward"></i>
                                    </button>
                                    
                                    <!-- Volume Control -->
                                    <div class="volume-control">
                                        <button class="control-button" id="volume-btn" title="Volume (M)">
                                            <i class="fas fa-volume-up"></i>
                                        </button>
                                        <div class="volume-slider" id="volume-slider">
                                            <div class="volume-slider-fill" id="volume-slider-fill"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Time Display -->
                                    <div class="time-display" id="time-display">
                                        <span id="current-time">0:00</span> / 
                                        <span id="duration-time">0:00</span>
                                    </div>
                                </div>
                                
                                <!-- Right Controls -->
                                <div class="right-controls">
                                    <!-- Playback Rate -->
                                    <div class="playback-rate-container" style="position: relative;">
                                        <button class="control-button" id="playback-rate-btn" title="Kecepatan Putar">
                                            <i class="fas fa-tachometer-alt"></i>
                                        </button>
                                        <div class="playback-rate-menu" id="playback-rate-menu">
                                            <div class="playback-rate-item" data-rate="0.5">0.5x</div>
                                            <div class="playback-rate-item" data-rate="0.75">0.75x</div>
                                            <div class="playback-rate-item active" data-rate="1">Normal</div>
                                            <div class="playback-rate-item" data-rate="1.25">1.25x</div>
                                            <div class="playback-rate-item" data-rate="1.5">1.5x</div>
                                            <div class="playback-rate-item" data-rate="2">2x</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Settings Menu -->
                                    <div class="settings-container" style="position: relative;">
                                        <button class="control-button" id="settings-btn" title="Pengaturan">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <div class="settings-menu" id="settings-menu">
                                            <div class="settings-item" id="quality-option">
                                                <i class="fas fa-hd"></i>
                                                <span>Kualitas</span>
                                            </div>
                                            <div class="settings-item" id="subtitles-option">
                                                <i class="fas fa-closed-captioning"></i>
                                                <span>Subtitle</span>
                                            </div>
                                            <div class="settings-item" id="pip-option">
                                                <i class="fas fa-picture-in-picture"></i>
                                                <span>Picture-in-Picture</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Fullscreen -->
                                    <button class="control-button" id="fullscreen-btn" title="Layar Penuh (F)">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('mitra.kursus.show', $kursus) }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Materi
                        </a>
                        
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            @if($isHosted)
                                Video dari Google Drive
                            @elseif($isYouTube)
                                Video dari YouTube
                            @elseif($isLocalVideo)
                                Video Lokal
                            @else
                                Video
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk Video Player dengan Fitur Lengkap -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Video Player Initialized');
    
    // ============================
    // ELEMENTS
    // ============================
    const videoWrapper = document.getElementById('video-wrapper');
    const videoContainer = document.getElementById('video-container');
    const videoPlayer = document.getElementById('main-video-player') || document.getElementById('video-iframe');
    const videoLoading = document.getElementById('video-loading');
    const centerPlayButton = document.getElementById('center-play-button');
    const videoControls = document.getElementById('video-controls');
    
    // Control Buttons
    const playPauseBtn = document.getElementById('play-pause-btn');
    const skipBackwardBtn = document.getElementById('skip-backward-btn');
    const skipForwardBtn = document.getElementById('skip-forward-btn');
    const volumeBtn = document.getElementById('volume-btn');
    const volumeSlider = document.getElementById('volume-slider');
    const volumeSliderFill = document.getElementById('volume-slider-fill');
    const fullscreenBtn = document.getElementById('fullscreen-btn');
    const playbackRateBtn = document.getElementById('playback-rate-btn');
    const playbackRateMenu = document.getElementById('playback-rate-menu');
    const settingsBtn = document.getElementById('settings-btn');
    const settingsMenu = document.getElementById('settings-menu');
    
    // Progress and Time
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const currentTimeEl = document.getElementById('current-time');
    const durationTimeEl = document.getElementById('duration-time');
    const timeDisplay = document.getElementById('time-display');
    
    // ============================
    // STATE VARIABLES
    // ============================
    let isPlaying = false;
    let isFullscreen = false;
    let isMuted = false;
    let currentVolume = 1;
    let playbackRate = 1;
    let isLocalVideo = {{ $isLocalVideo ? 'true' : 'false' }};
    let inactivityTimer;
    let controlsVisible = false;
    let isDraggingProgress = false;
    let isDraggingVolume = false;
    
    // ============================
    // INITIALIZATION
    // ============================
    initVideoPlayer();
    
    function initVideoPlayer() {
        console.log('🎬 Initializing video player...');
        
        if (videoPlayer && videoPlayer.tagName === 'VIDEO') {
            setupLocalVideoPlayer();
        } else if (videoPlayer && videoPlayer.tagName === 'IFRAME') {
            setupIframePlayer();
        } else {
            console.error('❌ No video player element found!');
            hideLoading();
        }
        
        // Hide loading after timeout
        setTimeout(hideLoading, 5000);
        
        // Initialize volume slider
        updateVolumeSlider();
    }
    
    function setupLocalVideoPlayer() {
        console.log('🔧 Setting up local video player...');
        
        // Set initial volume
        videoPlayer.volume = currentVolume;
        videoPlayer.playbackRate = playbackRate;
        
        // Event Listeners
        videoPlayer.addEventListener('loadedmetadata', handleVideoMetadata);
        videoPlayer.addEventListener('timeupdate', handleTimeUpdate);
        videoPlayer.addEventListener('play', handlePlay);
        videoPlayer.addEventListener('pause', handlePause);
        videoPlayer.addEventListener('ended', handleEnded);
        videoPlayer.addEventListener('volumechange', handleVolumeChange);
        videoPlayer.addEventListener('ratechange', handleRateChange);
        
        // Control Button Events
        centerPlayButton.addEventListener('click', togglePlayPause);
        playPauseBtn.addEventListener('click', togglePlayPause);
        skipBackwardBtn.addEventListener('click', skipBackward);
        skipForwardBtn.addEventListener('click', skipForward);
        
        // Progress Bar Events
        progressContainer.addEventListener('click', seekToPosition);
        progressContainer.addEventListener('mousedown', startDraggingProgress);
        document.addEventListener('mousemove', handleProgressDrag);
        document.addEventListener('mouseup', stopDraggingProgress);
        
        // Touch events for mobile
        progressContainer.addEventListener('touchstart', startDraggingProgress);
        document.addEventListener('touchmove', handleProgressDrag);
        document.addEventListener('touchend', stopDraggingProgress);
        
        // Volume Control Events
        volumeBtn.addEventListener('click', toggleMute);
        volumeSlider.addEventListener('click', setVolume);
        volumeSlider.addEventListener('mousedown', startDraggingVolume);
        document.addEventListener('mousemove', handleVolumeDrag);
        document.addEventListener('mouseup', stopDraggingVolume);
        
        // Playback Rate Menu
        playbackRateBtn.addEventListener('click', togglePlaybackRateMenu);
        playbackRateMenu.querySelectorAll('.playback-rate-item').forEach(item => {
            item.addEventListener('click', function() {
                const rate = parseFloat(this.dataset.rate);
                setPlaybackRate(rate);
                playbackRateMenu.classList.remove('show');
            });
        });
        
        // Settings Menu
        settingsBtn.addEventListener('click', toggleSettingsMenu);
        
        // Fullscreen
        fullscreenBtn.addEventListener('click', toggleFullscreen);
        document.addEventListener('fullscreenchange', handleFullscreenChange);
        document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
        document.addEventListener('mozfullscreenchange', handleFullscreenChange);
        
        // Mouse movement detection for controls visibility
        videoWrapper.addEventListener('mousemove', showControls);
        videoWrapper.addEventListener('mouseleave', startInactivityTimer);
        videoWrapper.addEventListener('click', showControls);
        
        // Keyboard shortcuts
        document.addEventListener('keydown', handleKeyboardShortcuts);
        
        // Click video to play/pause
        videoWrapper.addEventListener('click', function(e) {
            if (e.target !== centerPlayButton && 
                e.target !== playPauseBtn && 
                !e.target.closest('.video-controls-container')) {
                togglePlayPause();
            }
        });
        
        // Double click for fullscreen
        videoWrapper.addEventListener('dblclick', toggleFullscreen);
        
        // Start inactivity timer
        startInactivityTimer();
    }
    
    function setupIframePlayer() {
        console.log('🔧 Setting up iframe player...');
        
        // For iframe, hide custom controls as they won't work
        videoControls.style.display = 'none';
        centerPlayButton.style.display = 'none';
        
        // Just handle loading state
        hideLoading();
    }
    
    // ============================
    // VIDEO EVENT HANDLERS
    // ============================
    function handleVideoMetadata() {
        console.log('📊 Video metadata loaded');
        updateDurationDisplay();
        hideLoading();
    }
    
    function handleTimeUpdate() {
        if (!isDraggingProgress) {
            updateProgressBar();
            updateCurrentTimeDisplay();
        }
    }
    
    function handlePlay() {
        isPlaying = true;
        updatePlayPauseButton();
        centerPlayButton.classList.add('playing');
        centerPlayButton.querySelector('i').className = 'fas fa-pause';
        startInactivityTimer();
    }
    
    function handlePause() {
        isPlaying = false;
        updatePlayPauseButton();
        centerPlayButton.classList.remove('playing');
        centerPlayButton.querySelector('i').className = 'fas fa-play';
    }
    
    function handleEnded() {
        isPlaying = false;
        updatePlayPauseButton();
        centerPlayButton.classList.remove('playing');
        centerPlayButton.querySelector('i').className = 'fas fa-replay';
        
        // Show replay message
        showNotification('Video selesai. Klik untuk memutar ulang', 'info', 2000);
    }
    
    function handleVolumeChange() {
        currentVolume = videoPlayer.volume;
        isMuted = videoPlayer.muted;
        updateVolumeButton();
        updateVolumeSlider();
    }
    
    function handleRateChange() {
        playbackRate = videoPlayer.playbackRate;
        updatePlaybackRateButton();
    }
    
    // ============================
    // PLAYBACK CONTROLS
    // ============================
    function togglePlayPause() {
        if (videoPlayer.paused) {
            videoPlayer.play();
        } else {
            videoPlayer.pause();
        }
        showControls();
    }
    
    function skipBackward() {
        if (videoPlayer.currentTime > 10) {
            videoPlayer.currentTime -= 10;
        } else {
            videoPlayer.currentTime = 0;
        }
        showControls();
        showNotification('Mundur 10 detik', 'info', 1000);
    }
    
    function skipForward() {
        videoPlayer.currentTime += 10;
        showControls();
        showNotification('Maju 10 detik', 'info', 1000);
    }
    
    function setPlaybackRate(rate) {
        videoPlayer.playbackRate = rate;
        playbackRate = rate;
        
        // Update menu selection
        playbackRateMenu.querySelectorAll('.playback-rate-item').forEach(item => {
            if (parseFloat(item.dataset.rate) === rate) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
        
        showNotification('Kecepatan: ' + rate + 'x', 'info', 1000);
    }
    
    // ============================
    // PROGRESS BAR
    // ============================
    function updateProgressBar() {
        if (!videoPlayer.duration) return;
        
        const percent = (videoPlayer.currentTime / videoPlayer.duration) * 100;
        progressBar.style.width = percent + '%';
    }
    
    function seekToPosition(e) {
        if (!videoPlayer.duration) return;
        
        const rect = progressContainer.getBoundingClientRect();
        const pos = (e.clientX - rect.left) / rect.width;
        videoPlayer.currentTime = pos * videoPlayer.duration;
        showControls();
    }
    
    function startDraggingProgress(e) {
        isDraggingProgress = true;
        videoWrapper.classList.add('scaled');
        showControls();
    }
    
    function handleProgressDrag(e) {
        if (!isDraggingProgress) return;
        
        e.preventDefault();
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        if (!clientX) return;
        
        const rect = progressContainer.getBoundingClientRect();
        const pos = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
        progressBar.style.width = (pos * 100) + '%';
        
        // Update time display while dragging
        if (videoPlayer.duration) {
            const newTime = pos * videoPlayer.duration;
            updateTimeDisplay(newTime, videoPlayer.duration);
        }
    }
    
    function stopDraggingProgress(e) {
        if (!isDraggingProgress) return;
        
        isDraggingProgress = false;
        videoWrapper.classList.remove('scaled');
        
        if (videoPlayer.duration) {
            const rect = progressContainer.getBoundingClientRect();
            const clientX = e.clientX || (e.changedTouches && e.changedTouches[0].clientX);
            if (clientX) {
                const pos = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
                videoPlayer.currentTime = pos * videoPlayer.duration;
            }
        }
    }
    
    // ============================
    // VOLUME CONTROLS
    // ============================
    function toggleMute() {
        videoPlayer.muted = !videoPlayer.muted;
        showControls();
        
        if (videoPlayer.muted) {
            showNotification('Volume dimatikan', 'info', 1000);
        } else {
            showNotification('Volume dihidupkan', 'info', 1000);
        }
    }
    
    function setVolume(e) {
        const rect = volumeSlider.getBoundingClientRect();
        const pos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
        currentVolume = pos;
        videoPlayer.volume = currentVolume;
        videoPlayer.muted = currentVolume === 0;
        updateVolumeSlider();
        showControls();
    }
    
    function startDraggingVolume(e) {
        isDraggingVolume = true;
        showControls();
    }
    
    function handleVolumeDrag(e) {
        if (!isDraggingVolume) return;
        
        e.preventDefault();
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        if (!clientX) return;
        
        const rect = volumeSlider.getBoundingClientRect();
        const pos = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
        currentVolume = pos;
        videoPlayer.volume = currentVolume;
        videoPlayer.muted = currentVolume === 0;
        updateVolumeSlider();
    }
    
    function stopDraggingVolume(e) {
        isDraggingVolume = false;
    }
    
    function updateVolumeSlider() {
        const volume = videoPlayer.muted ? 0 : videoPlayer.volume;
        volumeSliderFill.style.width = (volume * 100) + '%';
    }
    
    // ============================
    // TIME DISPLAY
    // ============================
    function updateCurrentTimeDisplay() {
        if (!videoPlayer.duration) return;
        updateTimeDisplay(videoPlayer.currentTime, videoPlayer.duration);
    }
    
    function updateDurationDisplay() {
        if (!videoPlayer.duration) return;
        updateTimeDisplay(videoPlayer.currentTime, videoPlayer.duration);
    }
    
    function updateTimeDisplay(current, duration) {
        currentTimeEl.textContent = formatTime(current);
        durationTimeEl.textContent = formatTime(duration);
    }
    
    function formatTime(seconds) {
        if (isNaN(seconds)) return '0:00';
        
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = Math.floor(seconds % 60);
        
        if (hours > 0) {
            return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        } else {
            return `${minutes}:${secs.toString().padStart(2, '0')}`;
        }
    }
    
    // ============================
    // UI UPDATES
    // ============================
    function updatePlayPauseButton() {
        if (videoPlayer.paused) {
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
            playPauseBtn.title = 'Play (Space)';
        } else {
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
            playPauseBtn.title = 'Pause (Space)';
        }
    }
    
    function updateVolumeButton() {
        if (videoPlayer.muted || videoPlayer.volume === 0) {
            volumeBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
            volumeBtn.title = 'Unmute (M)';
        } else if (videoPlayer.volume < 0.5) {
            volumeBtn.innerHTML = '<i class="fas fa-volume-down"></i>';
            volumeBtn.title = 'Volume (M)';
        } else {
            volumeBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
            volumeBtn.title = 'Volume (M)';
        }
    }
    
    function updatePlaybackRateButton() {
        playbackRateBtn.title = 'Kecepatan: ' + playbackRate + 'x';
    }
    
    function updateFullscreenButton() {
        if (isFullscreen) {
            fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
            fullscreenBtn.title = 'Keluar Fullscreen (F)';
        } else {
            fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
            fullscreenBtn.title = 'Fullscreen (F)';
        }
    }
    
    // ============================
    // MENUS
    // ============================
    function togglePlaybackRateMenu() {
        playbackRateMenu.classList.toggle('show');
        settingsMenu.classList.remove('show');
        showControls();
    }
    
    function toggleSettingsMenu() {
        settingsMenu.classList.toggle('show');
        playbackRateMenu.classList.remove('show');
        showControls();
    }
    
    // Close menus when clicking outside
    document.addEventListener('click', function(e) {
        if (!playbackRateBtn.contains(e.target) && !playbackRateMenu.contains(e.target)) {
            playbackRateMenu.classList.remove('show');
        }
        
        if (!settingsBtn.contains(e.target) && !settingsMenu.contains(e.target)) {
            settingsMenu.classList.remove('show');
        }
    });
    
    // ============================
    // FULLSCREEN
    // ============================
    function toggleFullscreen() {
        if (!isFullscreen) {
            enterFullscreen();
        } else {
            exitFullscreen();
        }
        showControls();
    }
    
    function enterFullscreen() {
        const elem = videoWrapper;
        
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    }
    
    function exitFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
    
    function handleFullscreenChange() {
        isFullscreen = !!(document.fullscreenElement || 
                         document.webkitFullscreenElement ||
                         document.mozFullScreenElement ||
                         document.msFullscreenElement);
        updateFullscreenButton();
        
        if (isFullscreen) {
            videoWrapper.classList.add('fullscreen');
        } else {
            videoWrapper.classList.remove('fullscreen');
        }
    }
    
    // ============================
    // CONTROLS VISIBILITY
    // ============================
    function showControls() {
        controlsVisible = true;
        videoWrapper.classList.add('controls-visible');
        videoWrapper.classList.remove('inactive');
        
        // Show center play button only when paused
        if (videoPlayer.paused) {
            centerPlayButton.style.opacity = '1';
        }
        
        clearTimeout(inactivityTimer);
        startInactivityTimer();
    }
    
    function startInactivityTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(() => {
            if (!videoPlayer.paused) {
                hideControls();
            }
        }, 3000);
    }
    
    function hideControls() {
        controlsVisible = false;
        videoWrapper.classList.remove('controls-visible');
        videoWrapper.classList.add('inactive');
        
        // Always hide center button when inactive
        centerPlayButton.style.opacity = '0';
        
        // Hide menus
        playbackRateMenu.classList.remove('show');
        settingsMenu.classList.remove('show');
    }
    
    // ============================
    // KEYBOARD SHORTCUTS
    // ============================
    function handleKeyboardShortcuts(e) {
        // Ignore if user is typing in input
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        
        switch(e.key.toLowerCase()) {
            case ' ':
            case 'k':
                e.preventDefault();
                togglePlayPause();
                break;
                
            case 'f':
                toggleFullscreen();
                break;
                
            case 'm':
                e.preventDefault();
                toggleMute();
                break;
                
            case 'arrowleft':
                e.preventDefault();
                skipBackward();
                break;
                
            case 'arrowright':
                e.preventDefault();
                skipForward();
                break;
                
            case '>':
            case '.':
                e.preventDefault();
                if (videoPlayer.playbackRate < 2) {
                    setPlaybackRate(videoPlayer.playbackRate + 0.25);
                }
                break;
                
            case '<':
            case ',':
                e.preventDefault();
                if (videoPlayer.playbackRate > 0.5) {
                    setPlaybackRate(videoPlayer.playbackRate - 0.25);
                }
                break;
                
            case '0':
            case 'home':
                e.preventDefault();
                videoPlayer.currentTime = 0;
                showControls();
                break;
                
            case 'end':
                e.preventDefault();
                videoPlayer.currentTime = videoPlayer.duration;
                showControls();
                break;
                
            case 'l':
                e.preventDefault();
                skipForward();
                break;
                
            case 'j':
                e.preventDefault();
                skipBackward();
                break;
        }
    }
    
    // ============================
    // HELPER FUNCTIONS
    // ============================
    function hideLoading() {
        if (videoLoading) {
            videoLoading.style.display = 'none';
        }
    }
    
    function showNotification(message, type = 'info', duration = 2000) {
        // Remove existing notification
        const existingNotif = document.querySelector('.video-notification');
        if (existingNotif) {
            existingNotif.remove();
        }
        
        // Create new notification
        const notif = document.createElement('div');
        notif.className = `video-notification alert alert-${type}`;
        notif.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Style notification
        notif.style.cssText = `
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            padding: 10px 20px;
            border-radius: 8px;
            background: ${type === 'info' ? '#2196F3' : type === 'success' ? '#4CAF50' : '#FF9800'};
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            animation: slideUp 0.3s ease;
            max-width: 80%;
            text-align: center;
        `;
        
        document.body.appendChild(notif);
        
        // Remove after duration
        setTimeout(() => {
            if (notif.parentNode) {
                notif.style.opacity = '0';
                notif.style.transform = 'translateX(-50%) translateY(-20px)';
                notif.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    if (notif.parentNode) {
                        notif.parentNode.removeChild(notif);
                    }
                }, 300);
            }
        }, duration);
    }
    
    // ============================
    // GLOBAL FUNCTIONS (untuk inline event handlers)
    // ============================
    window.handleVideoLoadedSuccess = function() {
        console.log('✅ Video loaded successfully!');
        hideLoading();
        showControls();
    };
    
    window.handleVideoPlayerError = function(event) {
        console.error('❌ Video error:', event);
        hideLoading();
        showNotification('Gagal memuat video', 'error');
    };
    
    // Add CSS for notification animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
});
</script>

<!-- Tambahkan Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Tambahkan Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection