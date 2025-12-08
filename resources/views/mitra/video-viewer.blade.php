{{-- resources/views/mitra/video-viewer.blade.php --}}
@extends('mitra.layouts.app')

@section('title', $material->title . ' - ' . $kursus->judul_kursus)

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-video me-2"></i>{{ $material->title }}
                </h5>
                <a href="{{ url('/mitra/kursus/' . $kursus->id) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Materi
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <!-- Video Player -->
                    <div class="video-container mb-4">
                        @php
                            $videoId = null;
                            if (str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be')) {
                                // Extract YouTube ID
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches);
                                $videoId = $matches[1] ?? null;
                        @endphp
                            @if($videoId)
                            <div class="ratio ratio-16x9">
                                <iframe 
                                    src="https://www.youtube.com/embed/{{ $videoId }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            @endif
                        @php
                            } elseif (str_contains($videoUrl, 'vimeo.com')) {
                                // Extract Vimeo ID
                                preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $videoUrl, $matches);
                                $videoId = $matches[1] ?? null;
                        @endphp
                            @if($videoId)
                            <div class="ratio ratio-16x9">
                                <iframe 
                                    src="https://player.vimeo.com/video/{{ $videoId }}" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            @endif
                        @php
                            } else {
                                // Direct video file or other providers
                        @endphp
                            <div class="ratio ratio-16x9">
                                <video controls class="w-100" style="border-radius: 8px;">
                                    <source src="{{ $videoUrl }}" type="video/mp4">
                                    Browser Anda tidak mendukung tag video.
                                </video>
                            </div>
                        @php
                            }
                        @endphp
                    </div>

                    <!-- Video Info -->
                    <div class="video-info mb-4">
                        <h6>Deskripsi Video</h6>
                        <p class="text-muted">
                            {{ $material->description ?? 'Tidak ada deskripsi' }}
                        </p>
                        <div class="d-flex gap-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Durasi: {{ $material->duration_video ?? 'N/A' }} menit
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Diunggah: {{ $material->created_at->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Completion Status -->
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-video fa-3x text-primary mb-2"></i>
                                <h5>Status Video</h5>
                            </div>
                            
                            <div id="video-status">
                                @php
                                    $progress = \App\Models\MaterialProgress::where('user_id', auth()->id())
                                        ->where('material_id', $material->id)
                                        ->first();
                                @endphp
                                
                                @if($progress && $progress->video_status === 'completed')
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Video telah ditonton</strong>
                                        <p class="mb-0 small">Anda sudah menyelesaikan video ini</p>
                                    </div>
                                    <a href="{{ url('/mitra/kursus/' . $kursus->id) }}" class="btn btn-success">
                                        <i class="fas fa-check me-1"></i> Lanjutkan ke Materi Berikutnya
                                    </a>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Video belum selesai</strong>
                                        <p class="mb-0 small">Tonton video hingga selesai</p>
                                    </div>
                                    
                                    <button type="button" class="btn btn-primary" id="mark-completed-btn">
                                        <i class="fas fa-check me-1"></i> Tandai sebagai Telah Ditonton
                                    </button>
                                    
                                    <p class="text-muted small mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        Pastikan Anda telah menonton video hingga selesai sebelum menandai
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Course Info -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Kursus</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-book me-2 text-primary"></i>
                                    <strong>Kursus:</strong> {{ $kursus->judul_kursus }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-list-ol me-2 text-primary"></i>
                                    <strong>Urutan Materi:</strong> {{ $material->order }}
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    <strong>Durasi:</strong> {{ $material->duration_video ?? 0 }} menit
                                </li>
                                @if($material->attendance_required)
                                <li>
                                    <i class="fas fa-clipboard-check me-2 text-warning"></i>
                                    <strong>Wajib Hadir</strong>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const markCompletedBtn = document.getElementById('mark-completed-btn');
        
        if (markCompletedBtn) {
            markCompletedBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Konfirmasi',
                    html: 'Apakah Anda sudah menonton video hingga selesai?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, sudah ditonton',
                    cancelButtonText: 'Belum'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Sedang menandai video sebagai telah ditonton',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send AJAX request
                        fetch('{{ route("mitra.kursus.material.mark-video", [$kursus->id, $material->id]) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    // Reload page to update status
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan: ' + error.message,
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        });
                    }
                });
            });
        }
    });
</script>
@endpush
@endsection