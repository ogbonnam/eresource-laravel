<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'created_by',
        'title',
        'description',
        'instructions',
        'total_marks',
        'due_at',
        'allow_late_submission',
        'late_submission_until',
        'allow_resubmission',
        'max_attempts',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'late_submission_until' => 'datetime',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'allow_late_submission' => 'boolean',
        'allow_resubmission' => 'boolean',
        'max_attempts' => 'integer',
    ];

    /**
     * Course this assignment belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Teacher who created the assignment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Student submissions.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function attachments(): HasMany 
    { 
        return $this->hasMany( AssignmentAttachment::class ); 
    }
}