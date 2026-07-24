<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PlacementTest extends Model
{
    protected $fillable = [
        'created_by',
    ];
   public function admin():BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
     public function tests():MorphMany
    {
        return $this->morphMany(Test::class, 'testable');
    }
}
