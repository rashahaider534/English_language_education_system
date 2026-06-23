<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAttemptAnswer extends Model
{
    public function userAttempt()
    {
        return $this->belongsTo(UserAttempt::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
