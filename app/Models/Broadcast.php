<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'title',
        'message',
        'target_type',
        'department_id',
        'google_sheet_url',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'department_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(BroadcastRecipient::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(BroadcastAttachment::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(BroadcastView::class);
    }
}