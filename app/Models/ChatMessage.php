<?php

namespace App\Models;

use App\Enums\ChatMessageRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_session_id',
        'role',
        'content',
        'corrected_content',
        'metadata',
    ];

    protected $casts = [
        'role' => ChatMessageRole::class,
        'metadata' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(ChatCorrection::class);
    }

    public function isFromUser(): bool
    {
        return $this->role === ChatMessageRole::USER;
    }
}
