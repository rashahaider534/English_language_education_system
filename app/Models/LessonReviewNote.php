<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonReviewNote extends Model
{
    protected $fillable = ['lesson_review_id' , 'admin_id' , 'message'];

    public function lesson_review():belongsTo
    {
        return $this->belongsTo(LessonReview::class);
    }

    public function admin():belongsTo
    {
        return $this->belongsTo(User::class ,'admin_id');
    }
}
