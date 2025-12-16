@extends('mitra.layouts.app')

@section('title', 'Video Materi - ' . $material->title)

@section('content')
<style>
    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        max-width: 100%;
        background: #000;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
    
    .video-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .questions-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #dee2e6;
    }
    
    .question-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
    }
    
    .question-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Video Player -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-play-circle me-2"></i>
                        {{ $material->title }}
                    </h4>
                    <small>{{ $material->description }}</small>
                </div>
                <div class="card-body">
                    @if($videoData['embed_url'])
                        @if($videoData['type'] === 'hosted')
                            <!-- Google Drive Video -->
                            <div class="video-container">
                                <iframe 
                                    src="{{ $videoData['embed_url'] }}" 
                                    allow="autoplay; fullscreen; picture-in-picture"
                                    allowfullscreen
                                    id="video-player">
                                </iframe>
                            </div>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Video dari Google Drive. Pastikan Anda memiliki koneksi internet yang stabil.
                            </div>
                        @elseif($videoData['type'] === 'youtube')
                            <!-- YouTube Video -->
                            <div class="video-container">
                                <iframe 
                                    src="{{ $videoData['embed_url'] }}" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    id="video-player">
                                </iframe>
                            </div>
                        @elseif($videoData['type'] === 'vimeo')
                            <!-- Vimeo Video -->
                            <div class="video-container">
                                <iframe 
                                    src="{{ $videoData['embed_url'] }}" 
                                    allow="autoplay; fullscreen; picture-in-picture"
                                    allowfullscreen
                                    id="video-player">
                                </iframe>
                            </div>
                        @else
                            <!-- External Video -->
                            <div class="video-container">
                                <iframe 
                                    src="{{ $videoData['embed_url'] }}" 
                                    allow="autoplay; fullscreen"
                                    allowfullscreen
                                    id="video-player">
                                </iframe>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            URL video tidak tersedia atau tidak valid.
                        </div>
                    @endif
                    
                    <!-- Progress Tracking -->
                    <div class="progress-section mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Progress Video</span>
                            <span class="text-muted" id="progress-percentage">
                                {{ $progress->video_progress ?? 0 }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" 
                                 id="progress-bar" 
                                 role="progressbar" 
                                 style="width: {{ $progress->video_progress ?? 0 }}%"
                                 aria-valuenow="{{ $progress->video_progress ?? 0 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        @if($progress && $progress->video_status === 'completed')
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle me-2"></i>
                                Video telah selesai ditonton!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Video Questions (if any) -->
            @if($videoQuestions && count($videoQuestions) > 0)
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            Pertanyaan Video ({{ count($videoQuestions) }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($videoQuestions as $question)
                            <div class="question-item">
                                <p class="fw-bold mb-2">Pertanyaan {{ $loop->iteration }} ({{ $question->time_in_seconds }} detik):</p>
                                <p class="mb-3">{{ $question->question }}</p>
                                
                                @php
                                    $options = json_decode($question->options, true);
                                @endphp
                                
                                @if(is_array($options))
                                    <div class="options">
                                        @foreach($options as $index => $option)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="question_{{ $question->id }}"
                                                       id="option_{{ $question->id }}_{{ $index }}"
                                                       disabled>
                                                <label class="form-check-label" for="option_{{ $question->id }}_{{ $index }}">
                                                    {{ $option }}
                                                    @if($index == $question->correct_option)
                                                        <span class="badge bg-success ms-2">Jawaban Benar</span>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if($question->explanation)
                                    <div class="alert alert-light mt-2">
                                        <small class="text-muted">
                                            <strong>Penjelasan:</strong> {{ $question->explanation }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <!-- Material Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Materi
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-book me-2"></i> Tipe Video:</span>
                            <span class="fw-bold">
                                @if($videoData['type'] === 'hosted')
                                    Google Drive
                                @elseif($videoData['type'] === 'youtube')
                                    YouTube
                                @elseif($videoData['type'] === 'vimeo')
                                    Vimeo
                                @else
                                    Eksternal
                                @endif
                            </span>
                        </li>
                        
                        @if($material->duration)
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="fas fa-clock me-2"></i> Durasi:</span>
                                <span class="fw-bold">{{ ceil($material->duration / 60) }} menit</span>
                            </li>
                        @endif
                        
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-check-circle me-2"></i> Status:</span>
                            <span class="fw-bold">
                                @if($progress && $progress->video_status === 'completed')
                                    <span class="text-success">Selesai</span>
                                @else
                                    <span class="text-warning">Dalam Proses</span>
                                @endif
                            </span>
                        </li>
                        
                        @if($videoQuestions && count($videoQuestions) > 0)
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="fas fa-question me-2"></i> Pertanyaan:</span>
                                <span class="fw-bold">{{ count($videoQuestions) }} soal</span>
                            </li>
                        @endif
                    </ul>
                    
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('mitra.kursus.show', $kursus) }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Kursus
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Player Settings -->
            @if(!empty($videoData['player_config']))
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>
                            Pengaturan Pemutar
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $config = $videoData['player_config'];
                        @endphp
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-{{ $config['allow_skip'] ?? true ? 'check text-success' : 'times text-danger' }} me-2"></i>
                                Dapat dilewati: {{ $config['allow_skip'] ?? true ? 'Ya' : 'Tidak' }}
                            </li>
                            @if($config['require_completion'] ?? true)
                                <li class="mb-2">
                                    <i class="fas fa-percentage me-2"></i>
                                    Minimal tonton: {{ $config['min_watch_percentage'] ?? 90 }}%
                                </li>
                            @endif
                            @if($config['auto_pause_on_question'] ?? false)
                                <li class="mb-2">
                                    <i class="fas fa-pause me-2"></i>
                                    Otomatis jeda saat pertanyaan
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript untuk tracking progress video -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoIframe = document.getElementById('video-player');
    const progressBar = document.getElementById('progress-bar');
    const progressPercentage = document.getElementById('progress-percentage');
    let updateInterval;
    let lastUpdateTime = 0;
    const updateFrequency = 10000; // Update setiap 10 detik
    const kursusId = {{ $kursus->id }};
    const materialId = {{ $material->id }};
    
    // Kirim progress ke server
    function sendProgress(percentage, currentTime, duration) {
        const now = Date.now();
        
        // Cegah terlalu sering mengirim request
        if (now - lastUpdateTime < updateFrequency) {
            return;
        }
        
        lastUpdateTime = now;
        
        fetch('{{ route("mitra.kursus.material.video.progress", ["kursus" => $kursus->id, "material" => $material->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                progress_percentage: percentage,
                current_time: currentTime,
                duration: duration
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update progress bar
                if (progressBar && progressPercentage) {
                    const newPercentage = data.progress.video_progress || percentage;
                    progressBar.style.width = newPercentage + '%';
                    progressBar.setAttribute('aria-valuenow', newPercentage);
                    progressPercentage.textContent = newPercentage + '%';
                    
                    // Jika video selesai, tampilkan pesan
                    if (data.is_completed) {
                        const alertHTML = `
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle me-2"></i>
                                Video telah selesai ditonton!
                            </div>
                        `;
                        
                        // Cek apakah sudah ada alert, jika belum tambahkan
                        const progressSection = document.querySelector('.progress-section');
                        const existingAlert = progressSection.querySelector('.alert-success');
                        if (!existingAlert) {
                            progressSection.insertAdjacentHTML('beforeend', alertHTML);
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error updating progress:', error);
        });
    }
    
    // Fungsi untuk mendapatkan estimasi progress (karena kita tidak bisa mengakses iframe langsung)
    function estimateProgress() {
        // Untuk video eksternal, kita hanya bisa mengestimasi
        // Anda bisa mengganti logika ini berdasarkan kebutuhan
        const estimatedPercentage = Math.min((lastUpdateTime / (lastUpdateTime + 1000)) * 100, 95);
        return estimatedPercentage;
    }
    
    // Mulai tracking progress
    if (videoIframe) {
        // Kirim progress awal
        setTimeout(() => {
            sendProgress(0, 0, 0);
        }, 2000);
        
        // Update progress secara berkala
        updateInterval = setInterval(() => {
            const percentage = estimateProgress();
            sendProgress(percentage, 0, 0);
        }, updateFrequency);
    }
    
    // Hentikan tracking saat halaman ditutup
    window.addEventListener('beforeunload', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
            // Kirim progress terakhir
            const percentage = estimateProgress();
            sendProgress(percentage, 0, 0);
        }
    });
    
    // Tambahan: Jika video selesai (misalnya setelah 30 detik), tandai sebagai selesai
    setTimeout(() => {
        const config = @json($videoData['player_config'] ?? []);
        const minPercentage = config.min_watch_percentage || 90;
        
        // Simulasi: jika user menonton minimal 30 detik, anggap selesai
        if (lastUpdateTime > 30000) {
            sendProgress(minPercentage, 30, 30);
        }
    }, 30000);
});
</script>
@endsection