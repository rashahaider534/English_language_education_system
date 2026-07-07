<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevelException extends Model
{
    protected $fillable = [
        'user_id',
        'requested_level_id',
        'status',
        'reason',
        'review_note',
        'executed_by',
        'executed_at',
    ];
    public  function Level():BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function admin():BelongsTo
    {
        return $this->belongsTo(User::class,'executed_by');
    }
}
