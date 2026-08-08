<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;
class FirebaseToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'device_type',
        'device_name',
        'last_used_at'
    ];

        protected $casts = [
        'last_used_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function cleanup()
    {
        self::where('created_at', '<', now()->subMonths(3))->delete();
    }
}
