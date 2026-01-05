@extends('mitra.layouts.app')

@php
    $noSidebar = true;
@endphp

@section('title', 'Verifikasi Sertifikat')

<style>
    /* ===== RESPONSIVE DESIGN UNTUK MOBILE (≤500px) ===== */
    @media (max-width: 500px) {
        .main-content {
            padding: 15px 15px !important;
            margin: 10px 10px !important;
            border-radius: 15px !important;
            max-width: 95% !important;
            overflow: hidden !important;
        }
        
        /* STATUS SECTION */
        .text-center.mb-4 {
            margin-bottom: 20px !important;
            padding: 13px !important;
            background: linear-gradient(135deg, #f8fff8, #f0fff0) !important;
            border-radius: 12px !important;
            border-left: 4px solid #28a745 !important;
        }
        
        .display-4.text-success {
            font-size: 3rem !important;
            margin-bottom: 10px !important;
        }
        
        h2.fw-bold.text-success {
            font-size: 1.3rem !important;
            margin-bottom: 8px !important;
            line-height: 1.3 !important;
        }
        
        .text-center.mb-4 p.text-muted {
            font-size: 0.85rem !important;
            line-height: 1.4 !important;
            margin-bottom: 0 !important;
            color: #6c757d !important;
        }
        
        /* PREVIEW PDF CARD */
        .card.shadow.mb-4 {
            margin-bottom: 20px !important;
            border-radius: 10px !important;
            overflow: hidden !important;
        }
        
        .card-header.bg-white.fw-bold {
            font-size: 0.95rem !important;
            padding: 12px 15px !important;
            background-color: #fff !important;
            border-bottom: 1px solid #eee !important;
        }
        
        .card-body.p-0 {
            height: 300px !important; /* Tinggi lebih kecil untuk mobile */
        }
        
        iframe {
            height: 100% !important;
            min-height: 300px !important;
        }
        
        /* TABLE STYLES FOR MOBILE */
        table.table.table-borderless {
            font-size: 0.85rem !important;
            width: 100% !important;
        }
        
        table.table.table-borderless tr {
            display: flex !important;
            flex-direction: column !important;
            margin-bottom: 15px !important;
            padding-bottom: 15px !important;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        
        table.table.table-borderless tr:last-child {
            border-bottom: none !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        
        table.table.table-borderless th {
            width: 100% !important;
            font-size: 0.8rem !important;
            color: #1e3c72 !important;
            font-weight: 600 !important;
            margin-bottom: 5px !important;
            padding: 0 !important;
        }
        
        table.table.table-borderless td {
            width: 100% !important;
            padding: 0 !important;
            font-size: 0.9rem !important;
            color: #333 !important;
            word-break: break-word !important;
        }
        
        /* Badge styling */
        .badge.bg-dark {
            font-size: 0.75rem !important;
            padding: 5px 10px !important;
            border-radius: 20px !important;
            display: inline-block !important;
            max-width: 100% !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            background-color: #343a40 !important;
        }
        
        /* Mobile Typography */
        body {
            font-size: 14px !important;
            line-height: 1.4 !important;
            background-color: #f8f9fa !important;
        }
        
        /* Padding untuk section */
        .mb-4 {
            margin-bottom: 1rem !important;
        }
        
        /* Tombol jika ada (untuk future enhancement) */
        .btn {
            padding: 10px 20px !important;
            font-size: 0.9rem !important;
            border-radius: 8px !important;
        }
        
        /* Nonaktifkan efek hover di mobile */
        .hover-shadow:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    }
</style>

@section('content')
    <!-- STATUS -->
    <div class="text-center mb-4">
        <div class="display-4 text-success mb-2">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 class="fw-bold text-success">Sertifikat Terverifikasi</h2>
        <p class="text-muted">
            Sertifikat ini telah diverifikasi dan valid.  
            Anda dapat menggunakan sertifikat ini untuk pelatihan mitra selanjutnya.
        </p>
    </div>

    <!-- PREVIEW PDF -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white fw-bold">
            Preview Sertifikat
        </div>
        <div class="card-body p-0">
            <iframe 
                src="{{ route('certificates.publicPdf', $certificate->id_kredensial) }}"
                width="100%" 
                height="700"
                style="border:none;">
            </iframe>
        </div>
    </div>

    <!-- DETAIL -->
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">
            Detail Sertifikat
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="200">No. Sertifikat</th>
                    <td>{{ $certificate->certificate_number }}</td>
                </tr>
                <tr>
                    <th>Penerima</th>
                    <td>{{ $certificate->user->nama }}</td>
                </tr>
                <tr>
                    <th>Kursus</th>
                    <td>{{ $certificate->kursus->judul_kursus }}</td>
                </tr>
                <tr>
                    <th>Tanggal Terbit</th>
                    <td>{{ $certificate->issued_at->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <th>ID Kredensial</th>
                    <td>
                        <span class="badge bg-dark">
                            {{ $certificate->id_kredensial }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection
