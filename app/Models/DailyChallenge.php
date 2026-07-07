<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DailyChallenge extends Model
{
    protected $fillable = [ 'date' , 'challenge_id'];
 public function challenge():BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_challenges' , 'daily_challenge_id' , 'user_id' )
                    ->withPivot('progress', 'is_completed', 'completed_at', 'reward_claimed' , 'reward_claimed_at');
    }
}
