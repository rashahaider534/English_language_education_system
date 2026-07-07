<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rate extends Model
{
    protected $fillable =
        ['stars_count'];
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function course():BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
