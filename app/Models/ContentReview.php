<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewer_id',
        'status',
        'claimed_at',
        'completed_at',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'completed_at' => 'datetime',
        'status' => ReviewStatus::class
    ];

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function notes()
    {
        return $this->hasMany(ContentReviewNote::class);
    }
}
