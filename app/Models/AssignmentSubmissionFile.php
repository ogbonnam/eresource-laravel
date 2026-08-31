<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmissionFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_submission_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    /**
     * Submission this file belongs to.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(
            AssignmentSubmission::class,
            'assignment_submission_id'
        );
    }
}
