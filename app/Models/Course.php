<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'description',
        'level_id',
        'teacher_id'
    ];

    public function level():BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
     public function teacher():BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
     public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_courses')
                    ->withPivot('status','started_at', 'completed_at');
    }
      public function lessons():HasMany
    {
        return $this->hasMany(Lesson::class);
    }
      public function tests()
    {
        return $this->morphMany(Test::class, 'testable');
    }
     public function rates():HasMany
    {
        return $this->hasMany(Rate::class);
    }

}
