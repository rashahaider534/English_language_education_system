<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentReviewNote extends Model
{
    protected $fillable = [
        'content_review_id',
        'admin_id',
        'message',
        'is_system_generated',
        'reviewable_type',
        'reviewable_id'
    ];

    protected $casts = [
        'is_system_generated' => 'boolean',
    ];

    public function review()
    {
        return $this->belongsTo(ContentReview::class, 'content_review_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
