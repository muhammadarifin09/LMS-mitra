<?php

// app/Models/VideoQuestion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'order',
        'time_in_seconds',
        'question',
        'options',
        'correct_option',
        'points',
        'explanation',
        'required_to_continue'
    ];

    protected $casts = [
        'options' => 'array',
        'required_to_continue' => 'boolean',
    ];

    public function material()
    {
        return $this->belongsTo(Materials::class);
    }

    public function getFormattedOptionsAttribute()
    {
        return collect($this->options)->map(function ($option, $index) {
            return [
                'index' => $index,
                'text' => $option,
                'letter' => chr(65 + $index), // A, B, C, D
                'is_correct' => $index === $this->correct_option
            ];
        });
    }

    public function getTimeFormattedAttribute()
    {
        $minutes = floor($this->time_in_seconds / 60);
        $seconds = $this->time_in_seconds % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}