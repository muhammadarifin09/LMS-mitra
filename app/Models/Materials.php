<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materials extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'type',
        'order',
        'material_type',
        'description',
        'duration',
        'auto_duration',
        'file_path',
        'video_url',
        'video_type',
        'video_file',
        'allow_skip',
        'player_config',
        'has_video_questions',
        'require_video_completion',
        'question_count',
        'total_video_points',
        'is_active',
        'attendance_required',
        'learning_objectives',
        'soal_pretest',
        'durasi_pretest',
        'is_pretest',
        'soal_posttest',
        'durasi_posttest',
        'is_posttest',
        'total_views',
        'total_completions',
        'avg_completion_time',
    ];

    protected $casts = [
        'file_path' => 'array',
        'soal_pretest' => 'array',
        'soal_posttest' => 'array',
        'learning_objectives' => 'array',
        'player_config' => 'array',
        'video_file' => 'array', // Cast video_file as array for JSON
        'is_active' => 'boolean',
        'attendance_required' => 'boolean',
        'allow_skip' => 'boolean',
        'has_video_questions' => 'boolean',
        'require_video_completion' => 'boolean',
        'is_pretest' => 'boolean',
        'is_posttest' => 'boolean',
        'auto_duration' => 'boolean',
    ];

    // Relationships
    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'course_id');
    }

    public function videoQuestions()
    {
        return $this->hasMany(VideoQuestion::class);
    }

    public function userProgress()
    {
        return $this->hasMany(UserVideoProgress::class);
    }

    // Helper methods untuk Google Drive
    public function getGoogleDriveFileId()
    {
        if ($this->video_type === 'hosted' && $this->video_file) {
            $videoData = is_array($this->video_file) ? $this->video_file : json_decode($this->video_file, true);
            return $videoData['file_id'] ?? null;
        }
        return null;
    }

    public function getGoogleDriveEmbedUrl()
    {
        $fileId = $this->getGoogleDriveFileId();
        if ($fileId) {
            return "https://drive.google.com/file/d/{$fileId}/preview";
        }
        return null;
    }
}