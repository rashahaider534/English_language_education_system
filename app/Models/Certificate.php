<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    Protected $fillable = [
    'user_level_id',
    ' certificate_number',
    'student_name',
    ' level_name',
    ' issued_at'
    ];
    public function userLevel()
    {
        return $this->belongsTo(UserLevel::class);
    }

}
