<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
    ];
    public function podcasts():HasMany
{
        return $this->hasMany(Podcast::class);
}

}
