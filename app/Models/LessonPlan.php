<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonPlan extends Model
{
    protected $fillable = [
        'teacher_id',
        'faculty_id',
        'class_id',
        'subject_id',
        'week',
        'lesson_date',
        'topic',
        'objectives',
        'activities',
        'assessment',
        'file_path',
        'status',
        'teacher_comment',
        'vetter_comment',
        'vetted_by',
        'vetted_at',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'lesson_date' => 'date',
        'vetted_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function vetter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vetted_by');
    }
}