<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    protected $fillable = [
        'bio',
        'points',
        'streak',
        'last_activate_date'
    ];
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
