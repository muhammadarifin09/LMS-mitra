<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'nilai';

    protected $primaryKey = 'id_nilai'; // ⬅️ WAJIB

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'kursus_id',
        'nilai',
        'status'
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class);
    }
}

