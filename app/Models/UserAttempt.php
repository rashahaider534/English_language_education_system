<?php

namespace App\Models;

use App\Enums\AttemptStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAttempt extends Model
{
    protected $table = 'user_attempts';
    protected $fillable = ['user_id' , 'test_id' , 'score' , 'status' ,'started_at', 'completed_at'];
    protected $casts = ['status' => AttemptStatus::class];
     public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function test():BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function answers():HasMany
    {
        return $this->hasMany(UserAttemptAnswer::class,'attempt_id');
    }
}
