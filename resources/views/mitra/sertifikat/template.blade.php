<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $certificate->certificate_number }}</title>

    <style>
        @page { margin: 0; size: A4 landscape; }
        body { margin: 0; padding: 0; font-family: "Arial MT Pro", Arial, sans-serif; }
        .page { width: 297mm; height: 210mm; position: relative; }

        /* ===== TITLE ===== */
        .title {
            position: absolute;
            top: 42mm;
            width: 100%;
            text-align: center;
        }

        .title h1 {
            font-size: 36px;
            margin: 0;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .certificate-number {
            margin-top: 8px;
            font-size: 14px;
        }

        /* ===== CONTENT ===== */
        .content {
            position: absolute;
            top: 65mm;
            width: 100%;
            text-align: center;
        }

        .label {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .name {
            font-size: 32px;
            font-weight: bold;
            margin: 10px auto 18px;
            max-width: 80%;
            white-space: nowrap;
        }

        .desc {
            font-size: 16px;
            margin-bottom: 22px;
        }

        .course {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 25px;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .detail {
            font-size: 15px;
            line-height: 1.6;
        }
    </style>
</head>

<body>
@php
    // Encode gambar langsung di view
    $imagePath = public_path('img/sertifikat_bg.png');
    $backgroundImage = '';
    
    if (file_exists($imagePath)) {
        $imageInfo = getimagesize($imagePath);
        $mimeType = $imageInfo['mime'] ?? 'image/png';
        $imageContent = file_get_contents($imagePath);
        $backgroundImage = 'data:' . $mimeType . ';base64,' . base64_encode($imageContent);
    }
@endphp
<div class="page">
    
    <!-- Gunakan backgroundImage yang sudah di-encode -->
    @if($backgroundImage)
    <img src="{{ $backgroundImage }}" 
         style="position: fixed; top: 0; left: 0; width: 297mm; height: 210mm; z-index: -1;">
    @else
    <div style="position: fixed; top: 0; left: 0; width: 297mm; height: 210mm; z-index: -1; background-color: #f5f5f5; border: 2px dashed #ccc;">
        <!-- Placeholder jika gambar tidak ditemukan -->
    </div>
    @endif

    <!-- Judul -->
    <div class="title">
        <h1>SERTIFIKAT PELATIHAN</h1>
        <div class="certificate-number">
            {{ $certificate->certificate_number }}/MOOC/BPS.TanahLaut/{{ $certificate->issued_at->format('Y') }}
        </div>
    </div>

    <!-- Konten -->
    <div class="content">
        <div class="label">Diberikan Kepada:</div>

        <div class="name">{{ $user->nama }}</div>

        <div class="desc">Telah Menyelesaikan Kursus</div>

        <div class="course">{{ $kursus->judul_kursus }}</div>

        <div class="detail">
            Pada tanggal
            {{ $enrollment->completed_at
                ? $enrollment->completed_at->translatedFormat('j F Y')
                : $certificate->issued_at->translatedFormat('j F Y') }}
            selama {{ $kursus->durasi_jam ?? '--' }} JP
            <br>
            <br>
            Tanah Laut, {{ $certificate->issued_at->translatedFormat('j F Y') }}
        </div>
    </div>

</div>
</body>
</html>
