<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class YearGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'access_code',
        'require_approval',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'require_approval' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'year_group_id');
    }

    protected static function booted(): void
    {
        static::creating(function (YearGroup $yearGroup) {
            if (empty($yearGroup->access_code)) {
                do {
                    $code = 'YG-' . strtoupper(Str::random(8));
                } while (
                    static::where('access_code', $code)->exists()
                );

                $yearGroup->access_code = $code;
            }
        });
    }
}
