<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;
use Illuminate\Support\Str;
class AppNotification extends Model
{
    protected $table = 'notifications';
    public $incrementing = false;
protected $keyType = 'string';
    protected $fillable = [
        'id',
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


}
