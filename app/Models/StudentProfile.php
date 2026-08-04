<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class StudentProfile extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'bio',
        'points',
        'streak',
        'last_activate_date'
    ];
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('student_profile_image')
            ->singleFile();
    }
}
