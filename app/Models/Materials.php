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
        'description'
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'course_id');
    }

    public function progress()
    {
        return $this->hasMany(MaterialProgress::class, 'material_id');
    }
}