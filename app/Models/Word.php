<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Word extends Model
{
    protected $fillable = [
        'lesson_id',
        'word_en',
        'word_ar',
    ];
     public function lesson():BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
       public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_words')
                    ->withPivot('status', 'added_at');
    }
}
