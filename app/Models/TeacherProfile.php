<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TeacherProfile extends Model implements HasMedia
{
    use InteractsWithMedia;
     protected $fillable =
     [
        'user_id',
        'bio',
     ];
     public function user():BelongsTo
     {
        return $this->belongsTo(User::class);
     }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('teacher_profile_image')
            ->singleFile();
    }
}
