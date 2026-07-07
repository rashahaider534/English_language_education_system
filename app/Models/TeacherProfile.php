<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherProfile extends Model
{
     protected $fillable =
     [
        'bio',
     ];
     public function user():BelongsTo
     {
        return $this->belongsTo(User::class);
     }
}
