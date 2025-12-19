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

</style>
    <div class="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
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