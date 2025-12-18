<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kursus: {{ $kursus->judul_kursus }}</title>
    <style>
        /* ===== RESET & DASAR ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            background-color: #ffffff;
        }
        
        /* ===== HEADER ===== */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c3e50;
            position: relative;
        }
        
        .header h1 {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .header .subtitle {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 8px;
        }
        
        .header-info {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 9px;
            color: #95a5a6;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            font-size: 9px;
            font-weight: 600;
            margin-top: 5px;
        }
        
        /* ===== INFO BOX ===== */
        .info-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .info-box h3 {
            font-size: 12px;
            color: #2c3e50;
            margin-bottom: 12px;
            padding-bottom: 5px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
        }
        
        .info-box h3 i {
            margin-right: 8px;
            color: #667eea;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 3px;
            font-size: 9px;
            display: flex;
            align-items: center;
        }
        
        .info-label i {
            margin-right: 5px;
            font-size: 10px;
            color: #6c757d;
        }
        
        .info-value {
            color: #212529;
            font-size: 10px;
            padding-left: 15px;
        }
        
        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            font-size: 20px;
            margin-bottom: 8px;
            opacity: 0.8;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 3px;
            color: #2c3e50;
        }
        
        .stat-label {
            font-size: 9px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Warna statistik */
        .stat-1 { border-top: 4px solid #17a2b8; }
        .stat-2 { border-top: 4px solid #28a745; }
        .stat-3 { border-top: 4px solid #ffc107; }
        .stat-4 { border-top: 4px solid #dc3545; }
        
        /* ===== TABEL ===== */
        .table-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 12px;
            color: #2c3e50;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 8px;
            color: #667eea;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
        }
        
        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        th {
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            border: none;
        }
        
        td {
            padding: 7px 6px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #f1f3f4;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* ===== PROGRESS BAR ===== */
        .progress-container {
            height: 14px;
            background-color: #e9ecef;
            border-radius: 7px;
            overflow: hidden;
            position: relative;
            margin: 2px 0;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 7px;
            transition: width 0.3s ease;
        }
        
        .progress-text {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: 600;
            color: white;
            text-shadow: 0 0 1px rgba(0,0,0,0.5);
        }
        
        /* Warna progress */
        .progress-0-49 { background-color: #dc3545; }
        .progress-50-79 { background-color: #ffc107; }
        .progress-80-100 { background-color: #28a745; }
        
        /* ===== BADGE STYLE ===== */
        .badge-nilai {
            display: inline-block;
            padding: 3px 8px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 10px;
            font-weight: 600;
            font-size: 9px;
            min-width: 45px;
            text-align: center;
        }
        
        .badge-type {
            display: inline-block;
            padding: 2px 6px;
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            font-size: 8px;
            text-transform: uppercase;
        }
        
        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 3px;
            font-size: 8px;
            color: #155724;
        }
        
        /* ===== SUMMARY BOX ===== */
        .summary-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            margin-top: 15px;
            font-size: 9px;
            color: #6c757d;
        }
        
        .summary-item {
            display: inline-block;
            margin-right: 15px;
        }
        
        .summary-item i {
            margin-right: 5px;
        }
        
        /* ===== FOOTER ===== */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 8px;
            color: #6c757d;
        }
        
        .footer-info {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
        }
        
        /* ===== UTILITY ===== */
        .mb-3 { margin-bottom: 15px; }
        .mt-3 { margin-top: 15px; }
        .font-weight-bold { font-weight: 700; }
        
        /* ===== PRINT OPTIMIZATION ===== */
        @media print {
            body {
                font-size: 9pt;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
            }
            
            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
            }
        }
        
        /* ===== WATERMARK ===== */
        .watermark {
            position: fixed;
            bottom: 30px;
            right: 20px;
            opacity: 0.05;
            font-size: 60px;
            color: #667eea;
            transform: rotate(-45deg);
            pointer-events: none;
            z-index: -1;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">LMS REPORT</div>
    
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN DETAIL KURSUS</h1>
        <div class="subtitle">Sistem Manajemen Pembelajaran - LMS</div>
        <div class="badge">Laporan Lengkap</div>
        
        <div class="header-info">
            <div>
                <strong>ID:</strong> KRS-{{ $kursus->id }} | 
                <strong>Tanggal:</strong> {{ now()->format('d/m/Y H:i') }}
            </div>
            <div>
                <strong>Halaman:</strong> 1/1
            </div>
        </div>
    </div>
    
    <!-- Informasi Kursus -->
    <div class="info-box">
        <h3><i class="fas fa-info-circle"></i> INFORMASI KURSUS</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label"><i class="fas fa-book"></i> Judul Kursus</div>
                <div class="info-value">{{ $kursus->judul_kursus }}</div>
            </div>
            <div class="info-item">
                <div class="info-label"><i class="fas fa-layer-group"></i> Total Materi</div>
                <div class="info-value">{{ $kursus->materials->count() }} Materi</div>
            </div>
            <div class="info-item">
                <div class="info-label"><i class="fas fa-users"></i> Total Peserta</div>
                <div class="info-value">{{ $kursus->enrollments->count() }} Peserta</div>
            </div>
            <div class="info-item">
                <div class="info-label"><i class="fas fa-calendar"></i> Tanggal Dibuat</div>
                <div class="info-value">{{ $kursus->created_at->format('d M Y H:i') }}</div>
            </div>
        </div>
        @if($kursus->deskripsi_singkat)
        <div class="info-item mt-3">
            <div class="info-label"><i class="fas fa-align-left"></i> Deskripsi</div>
            <div class="info-value">{{ $kursus->deskripsi_singkat }}</div>
        </div>
        @endif
    </div>
    
    <!-- Statistik Utama -->
    <div class="stats-grid">
        <div class="stat-card stat-1">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-value">{{ number_format($totalProgress, 1) }}%</div>
            <div class="stat-label">Rata-rata Progress</div>
        </div>
        <div class="stat-card stat-2">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div class="stat-value">{{ number_format($totalNilai, 1) }}</div>
            <div class="stat-label">Rata-rata Nilai</div>
        </div>
        <div class="stat-card stat-3">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value">{{ $pesertaSelesai }}</div>
            <div class="stat-label">Peserta Selesai</div>
        </div>
        <div class="stat-card stat-4">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-value">{{ count($pesertaData) - $pesertaSelesai }}</div>
            <div class="stat-label">Belum Selesai</div>
        </div>
    </div>
    
    <!-- Daftar Peserta -->
    <div class="table-section">
        <h3 class="section-title"><i class="fas fa-users"></i> DAFTAR PESERTA</h3>
        
        @if($kursus->enrollments->isEmpty())
        <div class="summary-box">
            <i class="fas fa-info-circle"></i> Belum ada peserta yang mendaftar pada kursus ini.
        </div>
        @else
        <table>
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="23%">Nama Peserta</th>
                    <th width="18%">Kontak</th>
                    <th width="12%">Tanggal Daftar</th>
                    <th width="28%">Progress Belajar</th>
                    <th width="10%">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pesertaData as $data)
                @php
                    $progressPercentage = $data['progress_percentage'];
                    $completedMaterials = $data['completed_materials'];
                    $totalMaterials = $data['total_materials'];
                    
                    // Tentukan warna progress
                    $progressClass = 'progress-0-49';
                    if ($progressPercentage >= 80) {
                        $progressClass = 'progress-80-100';
                    } elseif ($progressPercentage >= 50) {
                        $progressClass = 'progress-50-79';
                    }
                    
                    // Progress width
                    $progressWidth = max($progressPercentage, 3);
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $data['user']->nama ?? ($data['user']->name ?? 'N/A') }}</strong></td>
                    <td class="text-muted">{{ $data['user']->email ?? ($data['user']->username ?? 'N/A') }}</td>
                    <td class="text-center">{{ $data['enrollment']->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="progress-container">
                            <div class="progress-bar {{ $progressClass }}"
                                @style([
                                    'width' => $progressWidth . '%',
                                ])>
                            </div>
                            <div class="progress-text">{{ $progressPercentage }}% ({{ $completedMaterials }}/{{ $totalMaterials }})</div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge-nilai">{{ number_format($data['nilai'], 1) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Ringkasan Peserta -->
        @php
            $highProgress = collect($pesertaData)->where('progress_percentage', '>=', 80)->count();
            $mediumProgress = collect($pesertaData)->whereBetween('progress_percentage', [50, 79])->count();
            $lowProgress = collect($pesertaData)->where('progress_percentage', '<', 50)->count();
        @endphp
        
        <div class="summary-box">
            <div class="summary-item">
                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                Selesai: {{ $pesertaSelesai }}
            </div>
            <div class="summary-item">
                <i class="fas fa-chart-line" style="color: #28a745;"></i>
                ≥80%: {{ $highProgress }}
            </div>
            <div class="summary-item">
                <i class="fas fa-chart-line" style="color: #ffc107;"></i>
                50-79%: {{ $mediumProgress }}
            </div>
            <div class="summary-item">
                <i class="fas fa-chart-line" style="color: #dc3545;"></i>
                &lt;50%: {{ $lowProgress }}
            </div>
        </div>
        @endif
    </div>
    
    <!-- Daftar Materi -->
    @if($kursus->materials->isNotEmpty())
    <div class="table-section">
        <h3 class="section-title"><i class="fas fa-book"></i> DAFTAR MATERI</h3>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="55%">Judul Materi</th>
                    <th width="20%">Jenis</th>
                    <th width="20%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kursus->materials as $material)
                @php
                    $materialType = $material->material_type ?? $material->tipe_material ?? 'unknown';
                    
                    // Normalisasi tipe
                    $typeMap = [
                        'video' => 'Video',
                        'dokumen' => 'Dokumen', 'document' => 'Dokumen',
                        'kuis' => 'Kuis', 'quiz' => 'Kuis',
                        'theory' => 'Teori', 'teori' => 'Teori',
                        'pre_test' => 'Pre Test',
                        'post_test' => 'Post Test'
                    ];
                    
                    $typeDisplay = $typeMap[strtolower($materialType)] ?? ucfirst($materialType);
                    
                    // Status
                    $isActive = $material->is_active ?? true;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $material->title ?? $material->judul_material ?? 'N/A' }}</td>
                    <td><span class="badge-type">{{ $typeDisplay }}</span></td>
                    <td>
                        @if($isActive)
                        <span class="badge-status">Aktif</span>
                        @else
                        <span style="display: inline-block; padding: 2px 6px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 3px; font-size: 8px; color: #721c24;">Nonaktif</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Ringkasan Materi -->
        @php
            $typeCounts = $kursus->materials->groupBy(function($material) use ($typeMap) {
                $type = strtolower($material->material_type ?? $material->tipe_material ?? 'other');
                return $typeMap[$type] ?? 'Lainnya';
            })->map->count();
        @endphp
        
        <div class="summary-box">
            <strong>Distribusi Materi:</strong>
            @foreach($typeCounts as $type => $count)
            <span class="summary-item">{{ $type }}: {{ $count }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <div>Laporan ini dibuat otomatis oleh Sistem LMS</div>
        <div class="footer-info">
            <div>Dokumen ID: LAP-{{ $kursus->id }}-{{ date('YmdHis') }}</div>
            <div>© {{ date('Y') }} - Hak Cipta Dilindungi</div>
        </div>
    </div>
</body>
</html>