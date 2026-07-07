<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Challenge extends Model
{
    protected $fillable = [
         'title_en',
         'title_ar',
         'description_en',
         'description_ar',
         'rule_type',
         'target_value',
         'reward_points',
         'is_active',
         'created_by'
    ];
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
       public function dailyChallenges():HasMany
    {
        return $this->hasMany(DailyChallenge::class);
    }

}
