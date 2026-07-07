<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLevel extends Model
{
    protected $table = 'user_levels';
    protected $fillable = [ 'level_id' , 'user_id' , 'status' , 'enrolled_at' , 'completed_at'];

    public function level():BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }
}
