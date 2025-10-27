<h2>Tambah Biodata Mitra</h2>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('biodata.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <!-- Data untuk User & Login -->
    <h3>Data Login Mitra</h3>
    
    <div>
        <label>ID Sobat:</label>
        <input type="text" name="id_sobat" placeholder="ID Sobat" value="{{ old('id_sobat') }}" required>
        <small>ID Sobat akan menjadi password login mitra</small>
    </div><br>

    <div>
        <label>Email/Username:</label>
        <input type="email" name="username_sobat" placeholder="Email" value="{{ old('username_sobat') }}" required>
        <small>Email akan menjadi username login mitra</small>
    </div><br>

    <hr>

    <!-- Data Biodata Sesuai Tabel -->
    <h3>Data Pribadi</h3>

    <div>
        <label>Nama Lengkap:</label>
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" value="{{ old('nama_lengkap') }}" required>
    </div><br>

    <div>
        <label>Tempat Lahir:</label>
        <input type="text" name="tempat_lahir" placeholder="Tempat Lahir" value="{{ old('tempat_lahir') }}" required>
    </div><br>

    <div>
        <label>Tanggal Lahir:</label>
        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
    </div><br>

    <div>
        <label>Jenis Kelamin:</label>
        <select name="jenis_kelamin" required>
            <option value="">Pilih Jenis Kelamin</option>
            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div><br>

    <div>
        <label>Alamat Lengkap:</label>
        <textarea name="alamat" placeholder="Alamat Lengkap" required>{{ old('alamat') }}</textarea>
    </div><br>

    <div>
        <label>No Telepon/HP:</label>
        <input type="text" name="no_telepon" placeholder="No Telepon" value="{{ old('no_telepon') }}" required>
    </div><br>

    <div>
        <label>Foto Profil:</label>
        <input type="file" name="foto_profil" accept="image/*">
    </div><br>

    <h3>Data Profesional</h3>

    <div>
        <label>Pekerjaan:</label>
        <input type="text" name="pekerjaan" placeholder="Pekerjaan" value="{{ old('pekerjaan') }}" required>
    </div><br>

    <div>
        <label>Instansi:</label>
        <input type="text" name="instansi" placeholder="Instansi" value="{{ old('instansi') }}" required>
    </div><br>

    <div>
        <label>Pendidikan Terakhir:</label>
        <select name="pendidikan_terakhir" required>
            <option value="">Pilih Pendidikan Terakhir</option>
            <option value="SD" {{ old('pendidikan_terakhir') == 'SD' ? 'selected' : '' }}>SD</option>
            <option value="SMP" {{ old('pendidikan_terakhir') == 'SMP' ? 'selected' : '' }}>SMP</option>
            <option value="SMA" {{ old('pendidikan_terakhir') == 'SMA' ? 'selected' : '' }}>SMA</option>
            <option value="D3" {{ old('pendidikan_terakhir') == 'D3' ? 'selected' : '' }}>D3</option>
            <option value="S1" {{ old('pendidikan_terakhir') == 'S1' ? 'selected' : '' }}>S1</option>
            <option value="S2" {{ old('pendidikan_terakhir') == 'S2' ? 'selected' : '' }}>S2</option>
            <option value="S3" {{ old('pendidikan_terakhir') == 'S3' ? 'selected' : '' }}>S3</option>
        </select>
    </div><br>

    <button type="submit">Simpan Biodata Mitra</button>
    <a href="{{ route('biodata.index') }}">Kembali</a>
</form>