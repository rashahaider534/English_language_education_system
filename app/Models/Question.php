<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Http\Resources\Answer\ArrangeAnswerResource;
use App\Http\Resources\Answer\FillAnswerResource;
use App\Http\Resources\Answer\McqAnswerResource;
use App\Http\Resources\Answer\PairAnswerResource;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Question extends Model implements HasMedia
{
    use InteractsWithMedia,SoftDeletes,HasTranslations;
    protected $fillable = ['type', 'score', 'title_question_en' , 'title_question_ar', 'text_question' , 'difficulty','user_id','previous_question_id'];
    public function mcqAnswers():HasMany
    {
        return $this->hasMany(McqAnswer::class);
    }

    public function fillAnswers():HasMany
    {
        return $this->hasMany(FillAnswer::class);
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
    public function getAnswersRelationName(): string
    {
        return match ($this->type) {
            'MCQ'     => 'mcqAnswers',
            'FILL'    => 'fillAnswers',
            'ARRANGE' => 'arrangeAnswers',
            'PAIR'    => 'pairAnswers',
            default   => throw new \Exception("Unknown question type"),
        };
    }

    public function getAnswerResource(): string
    {
        return match ($this->type) {
            'MCQ' => McqAnswerResource::class,
            'FILL' => FillAnswerResource::class,
            'ARRANGE' => ArrangeAnswerResource::class,
            'PAIR' => PairAnswerResource::class,
        };
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('audio')->singleFile();

        $this->addMediaCollection('image')->singleFile();
    }

    public function isUsedInPublishedTests(): bool
    {
        return $this->tests()->where('status', 'published')->exists();
    }

    public function isUsedInArchivedTests(): bool
    {
        return $this->tests()->where('status', 'archived')->exists();
    }

    public function getPublishedTestsAttribute()
    {
        return $this->tests()->where('status', ContentStatus::PUBLISHED->value)->get(['tests.id', 'tests.title_en']);
    }

    public function getArchivedTestsAttribute()
    {
        return $this->tests()->where('status', ContentStatus::ARCHIVED->value)->get(['tests.id', 'tests.title_en']);
    }
    public function getInReviewTestsAttribute()
    {
        return $this->tests()->where('status', ContentStatus::IN_REVIEW->value)->get(['tests.id', 'tests.title_en']);
    }
    public function getApprovedTestsAttribute()
    {
        return $this->tests()->where('status', ContentStatus::APPROVED->value)->get(['tests.id', 'tests.title_en']);
    }


    protected $appends = ['published_tests','archived_tests','in_review_tests','approved_tests'];

    public function scopeHasChildren($query)
    {
        return $query->whereExists(function ($subQuery) {
            $subQuery->select(DB::raw(1))
                ->from('questions as children')
                ->whereColumn('children.previous_question_id', 'questions.id')
                ->whereNull('children.deleted_at');
        });
    }

    public function nextVersion():HasOne
    {
        return $this->hasOne(Question::class, 'previous_question_id');
    }

    public function previousVersion():BelongsTo
    {
        return $this->belongsTo(Question::class, 'previous_question_id');
    }
}
