<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAttemptAnswer extends Model
{
    protected $table = 'user_attempt_answers';
    protected $fillable = ['attempt_id' , 'question_id' , 'answer_json' , 'score'];
    public function userAttempt():BelongsTo
    {
        return $this->belongsTo(UserAttempt::class);
    }

    public function question():BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
