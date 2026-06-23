<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyChallenge extends Model
{
 public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_challenges')
            ->withPivot('progress', 'is_completed', 'completed_at', 'reward_claimed');
    }
}
