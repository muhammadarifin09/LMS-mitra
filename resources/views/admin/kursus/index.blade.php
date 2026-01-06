@extends('layouts.admin')

@section('title', 'Manajemen Kursus - MOOC BPS')

@section('styles')
<style>
    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    .table-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3c72;
        margin: 0;
    }

    .btn-tambah {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-tambah:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        color: white;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-edit {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }

    .btn-edit:hover {
        background: #3498db;
        color: white;
        transform: translateY(-2px);
    }

    .btn-delete {
        background: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
    }

    .btn-delete:hover {
        background: #e74c3c;
        color: white;
        transform: translateY(-2px);
    }

    .btn-view {
        background: rgba(155, 89, 182, 0.1);
        color: #9b59b6;
    }

    .btn-materi {
        background: rgba(46, 204, 113, 0.1);
        color: #27ae60;
    }

    .btn-materi:hover {
        background: #27ae60;
        color: white;
        transform: translateY(-2px);
    }

    .btn-view:hover {
        background: #9b59b6;
        color: white;
        transform: translateY(-2px);
    }

    .btn-detail {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }

    .btn-detail:hover {
        background: #3498db;
        color: white;
        transform: translateY(-2px);
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-aktif {
        background: rgba(46, 204, 113, 0.1);
        color: #27ae60;
    }

    .status-draft {
        background: rgba(243, 156, 18, 0.1);
        color: #f39c12;
    }

    .status-nonaktif {
        background: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
    }

    .search-box {
        display: flex;
        margin-bottom: 20px;
    }

    .search-box input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px 0 0 8px;
        border-right: none;
        font-size: 14px;
    }

    .search-box button {
        padding: 10px 20px;
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
        border: none;
        border-radius: 0 8px 8px 0;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .search-box button:hover {
        background: linear-gradient(135deg, #2a5298, #1e3c72);
    }

    /* Pagination Styles */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        border-top: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    .pagination {
        margin: 0;
    }

    .page-link {
        color: #1e3c72;
        background-color: white;
        border: 1px solid #dee2e6;
        padding: 8px 16px;
        margin: 0 4px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background-color: #1e3c72;
        color: white;
        border-color: #1e3c72;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(30, 60, 114, 0.2);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
        border-color: #1e3c72;
        font-weight: 600;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.9rem;
        text-align: center;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .perpage-select {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .perpage-select select {
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: white;
        color: #1e3c72;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .perpage-select select:hover {
        border-color: #1e3c72;
    }

    .perpage-select select:focus {
        outline: none;
        border-color: #1e3c72;
        box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
    }

    .footer-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 25px;
        border-top: 1px solid #e9ecef;
        background: #f8f9fa;
        color: #6c757d;
        font-size: 0.85rem;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
        border-radius: 16px 16px 0 0;
        border-bottom: none;
        padding: 20px 25px;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.4rem;
    }

    .btn-close-white {
        filter: invert(1);
    }

    .modal-body {
        padding: 25px;
    }

    .modal-image-container {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 300px;
        max-height: 500px;
    }

    .modal-course-image {
        width: 100%;
        height: auto;
        max-height: 500px;
        object-fit: contain;
    }

    .modal-image-placeholder {
        width: 100%;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 4rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-item strong {
        color: #1e3c72;
        min-width: 140px;
    }

    .info-item span {
        color: #5a6c7d;
    }

    .modal-meta {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .modal-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #5a6c7d;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .modal-meta-item i {
        color: #1e3c72;
        width: 16px;
    }

    /* Responsif */
    @media (max-width: 992px) {
        .pagination-container {
            flex-direction: column;
            gap: 20px;
        }
        
        .pagination-controls {
            width: 100%;
            justify-content: space-between;
        }
    }

    @media (max-width: 768px) {
        .table-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .table-header .d-flex {
            width: 100%;
            justify-content: space-between;
        }

        .action-buttons {
            flex-wrap: wrap;
        }

        .modal-image-container {
            min-height: 200px;
            max-height: 300px;
        }

        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }

        .page-link {
            padding: 6px 12px;
            margin: 2px;
            font-size: 0.9rem;
        }

        .footer-info {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }
    }

    @media (max-width: 576px) {
        .modal-image-container {
            min-height: 150px;
            max-height: 250px;
        }

        .pagination {
            font-size: 0.8rem;
        }

        .page-link {
            padding: 4px 8px;
            margin: 1px;
        }

        .perpage-select {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
    }
</style>
@endsection

@section('content')
<!-- WELCOME SECTION -->
<div class="welcome-section">
    <h1 class="welcome-title">Manajemen Kursus</h1>
    <p class="welcome-subtitle">
        Kelola semua kursus yang tersedia di platform MOOC BPS. Tambah, edit, atau hapus kursus sesuai kebutuhan.
    </p>
</div>

<!-- SEARCH AND FILTER -->
<div class="search-box">
    <input type="text" placeholder="Cari kursus berdasarkan judul, pelaksana, atau deskripsi..." 
           value="{{ request('search') }}" 
           onkeypress="if(event.key === 'Enter') searchCourses()">
    <button type="button" onclick="searchCourses()">
        <i class="fas fa-search"></i>
        Cari
    </button>
</div>

<!-- TABLE SECTION -->
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Daftar Kursus</h2>
        <a href="{{ route('admin.kursus.create') }}" class="btn-tambah">
            <i class="fas fa-plus-circle"></i>
            Tambah Kursus
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Kursus</th>
                    <th>Pelaksana</th>
                    <th>Kategori</th>
                    <th>Jam Pelajaran (JP) </th>
                    <th>Status</th>
                    <th>Peserta</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($kursus) && $kursus->count() > 0)
                    @foreach($kursus as $index => $item)
                    <tr>
                        <td>{{ ($kursus->currentPage() - 1) * $kursus->perPage() + $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->judul_kursus }}</strong>
                        </td>
                        <td>{{ $item->pelaksana }}</td>
                        <td>
                            @if($item->kategori == 'pemula')
                                <span class="badge bg-primary">Pemula</span>
                            @elseif($item->kategori == 'menengah')
                                <span class="badge bg-warning">Menengah</span>
                            @else
                                <span class="badge bg-danger">Lanjutan</span>
                            @endif
                        </td>
                        <td>{{ $item->durasi_jam }} JP</td>
                        <td>
                            @if($item->status == 'aktif')
                                <span class="status-badge status-aktif">Aktif</span>
                            @elseif($item->status == 'draft')
                                <span class="status-badge status-draft">Draft</span>
                            @else
                                <span class="status-badge status-nonaktif">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $item->peserta_terdaftar }} / 
                                @if($item->kuota_peserta)
                                    {{ $item->kuota_peserta }}
                                @else
                                    ∞
                                @endif
                            </small>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- Tombol Detail - Selalu Tampil -->
                                <button type="button" class="btn-action btn-detail" title="Detail Kursus" 
                                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <!-- Tombol Materi -->
                                <a href="{{ route('admin.kursus.materials.index', $item) }}" class="btn-action btn-materi" title="Kelola Materi">
                                    <i class="fas fa-book-open"></i>
                                </a>

                                <!-- Tombol Edit -->
                                <a href="{{ route('admin.kursus.edit', $item->id) }}" class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.kursus.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" title="Hapus" 
                                            onclick="confirmDelete(event, this.closest('form'))">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal untuk Detail Kursus -->
                    <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailModalLabel{{ $item->id }}">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Detail Kursus: {{ $item->judul_kursus }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Gambar Kursus jika ada -->
                                    @if($item->gambar_kursus)
                                    <div class="modal-image-container mb-4">
                                        <img src="{{ asset('storage/' . $item->gambar_kursus) }}" 
                                            alt="{{ $item->judul_kursus }}" 
                                            class="modal-course-image"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="modal-image-placeholder" style="display: none;">
                                            <i class="fas fa-book-open"></i>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Informasi Utama -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <strong><i class="fas fa-user-tie me-2"></i>Pelaksana:</strong>
                                                <span>{{ $item->pelaksana }}</span>
                                            </div>

                                            <div class="info-item">
                                                <strong><i class="fas fa-clock me-2"></i>JP:</strong>
                                                <span>{{ $item->durasi_jam }} JP</span>
                                            </div>

                                            <div class="info-item">
                                                <strong><i class="fas fa-users me-2"></i>Peserta:</strong>
                                                <span>
                                                    {{ $item->peserta_terdaftar }} /
                                                    @if($item->kuota_peserta)
                                                        {{ $item->kuota_peserta }}
                                                    @else
                                                        Tidak Terbatas
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <strong><i class="fas fa-chart-line me-2"></i>Kategori:</strong>
                                                <span>
                                                    @if($item->kategori == 'pemula')
                                                        <span class="badge bg-primary">Pemula</span>
                                                    @elseif($item->kategori == 'menengah')
                                                        <span class="badge bg-warning">Menengah</span>
                                                    @else
                                                        <span class="badge bg-blue">Lanjutan</span>
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="info-item">
                                                <strong><i class="fas fa-toggle-on me-2"></i>Status:</strong>
                                                <span>
                                                    @if($item->status == 'aktif')
                                                        <span class="status-badge status-aktif">Aktif</span>
                                                    @elseif($item->status == 'draft')
                                                        <span class="status-badge status-draft">Draft</span>
                                                    @else
                                                        <span class="status-badge status-nonaktif">Nonaktif</span>
                                                    @endif
                                                </span>
                                            </div>

                                            @if($item->tanggal_mulai && $item->tanggal_selesai)
                                            <div class="info-item">
                                                <strong><i class="fas fa-calendar me-2"></i>Periode:</strong>
                                                <span>
                                                    {{ $item->tanggal_mulai->format('d M Y') }} -
                                                    {{ $item->tanggal_selesai->format('d M Y') }}
                                                </span>
                                            </div>
                                            @endif

                                            @if(!empty($item->enroll_code))
                                                <div class="info-item mt-2">
                                                    <strong>
                                                        <i class="fas fa-key me-2 text-blue"></i>
                                                        Kode Enroll:
                                                    </strong>
                                                    <span class="badge bg-black text-white">
                                                        {{ $item->enroll_code }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="info-item mt-2">
                                                    <strong>
                                                        <i class="fas fa-unlock me-2 text-success"></i>
                                                        Akses Kursus:
                                                    </strong>
                                                    <span class="badge bg-success">
                                                        Terbuka (Tanpa Kode)
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Deskripsi Kursus -->
                                    <div class="course-info mb-4">
                                        <h6 class="fw-bold text-primary mb-3">
                                            <i class="fas fa-align-left me-2"></i>Deskripsi Kursus:
                                        </h6>
                                        <div class="card">
                                            <div class="card-body">
                                                <p class="card-text">{{ $item->deskripsi_kursus }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Informasi Tambahan -->
                                    <div class="row">
                                        @if($item->output_pelatihan)
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-primary mb-3">
                                                <i class="fas fa-bullseye me-2"></i>Output Pelatihan:
                                            </h6>
                                            <div class="card">
                                                <div class="card-body">
                                                    <ul class="mb-0 ps-3">
                                                        @foreach(explode("\n", $item->output_pelatihan) as $output)
                                                            @if(trim($output))
                                                                <li>{{ trim($output) }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if($item->persyaratan)
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-primary mb-3">
                                                <i class="fas fa-clipboard-list me-2"></i>Persyaratan:
                                            </h6>
                                            <div class="card">
                                                <div class="card-body">
                                                    <ul class="mb-0 ps-3">
                                                        @foreach(explode("\n", $item->persyaratan) as $syarat)
                                                            @if(trim($syarat))
                                                                <li>{{ trim($syarat) }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    @if($item->fasilitas)
                                    <div class="mb-3">
                                        <h6 class="fw-bold text-primary mb-3">
                                            <i class="fas fa-gift me-2"></i>Fasilitas:
                                        </h6>
                                        <div class="card">
                                            <div class="card-body">
                                                <ul class="mb-0 ps-3">
                                                    @foreach(explode("\n", $item->fasilitas) as $fasilitas)
                                                        @if(trim($fasilitas))
                                                            <li>{{ trim($fasilitas) }}</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Tutup
                                    </button>
                                    <a href="{{ route('admin.kursus.edit', $item->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Edit Kursus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-book me-2"></i>
                            @if(!isset($kursus))
                                Variabel $kursus tidak terdefinisi
                            @else
                                Tidak ada data kursus
                            @endif
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- PAGINATION SECTION -->
    @if(isset($kursus) && $kursus->hasPages())
    <div class="pagination-container">
        <!-- Kontrol Per Page -->
        <div class="pagination-controls">
            <div class="perpage-select">
                <span>Tampilkan:</span>
                <select onchange="changePerPage(this.value)">
                    <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>data per halaman</span>
            </div>

            <!-- Pagination Navigation -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if($kursus->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $kursus->previousPageUrl() }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $current = $kursus->currentPage();
                        $last = $kursus->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp

                    {{-- First Page Link --}}
                    @if($start > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $kursus->url(1) }}">1</a>
                        </li>
                        @if($start > 2)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif

                    {{-- Array Of Links --}}
                    @for($page = $start; $page <= $end; $page++)
                        @if($page == $current)
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $kursus->url($page) }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endfor

                    {{-- Last Page Link --}}
                    @if($end < $last)
                        @if($end < $last - 1)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link" href="{{ $kursus->url($last) }}">{{ $last }}</a>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if($kursus->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $kursus->nextPageUrl() }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>

        <!-- Pagination Info -->
        <div class="pagination-info">
            Menampilkan {{ ($kursus->currentPage() - 1) * $kursus->perPage() + 1 }} 
            sampai {{ min($kursus->currentPage() * $kursus->perPage(), $kursus->total()) }} 
            dari {{ $kursus->total() }} data
            @if(request()->has('search'))
                untuk pencarian "{{ request('search') }}"
            @endif
        </div>
    </div>
    @else
        <!-- Hanya tampilkan perpage selector jika ada data -->
        @if(isset($kursus) && $kursus->count() > 0)
        <div class="pagination-container">
            <div class="pagination-controls">
                <div class="perpage-select">
                    <span>Tampilkan:</span>
                    <select onchange="changePerPage(this.value)">
                        <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span>data per halaman</span>
                </div>
            </div>
            
            <div class="pagination-info">
                Menampilkan 1 sampai {{ $kursus->count() }} dari {{ $kursus->total() }} data
                @if(request()->has('search'))
                    untuk pencarian "{{ request('search') }}"
                @endif
            </div>
        </div>
        @endif
    @endif
</div>

<!-- FOOTER INFO -->
<div class="footer-info">

    <div>
        Total Data: {{ $kursus->total() ?? 0 }} Kursus
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Delete Confirmation dengan SweetAlert
    function confirmDelete(event, form) {
        event.preventDefault();
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Kursus akan dihapus permanen dan tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    // Search functionality
    function searchCourses() {
        const searchTerm = document.querySelector('.search-box input').value;
        const url = new URL(window.location.href);
        
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }
        
        // Reset to page 1 when searching
        url.searchParams.set('page', '1');
        
        // Keep per_page parameter
        const perPage = '{{ request('per_page', 10) }}';
        if (perPage && perPage !== '10') {
            url.searchParams.set('per_page', perPage);
        }
        
        window.location.href = url.toString();
    }

    // Change items per page
    function changePerPage(value) {
        const url = new URL(window.location.href);
        
        // Reset to page 1 when changing per page
        url.searchParams.set('page', '1');
        url.searchParams.set('per_page', value);
        
        // Keep search parameter if exists
        const searchTerm = document.querySelector('.search-box input').value;
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        }
        
        window.location.href = url.toString();
    }

    // Enter key untuk search
    document.querySelector('.search-box input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchCourses();
        }
    });

    // Handle modal image errors
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.modal-course-image').forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
                const placeholder = this.nextElementSibling;
                if (placeholder && placeholder.classList.contains('modal-image-placeholder')) {
                    placeholder.style.display = 'flex';
                }
            });
        });
    });

    // Modal show event untuk menangani gambar modal
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            const modalImage = this.querySelector('.modal-course-image');
            if (modalImage) {
                // Force reload image to ensure it displays correctly
                modalImage.src = modalImage.src;
            }
        });
    });
</script>
@endsection