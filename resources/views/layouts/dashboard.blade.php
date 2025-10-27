<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MOCC BPS')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
        }
        
        /* Navigation - Sticky dengan teks besar */
        .main-nav {
            background: rgba(255, 255, 255, 0.98);
            padding: 15px 60px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid #1e3c72;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }
        
        .nav-brand {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e3c72;
            text-decoration: none;
            position: relative;
            padding-right: 25px;
        }

        .nav-brand::after {
            content: "";
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 60px;
            width: 1.5px;
            background: linear-gradient(to bottom, rgba(42, 82, 152, 0.7));
            border-radius: 2px;
        }
        
        .nav-brand span {
            color: #2a5298;
        }
        
        /* Logo MOCC BPS sebagai gambar */
        .logo-image {
            height: 50px;
            width: auto;
            transition: transform 0.3s ease;
        }
        
        .logo-image:hover {
            transform: scale(1.05);
        }
        
        .nav-menu {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Style untuk ikon navigasi */
        .nav-icon {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e3c72;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(30, 60, 114, 0.1);
        }

        .nav-icon:hover {
            background: rgba(30, 60, 114, 0.2);
            color: #2a5298;
            transform: scale(1.1);
        }

        /* Badge notifikasi */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        /* Perbesar ukuran teks navigasi */
        .nav-item {
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            color: #1e3c72;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .nav-item:hover, .nav-item.active {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            transform: translateY(-2px);
        }

        /* User Profile & Avatar Styles - DIPERBAIKI */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            border-radius: 20px;
            background: rgba(30, 60, 114, 0.05);
            transition: all 0.3s ease;
            margin-left: 15px;
            text-decoration: none;
            border: 1px solid rgba(30, 60, 114, 0.1);
        }

        .user-profile:hover {
            background: rgba(30, 60, 114, 0.1);
            text-decoration: none;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid white;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .avatar-initials {
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .user-name {
            font-weight: 600;
            color: #1e3c72;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
            line-height: 1.2;
        }

        .user-status {
            font-size: 0.7rem;
            color: #5a6c7d;
            display: flex;
            align-items: center;
            gap: 4px;
            line-height: 1.2;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background: #28a745;
            border-radius: 50%;
            display: inline-block;
        }

        /* CSS untuk Fallback Image */
        .avatar-image[src=""],
        .avatar-image:not([src]) {
            opacity: 0;
        }

        .avatar-image:not([src]) + .avatar-initials,
        .avatar-image[src=""] + .avatar-initials {
            display: flex !important;
        }

        /* Responsif untuk User Profile */
        @media (max-width: 768px) {
            .main-nav {
                padding: 12px 20px;
            }
            
            .user-profile {
                margin-left: 10px;
                padding: 5px 10px;
            }
            
            .user-avatar {
                width: 35px;
                height: 35px;
            }
            
            .avatar-initials {
                font-size: 0.75rem;
            }
            
            .user-name {
                font-size: 0.8rem;
                max-width: 100px;
            }
            
            .user-status {
                font-size: 0.65rem;
            }

            .nav-item {
                padding: 8px 15px;
                font-size: 0.9rem;
            }

            .nav-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .user-info {
                display: none;
            }
            
            .user-profile {
                padding: 6px;
                background: transparent;
                border: none;
            }
            
            .user-profile:hover {
                background: rgba(30, 60, 114, 0.1);
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<nav class="main-nav">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <!-- Logo MOCC BPS sebagai gambar -->
                <a href="#" class="nav-brand">
                    <img src="{{ asset('img/Logo_E-Learning.png') }}" alt="MOCC BPS Logo" class="logo-image">
                </a>
                <div class="nav-menu ms-5">
                    <a href="/beranda" class="nav-item">Beranda</a>
                    <a href="/dashboard" class="nav-item active">Dashboard</a>
                    <a href="/kursus" class="nav-item">Kursus</a>
                    <a href="#" class="nav-item">Kursus Saya</a>
                </div>
            </div>
            
            <!-- Tambahkan bagian ikon di sini -->
            <div class="d-flex align-items-center">
                <!-- Ikon Bahasa -->
                <div class="nav-icon me-3">
                    <i class="fas fa-globe"></i>
                </div>
                
                <!-- Ikon Notifikasi -->
                <div class="nav-icon me-3">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <!-- User Profile dengan Foto - VERSI DIPERBAIKI -->
                <a href="{{ route('profil.index') }}" class="user-profile">
                    <div class="user-avatar">
                        @auth
                            @php
                                $user = Auth::user();
                                $biodata = $user->biodata ?? null;
                                $initials = strtoupper(substr($user->name, 0, 2));
                            @endphp
                            
                            @if($biodata && $biodata->foto_profil)
                                <img src="{{ asset('storage/' . $biodata->foto_profil) }}" 
                                     alt="Foto Profil" 
                                     class="avatar-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="avatar-initials" style="display: none;">{{ $initials }}</div>
                            @else
                                <div class="avatar-initials">{{ $initials }}</div>
                            @endif
                        @endauth
                    </div>
                    <div class="user-info">
                        <div class="user-name">
                            {{ Auth::user()->biodata->nama_lengkap ?? Auth::user()->name }}
                        </div>
                        <div class="user-status">
                            <span class="status-dot"></span>
                            Online
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        @yield('content')
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle image loading errors
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.avatar-image').forEach(img => {
                img.addEventListener('error', function() {
                    this.style.display = 'none';
                    const initials = this.nextElementSibling;
                    if (initials && initials.classList.contains('avatar-initials')) {
                        initials.style.display = 'flex';
                    }
                });
            });
        });
    </script>
</body>
</html>