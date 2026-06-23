<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'description',
        'level_id',
        'teacher_id'
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
     public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
     public function users()
    {
        return $this->belongsToMany(User::class, 'user_courses');
    }
      public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
      public function tests()
    {
        return $this->morphMany(Test::class, 'testable');
    }
     public function rates()
    {
        return $this->hasMany(Rate::class);
    }

}
