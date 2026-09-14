<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarkScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'past_paper_id',
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

    public function pastPaper(): BelongsTo
    {
        return $this->belongsTo(
            PastPaper::class
        );
    }
}