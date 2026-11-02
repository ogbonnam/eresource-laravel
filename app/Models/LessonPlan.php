<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'files',
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
        'files' => 'array',
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

    /**
     * Every file of this lesson plan: the old single file_path (if any)
     * followed by the newer multiple uploads.
     *
     * Each item: ['path' => ..., 'name' => ..., 'size' => ..., 'url' => ...]
     */
    public function getAllFilesAttribute(): array
    {
        $files = collect($this->files ?? []);

        if ($this->file_path) {
            $files->prepend([
                'path' => $this->file_path,
                'name' => basename($this->file_path),
                'size' => null,
            ]);
        }

        return $files
            ->map(fn ($file) => $file + [
                'url' => Storage::disk('public')->url($file['path']),
            ])
            ->values()
            ->all();
    }

    /**
     * Ready-made download links (used by the Filament admin page).
     */
    public function getFileLinksAttribute(): array
    {
        return collect($this->all_files)
            ->map(fn ($file) => '<a href="' . e($file['url']) . '" target="_blank" '
                . 'style="text-decoration: underline;">' . e($file['name']) . '</a>')
            ->all();
    }
}