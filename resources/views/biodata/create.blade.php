<h2>Tambah Biodata Mitra</h2>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('biodata.store') }}" method="POST">
    @csrf
    <input name="id_sobat" placeholder="ID Sobat"><br>
    <input name="nama" placeholder="Nama"><br>
    <input name="username_sobat" placeholder="Email"><br>
    <input name="no_hp" placeholder="No HP"><br>
    <input name="kecamatan" placeholder="Kecamatan"><br>
    <input name="desa" placeholder="Desa"><br>
    <textarea name="alamat" placeholder="Alamat"></textarea><br>
    <button type="submit">Simpan</button>
</form>
