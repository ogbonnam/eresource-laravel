<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PastPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'class_id',
        'uploaded_by',
        'title',
        'exam_type',
        'exam_year',
        'file_path',
        'file_name',
        'mime_type',
        'extracted_text',
        'status',
        'processing_error',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Subject this past paper belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Class/level this past paper is intended for.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Admin/user who uploaded the paper.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function questionSections(): HasMany
    {
        return $this->hasMany(QuestionSection::class)
            ->orderBy('sort_order');
    }

    public function markSchemes(): HasMany
    {
        return $this->hasMany(MarkScheme::class);
    }
}