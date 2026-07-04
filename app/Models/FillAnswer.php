<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FillAnswer extends Model
{
    protected $fillable = [
        'question_id',
        'text_answer',
        'blank_order'
    ];
   public function question():BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
