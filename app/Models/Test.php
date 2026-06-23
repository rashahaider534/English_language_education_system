<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
     public function testable()
    {
        return $this->morphTo();
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'test_questions')->withPivot('order');
    }

    public function attempts()
    {
        return $this->hasMany(UserAttempt::class);
    }
}
