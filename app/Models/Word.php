<?php

namespace App\Models;

use App\Enums\WordStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Word extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'lesson_id',
        'word_en',
        'word_ar',
    ];
     public function lesson():BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
       public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_words')
                    ->withPivot('status', 'added_at');
    }
     protected function casts(): array
    {
        return [
            'status' => WordStatus::class,
        ];
    }
    
}
