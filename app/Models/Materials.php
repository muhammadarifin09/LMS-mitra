<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materials extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title', 
        'order',
        'type',
        'description',
        'file_path',
        'video_url',
        'duration',
        'is_active',
        // FIELD UNTUK PRETEST
        'soal_pretest',
        'durasi_pretest',
        // FIELD UNTUK POSTTEST - TAMBAHKAN INI
        'soal_posttest',
        'durasi_posttest',
        'passing_grade',
        'is_pretest',
        'attendance_required',
        'material_type'
    ];

    protected $casts = [
        'duration' => 'integer',
        'is_active' => 'boolean',
        'order' => 'integer',
        'durasi_pretest' => 'integer',
        'durasi_posttest' => 'integer', // TAMBAHKAN INI
        'passing_grade' => 'integer',
        'is_pretest' => 'boolean',
        'soal_pretest' => 'array', // UNTUK MENYIMPAN SOAL PRETEST DALAM JSON
        'soal_posttest' => 'array' // TAMBAHKAN INI - UNTUK MENYIMPAN SOAL POSTTEST DALAM JSON
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'course_id');
    }

    public function progress()
    {
        return $this->hasMany(MaterialProgress::class, 'material_id');
    }

    // Scope untuk materi aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk urutan
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Scope untuk pretest
    public function scopePretest($query)
    {
        return $query->where('is_pretest', true);
    }

    // Scope untuk posttest
    public function scopePosttest($query)
    {
        return $query->where('type', 'post_test');
    }

    // Cek apakah material adalah pretest
    public function getIsPretestMaterialAttribute()
    {
        return $this->is_pretest || $this->type === 'pre_test';
    }

    // Cek apakah material adalah posttest
    public function getIsPosttestMaterialAttribute()
    {
        return $this->type === 'post_test';
    }

    // Get jumlah soal berdasarkan jenis test
    public function getJumlahSoalAttribute()
    {
        if ($this->type === 'pre_test') {
            return $this->soal_pretest ? count($this->soal_pretest) : 0;
        } elseif ($this->type === 'post_test') {
            return $this->soal_posttest ? count($this->soal_posttest) : 0;
        }
        return 0;
    }

    // Get durasi berdasarkan jenis test
    public function getDurasiTestAttribute()
    {
        if ($this->type === 'pre_test') {
            return $this->durasi_pretest;
        } elseif ($this->type === 'post_test') {
            return $this->durasi_posttest;
        }
        return 0;
    }

    // Get soal berdasarkan jenis test
    public function getSoalTestAttribute()
    {
        if ($this->type === 'pre_test') {
            return $this->soal_pretest;
        } elseif ($this->type === 'post_test') {
            return $this->soal_posttest;
        }
        return [];
    }

    public function hasContentType($type)
    {
        return in_array($type, explode(',', $this->material_type));
    }

    public function getContentTypesAttribute()
    {
        return explode(',', $this->material_type);
    }
}