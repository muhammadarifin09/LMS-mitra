<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';

    protected $fillable = [
        'user_id',
        'kursus_id',
        'status',
        'progress_percentage',
        'completed_activities',
        'total_activities',
        'enrolled_at',
        'completed_at'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(M_User::class, 'user_id');
    }

    // Relasi ke Kursus
    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    // Scope untuk kursus yang sedang diikuti
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Update progress
    public function updateProgress($completed, $total)
    {
        $this->update([
            'completed_activities' => $completed,
            'total_activities' => $total,
            'progress_percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
            'status' => ($completed == $total && $total > 0) ? 'completed' : 'in_progress'
        ]);
    }
}