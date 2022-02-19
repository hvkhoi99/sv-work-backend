<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model
{
    protected $guarded = [];

    public function message() {
        return $this->belongsTo('App\Models\Message');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
