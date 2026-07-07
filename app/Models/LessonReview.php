<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonReview extends Model
{
    protected $fillable = ['lesson_id' , 'assigned_to' , 'status' , 'claimed_at' , 'completed_at'];
    public function lesson():BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function admin():BelongsTo
    {
        return $this->belongsTo(User::class ,'assigned_to');
    }

    public function notes():HasMany
    {
        return $this->hasMany(LessonReviewNote::class);
    }
}
