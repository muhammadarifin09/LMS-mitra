<?php
// app/Models/Biodata.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biodata extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit
    protected $table = 'biodata';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_telepon',
        'foto_profil',
        'pekerjaan',
        'instansi',
        'pendidikan_terakhir'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Perbaiki foreign key jika perlu
    public function user()
    {
        return $this->belongsTo(M_User::class, 'user_id');
    }
}