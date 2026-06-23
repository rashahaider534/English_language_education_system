<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
     public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
       public function users()
    {
        return $this->belongsToMany(User::class, 'user_words')
            ->withPivot('status', 'added_at');
    }
}
