<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    protected $fillable = ['type', 'score', 'title_question_en' , 'title_question_ar', 'text_question' , 'difficulty'];
    public function mcqAnswers():HasMany
    {
        return $this->hasMany(McqAnswer::class);
    }

    public function fillAnswers():HasOne
    {
        return $this->hasOne(FillAnswer::class);
    }

    public function arrangeAnswers():HasMany
    {
        return $this->hasMany(ArrangeAnswer::class);
    }

    public function pairAnswers():HasMany
    {
        return $this->hasMany(PairAnswer::class);
    }
    public function tests():BelongsToMany
    {
        return $this->belongsToMany(Test::class, 'test_questions')
                    ->withPivot('order');
    }
}
