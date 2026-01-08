<?php
// app/Models/Biodata.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biodata extends Model
{
    use HasFactory;

    protected $table = 'biodata';

    // Tentukan primary key yang bukan id default
    protected $primaryKey = 'id_sobat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_sobat', // Primary key
        'user_id',
        'username_sobat',
        'nama_lengkap',
        'kecamatan',
        'desa',
        'alamat',
        'no_telepon',
        'foto_profil',
        'posisi',
        'posisi_daftar',
        'alamat_prov',
        'alamat_kab',
        'tempat_tanggal_lahir',
        'jenis_kelamin',
        'pendidikan',
        'pekerjaan',
        'deskripsi_pekerjaan_lain',
    ];

    public function user()
    {
        return $this->belongsTo(M_User::class, 'user_id');
    }
}