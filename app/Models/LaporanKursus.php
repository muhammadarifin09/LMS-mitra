<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKursus extends Model
{
    protected $table = 'laporan_kursus';

    protected $fillable = [
        'kursus_id',
        'periode',
        'total_peserta',
        'peserta_selesai',
        'rata_rata_progress',
        'rata_rata_nilai',
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class);
    }
}
