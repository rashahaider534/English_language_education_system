<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Traits\HasContentReviews;
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
    use InteractsWithMedia, HasTranslations , HasContentReviews;
    protected $fillable =
        ['title_en','title_ar','course_id','status','order','xp_points'];
    protected $casts = [
        'status' => ContentStatus::class,
    ];
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

      public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('videos')
            ->singleFile();
    }

    public function latestReview()
    {
        return $this->morphOne(ContentReview::class, 'reviewable')
            ->latestOfMany('completed_at');
    }
    public function activeTest()
    {
        return $this->morphOne(Test::class, 'testable')
            ->whereDoesntHave('nextVersion')
            ->where('status', '!=', ContentStatus::ARCHIVED)
            ->latestOfMany();
    }
}
