<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Test extends Model
{
    protected $fillable = [
        'testable_id',
        'testable_type',
        'passing_score',
        'title_en',
        'title_ar',
        'is_active',
        'status',
    ];
     public function testable():MorphTo
    {
        return $this->morphTo();
    }

    public function questions():BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'test_questions')
                    ->withPivot('order');
    }

    public function attempts():HasMany
    {
        return $this->hasMany(UserAttempt::class);
    }
}
