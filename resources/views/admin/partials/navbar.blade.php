<nav class="main-nav">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.dashboard') }}" class="nav-brand">
                <img src="{{ asset('img/Logo_E-Learning.png') }}" alt="MOCC BPS Logo" class="logo-image">
            </a>
            <div class="nav-menu ms-5">
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Manajemen User</a>
                <a href="{{ route('admin.kursus.index') }}" class="nav-item {{ request()->routeIs('admin.kursus.*') ? 'active' : '' }}">Manajemen Kursus</a>
                <a href="{{ route('admin.biodata.index') }}" class="nav-item {{ request()->routeIs('admin.biodata.*') ? 'active' : '' }}">Manajemen Biodata</a>
            </div>
        </div>
        
        <div class="d-flex align-items-center">
            <div class="nav-icon me-3">
                <i class="fas fa-globe"></i>
            </div>
            
            <!-- Tambahkan setelah bagian globe icon -->
<div class="notification-wrapper position-relative">
    <div class="nav-icon me-4" id="notificationIcon">
        <i class="fas fa-bell"></i>
        <span class="notification-badge">3</span>
    </div>
    
    <!-- Dropdown Notifikasi -->
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h6>Notifikasi</h6>
            <a href="{{ route('admin.notifications.index') }}" class="view-all">Lihat Semua</a>
        </div>
        
        <div class="notification-list">
            <!-- Notifikasi 1 -->
            <div class="notification-item unread">
                <div class="notification-icon">
                    <i class="fas fa-user-plus text-primary"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">User Baru Bergabung</div>
                    <div class="notification-message">Budi Santoso telah bergabung sebagai peserta</div>
                    <div class="notification-time">5 menit yang lalu</div>
                </div>
            </div>
            
            <!-- Notifikasi 2 -->
            <div class="notification-item unread">
                <div class="notification-icon">
                    <i class="fas course-completed text-success"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">Kursus Selesai</div>
                    <div class="notification-message">Andi telah menyelesaikan kursus "Data Analysis"</div>
                    <div class="notification-time">1 jam yang lalu</div>
                </div>
            </div>
            
            <!-- Notifikasi 3 -->
            <div class="notification-item">
                <div class="notification-icon">
                    <i class="fas fa-comment text-info"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">Komentar Baru</div>
                    <div class="notification-message">Siti memberikan komentar pada diskusi</div>
                    <div class="notification-time">2 jam yang lalu</div>
                </div>
            </div>
        </div>
        
        <div class="notification-footer">
            <a href="{{ route('admin.notifications.markAllRead') }}" class="mark-all-read">
                <i class="fas fa-check-double"></i>
                Tandai Sudah Dibaca Semua
            </a>
        </div>
    </div>
