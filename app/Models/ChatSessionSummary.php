<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatSessionSummary extends Model
{
    protected $fillable = [
        'chat_session_id',
        'overall_feedback',
        'strengths',
        'weaknesses',
        'estimated_level',
        'xp_awarded',
    ];

    protected $casts = [
        'strengths' => 'array',
        'weaknesses' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }
}
