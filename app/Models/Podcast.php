<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Podcast extends Model
{
    protected $fillable = [
        'topic_id',
        'level_id',
        'point_required',
    ];
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function level():BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}
