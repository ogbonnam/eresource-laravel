<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'past_paper_id',
        'section_id',
        'subject_id',
        'class_id',

        'topic',
        'subtopic',
        'concept',

        'question_type',
        'difficulty',
        'command_word',

        'question',
        'marks',
        'answer',
        'explanation',

        'source_type',
        'source_question_id',

        'status',
        'reviewed_by',
        'reviewed_at',

        'ai_model',
        'generation_notes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /*
     * The past paper this question came from.
     */
    public function pastPaper(): BelongsTo
    {
        return $this->belongsTo(PastPaper::class);
    }

    /*
     * Subject the question belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /*
     * Class / level the question is intended for.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /*
     * Admin/user who reviewed the question.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /*
     * If this question was generated from another question,
     * this points to the original question.
     */
    public function sourceQuestion(): BelongsTo
    {
        return $this->belongsTo(
            Question::class,
            'source_question_id'
        );
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)
            ->orderBy('sort_order');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(QuestionSection::class, 'section_id');
    }
}