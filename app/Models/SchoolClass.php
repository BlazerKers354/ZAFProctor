<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade_level',
        'description',
        'homeroom_teacher_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get homeroom teacher
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    /**
     * Get students in this class (current)
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'class_id');
    }

    /**
     * Get all students ever enrolled in this class
     */
    public function enrolledStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_student')
            ->withPivot('academic_year')
            ->withTimestamps();
    }

    /**
     * Scope for active classes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by grade level
     */
    public function scopeGradeLevel($query, $level)
    {
        return $query->where('grade_level', $level);
    }

    /**
     * Get full name with grade
     */
    public function getFullNameAttribute(): string
    {
        return "Kelas {$this->name}";
    }
}
