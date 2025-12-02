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
        // FIELD UNTUK POSTTEST
        'soal_posttest',
        'durasi_posttest',
        'is_pretest',
        'attendance_required',
        'material_type',
        'learning_objectives', // Tambahkan ini jika diperlukan
        'duration_video' // Tambahkan ini jika diperlukan
    ];

    protected $casts = [
        'duration' => 'integer',
        'is_active' => 'boolean',
        'order' => 'integer',
        'durasi_pretest' => 'integer',
        'durasi_posttest' => 'integer',
        'is_pretest' => 'boolean',
        'soal_pretest' => 'array', // UNTUK MENYIMPAN SOAL PRETEST DALAM JSON
        'soal_posttest' => 'array', // UNTUK MENYIMPAN SOAL POSTTEST DALAM JSON
        'learning_objectives' => 'array', // Tambahkan ini jika digunakan
        'file_path' => 'array' // Tambahkan ini jika file_path disimpan sebagai array
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
        // Jika material_type disimpan sebagai string comma separated
        if (is_string($this->material_type)) {
            return in_array($type, explode(',', $this->material_type));
        }
        
        // Jika learning_objectives digunakan untuk menyimpan content types
        if ($this->learning_objectives && is_array($this->learning_objectives)) {
            return in_array($type, $this->learning_objectives);
        }
        
        return false;
    }

    public function getContentTypesAttribute()
    {
        // Prioritaskan learning_objectives jika ada
        if ($this->learning_objectives && is_array($this->learning_objectives)) {
            return $this->learning_objectives;
        }
        
        // Fallback ke material_type jika learning_objectives tidak ada
        if (is_string($this->material_type)) {
            return explode(',', $this->material_type);
        }
        
        return [];
    }

    // Helper method untuk cek apakah materi memiliki file
    public function hasFile()
    {
        return !empty($this->file_path);
    }

    // Helper method untuk cek apakah materi memiliki video
    public function hasVideo()
    {
        return !empty($this->video_url);
    }

    // Helper method untuk cek apakah materi memiliki pretest
    public function hasPretest()
    {
        return !empty($this->soal_pretest) && is_array($this->soal_pretest) && count($this->soal_pretest) > 0;
    }

    // Helper method untuk cek apakah materi memiliki posttest
    public function hasPosttest()
    {
        return !empty($this->soal_posttest) && is_array($this->soal_posttest) && count($this->soal_posttest) > 0;
    }

    // Get file paths sebagai array
    public function getFilePathsAttribute()
    {
        if (is_string($this->file_path)) {
            return json_decode($this->file_path, true) ?? [];
        }
        
        return $this->file_path ?? [];
    }
}