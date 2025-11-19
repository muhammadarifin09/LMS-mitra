<!-- Notifikasi Dropdown -->
<div class="nav-icon me-4 notification-container" id="notificationContainer">
    <i class="fas fa-bell"></i>
    <span class="notification-badge">{{ $unreadCount ?? 3 }}</span>
    
    <!-- Dropdown Notifikasi -->
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h6>Notifikasi</h6>
            <span class="notification-count">{{ $unreadCount ?? 3 }} Pesan Baru</span>
        </div>
        
        <div class="notification-list">
            <!-- Notifikasi 1 -->
            <div class="notification-item unread">
                <div class="notification-icon">
                    <i class="fas fa-user-plus text-primary"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">Pendaftaran Baru</div>
                    <div class="notification-message">Budi Santoso mendaftar kursus Statistik Dasar</div>
                    <div class="notification-time">2 menit lalu</div>
                </div>
            </div>
            
            <!-- Notifikasi 2 -->
            <div class="notification-item unread">
                <div class="notification-icon">
                    <i class="fas fa-comment-dots text-success"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">Pesan Baru</div>
                    <div class="notification-message">Anda memiliki 5 pesan belum dibaca</div>
                    <div class="notification-time">1 jam lalu</div>
                </div>
            </div>
            
            <!-- Notifikasi 3 -->
            <div class="notification-item">
                <div class="notification-icon">
                    <i class="fas fa-tasks text-warning"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">Tugas Baru</div>
                    <div class="notification-message">Tugas Analisis Data perlu diperiksa</div>
                    <div class="notification-time">3 jam lalu</div>
                </div>
            </div>
        </div>
        
        <div class="notification-footer">
            <a href="{{ route('admin.notifications.index') }}" class="view-all-notifications">Lihat Semua Notifikasi</a>
        </div>
    </div>
</div>

<style>
    /* Notification Styles */
    .notification-container {
        position: relative;
        cursor: pointer;
    }

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
        font-weight: 600;
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
        max-width: 90vw;
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
    }

    .notification-header h6 {
        margin: 0;
        font-weight: 700;
        color: #1e3c72;
    }

    .notification-count {
        font-size: 0.8rem;
        color: #e74c3c;
        font-weight: 600;
    }

    .notification-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        align-items: flex-start;
        padding: 15px 20px;
        border-bottom: 1px solid #f8f9fa;
        transition: background-color 0.2s ease;
        position: relative;
    }

    .notification-item:hover {
        background: rgba(30, 60, 114, 0.05);
    }

    .notification-item.unread {
        background: rgba(30, 60, 114, 0.03);
        border-left: 3px solid #1e3c72;
    }

    .notification-item.unread::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        background: #e74c3c;
        border-radius: 50%;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(30, 60, 114, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
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
        line-height: 1.4;
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #adb5bd;
    }

    .notification-footer {
        padding: 15px 20px;
        text-align: center;
        border-top: 1px solid #e9ecef;
    }

    .view-all-notifications {
        color: #1e3c72;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: color 0.2s ease;
    }

    .view-all-notifications:hover {
        color: #2a5298;
        text-decoration: underline;
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
</style>

<script>
    // Notification Dropdown Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const notificationContainer = document.getElementById('notificationContainer');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const dropdownBackdrop = document.getElementById('dropdownBackdrop');

        // Toggle notification dropdown
        function toggleNotificationDropdown() {
            const isShowing = notificationDropdown.classList.contains('show');
            
            // Tutup user dropdown jika terbuka
            const userDropdown = document.getElementById('userDropdownMenu');
            if (userDropdown && userDropdown.classList.contains('show')) {
                userDropdown.classList.remove('show');
            }
            
            if (isShowing) {
                hideNotificationDropdown();
            } else {
                showNotificationDropdown();
            }
        }

        function showNotificationDropdown() {
            notificationDropdown.classList.add('show');
            if (dropdownBackdrop) {
                dropdownBackdrop.style.display = 'block';
            }
            document.addEventListener('click', handleNotificationClickOutside);
        }

        function hideNotificationDropdown() {
            notificationDropdown.classList.remove('show');
            if (dropdownBackdrop) {
                dropdownBackdrop.style.display = 'none';
            }
            document.removeEventListener('click', handleNotificationClickOutside);
        }

        function handleNotificationClickOutside(event) {
            if (!notificationContainer.contains(event.target) && !notificationDropdown.contains(event.target)) {
                hideNotificationDropdown();
            }
        }

        // Event listeners untuk notification
        notificationContainer.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleNotificationDropdown();
        });

        // Mark notification as read ketika diklik
        notificationDropdown.addEventListener('click', function(e) {
            const notificationItem = e.target.closest('.notification-item');
            if (notificationItem) {
                notificationItem.classList.remove('unread');
                updateNotificationBadge();
            }
        });

        // Close dropdowns on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && notificationDropdown.classList.contains('show')) {
                hideNotificationDropdown();
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                if (dropdownBackdrop) {
                    dropdownBackdrop.style.display = 'none';
                }
            } else if (notificationDropdown.classList.contains('show')) {
                if (dropdownBackdrop) {
                    dropdownBackdrop.style.display = 'block';
                }
            }
        });

        // Update notification badge count
        function updateNotificationBadge() {
            const unreadCount = document.querySelectorAll('.notification-item.unread').length;
            const badge = document.querySelector('.notification-badge');
            const countElement = document.querySelector('.notification-count');
            
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
            
            if (countElement) {
                countElement.textContent = unreadCount + ' Pesan Baru';
            }
        }

        // Inisialisasi badge count
        updateNotificationBadge();
    });
</script>