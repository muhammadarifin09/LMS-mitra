<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number',
        'enrollment_id',
        'user_id',
        'kursus_id',
        'file_path',
        'download_url',
        'issued_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(M_User::class);
    }

    // Relasi ke Kursus
    public function kursus()
    {
        return $this->belongsTo(Kursus::class);
    }

    // Relasi ke Enrollment
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    // Scope untuk sertifikat milik user tertentu
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Cek apakah sertifikat sudah memiliki file
    public function hasFile()
    {
        return !empty($this->file_path);
    }
}