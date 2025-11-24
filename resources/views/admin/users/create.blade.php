@extends('layouts.admin')

@section('title', 'Tambah User - MOCC BPS')

@section('styles')
<style>
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    .form-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3c72;
        margin: 0;
    }

    .btn-back {
        background: #6c757d;
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

    .btn-back:hover {
        background: #5a6268;
        color: white;
        transform: translateY(-2px);
    }

    .btn-save {
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

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        color: white;
    }

    .form-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #1e3c72;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
    }

    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 5px;
    }

    .password-toggle {
        cursor: pointer;
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        background: none;
        border: none;
        z-index: 5;
    }

    .password-toggle:hover {
        color: #1e3c72;
    }

    .password-input-group {
        position: relative;
    }

    /* Responsif */
    @media (max-width: 768px) {
        .form-body {
            padding: 20px;
        }

        .form-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .form-header .d-flex {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>
@endsection

@section('content')
<!-- WELCOME SECTION -->
<div class="welcome-section">
    <h1 class="welcome-title">Tambah User Baru</h1>
    <p class="welcome-subtitle">
        Buat akun user baru dengan mengisi form di bawah. Pilih biodata yang tersedia atau buat user tanpa biodata.
    </p>
</div>

<!-- FORM SECTION -->
<div class="form-container">
    <div class="form-header">
        <h2 class="form-title">Form Tambah User</h2>
        <a href="{{ route('admin.users.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>
    
    <div class="form-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

                    <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="biodata_id" class="form-label">Pilih Biodata</label>
                                    <select name="biodata_id" id="biodata_id" class="form-control" required>
                                        <option value="">-- Pilih Biodata --</option>
                                        @foreach($availableBiodata as $biodata)
                                            <option value="{{ $biodata->id_sobat }}" {{ old('biodata_id') == $biodata->id_sobat ? 'selected' : '' }}>
                                                {{ $biodata->id_sobat }} - {{ $biodata->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Pilih biodata yang sudah terdaftar untuk dikaitkan dengan user ini</div>
                                    @error('user_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama" class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="{{ old('nama') }}" readonly>
                                    <div class="form-text">Nama akan otomatis terisi dari biodata yang dipilih</div>
                                    @error('nama')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username" class="form-label">Username (Email) *</label>
                                    <input type="email" class="form-control" id="username" name="username" 
                                           value="{{ old('username') }}" required>
                                    @error('username')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role" class="form-label">Role *</label>
                                    <select class="form-control" id="role" name="role" required>
                                        <option value="">Pilih Role</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="mitra" {{ old('role') == 'mitra' ? 'selected' : '' }}>Mitra</option>
                                        <option value="instruktur" {{ old('role') == 'instruktur' ? 'selected' : '' }}>Instruktur</option>
                                        <option value="moderator" {{ old('role') == 'moderator' ? 'selected' : '' }}>Moderator</option>
                                    </select>
                                    @error('role')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">Password *</label>
                                    <div class="password-input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password *</label>
                                    <div class="password-input-group">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i>
                                Simpan User
                            </button>
                        </div>
                    </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Password Toggle Function
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const toggleButton = passwordField.nextElementSibling;
        const toggleIcon = toggleButton.querySelector('i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fill nama when biodata is selected
        const biodataSelect = document.getElementById('biodata_id');
        const namaInput = document.getElementById('nama');
        
        // Get biodata data from server
        const biodataOptions = @json($availableBiodata->keyBy('id_sobat')->toArray());
        
        biodataSelect.addEventListener('change', function() {
            const selectedBiodataId = this.value;
            if (selectedBiodataId && biodataOptions[selectedBiodataId]) {
                namaInput.value = biodataOptions[selectedBiodataId].nama_lengkap;
            } else {
                namaInput.value = '';
            }
        });
        
        // Trigger change on page load if there's a selected value
        if (biodataSelect.value) {
            biodataSelect.dispatchEvent(new Event('change'));
        }
        
        // Form validation
        const form = document.getElementById('createUserForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        // Check if there's a success message and redirect after 2 seconds
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                window.location.href = '{{ route("admin.users.index") }}';
            }, 2000);
        }
        
        // Form validation
        form.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error Validasi',
                    text: 'Password dan Konfirmasi Password tidak cocok!',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
                confirmPassword.focus();
            }
        });

        // SweetAlert for form submission
        form.addEventListener('submit', function(e) {
            const biodataId = document.getElementById('biodata_id').value;
            const username = document.getElementById('username').value;
            const role = document.getElementById('role').value;
            
            if (!biodataId || !username || !role) {
                e.preventDefault();
                Swal.fire({
                    title: 'Form Tidak Lengkap',
                    text: 'Harap lengkapi semua field yang wajib diisi!',
                    icon: 'warning',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>
@endsection