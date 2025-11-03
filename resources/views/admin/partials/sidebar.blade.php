<div class="sidebar">
    <!-- Admin Menu Section -->
    <div class="sidebar-section">
        <div class="sidebar-title">Menu Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.index*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Manajemen User</span>
        </a>
        <a href="{{ route('admin.biodata.index') }}" class="sidebar-item {{ request()->routeIs('admin.biodata.*') ? 'active' : '' }}">
            <i class="fas fa-id-card"></i>
            <span>Manajemen Biodata</span>
        </a>
        <a href="{{ route('admin.kursus.index') }}" class="sidebar-item {{ request()->routeIs('admin.kursus.*') ? 'active' : '' }}">
            <i class="fas fa-book"></i>
            <span>Manajemen Kursus</span>
        </a>
    </div>



    <!-- Account Section -->    
    <div class="sidebar-section">
        <a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
            <i class="fas fa-user-cog"></i>
            <span>Profil Admin</span>
        </a>
        <a href="#" class="sidebar-item text-danger" onclick="confirmLogout()">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </div>
</div>