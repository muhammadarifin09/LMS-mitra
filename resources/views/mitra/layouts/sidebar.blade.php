<style>
    /* Sidebar Styles (Desktop) */
    .sidebar {
        width: 300px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 30px 0;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar-section {
        margin-bottom: 30px;
    }

    .sidebar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e3c72;
        padding: 0 25px 15px 25px;
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 15px;
    }

    .sidebar-item {
        display: flex;
        align-items: center;
        padding: 12px 25px;
        color: #5a6c7d;
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .sidebar-item:hover {
        background: rgba(30, 60, 114, 0.1);
        color: #1e3c72;
        border-left-color: #1e3c72;
    }

    .sidebar-item.active {
        background: rgba(30, 60, 114, 0.15);
        color: #1e3c72;
        border-left-color: #1e3c72;
        font-weight: 600;
    }

    .sidebar-item i {
        width: 20px;
        margin-right: 12px;
        font-size: 1.1rem;
    }

    /* Mobile Bottom Navigation Bar */
    .mobile-navbar {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        padding: 2px 0 3px;
        border-top: 1px solid #e9ecef;
    }

    .mobile-nav-items {
        display: flex;
        justify-content: space-around;
        align-items: center;
        max-width: 500px;
        margin: 0 auto;
    }

    .mobile-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 10px;
        transition: all 0.3s ease;
        flex: 1;
        min-width: 0;
    }

    .mobile-nav-icon {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
        font-size: 1.2rem;
        color: #5a6c7d;
        transition: all 0.3s ease;
    }

    .mobile-nav-label {
        font-size: 0.7rem;
        color: #5a6c7d;
        font-weight: 500;
        text-align: center;
        transition: all 0.3s ease;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .mobile-nav-item.active {
        background: rgba(30, 60, 114, 0.08);
    }

    .mobile-nav-item.active .mobile-nav-icon {
        color: #1e3c72;
        transform: translateY(-2px);
    }

    .mobile-nav-item.active .mobile-nav-label {
        color: #1e3c72;
        font-weight: 600;
    }

    .mobile-nav-item:hover .mobile-nav-icon {
        color: #1e3c72;
        transform: translateY(-2px);
    }

    .mobile-nav-item:hover .mobile-nav-label {
        color: #1e3c72;
    }

    /* Logout item khusus */
    .mobile-nav-item.logout .mobile-nav-icon {
        color: #e74c3c;
    }

    .mobile-nav-item.logout.active {
        background: rgba(231, 76, 60, 0.08);
    }

    .mobile-nav-item.logout.active .mobile-nav-icon,
    .mobile-nav-item.logout:hover .mobile-nav-icon {
        color: #e74c3c;
    }

    .mobile-nav-item.logout.active .mobile-nav-label,
    .mobile-nav-item.logout:hover .mobile-nav-label {
        color: #e74c3c;
    }

    /* Responsif - Mobile Bottom Nav */
    @media (max-width: 768px) {
        .dashboard-container {
            flex-direction: column;
            padding-bottom: 4px; /* Space untuk bottom navbar */
        }
        
        .sidebar {
            width: 100%;
            order: 2;
            display: none; /* Sembunyikan sidebar di mobile */
        }
        
        .mobile-navbar {
            display: block; /* Tampilkan bottom navbar */
        }
        
        .main-content {
            margin-bottom: 20px;
        }
    }

    /* Desktop - Sembunyikan mobile navbar */
    @media (min-width: 769px) {
        .mobile-navbar {
            display: none;
        }
        
        .sidebar {
            display: block;
        }
    }

    /* Tablet Landscape */
    @media (max-width: 1024px) and (min-width: 769px) {
        .sidebar {
            width: 250px; /* Lebih kecil di tablet */
        }
        
        .sidebar-item {
            padding: 1px 2px;
        }
        
        .sidebar-title {
            padding: 0 20px 12px 20px;
            font-size: 1rem;
        }
    }
</style>

<!-- Desktop Sidebar (Visible di desktop/tablet) -->
<div class="sidebar">
    <!-- Data Section -->
    <div class="sidebar-section">
        <div class="sidebar-title">Dashboard</div>
        <a href="{{ route('profil.index') }}" class="sidebar-item {{ request()->routeIs('profil.*') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Profil</span>
        </a>
        <a href="#" class="sidebar-item">
            <i class="fas fa-chart-line"></i>
            <span>Nilai</span>
        </a>
        <a href="#" class="sidebar-item">
            <i class="fas fa-flag"></i>
            <span>Laporan</span>
        </a>
        <a href="#" class="sidebar-item text-danger" onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin keluar?')) document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</div>

<!-- Mobile Bottom Navigation Bar (Visible hanya di mobile) -->
<nav class="mobile-navbar">
    <div class="mobile-nav-items">
        <a href="{{ route('profil.index') }}" 
           class="mobile-nav-item {{ request()->routeIs('profil.*') ? 'active' : '' }}"
           title="Profil">
            <div class="mobile-nav-icon">
                <i class="fas fa-user"></i>
            </div>
            <div class="mobile-nav-label">Profil</div>
        </a>
        
        <a href="#" 
           class="mobile-nav-item"
           title="Nilai">
            <div class="mobile-nav-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="mobile-nav-label">Nilai</div>
        </a>
        
        <a href="#" 
           class="mobile-nav-item"
           title="Laporan">
            <div class="mobile-nav-icon">
                <i class="fas fa-flag"></i>
            </div>
            <div class="mobile-nav-label">Laporan</div>
        </a>
        
        <a href="#" 
           class="mobile-nav-item logout"
           onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin keluar?')) document.getElementById('logout-form-mobile').submit();"
           title="Keluar">
            <div class="mobile-nav-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="mobile-nav-label">Keluar</div>
        </a>
    </div>
</nav>

<!-- Logout form untuk mobile -->
<form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Active state untuk mobile navbar
    const mobileNavItems = document.querySelectorAll('.mobile-nav-item:not(.logout)');
    mobileNavItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (!this.href || this.href === '#') {
                e.preventDefault();
            }
            mobileNavItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Touch feedback untuk mobile
    mobileNavItems.forEach(item => {
        item.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.95)';
        });
        
        item.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Prevent accidental logout tap
    const logoutBtn = document.querySelector('.mobile-nav-item.logout');
    if (logoutBtn) {
        let logoutTimer;
        logoutBtn.addEventListener('touchstart', function(e) {
            logoutTimer = setTimeout(() => {
                if (confirm('Apakah Anda yakin ingin keluar?')) {
                    document.getElementById('logout-form-mobile').submit();
                }
            }, 500); // Delay 500ms untuk prevent accidental tap
        });
        
        logoutBtn.addEventListener('touchend', function(e) {
            clearTimeout(logoutTimer);
        });
        
        logoutBtn.addEventListener('touchmove', function(e) {
            clearTimeout(logoutTimer);
        });
    }
});
</script>