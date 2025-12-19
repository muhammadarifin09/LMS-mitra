@extends('layouts.app')

@section('title', 'Preview Sertifikat')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('certificates.index') }}">Sertifikat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Preview</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-certificate text-primary me-2"></i>
                            Preview Sertifikat
                        </h4>
                        <div>
                            <a href="{{ route('certificates.download', $certificate) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-download me-1"></i>Unduh PDF
                            </a>
                            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="fas fa-print me-1"></i>Cetak
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Kursus:</strong> {{ $certificate->kursus->judul_kursus }}</p>
                            <p class="mb-1"><strong>Penerbit:</strong> {{ $certificate->kursus->penerbit }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>No. Sertifikat:</strong> {{ $certificate->certificate_number }}</p>
                            <p class="mb-1"><strong>Tanggal Terbit:</strong> {{ $certificate->issued_at->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>

                    <!-- Iframe untuk preview PDF -->
                    <div class="border rounded overflow-hidden">
                        <iframe 
                            src="{{ route('certificates.preview', $certificate) }}" 
                            width="100%" 
                            height="800"
                            style="border: none;">
                        </iframe>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Sertifikat ini merupakan bukti bahwa Anda telah menyelesaikan kursus 
                        <strong>{{ $certificate->kursus->judul_kursus }}</strong> 
                        dengan baik.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection