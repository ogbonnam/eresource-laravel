<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Assignment;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'name',
        'code',
        'description',
        'thumbnail',
        'is_active',
        'enrollment_code',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * The class this course belongs to.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * The subject taught in this course.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /** * Assignments belonging to this course. */ 
    public function assignments(): HasMany { 
        return $this->hasMany(Assignment::class); 
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'enrollments'
        )
            ->withPivot([
                'status',
                'enrolled_at',
            ])
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            if (empty($course->enrollment_code)) {
                do {
                    $code = strtoupper(\Illuminate\Support\Str::random(8));
                } while (
                    static::where('enrollment_code', $code)->exists()
                );

                $course->enrollment_code = $code;
            }
        });
    }
}