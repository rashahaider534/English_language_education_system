<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLevel extends Model
{
    
      public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}
