<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;
class AppNotification extends Model
{
     protected $fillable = [
       'user_id',
        'title',
        'body',
        'data',
        'type',
        'read',
        'read_at'
    ];

        protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now()
        ]);
    }

}
