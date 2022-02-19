<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    public function userMessages() {
        return $this->hasMany('App\Models\UserMessage');
    }
}
