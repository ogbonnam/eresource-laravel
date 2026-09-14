<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // public function courses(): HasMany
    // {
    //     return $this->hasMany(Course::class);
    // }

    /* 
    |-------------------------------------------------------------------------- 
    | Classes 
    |-------------------------------------------------------------------------- 
    */ 
    public function classes(): BelongsToMany 
    { 
        return $this->belongsToMany( SchoolClass::class, 'class_subject', 'subject_id', 'class_id' )->withTimestamps(); 
    } 
    /* 
    |-------------------------------------------------------------------------- 
    | Courses 
    |-------------------------------------------------------------------------- 
    */ 
    public function courses(): HasMany 
    { 
        return $this->hasMany( Course::class, 'subject_id' ); 
    }

    public function pastPapers(): HasMany
    {
        return $this->hasMany(PastPaper::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}