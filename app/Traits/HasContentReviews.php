<?php

namespace App\Traits;

use App\Models\ContentReview;
use App\Models\ContentReviewNote;

trait HasContentReviews
{
    public function reviews()
    {
        return $this->morphMany(ContentReview::class, 'reviewable');
    }

    public function openReview()
    {
        return $this->morphOne(ContentReview::class, 'reviewable')
            ->where('status', 'in_review');
    }

    public function scopeWithoutOpenReview($query)
    {
        return $query->whereDoesntHave('reviews', fn ($q) => $q->where('status', 'in_review'));
    }

    public function hasOpenReview(): bool
    {
        return $this->reviews()->where('status', 'in_review')->exists();
    }

    public function reviewNotes()
    {
        return $this->morphMany(ContentReviewNote::class, 'reviewable');
    }
}
