<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'class_id',
        'student_id',
        'phone',
        'avatar',
        'is_active',
        'is_approved',
        'approved_at',
        'approved_by',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the role for this user
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the class for this user (student)
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Alias for class() relationship
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the user who approved this user
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get courses taught by this user (teacher)
     */
    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /**
     * Get courses enrolled by this user (student)
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_student')
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    /**
     * Get exams created by this user
     */
    public function createdExams(): HasMany
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    /**
     * Get exam attempts by this user
     */
    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Get proctoring logs for this user
     */
    public function proctoringLogs(): HasMany
    {
        return $this->hasMany(ProctoringLog::class);
    }

    /**
     * Get audit logs for this user
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role?->name === Role::ADMIN;
    }

    /**
     * Check if user is teacher
     */
    public function isTeacher(): bool
    {
        return $this->role?->name === Role::TEACHER;
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->role?->name === Role::STUDENT;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role?->name, $roles);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for approved users
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for pending approval users
     */
    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope for users by role
     */
    public function scopeByRole($query, string $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Update last login info
     */
    public function updateLastLogin(): void
    {
        $this->last_login_at = now();
        $this->last_login_ip = request()->ip();
        $this->save();
    }

    /**
     * Get avatar URL or default
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=4f46e5&color=fff';
    }

    /**
     * Check if user is approved
     */
    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    /**
     * Approve user
     */
    public function approve(): void
    {
        $this->is_approved = true;
        $this->approved_at = now();
        $this->approved_by = auth()->id();
        $this->save();
    }
}
