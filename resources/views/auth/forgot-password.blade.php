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
        
        /* Animasi untuk alert */
        .alert-success, .alert-danger {
            animation: fadeIn 0.3s ease-in;
            border-left: 4px solid;
        }
        
        .alert-success {
            border-left-color: #28a745;
        }
        
        .alert-danger {
            border-left-color: #dc3545;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .success-icon {
            color: #28a745;
            font-size: 1.2rem;
            margin-right: 10px;
        }
    </style>
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

            <!-- Alert untuk success message -->
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle success-icon"></i>
                    <div>
                        <strong>Sukses!</strong> {{ session('status') }}
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                
                <!-- Info tambahan setelah reset link dikirim -->
                <div class="card border-success mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="fas fa-info-circle me-2"></i>Langkah Selanjutnya
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <small>Periksa inbox email Anda</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <small>Link akan kedaluwarsa dalam 60 menit</small>
                            </li>
                            <li>
                                <i class="fas fa-spam text-danger me-2"></i>
                                <small>Periksa folder spam jika tidak ditemukan</small>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Alert untuk error messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Form hanya ditampilkan jika belum ada success message -->
            @if (!session('status'))
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" 
                               class="form-control @error('username') is-invalid @enderror" 
                               placeholder="Masukkan username Anda" 
                               value="{{ old('username') }}" 
                               required
                               autofocus>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-2">
                            <small>
                                <i class="fas fa-info-circle text-primary me-1"></i>
                                Masukkan username yang terdaftar di sistem
                            </small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <a href="{{ route('login.page') }}" class="text-primary small text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Link Reset Password
                    </button>
                </form>
            @else
                <!-- Tombol untuk kembali ke login setelah success -->
                <div class="text-center mt-4">
                    <a href="{{ route('login.page') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Kembali ke Halaman Login
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script untuk auto-hide alert dan efek -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alert setelah 8 detik (lebih lama untuk success message)
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(successAlert);
                    bsAlert.close();
                }, 8000);
            }
            
            // Auto-hide error alert setelah 5 detik
            const errorAlerts = document.querySelectorAll('.alert-danger');
            errorAlerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Fokus ke input username jika ada error dan form masih ditampilkan
            const usernameInput = document.getElementById('username');
            const errorAlert = document.querySelector('.alert-danger');
            if (usernameInput && errorAlert) {
                usernameInput.focus();
                
                // Tambahkan efek shake pada form
                const form = document.querySelector('form');
                if (form) {
                    form.classList.add('animate__animated', 'animate__shakeX');
                    setTimeout(() => {
                        form.classList.remove('animate__animated', 'animate__shakeX');
                    }, 1000);
                }
            }
        });
    </script>
</body>
</html>