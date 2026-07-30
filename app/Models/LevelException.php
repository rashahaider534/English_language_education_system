<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\HasTranslations;
use App\Enums\LevelExceptionStatus;
class LevelException extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;
    protected $fillable = [
        'user_id',
        'requested_level_id',
        'recommended_level_id',
        'status',
        'reason',
        'review_note',
        'executed_by',
        'executed_at',
    ];
    public  function requestedLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'requested_level_id');
    }
    public  function recommendedLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'recommended_level_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
    protected function casts(): array
    {
        return [
            'status' => LevelExceptionStatus::class,
        ];
    }
}
