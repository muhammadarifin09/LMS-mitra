<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materials extends Model
{
    protected $table = 'materials';
    
    protected $fillable = [
        'course_id',
        'title',
        'type',
        'order',
        'material_type',
        'description',
        'duration',
        'file_path',
        'video_url',
        'video_type',
        'is_active',
        'attendance_required',
        'learning_objectives',
        'soal_pretest',
        'durasi_pretest',
        'soal_posttest',
        'durasi_posttest',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'attendance_required' => 'boolean',
        'durasi_pretest' => 'integer',
        'durasi_posttest' => 'integer',
        'order' => 'integer',
        'duration' => 'integer',
        // JSON casting untuk semua field JSON
        'file_path' => 'array',
        'learning_objectives' => 'array',
        'soal_pretest' => 'array',
        'soal_posttest' => 'array',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-set order when creating
        static::creating(function ($material) {
            if (empty($material->order)) {
                $lastOrder = Materials::where('course_id', $material->course_id)->max('order');
                $material->order = $lastOrder ? $lastOrder + 1 : 1;
            }
        });

        // Reorder after deleting a material
        static::deleted(function ($material) {
            Materials::where('course_id', $material->course_id)
                ->where('order', '>', $material->order)
                ->decrement('order');
        });

        // Reorder after updating order (if changed manually)
        static::updating(function ($material) {
            if ($material->isDirty('order') && $material->order != $material->getOriginal('order')) {
                $oldOrder = $material->getOriginal('order');
                $newOrder = $material->order;
                $courseId = $material->course_id;

                if ($newOrder > $oldOrder) {
                    // Move down (order increased)
                    Materials::where('course_id', $courseId)
                        ->where('id', '!=', $material->id)
                        ->where('order', '>', $oldOrder)
                        ->where('order', '<=', $newOrder)
                        ->decrement('order');
                } else {
                    // Move up (order decreased)
                    Materials::where('course_id', $courseId)
                        ->where('id', '!=', $material->id)
                        ->where('order', '>=', $newOrder)
                        ->where('order', '<', $oldOrder)
                        ->increment('order');
                }
            }
        });
    }

    // Relationship sederhana tanpa eager loading yang berat
    public function kursus(): BelongsTo
    {
        return $this->belongsTo(Kursus::class, 'course_id')
            ->select(['id', 'judul_kursus', 'kode_kursus']);
    }

    public function videoQuestions(): HasMany
    {
        return $this->hasMany(VideoQuestion::class, 'material_id')
            ->select(['id', 'material_id', 'question', 'order', 'time_in_seconds'])
            ->orderBy('order');
    }

    // Hapus semua accessors yang complex
    // Tidak perlu get...Attribute() methods untuk appends
    
    // Helper methods sederhana
    public function getFileCount(): int
    {
        $files = $this->file_path;
        return is_array($files) ? count($files) : 0;
    }
    
    public function getPretestCount(): int
    {
        $soal = $this->soal_pretest;
        return is_array($soal) ? count($soal) : 0;
    }
    
    public function getPosttestCount(): int
    {
        $soal = $this->soal_posttest;
        return is_array($soal) ? count($soal) : 0;
    }
    
    // Simple query scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }
}