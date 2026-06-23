<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function mcqAnswers()
    {
        return $this->hasMany(McqAnswer::class);
    }

    public function fillAnswers()
    {
        return $this->hasMany(FillAnswer::class);
    }

    public function arrangeAnswers()
    {
        return $this->hasMany(ArrangeAnswer::class);
    }

    public function pairAnswers()
    {
        return $this->hasMany(PairAnswer::class);
    }
    public function tests()
    {
        return $this->belongsToMany(Test::class, 'test_questions')->withPivot('order');
    }
}
