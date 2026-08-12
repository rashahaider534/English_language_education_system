<?php

namespace App\Models;

use App\Enums\ChatMode;
use App\Enums\ChatSessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatSession extends Model
{
    protected $fillable = [
        'user_id',
        'topic_id',
        'mode',
        'status',
        'student_level_snapshot',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'mode' => ChatMode::class,
        'status' => ChatSessionStatus::class,
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ChatTopic::class, 'topic_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function summary(): HasOne
    {
        return $this->hasOne(ChatSessionSummary::class);
    }

    public function isActive(): bool
    {
        return $this->status === ChatSessionStatus::ACTIVE;
    }

    /**
     * جلب الجلسة الـ active الحالية لليوزر (لو موجودة) — تستخدم بـ GET /chat/sessions/active
     */
    public function scopeActiveForUser($query, int $userId)
    {
        return $query->where('user_id', $userId)
            ->where('status', ChatSessionStatus::ACTIVE);
    }
}
