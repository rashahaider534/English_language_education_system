<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsToMany(User::class, 'user_levels')
            ->withPivot('status', 'enrolled_at', 'completed_at');
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
     public function tests()
    {
        return $this->morphMany(Test::class, 'testable');
    }
    public function LevelException()
    {
        return $this->hasMany(LevelException::class);
    }
    


}
