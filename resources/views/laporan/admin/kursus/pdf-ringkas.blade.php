<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Kursus: {{ $kursus->judul_kursus }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            background-color: #f59e0b;
            color: #fff;
            border-radius: 12px;
            font-size: 9px;
        }

        .info-card {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .stats-mini {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }

        .stat-mini {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
        }

        .stat-mini-value {
            font-size: 14px;
            font-weight: bold;
        }

        .progress-item {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
        }

        .progress-bar-mini {
            flex-grow: 1;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            margin: 0 8px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
        }

        .quick-summary {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px;
            margin-top: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            color: #6c757d;
            margin-top: 20px;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
        }
    </style>
</head>

<body>

{{-- ================= HEADER ================= --}}
<div class="header">
    <h1>RINGKASAN KURSUS</h1>
    <div>{{ $kursus->judul_kursus }}</div>
    <div class="badge">Dokumen Ringkasan</div>
    <div style="font-size:8px;margin-top:5px;">
        Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

{{-- ================= INFO ================= --}}
<div class="info-card">
    <div class="info-row">
        <span>ID Kursus</span>
        <span>KRS-{{ $kursus->id }}</span>
    </div>
    <div class="info-row">
        <span>Total Materi</span>
        <span>{{ $totalMateri }}</span>
    </div>
    <div class="info-row">
        <span>Total Peserta</span>
        <span>{{ $totalPeserta }}</span>
    </div>
    <div class="info-row">
        <span>Tanggal Dibuat</span>
        <span>{{ $kursus->created_at->format('d/m/Y') }}</span>
    </div>
</div>

{{-- ================= STATS ================= --}}
<div class="stats-mini">
    <div class="stat-mini">
        <div class="stat-mini-value">{{ number_format($avgProgress, 1) }}%</div>
        <div>Rata-rata Progress</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-value">{{ $pesertaSelesai }}</div>
        <div>Selesai</div>
    </div>
</div>

{{-- ================= PROGRESS DISTRIBUSI ================= --}}
@php
    $categories = [
        ['min' => 80, 'max' => 100, 'label' => 'Tinggi (≥80%)', 'color' => '#28a745'],
        ['min' => 50, 'max' => 79,  'label' => 'Sedang (50–79%)', 'color' => '#ffc107'],
        ['min' => 0,  'max' => 49,  'label' => 'Rendah (<50%)',  'color' => '#dc3545'],
    ];
@endphp

@foreach ($categories as $cat)
    @php
        $count = collect($pesertaData)->filter(function ($d) use ($cat) {
            return $d['progress_percentage'] >= $cat['min']
                && $d['progress_percentage'] <= $cat['max'];
        })->count();

        $percentage = $totalPeserta > 0 ? round(($count / $totalPeserta) * 100) : 0;
    @endphp

    <div class="progress-item">
        <span style="min-width:80px">{{ $cat['label'] }}</span>

        <div class="progress-bar-mini">
            <div class="progress-fill"
                @style([
                    'width' => $percentage . '%',
                    'background-color' => $cat['color'],
                ])>
            </div>
        </div>

        <span style="min-width:40px;text-align:right">
            {{ $count }} ({{ $percentage }}%)
        </span>
    </div>
@endforeach

{{-- ================= QUICK SUMMARY ================= --}}
@php
    if ($avgProgress >= 70) {
        $statusText = 'Baik';
        $statusColor = '#28a745';
    } elseif ($avgProgress >= 40) {
        $statusText = 'Sedang';
        $statusColor = '#ffc107';
    } else {
        $statusText = 'Perlu Evaluasi';
        $statusColor = '#dc3545';
    }
@endphp

<div class="quick-summary">
    <div class="summary-row">
        <span>Status Kursus</span>
        <span @style(['font-weight' => '600', 'color' => $statusColor])>
            {{ $statusText }}
        </span>
    </div>

    <div class="summary-row">
        <span>Rata-rata Nilai</span>
        <span>{{ number_format($totalNilai, 1) }}</span>
    </div>

    @if($kursus->deskripsi_singkat)
        <div style="margin-top:8px;font-size:9px;">
            <strong>Deskripsi:</strong><br>
            {{ \Illuminate\Support\Str::limit($kursus->deskripsi_singkat, 120) }}
        </div>
    @endif
</div>

{{-- ================= FOOTER ================= --}}
<div class="footer">
    <div>Dokumen Ringkasan Kursus</div>
    <div>ID: RNG-{{ $kursus->id }}-{{ date('YmdHis') }}</div>
</div>

</body>
</html>
