<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'issued_at',
        'id_kredensial' // tambahkan ini
    ];

    protected $casts = [
        'issued_at' => 'datetime'
    ];

    /**
     * Boot method untuk model
     */
    protected static function boot()
    {
        parent::boot();

        // Generate id_kredensial saat membuat sertifikat
        static::creating(function ($certificate) {
            $certificate->id_kredensial = self::generateIdKredensial();
        });
    }

    /**
     * Generate unique id_kredensial (8 karakter alfanumerik)
     */
    public static function generateIdKredensial()
    {
        do {
            // Generate 8 karakter random (huruf dan angka)
            $id_kredensial = Str::upper(Str::random(8));
            
            // Pastikan hanya mengandung huruf dan angka
            if (!preg_match('/^[A-Z0-9]+$/', $id_kredensial)) {
                continue;
            }
            
            // Cek apakah sudah ada di database
        } while (self::where('id_kredensial', $id_kredensial)->exists());
        
        return $id_kredensial;
    }

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