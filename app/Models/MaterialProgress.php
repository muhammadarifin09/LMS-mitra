<?php

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
        'pretest_score',
        'posttest_score',
        'pretest_completed_at',
        'posttest_completed_at',
        'attempts',
        'is_completed',
        'completed_at'
    ];

    protected $casts = [
        'quiz_answers' => 'array',
        'completed_at' => 'datetime',
        'pretest_completed_at' => 'datetime',
        'posttest_completed_at' => 'datetime',
        'is_completed' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(M_User::class, 'user_id');
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

    public function markMaterialCompleted()
    {
        $this->update(['material_status' => 'completed']);
    }

    public function markVideoCompleted()
    {
        $this->update(['video_status' => 'completed']);
    }

    public function markPretestCompleted($score)
    {
        $this->update([
            'pretest_score' => $score,
            'pretest_completed_at' => now(),
            'is_completed' => true,
            'attempts' => $this->attempts + 1
        ]);
    }

    public function markPosttestCompleted($score)
    {
        $this->update([
            'posttest_score' => $score,
            'posttest_completed_at' => now(),
            'is_completed' => true,
            'attempts' => $this->attempts + 1
        ]);
    }

    public function isCompleted()
    {
        return $this->is_completed || (
            $this->attendance_status === 'completed' &&
            $this->material_status === 'completed' &&
            $this->video_status === 'completed'
        );
    }
    
    // HAPUS METODE isTestPassed karena tidak ada passing grade
}