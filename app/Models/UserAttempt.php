<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAttempt extends Model
{
     public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function answers()
    {
        return $this->hasMany(UserAttemptAnswer::class);
    }
}
