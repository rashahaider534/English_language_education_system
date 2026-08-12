<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatTopic extends Model
{
    protected $fillable = [
        'title',
        'description',
        'level_id',
        'focus_points',
        'system_prompt_addon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    /**
     * مواضيع مناسبة لمستوى معين وفعّالة، لاستخدامها بشاشة اختيار الموضوع
     */
    public function scopeActiveForLevel($query, int $levelId)
    {
        return $query->where('is_active', true)
            ->where('level_id', $levelId);
    }
}
