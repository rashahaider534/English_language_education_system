<?php

namespace App\Models;

use App\Enums\ChatErrorType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatCorrection extends Model
{
    protected $fillable = [
        'chat_message_id',
        'error_type',
        'original_fragment',
        'corrected_fragment',
        'explanation',
    ];

    protected $casts = [
        'error_type' => ChatErrorType::class,
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }
}
