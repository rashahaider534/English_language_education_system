<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelException extends Model
{
    public  function Level()
    {
        return $this->belongsTo(Level::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function  user_executed()
    {
        return $this->belongsTo(User::class,'executed_by');
    }
}
