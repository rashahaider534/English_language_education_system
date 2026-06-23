<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Level extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'order',
        'minimum_score',
        'maximum_score',
        'is_active',
        'price',
        'estimated_duration',
        'created_by'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(UserLevel::class)
            ->withPivot(
                'status',
                'enrolled_at',
                'completed_at'
            );
    }

    public function userLevels():HasMany
    {
        return $this->hasMany(UserLevel::class);
    }
    public function courses():HasMany
    {
        return $this->hasMany(Course::class);
    }
     public function tests()
    {
        return $this->morphMany(Test::class, 'testable');
    }
    public function LevelException()
    {
        return $this->hasMany(LevelException::class,'requested_level_id');
    }

    public function podcasts():HasMany
    {
        return $this->hasMany(Podcast::class);
    }

}
