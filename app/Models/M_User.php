<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class M_User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'password',
        'role',
        'nama',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function biodata()
    {
        return $this->hasOne(Biodata::class, 'user_id');
    }

    // Tambahkan di dalam class M_User
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'user_id');
    }

    public function enrolledCourses()
    {
        return $this->hasManyThrough(Kursus::class, Enrollment::class, 'user_id', 'id', 'id', 'kursus_id');
    }

    // Cek apakah user sudah enroll ke kursus tertentu
    public function hasEnrolled($kursus_id)
    {
        return $this->enrollments()->where('kursus_id', $kursus_id)->exists();
    }
}
