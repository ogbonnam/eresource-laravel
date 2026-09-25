<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'broadcast_id',
        'teacher_id',
        'first_opened_at',
        'last_opened_at',
        'read_at',
        'open_count',
        'acknowledged_at',
    ];

    protected $casts = [
        'first_opened_at' => 'datetime',
        'last_opened_at' => 'datetime',
        'read_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'open_count' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function broadcast(): BelongsTo
    {
        return $this->belongsTo(Broadcast::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}