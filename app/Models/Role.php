<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Role constants
     */
    public const ADMIN = 'admin';
    public const TEACHER = 'teacher';
    public const STUDENT = 'student';

    /**
     * Get users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role is admin
     */
    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }

    /**
     * Check if role is teacher
     */
    public function isTeacher(): bool
    {
        return $this->name === self::TEACHER;
    }

    /**
     * Check if role is student
     */
    public function isStudent(): bool
    {
        return $this->name === self::STUDENT;
    }
}
