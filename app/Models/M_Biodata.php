<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\M_User;
use Illuminate\Support\Facades\Hash;

class M_Biodata extends Model
{
    use HasFactory;

    protected $table = 'biodata';

    protected $primaryKey = 'id_sobat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_sobat',
        'nama',
        'username_sobat',
        'no_hp',
        'kecamatan',
        'desa',
        'alamat',
    ];

    protected static function booted()
    {
        static::created(function ($biodata) {
            // otomatis buat akun user baru
            M_User::create([
                'username' => $biodata->username_sobat,
                'password' => Hash::make($biodata->id_sobat),
                'role' => 'mitra',
            ]);
        });
    }
}
