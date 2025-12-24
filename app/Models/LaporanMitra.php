<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanMitra extends Model
{
    protected $table = 'laporan_mitra';

    protected $fillable = [
        'user_id',
        'id_sobat',
        'periode',
        'total_kursus_diikuti',
        'kursus_selesai',
        'rata_rata_progress',
        'rata_rata_nilai',
    ];

    public function user()
    {
        return $this->belongsTo(M_User::class);
    }

    public function biodata()
    {
        return $this->hasOne(Biodata::class, 'user_id', 'user_id');
    }

    // Accessor untuk format periode yang lebih baik
    public function getPeriodeFormattedAttribute()
    {
        // Format: Maret 2024
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        [$year, $month] = explode('-', $this->periode);
        return $months[$month] . ' ' . $year;
    }

    // Accessor untuk format nilai
    public function getRataRataNilaiFormattedAttribute()
    {
        return $this->rata_rata_nilai ? number_format($this->rata_rata_nilai, 2) : '-';
    }

    // Accessor untuk format progress
    public function getRataRataProgressFormattedAttribute()
    {
        return number_format($this->rata_rata_progress, 1) . '%';
    }

    // Scope untuk filter berdasarkan periode
    public function scopePeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    // Scope untuk filter berdasarkan user
    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope untuk mendapatkan laporan terbaru
    public function scopeTerbaru($query)
    {
        return $query->orderBy('periode', 'desc')
                    ->orderBy('created_at', 'desc');
    }
}