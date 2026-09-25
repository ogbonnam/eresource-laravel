<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'faculty_id',
    'staff_position',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Social accounts connected to this user.
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Determine whether the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Determine whether the user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Determine whether the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Determine whether the user can access a Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->isAdmin(),
            default => false,
        };
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function teachingCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'enrollments'
        )
            ->withPivot([
                'status',
                'enrolled_at',
            ])
            ->withTimestamps();
    }

    public function activeCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'enrollments'
        )
            ->wherePivot('status', 'active')
            ->withPivot([
                'status',
                'enrolled_at',
            ])
            ->withTimestamps();
    }

    /**
     * Courses taught by this user.
     */
    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'class_student',
            'student_id',
            'class_id'
        )->withTimestamps();
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function lessonPlans(): HasMany
    {
        return $this->hasMany(LessonPlan::class, 'teacher_id');
    }

    public function vettedLessonPlans(): HasMany
    {
        return $this->hasMany(LessonPlan::class, 'vetted_by');
    }

    public function sentBroadcasts()
    {
        return $this->hasMany(Broadcast::class, 'sender_id');
    }

    public function broadcastRecipients()
    {
        return $this->hasMany(BroadcastRecipient::class, 'teacher_id');
    }

    
    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}