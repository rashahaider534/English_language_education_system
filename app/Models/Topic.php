<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\TopicStatus;
use App\Traits\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Topic extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;
    protected $fillable = [
        'name_en',
        'name_ar',
        'status',
        'created_by'
    ];
    protected $casts = [
        'status' => TopicStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function podcasts(): HasMany
    {
        return $this->hasMany(Podcast::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

     public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('topic_image')
            ->singleFile();
    }
}
