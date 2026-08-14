<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Certificate extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_level_id',
        'certificate_number',
        'student_name',
        'level_name',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function userLevel()
    {
        return $this->belongsTo(UserLevel::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('certificate')->singleFile();
    }

    public function getCertificateUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('certificate');
    }
}
