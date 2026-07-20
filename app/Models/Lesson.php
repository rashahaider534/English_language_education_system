<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lesson extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;
    protected $fillable =
        ['title_en','title_ar','course_id','status','order','xp_points'];

    public function comments():HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class,'user_lessons')
                    ->withPivot('status', 'started_at', 'completed_at');
    }

    public function course():BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function tests():MorphMany
    {
        return $this->morphMany(Test::class, 'testable');
    }

    public function words():HasMany
    {
        return $this->hasMany(Word::class);
    }

    public function lessonReview():HasOne
    {
        return $this->hasOne(LessonReview::class);
    }
}
