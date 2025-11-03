@extends('layouts.admin')

@section('title', 'Manajemen User - MOCC BPS')

@section('styles')
<style>
    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }
    
    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }
    
    .table-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3c72;
        margin: 0;
    }
    
    .btn-tambah {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-tambah:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        color: white;
    }
    
    .table-responsive {
        padding: 0;
    }
    
    .table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        padding: 15px 12px;
        font-weight: 700;
        color: #1e3c72;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table tbody td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    .table tbody tr:hover {
        background-color: rgba(30, 60, 114, 0.03);
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    
    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
    }
    
    .btn-edit {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }
    
    .btn-edit:hover {
        background: #3498db;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-delete {
        background: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
    }
    
    .btn-delete:hover {
        background: #e74c3c;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-delete:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-delete:disabled:hover {
        background: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
        transform: none;
    }
    
    .badge-admin {
        background: #dc3545;
        color: white;
    }
    
    .badge-mitra {
        background: #198754;
        color: white;
    }
    
    .badge-instruktur {
        background: #0d6efd;
        color: white;
    }
    
    .badge-moderator {
        background: #fd7e14;
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #dee2e6;
    }
    
    .pagination-container {
        background: #f8f9fa;
        padding: 20px;
        border-top: 1px solid #e9ecef;
    }
    
    .page-info {
        color: #6c757d;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<!-- WELCOME SECTION -->
<div class="welcome-section">
    <h1 class="welcome-title">Manajemen User</h1>
    <p class="welcome-subtitle">
        Kelola data user sistem MOCC BPS dengan mudah. Lihat, edit, atau hapus data user sesuai kebutuhan.
    </p>
</div>

<!-- TABLE SECTION -->
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Daftar User</h2>
        <a href="{{ route('admin.users.create') }}" class="btn-tambah">
            <i class="fas fa-plus-circle"></i>
            Tambah User
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($users) && $users->count() > 0)
                    @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->nama ?? $user->name }}</td>
                        <td>{{ $user->username ?? $user->email }}</td>
                        <td>********</td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge badge-admin">Admin</span>
                            @elseif($user->role == 'mitra')
                                <span class="badge badge-mitra">Mitra</span>
                            @elseif($user->role == 'instruktur')
                                <span class="badge badge-instruktur">Instruktur</span>
                            @elseif($user->role == 'moderator')
                                <span class="badge badge-moderator">Moderator</span>
                            @else
                                <span class="badge bg-secondary">{{ $user->role }}</span>
                            @endif
                            
                            {{-- Tampilkan badge jika user memiliki biodata --}}
                            @if($user->biodata)
                                <span class="badge bg-info ms-1" title="Memiliki data biodata">
                                    <i class="fas fa-id-card"></i>
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-action btn-edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                {{-- Tombol hapus: nonaktif hanya untuk user sendiri --}}
                                @if(auth()->user()->id == $user->id)
                                    {{-- User sendiri - nonaktif --}}
                                    <button class="btn-action btn-delete" title="Tidak dapat menghapus akun sendiri" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    {{-- User lain - aktif (biodata tidak ikut terhapus) --}}
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" title="Hapus" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->nama ?? $user->name }}?\\n\\nData biodata terkait akan tetap tersimpan.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-database"></i>
                                <h4>Belum ada data user</h4>
                                <p>Silakan tambah user baru untuk memulai</p>
                                <a href="{{ route('admin.users.create') }}" class="btn-tambah mt-3">
                                    <i class="fas fa-plus-circle"></i>
                                    Tambah User Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if(isset($users) && method_exists($users, 'hasPages') && $users->hasPages())
    <div class="pagination-container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="page-info">
                @if($users->total() > 0)
                    Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} user
                @else
                    Tidak ada data user
                @endif
            </div>
            {{ $users->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert for delete confirmation
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                // Skip if button is disabled
                if (this.disabled) {
                    e.preventDefault();
                    return false;
                }
                
                e.preventDefault();
                const form = this.closest('form');
                const userName = this.closest('tr').querySelector('td:nth-child(2)').textContent;
                
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Apakah Anda yakin ingin menghapus user ${userName}?`,
                    html: `Apakah Anda yakin ingin menghapus user <strong>${userName}</strong>?<br><br>
                          <small class="text-muted">Data biodata terkait akan tetap tersimpan.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Search functionality (jika ada input search)
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            const tableRows = document.querySelectorAll('tbody tr');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endsection