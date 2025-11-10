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

<style>
    /* Sidebar Styles */
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

    /* Responsif */
    @media (max-width: 768px) {
        .dashboard-container {
            flex-direction: column;
        }
        
        .sidebar {
            width: 100%;
            order: 2;
        }
    }
</style>