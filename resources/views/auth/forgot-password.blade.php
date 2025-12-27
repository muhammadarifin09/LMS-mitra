<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | LMS MOOC Mitra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .left-panel {
            background-color: rgb(1, 76, 187);
            color: white;
            box-shadow: 8px 0 25px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        @media (max-width: 500px) {
            /* Layout utama */
            body {
                padding: 0;
                margin: 0;
                display: block;
                overflow-x: hidden;
            }
            
            .d-flex.flex-column.flex-md-row {
                height: auto !important;
                justify-content: flex-start !important;
            }
            
            .left-panel {
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                border-radius: 0 0 20px 20px;
            }
        }
        
        /* Tambahan CSS untuk perbaikan */
        .mooc-full-text {
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .description-container {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="min-vh-100 d-flex flex-column flex-md-row align-items-stretch justify-content-center">

     <!-- Kolom Kiri -->
    <div class="col-12 col-md-6 d-flex flex-column justify-content-center align-items-center p-5 left-panel">
        <img src="{{ asset('img/logo-bps.png') }}" alt="Logo BPS Tanah Laut" class="mb-3" style="width: 120px;">
        <h1 class="fw-bold text-center fs-3 fs-md-2 mooc-full-text">Badan Pusat Statistik Tanah Laut</h1>
        <p class="mt-2 text-center opacity-75 fs-6 mooc-full-text">LMS Massive Open Online Course Mitra</p>
        <div class="description-container text-center mt-3">
            <p class="mb-0">
                Platform pembelajaran mandiri bagi Mitra Statistik untuk mempersiapkan diri sebelum pelatihan tatap muka dan penugasan di lapangan dengan sistem belajar adaptif.
            </p>
        </div>
    </div>

    <!-- Kolom Kanan -->
    <div class="col-12 col-md-6 d-flex flex-column justify-content-center bg-white p-4 p-md-5">
        <div class="container" style="max-width: 400px;">
            <h2 class="fw-semibold text-dark mb-4 text-center text-md-start fs-3">Lupa Password</h2>
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" 
                           class="form-control @error('username') is-invalid @enderror" 
                           placeholder="Masukkan username Anda" 
                           value="{{ old('username') }}" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('login.page') }}" class="text-primary small text-decoration-none">
                        ← Kembali ke Login
                    </a>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">Kirim Link Reset Password</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>