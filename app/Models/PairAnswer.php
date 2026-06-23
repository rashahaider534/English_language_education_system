<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PairAnswer extends Model
{
    protected $fillable = [
        'question_id',
        'left_text',
        'right_text',
    ];
    public function question():BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
