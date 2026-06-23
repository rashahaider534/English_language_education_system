<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    public function podcasrs()
{
        return $this->hasMany(Podcast::class);
}

}
