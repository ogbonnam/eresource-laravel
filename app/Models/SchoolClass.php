<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'year_group_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function yearGroup(): BelongsTo
    {
        return $this->belongsTo(YearGroup::class, 'year_group_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'class_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'class_student',
            'class_id',
            'student_id'
        )->withTimestamps();
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subject',
            'class_id',
            'subject_id'
        )->withTimestamps();
    }

    public function pastPapers(): HasMany
    {
        return $this->hasMany(PastPaper::class, 'class_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'class_id');
    }
}
