<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McqAnswer extends Model
{
     public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
