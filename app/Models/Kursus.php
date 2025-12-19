<?php
// app/Models/Kursus.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// HAPUS LINE INI: use Illuminate\Database\Eloquent\SoftDeletes;

class Kursus extends Model
{
    use HasFactory; // HAPUS: , SoftDeletes
    
    protected $table = 'kursus';
    
    protected $fillable = [
        'judul_kursus',
        'deskripsi_kursus',
        'penerbit',
        'tingkat_kesulitan',
        'gambar_kursus',
        'durasi_jam',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'output_pelatihan',
        'persyaratan',
        'fasilitas',
        'kuota_peserta',
        'peserta_terdaftar'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'durasi_jam' => 'integer',
        'kuota_peserta' => 'integer',
        'peserta_terdaftar' => 'integer'
    ];

    // HAPUS SEMUA METHOD YANG BERHUBUNGAN DENGAN SOFT DELETES
    // (scope, withTrashed, dll tetap bisa digunakan jika perlu)

    // Scope untuk status aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Scope untuk kursus tersedia (aktif dan belum penuh)
    public function scopeTersedia($query)
    {
        return $query->where('status', 'aktif')
                    ->where(function($q) {
                        $q->whereNull('kuota_peserta')
                          ->orWhereRaw('peserta_terdaftar < kuota_peserta');
                    });
    }

    // Cek apakah kursus masih tersedia
    public function getTersediaAttribute()
    {
        return $this->status === 'aktif' && 
               ($this->kuota_peserta === null || $this->peserta_terdaftar < $this->kuota_peserta);
    }

    // ✅ TAMBAHKAN RELASI INI
    public function materials()
    {
        return $this->hasMany(Materials::class, 'course_id'); // ← Materials (plural)
    }
    
   // Tambahkan di dalam class Kursus
   
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'kursus_id');
    }

    // Scope untuk kursus yang di-enroll oleh user tertentu
    public function scopeEnrolledBy($query, $user_id)
    {
        return $query->whereHas('enrollments', function($q) use ($user_id) {
            $q->where('user_id', $user_id);
        });
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

}