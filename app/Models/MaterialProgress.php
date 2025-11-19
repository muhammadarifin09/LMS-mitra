<?php
// app/Models/MaterialProgress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialProgress extends Model
{
    use HasFactory;

    protected $table = 'material_progress';

    protected $fillable = [
        'user_id',
        'material_id',
        'attendance_status',
        'material_status', 
        'video_status',
        'quiz_answers',
        'completed_at'
    ];

    protected $casts = [
        'quiz_answers' => 'array',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(M_User::class, 'user_id'); // Ganti M_User jika perlu
    }

    public function material()
    {
        return $this->belongsTo(Materials::class, 'material_id');
    }

    // Helper methods
    public function markAttendanceCompleted()
    {
        $this->update(['attendance_status' => 'completed']);
    }

    public function markMaterialDownloaded()
    {
        $this->update(['material_status' => 'downloaded']);
    }

    public function markMaterialCompleted()
    {
        $this->update(['material_status' => 'completed']);
    }

    public function markVideoCompleted()
    {
        $this->update([
            'video_status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function isCompleted()
    {
        return $this->attendance_status === 'completed' &&
               $this->material_status === 'completed' &&
               $this->video_status === 'completed';
    }
}