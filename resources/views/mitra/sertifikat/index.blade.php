@extends('mitra.layouts.app')

@section('title', 'Sertifikat Saya')

@section('content')


<style>
.hover-shadow {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

/* Header */
    .header {
        margin-bottom: 30px;
    }

    .title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 10px;
    }

    .subtitle {
        font-size: 1.1rem;
        color: #5a6c7d;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #1e3c72;
        color: white;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        background-color: #2a4a8a;
        transform: translateY(-2px);
    }

    .table-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .table-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3c72;
        margin-bottom: 20px;
    }

    /* ===== RESPONSIVE DESIGN UNTUK MOBILE (≤400px) ===== */
    @media (max-width: 400px) {
        /* Container utama - konsisten dengan halaman lain */
        .main-content {
            padding: 15px 12px !important;
            margin: 10px 10px !important;
            border-radius: 15px !important;
            max-width: 100% !important;
            overflow: hidden !important;
        }
        
        /* Header Section - sama dengan halaman lain */
        .header {
            margin-bottom: 0px !important;
            padding: 0 !important;
            text-align: center !important;
            width: 100% !important;
        }
        
        .title {
            font-size: 1.5rem !important; /* Turun dari 2.2rem */
            margin-bottom: 5px !important;
            color: #1e3c72 !important;
            font-weight: 700 !important;
            text-align: center !important;
        }
        
        .subtitle {
            font-size: 0.9rem !important; /* Turun dari 1.1rem */
            line-height: 1.4 !important;
            color: #5a6c7d !important;
            text-align: center !important;
        }
        
        /* Empty State - sesuaikan padding dan font */
        .text-center.py-5 {
            padding: 30px 15px !important;
        }
        
        .text-center.py-5 i.fa-certificate {
            font-size: 3rem !important;
            margin-bottom: 15px !important;
            color: #5a6c7d !important;
        }
        
        .text-center.py-5 h4 {
            font-size: 1.2rem !important;
            margin-bottom: 8px !important;
            color: #5a6c7d !important;
        }
        
        .text-center.py-5 p {
            font-size: 0.9rem !important;
            margin-bottom: 20px !important;
            line-height: 1.4 !important;
            color: #5a6c7d !important;
        }
        
        .text-center.py-5 .btn-primary {
            padding: 10px 20px !important;
            font-size: 0.9rem !important;
            background: linear-gradient(135deg, #1e3c72, #2a5298) !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
        }
        
        /* Table Container - sesuaikan padding */
        .table-container {
            padding: 15px !important; /* Kurangi dari 20px */
            border-radius: 10px !important;
            margin: 0 !important;
            width: 100% !important;
        }
        
        /* Table Responsive - beri margin yang pas */
        .table-responsive {
            margin: 0 -5px !important;
            padding: 0 5px !important;
        }
        
        /* Table - sesuaikan font size */
        table.table {
            font-size: 0.85rem !important;
        }
        
        table.table thead th {
            font-size: 0.8rem !important;
            padding: 10px 8px !important;
            white-space: nowrap !important;
        }
        
        table.table tbody td {
            padding: 10px 8px !important;
            font-size: 0.85rem !important;
        }
        
        /* Judul kursus di tabel */
        table.table tbody td strong {
            font-size: 0.9rem !important;
        }
        
        /* Tombol di tabel */
        .btn-primary.btn-sm {
            padding: 6px 12px !important;
            font-size: 0.75rem !important;
            background: linear-gradient(135deg, #1e3c72, #2a5298) !important;
            border: none !important;
            border-radius: 5px !important;
        }
        
        /* Nonaktifkan efek hover di mobile */
        .hover-shadow:hover,
        .btn-action:hover,
        .btn-primary:hover {
            transform: none !important;
            box-shadow: none !important;
        }
        
        /* Mobile Typography */
        body {
            font-size: 14px !important;
            line-height: 1.4 !important;
        }
    }
</style>
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="title">Sertifikat Saya</h2>
                <p class="subtitle">Daftar sertifikat yang telah Anda peroleh setelah menyelesaikan kursus</p>
            </div>
        </div>
    </div>
        
    @if($certificates->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-certificate fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Belum ada sertifikat</h4>
            <p class="text-muted mb-4">Selesaikan kursus untuk mendapatkan sertifikat.</p>
            <a href="{{ route('mitra.kursus.saya') }}" class="btn btn-primary">
                <i class="fas fa-book me-1"></i>Lihat Kursus Saya
            </a>
        </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 35%">Judul Kursus</th>
                    <th style="width: 20%">Penerbit</th>
                    <th style="width: 15%">Tanggal Terbit</th>
                    <th style="width: 15%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($certificates as $index => $certificate)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $certificate->kursus->judul_kursus }}</strong>
                    </td>
                    <td>{{ $certificate->kursus->penerbit }}</td>
                    <td>
                        {{ $certificate->issued_at->translatedFormat('d/m/Y') }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('sertifikat.download', $certificate) }}" 
                        class="btn btn-primary btn-sm" style="background-color: #1e3c72;"
                        title="Unduh Sertifikat">
                            <i class="fas fa-download"></i> Unduh
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
@endsection