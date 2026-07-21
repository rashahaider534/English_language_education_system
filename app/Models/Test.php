<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Test extends Model
{
    use HasTranslations;
    protected $fillable = [
        'testable_id',
        'testable_type',
        'passing_score',
        'title_en',
        'title_ar',
        'is_active',
        'status',
        'previous_test_id'
    ];
    protected $casts = ['status' => ContentStatus::class];
     public function testable():MorphTo
    {
        return $this->morphTo();
    }

    public function questions():BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'test_questions')
                    ->withPivot('order')
                    ->withTimestamps() ;

    }

    public function attempts():HasMany
    {
        return $this->hasMany(UserAttempt::class);
    }
}
