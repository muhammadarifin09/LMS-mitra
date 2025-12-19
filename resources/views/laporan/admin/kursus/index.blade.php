@extends('layouts.admin')

@section('title', 'Laporan Kursus')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book-open mr-2"></i>
                        Laporan Data Kursus
                    </h3>
                    <div class="card-tools">
                       <!-- TAMBAH: Tombol Export Excel -->
                       <a href="{{ route('test.csv') }}"
                        class="btn btn-sm btn-success">
                            <i class="fas fa-file-csv"></i> Export Excel
                        </a>

                    </div>
                </div>
                <div class="card-body">
                    @if($kursus->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Belum ada data kursus untuk dilaporkan.
                    </div>
                    @else
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-striped table-bordered table-hover" style="width: 100%;">
                            <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <tr>
                                    <th style="width: 50px; text-align: center;">No</th>
                                    <th style="min-width: 200px;">Judul Kursus</th>
                                    <th style="width: 150px; text-align: center;">Jumlah Peserta</th>
                                    <th style="width: 140px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kursus as $item)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                                    <td style="vertical-align: middle;">
                                        <div style="font-weight: 600; color: #2d3748; margin-bottom: 3px;">
                                            {{ $item->judul_kursus }}
                                        </div>
                                        @if($item->deskripsi_singkat)
                                        <div style="font-size: 0.85rem; color: #718096; line-height: 1.4;">
                                            {{ Str::limit($item->deskripsi_singkat, 80) }}
                                        </div>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <div style="display: inline-block; padding: 6px 15px; border-radius: 20px; 
                                             background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); 
                                             color: white; font-weight: 600; font-size: 0.9rem;
                                             box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);
                                             min-width: 80px;">
                                            {{ $item->enrollments_count }} <small>Peserta</small>
                                        </div>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- DETAIL: ke halaman detail -->
                                            <a href="{{ route('admin.laporan.kursus.detail', $item->id) }}" 
                                               class="btn btn-info" 
                                               title="Detail Laporan"
                                               style="border-radius: 4px 0 0 4px; padding: 5px 10px;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <!-- PDF RINGKAS: export ringkas -->
                                            <a href="{{ route('admin.laporan.kursus.pdf.ringkas', $item->id) }}" 
                                               class="btn btn-warning" 
                                               title="Export Ringkasan PDF"
                                               target="_blank"
                                               style="border-radius: 0; padding: 5px 10px;">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                            <!-- PDF DETAIL: export detail -->
                                            <a href="{{ route('admin.laporan.kursus.pdf.detail', $item->id) }}" 
                                               class="btn btn-danger" 
                                               title="Export Detail PDF"
                                               target="_blank"
                                               style="border-radius: 0 4px 4px 0; padding: 5px 10px;">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if($kursus->isNotEmpty())
                            <tfoot>
                                <tr>
                                    <td colspan="2" style="text-align: right; font-weight: 600; color: #2d3748;">
                                        Total Peserta:
                                    </td>
                                    <td style="text-align: center; font-weight: 700;">
                                        <span style="display: inline-block; padding: 5px 12px; border-radius: 15px;
                                              background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
                                              color: white; font-size: 0.9rem;">
                                            {{ $kursus->sum('enrollments_count') }} Peserta
                                        </span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                    
                    <!-- Statistik Ringkas -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); 
                                  border-radius: 10px; padding: 15px; color: white; margin-bottom: 15px;">
                                <div style="display: flex; align-items: center;">
                                    <div style="flex: 1;">
                                        <div style="font-size: 0.9rem; opacity: 0.9;">Total Kursus</div>
                                        <div style="font-size: 1.8rem; font-weight: 700;">{{ $kursus->count() }}</div>
                                    </div>
                                    <div style="font-size: 2.5rem; opacity: 0.7;">
                                        <i class="fas fa-book"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
                                  border-radius: 10px; padding: 15px; color: white; margin-bottom: 15px;">
                                <div style="display: flex; align-items: center;">
                                    <div style="flex: 1;">
                                        <div style="font-size: 0.9rem; opacity: 0.9;">Total Peserta</div>
                                        <div style="font-size: 1.8rem; font-weight: 700;">{{ $kursus->sum('enrollments_count') }}</div>
                                    </div>
                                    <div style="font-size: 2.5rem; opacity: 0.7;">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); 
                                  border-radius: 10px; padding: 15px; color: white; margin-bottom: 15px;">
                                <div style="display: flex; align-items: center;">
                                    <div style="flex: 1;">
                                        <div style="font-size: 0.9rem; opacity: 0.9;">Rata-rata Peserta</div>
                                        <div style="font-size: 1.8rem; font-weight: 700;">{{ round($kursus->avg('enrollments_count'), 1) }}</div>
                                    </div>
                                    <div style="font-size: 2.5rem; opacity: 0.7;">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @if(!$kursus->isEmpty())
                <div class="card-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted" style="display: flex; align-items: center;">
                                <i class="fas fa-info-circle mr-2" style="color: #4f46e5;"></i>
                                <span>
                                    Data diperbarui: 
                                    <span id="realtimeTimestamp" style="font-weight: 600; color: #2d3748;">
                                        {{ now()->format('d M Y H:i:s') }}
                                    </span>
                                    <span id="realtimeIndicator" class="badge badge-success ml-2" 
                                          style="font-size: 8px; padding: 2px 6px; animation: pulse 2s infinite;">Live</span>
                                </span>
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted" style="font-weight: 500;">
                                <i class="fas fa-database mr-1"></i>
                                Total Data: {{ $kursus->count() }} kursus
                            </small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(!$kursus->isEmpty())
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable dengan konfigurasi ringan
        $('.table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10,
            "dom": '<"top"fl>rt<"bottom"ip><"clear">',
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
        
        
        // Untuk tombol Ringkasan PDF (kuning/warning)
        $(document).on('click', '.btn-warning[href*="pdf"]', function(e) {
            e.preventDefault();
            const pdfUrl = $(this).attr('href');
            const kursusName = $(this).closest('tr').find('td:nth-child(2) div:first-child').text().trim();
            
            Swal.fire({
                title: 'Export Ringkasan PDF?',
                html: `Apakah Anda yakin ingin export <strong>Ringkasan PDF</strong> untuk:<br>"${kursusName}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Export Ringkasan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve) => {
                        setTimeout(() => {
                            window.open(pdfUrl, '_blank');
                            resolve();
                        }, 500);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Ringkasan PDF Sedang Diproses!',
                        text: 'Laporan ringkasan sedang dibuat...',
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });
        
        // Untuk tombol Detail PDF (merah/danger)
        $(document).on('click', '.btn-danger[href*="pdf"]', function(e) {
            e.preventDefault();
            const pdfUrl = $(this).attr('href');
            const kursusName = $(this).closest('tr').find('td:nth-child(2) div:first-child').text().trim();
            
            Swal.fire({
                title: 'Export Detail PDF?',
                html: `Apakah Anda yakin ingin export <strong>Detail PDF Lengkap</strong> untuk:<br>"${kursusName}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Export Detail',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve) => {
                        setTimeout(() => {
                            window.open(pdfUrl, '_blank');
                            resolve();
                        }, 500);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Detail PDF Sedang Diproses!',
                        text: 'Laporan detail sedang dibuat (mungkin butuh waktu lebih lama)...',
                        icon: 'info',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }
            });
        });
        
        // Fungsi untuk update timestamp realtime dengan format Indonesia
        function updateRealtimeTimestamp() {
            const now = new Date();
            
            // Array nama bulan dalam bahasa Indonesia
            const monthNames = [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
            ];
            
            // Format tanggal
            const day = now.getDate().toString().padStart(2, '0');
            const month = monthNames[now.getMonth()];
            const year = now.getFullYear();
            
            // Format waktu dengan WIB (Indonesia Barat)
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            
            // Tambahkan leading zero untuk jam
            hours = hours.toString().padStart(2, '0');
            
            const timestamp = `${day} ${month} ${year} ${hours}:${minutes}:${seconds}`;
            document.getElementById('realtimeTimestamp').textContent = timestamp;
            
            // Animasi indikator live
            const indicator = document.getElementById('realtimeIndicator');
            indicator.style.opacity = indicator.style.opacity === '0.5' ? '1' : '0.5';
        }
        
        // Update setiap detik
        const timestampInterval = setInterval(updateRealtimeTimestamp, 1000);
        
        // Inisialisasi pertama
        updateRealtimeTimestamp();
        
        // Cleanup interval saat halaman di-unload
        $(window).on('beforeunload', function() {
            clearInterval(timestampInterval);
        });
    });
</script>
@endif
@endsection

@section('styles')
<style>
    /* Animasi untuk indikator Live */
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    /* Gaya untuk tabel */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    
    .table thead th {
        border-bottom: 2px solid #e2e8f0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f7fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .table tbody td {
        border-top: 1px solid #edf2f7;
        padding: 12px 15px;
    }
    
    .table tfoot td {
        background-color: #f8fafc;
        font-weight: 600;
        padding: 10px 15px;
        border-top: 2px solid #e2e8f0;
    }
    
    /* Gaya untuk card statistik */
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Gaya untuk tombol aksi */
    .btn-group .btn {
        transition: all 0.2s ease;
    }
    
    .btn-group .btn:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    /* Gaya responsif */
    @media (max-width: 768px) {
        .table-responsive {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .stat-card {
            margin-bottom: 10px;
        }
        
        .card-footer .col-md-6 {
            text-align: center !important;
            margin-bottom: 10px;
        }
    }
    
    /* Gaya untuk badge jumlah peserta */
    .participant-count {
        display: inline-block;
        padding: 6px 15px;
        border-radius: 20px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);
        min-width: 80px;
    }
</style>
@endsection