<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'past_paper_id',
        'section_key',
        'title',
        'instructions',
        'stimulus',
        'sort_order',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function pastPaper(): BelongsTo
    {
        return $this->belongsTo(PastPaper::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'section_id')
            ->orderBy('id');
    }
}