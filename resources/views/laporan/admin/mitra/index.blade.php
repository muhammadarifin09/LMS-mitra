@extends('layouts.admin')

@section('title', 'MOOC BPS - Laporan Mitra')

@section('styles')

<!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Main Content Area */
    .main-content {
        flex: 1;
        padding: 40px;
        background: rgba(255, 255, 255, 0.95);
        margin: 20px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 25px;
    }

    .welcome-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .welcome-subtitle {
        font-size: 1.1rem;
        line-height: 1.6;
        opacity: 0.9;
    }

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

    .btn-export {
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

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        color: white;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        width: auto;
        padding: 8px 15px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center; /* Center vertical */
        justify-content: center; /* Center horizontal */
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        font-size: 0.9rem;
    }

    .btn-view {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }

    .btn-view:hover {
        background: #3498db;
        color: white;
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')
<!-- WELCOME SECTION -->
<div class="welcome-section">
    <h1 class="welcome-title">Laporan Belajar Mitra</h1>
    <p class="welcome-subtitle">
        Monitor dan kelola progress belajar mitra BPS. Lihat detail kursus yang diikuti, progress, dan nilai mitra.
    </p>
</div>

<!-- TABLE SECTION -->
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Daftar Mitra</h2>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>ID Sobat</th>
                    <th>Kecamatan</th>
                    <th>Total Kursus</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if($mitra->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-database me-2"></i>
                            Tidak ada data mitra ditemukan
                        </td>
                    </tr>
                @else
                    @foreach($mitra as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->biodata->id_sobat ?? '-' }}</td>
                        <td>{{ $item->biodata->kecamatan ?? '-' }}</td>
                        <td>{{ $item->enrollments_count }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.laporan.mitra.detail', $item->id) }}" 
                                class="btn-action btn-view d-flex align-items-center" 
                                title="Lihat Detail">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>                                                                              

<!-- Copyright -->
<div class="text-center mt-5 pt-4 border-top">
    <p style="color: #5a6c7d; font-size: 0.9rem;">
        Copyright © 2025 | MOOC BPS
    </p>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Inisialisasi DataTable
    $(document).ready(function() {
        $('.table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json",
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    });
</script>   
@endsection