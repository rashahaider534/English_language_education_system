<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
       public function dailyChallenges()
    {
        return $this->hasMany(DailyChallenge::class);
    }

}