</div>
            
            <!-- User Profile dengan Dropdown -->
            <div class="user-profile" id="userProfileDropdown">
                <div class="user-avatar">
                    @auth
                        @php
                            $user = Auth::user();
                            // Ambil nama dari biodata jika ada, atau dari user nama
                            $displayNama = $user->biodata->nama_lengkap ?? $user->nama;
                            
                            // Generate inisial dari nama
                            $namaParts = explode(' ', $displayNama);
                            $initials = '';
                            
                            if (count($namaParts) >= 2) {
                                // Ambil huruf pertama dari kata pertama dan kedua
                                $initials = strtoupper(substr($namaParts[0], 0, 1) . substr($namaParts[1], 0, 1));
                            } else {
                                // Jika hanya satu kata, ambil 2 huruf pertama
                                $initials = strtoupper(substr($displayNama, 0, 2));
                            }
                        @endphp
                        
                        <!-- Tampilkan inisial saja -->
                        <div class="avatar-initials">{{ $initials }}</div>
                    @endauth
                </div>
                <div class="user-info">
                    <div class="user-nama">
                        {{ Auth::user()->biodata->nama_lengkap ?? Auth::user()->nama }}
                    </div>
                    <div class="user-status">
                        <span class="status-dot"></span>
                        {{ ucfirst(Auth::user()->role) }}
                    </div>
                </div>
                <i class="fas fa-chevron-down ms-2" style="font-size: 0.8rem; color: #6c757d;"></i>
            </div>

            <!-- Dropdown Menu -->
            <div class="user-dropdown" id="userDropdownMenu">
                <div class="dropdown-header">
                    <div class="dropdown-user-info">
                        <div class="dropdown-avatar">
                            @auth
                                @php
                                    $user = Auth::user();
                                    $displayNama = $user->biodata->nama_lengkap ?? $user->nama;
                                    
                                    // Generate inisial yang sama untuk dropdown
                                    $namaParts = explode(' ', $displayNama);
                                    $initials = '';
                                    
                                    if (count($namaParts) >= 2) {
                                        $initials = strtoupper(substr($namaParts[0], 0, 1) . substr($namaParts[1], 0, 1));
                                    } else {
                                        $initials = strtoupper(substr($displayNama, 0, 2));
                                    }
                                @endphp
                                
                                <!-- Tampilkan inisial saja di dropdown -->
                                <div class="dropdown-avatar-initials">{{ $initials }}</div>
                            @endauth
                        </div>
                        <div class="dropdown-user-details">
                            <div class="dropdown-user-nama">{{ Auth::user()->biodata->nama_lengkap ?? Auth::user()->nama }}</div>
                            <div class="dropdown-user-email">{{ Auth::user()->email }}</div>
                            <div class="dropdown-user-role">{{ ucfirst(Auth::user()->role) }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="dropdown-divider"></div>
                
                <div class="dropdown-divider"></div>
                
                <a href="#" class="dropdown-item text-danger" onclick="confirmLogout()">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Backdrop untuk mobile -->
<div class="dropdown-backdrop" id="dropdownBackdrop"></div>

<style>
    /* Dropdown Menu Styles */
    .user-profile {
        position: relative;
        cursor: pointer;
    }

    .user-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 10px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border: 1px solid #e9ecef;
        min-width: 280px;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .user-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-header {
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px 12px 0 0;
    }

    .dropdown-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dropdown-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .dropdown-avatar-image {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .dropdown-avatar-initials {
        color: white;
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
    }

    .dropdown-user-details {
        flex: 1;
        min-width: 0;
    }

    .dropdown-user-nama {
        font-weight: 700;
        color: #1e3c72;
        font-size: 1rem;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropdown-user-email {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropdown-user-role {
        font-size: 0.75rem;
        color: #28a745;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dropdown-divider {
        height: 1px;
        background: #e9ecef;
        margin: 8px 0;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #5a6c7d;
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .dropdown-item:hover {
        background: rgba(30, 60, 114, 0.05);
        color: #1e3c72;
        border-left-color: #1e3c72;
    }

    .dropdown-item i {
        width: 20px;
        margin-right: 12px;
        font-size: 1rem;
    }

    .dropdown-item.text-danger {
        color: #dc3545;
    }

    .dropdown-item.text-danger:hover {
        background: rgba(220, 53, 69, 0.05);
        color: #dc3545;
        border-left-color: #dc3545;
    }

    /* Backdrop untuk mobile */
    .dropdown-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.1);
        z-index: 999;
        display: none;
    }

    /* Responsif */
    @media (max-width: 768px) {
        .user-dropdown {
            position: fixed;
            top: auto;
            bottom: 0;
            left: 0;
            right: 0;
            border-radius: 12px 12px 0 0;
            margin-top: 0;
            min-width: auto;
        }
        
        .dropdown-backdrop {
            display: block;
        }
    }

    /* Notification Styles */
.notification-wrapper {
    position: relative;
}

.notification-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 10px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    border: 1px solid #e9ecef;
    width: 380px;
    max-height: 500px;
    z-index: 1001;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}

.notification-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.notification-header {
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.notification-header h6 {
    margin: 0;
    font-weight: 700;
    color: #1e3c72;
}

.notification-header .view-all {
    font-size: 0.8rem;
    color: #1e3c72;
    text-decoration: none;
    font-weight: 600;
}

.notification-header .view-all:hover {
    text-decoration: underline;
}

.notification-list {
    max-height: 350px;
    overflow-y: auto;
}

.notification-item {
    display: flex;
    padding: 15px 20px;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.3s ease;
    cursor: pointer;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: rgba(30, 60, 114, 0.05);
    border-left: 3px solid #1e3c72;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.notification-icon i {
    font-size: 1rem;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    color: #1e3c72;
    margin-bottom: 4px;
    font-size: 0.9rem;
}

.notification-message {
    color: #6c757d;
    font-size: 0.85rem;
    margin-bottom: 4px;
    line-height: 1.4;
}

.notification-time {
    font-size: 0.75rem;
    color: #adb5bd;
}

.notification-footer {
    padding: 12px 20px;
    border-top: 1px solid #e9ecef;
    text-align: center;
}

.mark-all-read {
    color: #6c757d;
    text-decoration: none;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.mark-all-read:hover {
    color: #1e3c72;
}

/* Notification Badge */
.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

/* Responsif */
@media (max-width: 768px) {
    .notification-dropdown {
        position: fixed;
        top: auto;
        bottom: 0;
        left: 0;
        right: 0;
        border-radius: 12px 12px 0 0;
        margin-top: 0;
        width: auto;
        max-height: 70vh;
    }
}

/* Scrollbar styling */
.notification-list::-webkit-scrollbar {
    width: 6px;
}

.notification-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
    // Dropdown Menu Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const userProfile = document.getElementById('userProfileDropdown');
        const dropdownMenu = document.getElementById('userDropdownMenu');
        const dropdownBackdrop = document.getElementById('dropdownBackdrop');
        
        // Notification elements
        const notificationIcon = document.getElementById('notificationIcon');
        const notificationDropdown = document.getElementById('notificationDropdown');

        // Toggle user dropdown
        function toggleDropdown() {
            const isShowing = dropdownMenu.classList.contains('show');
            
            if (isShowing) {
                hideDropdown();
            } else {
                // Close notification dropdown if open
                if (notificationDropdown.classList.contains('show')) {
                    hideNotificationDropdown();
                }
                showDropdown();
            }
        }

        function showDropdown() {
            dropdownMenu.classList.add('show');
            dropdownBackdrop.style.display = 'block';
            document.addEventListener('click', handleClickOutside);
        }

        function hideDropdown() {
            dropdownMenu.classList.remove('show');
            dropdownBackdrop.style.display = 'none';
            document.removeEventListener('click', handleClickOutside);
        }

        function handleClickOutside(event) {
            if (!userProfile.contains(event.target) && !dropdownMenu.contains(event.target)) {
                hideDropdown();
            }
        }

        // Toggle notification dropdown
        function toggleNotificationDropdown() {
            const isShowing = notificationDropdown.classList.contains('show');
            
            if (isShowing) {
                hideNotificationDropdown();
            } else {
                // Close user dropdown if open
                if (dropdownMenu.classList.contains('show')) {
                    hideDropdown();
                }
                showNotificationDropdown();
            }
        }

        function showNotificationDropdown() {
            notificationDropdown.classList.add('show');
            dropdownBackdrop.style.display = 'block';
            document.addEventListener('click', handleNotificationClickOutside);
        }

        function hideNotificationDropdown() {
            notificationDropdown.classList.remove('show');
            dropdownBackdrop.style.display = 'none';
            document.removeEventListener('click', handleNotificationClickOutside);
        }

        function handleNotificationClickOutside(event) {
            if (!notificationIcon.contains(event.target) && !notificationDropdown.contains(event.target)) {
                hideNotificationDropdown();
            }
        }

        // Event listeners for user dropdown
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown();
        });

        // Event listeners for notification dropdown
        notificationIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleNotificationDropdown();
        });

        dropdownBackdrop.addEventListener('click', function() {
            hideDropdown();
            hideNotificationDropdown();
        });

        // Close dropdown when clicking on dropdown items (except logout)
        dropdownMenu.addEventListener('click', function(e) {
            if (e.target.closest('.dropdown-item') && !e.target.closest('.dropdown-item.text-danger')) {
                hideDropdown();
            }
        });

        // Mark as read when clicking on notification item
        notificationDropdown.addEventListener('click', function(e) {
            const notificationItem = e.target.closest('.notification-item');
            if (notificationItem) {
                notificationItem.classList.remove('unread');
                updateNotificationBadge();
            }
        });

        // Close dropdowns on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideDropdown();
                hideNotificationDropdown();
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                dropdownBackdrop.style.display = 'none';
            } else if (dropdownMenu.classList.contains('show') || notificationDropdown.classList.contains('show')) {
                dropdownBackdrop.style.display = 'block';
            }
        });

        // Update notification badge count
        function updateNotificationBadge() {
            const unreadCount = document.querySelectorAll('.notification-item.unread').length;
            const badge = document.querySelector('.notification-badge');
            
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        // Initialize notification badge
        updateNotificationBadge();
    });

    // Logout Confirmation
    function confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda yakin ingin keluar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e3c72',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>

<!-- Hidden Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>