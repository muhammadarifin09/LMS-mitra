<div class="position-sticky pt-3">
    <!-- Header Sidebar -->
    <div class="bps-brand p-3 text-center">
        <img src="{{ asset('img/logo-bps.png') }}" alt="BPS" width="50" class="mb-2">
        <h6 class="mb-0">BPS Tanah Laut</h6>
        <small>Learning Management System</small>
        <div class="mt-2">
            <small class="badge bg-light text-dark">
                <i class="fas fa-user me-1"></i>
                {{ Auth::user()->name ?? 'User' }} (Mitra)
            </small>
        </div>
    </div>

    <!-- Navigation Menu -->
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('mitra.beranda') ? 'active' : '' }}" 
               href="{{ route('mitra.beranda') }}">
                <i class="fas fa-home me-2"></i>Beranda
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-book me-2"></i>Kursus Saya
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chalkboard-teacher me-2"></i>Pelatihan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-chart-line me-2"></i>Progress Belajar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-user me-2"></i>Profil
            </a>
        </li>
        
        <!-- Logout -->
        <li class="nav-item mt-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link text-danger bg-transparent border-0 w-100 text-start">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </li>
    </ul>
</div>