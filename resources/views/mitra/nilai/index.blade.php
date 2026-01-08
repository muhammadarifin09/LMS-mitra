@extends('mitra.layouts.app')

@section('title', 'Nilai')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 fw-bold text-primary mb-1 nilai-title">
                <span class="emoji">📊</span>
                <span class="text">Nilai Kursus</span>
            </h1>
            <p class="text-muted">Lihat perkembangan dan hasil evaluasi kursus Anda</p>
        </div>
        
        <!-- Tombol Action Group -->

        <div class="d-flex align-items-center gap-2 action-buttons">
            <!-- Tombol Ekspor PDF -->
           <button id="exportPdfBtn" class="btn btn-danger btn-action btn-icon-text">
                <i class="fas fa-file-pdf"></i>
                <span class="icon-label">PDF</span>
            </button>


            <!-- Tombol Simpan ke Arsip -->
            <form action="{{ route('mitra.nilai.simpan') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-success btn-action btn-icon-text">
                    <i class="fas fa-save"></i>
                    <span class="icon-label">Arsip</span>
                </button>
            </form>
        </div>

    </div>

    <!-- Data mitra untuk PDF (hidden) -->
    @php
        $user = auth()->user();
        $namaMitra = $user->name ?? ($user->nama ?? 'Mitra');
        
        // Hitung statistik dari SEMUA DATA yang di-enroll user
        $allEnrollments = \App\Models\Enrollment::with('kursus.materials')
            ->where('user_id', $user->id)
            ->get();
        
        // Inisialisasi variabel statistik
        $totalKursus = $allEnrollments->count();
        $lulusCount = 0;
        $tidakLulusCount = 0;
        $belumDinilaiCount = 0;
        
        // Loop melalui semua enrollment untuk menghitung statistik
        foreach ($allEnrollments as $enroll) {
            $kursus = $enroll->kursus;
            $nilaiAkhir = app(\App\Services\NilaiService::class)->hitungNilai($user->id, $kursus);
            $status = app(\App\Services\NilaiService::class)->statusNilai($nilaiAkhir);
            
            if ($status === 'lulus') {
                $lulusCount++;
            } elseif ($status === 'tidak_lulus') {
                $tidakLulusCount++;
            } else {
                $belumDinilaiCount++;
            }
        }
        
        // Hitung statistik dari data yang ditampilkan di halaman ini (untuk info)
        $currentPageCount = $nilai->count();
    @endphp
    <div id="mitraData" 
         data-nama="{{ $namaMitra }}"
         data-total="{{ $totalKursus }}"
         data-lulus="{{ $lulusCount }}"
         data-tidak-lulus="{{ $tidakLulusCount }}"
         data-belum="{{ $belumDinilaiCount }}"
         style="display: none;">
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-3 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total Kursus
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalKursus }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-3 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Lulus
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                {{ $lulusCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-danger border-3 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                Tidak Lulus
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                {{ $tidakLulusCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-danger opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-secondary border-3 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-secondary text-uppercase mb-1">
                                Belum Dinilai
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                {{ $belumDinilaiCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-secondary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-list-check me-2 text-primary"></i> Daftar Nilai Kursus
                </h5>
                @if($totalKursus > 0)
             
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 nilai-table" id="nilaiTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kursus</th>
                            <th class="text-center">Nilai</th>
                            <th class="text-center">Status</th>
                            <!-- <th class="text-center">Aksi</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nilai as $index => $n)
                            <tr class="align-middle">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                                <i class="fas fa-graduation-cap text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 fw-bold">{{ $n['kursus']->judul_kursus }}</h6>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="text-center">
                                    @if(isset($n['nilai']) && $n['nilai'] !== null)
                                        <span class="display-6 fw-bold 
                                            @if($n['nilai'] >= 80) text-success
                                            @elseif($n['nilai'] >= 60) text-warning
                                            @else text-danger
                                            @endif">
                                            {{ $n['nilai'] }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">Belum ada nilai</span>
                                    @endif
                                </td>

                                
                                <td class="text-center">
                                    @if(isset($n['status']) && $n['status'] === 'lulus')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill py-2 px-3">
                                            <i class="fas fa-check-circle me-1"></i> Lulus
                                        </span>
                                    @elseif(isset($n['status']) && $n['status'] === 'tidak_lulus')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill py-2 px-3">
                                            <i class="fas fa-times-circle me-1"></i> Tidak Lulus
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill py-2 px-3">
                                            <i class="fas fa-clock me-1"></i> Belum selesai
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- <td class="text-center">
                                    @if(isset($n['nilai']) && $n['nilai'] !== null)
                                        <button class="btn btn-outline-primary btn-sm view-details" 
                                                data-course="{{ $n['kursus']->judul_kursus }}"
                                                data-grade="{{ $n['nilai'] }}"
                                                data-status="{{ $n['status'] ?? 'belum' }}">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>
                                    @else
                                        <span class="text-muted fst-italic">-</span>
                                    @endif
                                </td> -->
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada data nilai</h5>
                                        <p class="text-muted">Silakan ikuti kursus terlebih dahulu</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($nilai->count())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Menampilkan
                    <strong>{{ $nilai->firstItem() }}</strong>
                    –
                    <strong>{{ $nilai->lastItem() }}</strong>
                    dari
                    <strong>{{ $nilai->total() }}</strong>
                    kursus
                </div>

                <div>
                    {{ $nilai->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal for Details -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Kursus</label>
                    <h6 id="modalCourseTitle" class="fw-bold"></h6>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Nilai</label>
                    <h2 id="modalGrade" class="fw-bold"></h2>
                </div>
                <div>
                    <label class="form-label text-muted">Status</label>
                    <div id="modalStatus"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- PDF Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-pdf me-2 text-danger"></i> Konfirmasi Ekspor PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Apakah Anda yakin ingin mengekspor data nilai?</h5>
                    <p class="text-muted">
                        Dokumen PDF akan berisi data nilai kursus Anda dengan format yang rapi.
                    </p>
                </div>
                
                <div class="alert alert-info">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle me-2 mt-1"></i>
                        <div>
                            <small>
                                <strong>Informasi:</strong> PDF akan berisi:
                                <ul class="mb-0 mt-2">
                                    <li>Nama mitra</li>
                                    <li>Tanggal cetak</li>
                                    <li>Daftar nilai kursus lengkap</li>
                                    <li>Statistik lengkap ({{ $lulusCount }} lulus, {{ $tidakLulusCount }} tidak lulus, {{ $belumDinilaiCount }} belum dinilai)</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmExport">
                    <i class="fas fa-download me-1"></i> Ya, Ekspor PDF
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 12px;
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .table td, .table th {
        vertical-align: middle;
    }
    
    .progress {
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
    }
    
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    .display-6 {
        font-size: 1.8rem;
    }
    
    .border-start {
        border-left-width: 4px !important;
    }
    
    /* Tombol action group */
    .gap-2 {
        gap: 0.5rem;
    }
    
    /* Progress bar untuk statistik ringkasan */
    .progress-bar {
        transition: width 0.6s ease;
    }

        /* ===== ACTION BUTTON RESPONSIVE ===== */
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 14px;
        font-size: 0.9rem;
        border-radius: 8px;
        white-space: nowrap;
    }

    /* ===== MOBILE MODE ===== */
    @media (max-width: 576px) {
        .action-buttons {
            width: 100%;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn-action {
            padding: 8px 10px;
            font-size: 0.75rem;
        }

        /* SEMBUNYIKAN TEKS, SISAKAN IKON */
        .btn-action .btn-text {
            display: none;
        }

        /* Perkecil ikon */
        .btn-action i {
            font-size: 1rem;
            margin: 0;
        }
    }

        /* ===== JUDUL NILAI KURSUS RESPONSIVE ===== */
    .nilai-title {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;       /* ⬅️ INI KUNCI: anti turun baris */
        white-space: nowrap;     /* ⬅️ pastikan 1 baris */
    }

    /* Mobile optimization */
    @media (max-width: 576px) {
        .nilai-title {
            font-size: 1.2rem;   /* lebih kecil agar muat */
        }

        .nilai-title .emoji {
            font-size: 1.3rem;
        }
    }
    

    /* ============================= */
/* MOBILE RESPONSIVE NILAI TABLE */
/* ============================= */
/* ============================= */
/* MOBILE TABLE – TETAP TABEL */
/* ============================= */
@media (max-width: 576px) {

    .nilai-table {
        width: 100%;
        table-layout: fixed;
    }

    /* Kolom Kursus (ikon + judul) */
    .nilai-table th:nth-child(1),
    .nilai-table td:nth-child(1) {
        width: 60%;
    }

    /* Kolom Nilai */
    .nilai-table th:nth-child(2),
    .nilai-table td:nth-child(2) {
        width: 18%;
        text-align: center;
    }

    /* Kolom Status */
    .nilai-table th:nth-child(3),
    .nilai-table td:nth-child(3) {
        width: 22%;
        text-align: right;
    }

    /* Padding super rapat */
    .nilai-table td {
        padding: 8px 4px;
        vertical-align: middle;
    }

    /* Judul kursus */
    .nilai-table h6 {
        font-size: 0.8rem;
        line-height: 1.2;
        margin-bottom: 2px;
        word-break: break-word;
    }

    .nilai-table small {
        font-size: 0.65rem;
    }

    /* Nilai */
    .nilai-table .display-6 {
        font-size: 1rem;
        line-height: 1;
    }

    .nilai-table .progress {
        width: 45px;
        height: 4px;
        margin: 3px auto 0;
    }

    /* BADGE STATUS – INI YANG PALING PENTING */
    .nilai-table .badge {
        font-size: 0.6rem;
        padding: 4px 6px;
        white-space: normal;      /* ⬅️ IZINKAN WRAP */
        text-align: center;
        line-height: 1.1;
        max-width: 100%;
    }

    /* Icon kiri */
    .nilai-table .rounded-circle {
        padding: 7px !important;
    }
}

/* ============================= */
/* ICON + TEXT RESPONSIVE ACTION */
/* ============================= */

.btn-icon-text {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

/* Label teks default */
.btn-icon-text .icon-label {
    font-size: 0.7rem;
    font-weight: 600;
    line-height: 1;
}

/* ===== MOBILE ===== */
@media (max-width: 576px) {
    .btn-icon-text {
        flex-direction: column;   /* icon atas, teks bawah */
        padding: 8px 10px;
        min-width: 48px;
    }

    .btn-icon-text i {
        font-size: 1.2rem;
    }

    .btn-icon-text .icon-label {
        display: block;
        margin-top: 2px;
    }
}

/* ===== DESKTOP ===== */
@media (min-width: 577px) {
    .btn-icon-text {
        flex-direction: row;
    }

    /* .btn-icon-text .icon-label {
        display: non; /* desktop: icon only */
    /* } */ 
/* ============================= */
/* ICON + TEXT RESPONSIVE ACTION */
/* ============================= */

.btn-icon-text {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 8px;
}

/* ICON LEBIH BESAR */
.btn-icon-text i {
    font-size: 1.4rem;
}

/* TEKS */
.btn-icon-text .icon-label {
    font-size: 0.8rem;
    font-weight: 600;
    line-height: 1;
}

/* ===== MOBILE ===== */
@media (max-width: 576px) {
    .btn-icon-text {
        flex-direction: column;   /* icon atas, teks bawah */
        min-width: 56px;
        padding: 8px 10px;
    }

    .btn-icon-text i {
        font-size: 1.5rem;
    }

    .btn-icon-text .icon-label {
        margin-top: 4px;
        font-size: 0.65rem;
    }
}

/* ===== DESKTOP ===== */
@media (min-width: 577px) {
    .btn-icon-text {
        flex-direction: row;   /* icon + teks sejajar */
    }

    .btn-icon-text i {
        font-size: 1.4rem;
    }

    .btn-icon-text .icon-label {
        display: inline;       /* ✅ PASTI MUNCUL */
    }
}

  
}


</style>

<!-- SELALU LOAD JSPDF, tidak perlu kondisi -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Detail Modal
    const detailButtons = document.querySelectorAll('.view-details');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const course = this.getAttribute('data-course');
            const grade = this.getAttribute('data-grade');
            const status = this.getAttribute('data-status');
            
            document.getElementById('modalCourseTitle').textContent = course;
            document.getElementById('modalGrade').textContent = grade;
            
            let statusBadge = '';
            if (status === 'lulus') {
                statusBadge = '<span class="badge bg-success">Lulus</span>';
            } else if (status === 'tidak_lulus') {
                statusBadge = '<span class="badge bg-danger">Tidak Lulus</span>';
            } else {
                statusBadge = '<span class="badge bg-secondary">Belum Dinilai</span>';
            }
            
            document.getElementById('modalStatus').innerHTML = statusBadge;
            detailModal.show();
        });
    });
    
    // PDF Export
    const exportPdfBtn = document.getElementById('exportPdfBtn');
    const exportModal = new bootstrap.Modal(document.getElementById('exportModal'));
    const confirmExportBtn = document.getElementById('confirmExport');
    
    exportPdfBtn.addEventListener('click', function() {
        exportModal.show();
    });
    
    confirmExportBtn.addEventListener('click', async function() {
        exportModal.hide();
        
        // Tampilkan loading
        showAlert('info', 'Membuat PDF...');
        
        try {
            await exportToPDF();
        } catch (error) {
            console.error('PDF export error:', error);
            showAlert('danger', 'Gagal membuat PDF: ' + error.message);
        }
    });
    
    // Handle form submit untuk Simpan ke Arsip
    const simpanArsipForm = document.querySelector('form[action*="simpan"]');
    if (simpanArsipForm) {
        simpanArsipForm.addEventListener('submit', function(e) {
            // Tampilkan loading
            showAlert('info', 'Menyimpan ke arsip...');
            
            // Bisa juga tambahkan spinner pada tombol
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Set timeout untuk mengembalikan tombol ke normal (jika form submit terlalu cepat)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
            
            // Lanjutkan submit form
            // Notifikasi sukses akan ditampilkan setelah redirect dari controller
        });
    }
    
    // Cek jika ada session message dari controller setelah simpan ke arsip
    @if(session('success'))
        showAlert('success', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showAlert('danger', '{{ session('error') }}');
    @endif
    
   async function exportToPDF() {
    // Pastikan jsPDF sudah terload
    if (typeof window.jspdf === 'undefined') {
        throw new Error('jsPDF belum terload. Silakan refresh halaman.');
    }
    
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    
    // Get mitra data from hidden element
    const mitraData = document.getElementById('mitraData');
    const namaMitra = mitraData ? mitraData.getAttribute('data-nama') : 'Mitra';
    
    // Page dimensions
    const pageWidth = doc.internal.pageSize.width;
    const pageHeight = doc.internal.pageSize.height;
    const margin = 15;
    
    // ==================== HEADER ====================
    // Logo/Title - TAMPIL DI TENGAH
    doc.setFontSize(20);
    doc.setTextColor(41, 128, 185);
    doc.setFont(undefined, 'bold');
    
    // Hitung posisi tengah untuk judul
    const title1 = 'LAPORAN NILAI KURSUS';
    const title1Width = doc.getTextWidth(title1);
    const title1X = (pageWidth - title1Width) / 2;
    doc.text(title1, title1X, 25);
    
    doc.setFontSize(14);
    doc.setTextColor(100, 100, 100);
    doc.setFont(undefined, 'normal');
    
    const title2 = 'SISTEM PEMBELAJARAN ONLINE';
    const title2Width = doc.getTextWidth(title2);
    const title2X = (pageWidth - title2Width) / 2;
    doc.text(title2, title2X, 35);
    
    // Line separator
    doc.setDrawColor(200, 200, 200);
    doc.line(margin, 42, pageWidth - margin, 42);
    
    // ==================== MITRA INFO ====================
    const infoY = 50;
    
    doc.setFontSize(14);
    doc.setTextColor(60, 60, 60);
    doc.setFont(undefined, 'bold');
    doc.text('INFORMASI MITRA', margin, infoY);
    
    // Nama mitra
    doc.setFontSize(12);
    doc.setFont(undefined, 'normal');
    doc.text('Nama: ' + namaMitra, margin + 5, infoY + 10);
    
    // Tanggal cetak
    const currentDate = new Date().toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    doc.text('Tanggal Cetak: ' + currentDate, margin + 5, infoY + 18);
    
    // ==================== TABLE DETAIL NILAI ====================
    const tableStartY = infoY + 35;
    
    doc.setFontSize(16);
    doc.setTextColor(60, 60, 60);
    doc.setFont(undefined, 'bold');
    
    // Judul tabel di tengah
    const tableTitle = 'DAFTAR NILAI KURSUS';
    const tableTitleWidth = doc.getTextWidth(tableTitle);
    const tableTitleX = (pageWidth - tableTitleWidth) / 2;
    doc.text(tableTitle, tableTitleX, tableStartY);
    
    // Prepare table data
    const headers = [['NO', 'NAMA KURSUS', 'NILAI', 'STATUS']];
    const rows = [];
    
    // Convert PHP data for PDF
    @foreach($nilai as $index => $n)
        rows.push([
            '{{ $index + 1 }}',
            '{{ $n['kursus']->judul_kursus }}',
            @if(isset($n['nilai']) && $n['nilai'] !== null)
                '{{ $n['nilai'] }}'
            @else
                '-'
            @endif,
            @if($n['status'] === 'lulus')
                'LULUS'
            @elseif($n['status'] === 'tidak_lulus')
                'TIDAK LULUS'
            @else
                'BELUM DINILAI'
            @endif
        ]);
    @endforeach
    
    // Create table
    doc.autoTable({
        head: headers,
        body: rows,
        startY: tableStartY + 10,
        margin: { 
            left: margin, 
            right: margin
        },
        theme: 'grid',
        headStyles: { 
            fillColor: [41, 128, 185], 
            textColor: 255,
            fontSize: 10,
            fontStyle: 'bold',
            halign: 'center'
        },
        bodyStyles: { 
            fontSize: 11,
            textColor: [60, 60, 60],
            cellPadding: 5
        },
        alternateRowStyles: {
            fillColor: [248, 248, 248]
        },
        columnStyles: {
            0: { 
                cellWidth: 15, 
                halign: 'center',
                fontStyle: 'bold'
            },
            1: { 
                cellWidth: 'auto',
                halign: 'left'
            },
            2: { 
                cellWidth: 25, 
                halign: 'center'
            },
            3: { 
                cellWidth: 35, 
                halign: 'center',
                fontStyle: 'bold'
            }
        },
        styles: {
            overflow: 'linebreak',
            cellPadding: 5,
            lineColor: [200, 200, 200],
            lineWidth: 0.1
        },
        didDrawPage: function(data) {
            // Footer
            const pageCount = doc.internal.getNumberOfPages();
            
            doc.setFontSize(9);
            doc.setTextColor(120, 120, 120);
            
            // Page number - di tengah
            doc.text(
                `Halaman ${data.pageNumber} dari ${pageCount}`,
                pageWidth / 2,
                pageHeight - 10,
                { align: 'center' }
            );
            
            // Tambahkan informasi mitra di footer setiap halaman (kecuali halaman pertama)
            if (data.pageNumber > 1) {
                doc.setFontSize(8);
                doc.setTextColor(150, 150, 150);
                doc.text(
                    `Mitra: ${namaMitra} | ${currentDate}`,
                    pageWidth / 2,
                    pageHeight - 20,
                    { align: 'center' }
                );
            }
        },
        // Menambahkan border untuk tabel yang lebih rapi
        tableLineColor: [200, 200, 200],
        tableLineWidth: 0.1
    });
    
    // ==================== SAVE PDF ====================
    // Generate filename
    const cleanName = namaMitra
        .replace(/[^\w\s-]/gi, '')
        .replace(/\s+/g, '_')
        .toLowerCase();
    
    const fileName = `laporan_nilai_${cleanName}.pdf`;
    
    // Save PDF
    doc.save(fileName);
    
    // Show success notification
    showAlert('success', 'Laporan PDF berhasil diunduh!');
}
    
    function showAlert(type, message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.custom-alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `custom-alert alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: none;
            border-radius: 8px;
        `;
        
        const icons = {
            'success': 'check-circle',
            'danger': 'exclamation-circle',
            'info': 'info-circle',
            'warning': 'exclamation-triangle'
        };
        
        alertDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${icons[type] || 'info-circle'} me-2"></i>
                <div>${message}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
@endsection