<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PairAnswer extends Model
{
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
