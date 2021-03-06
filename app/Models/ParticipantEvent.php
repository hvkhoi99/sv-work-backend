<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantEvent extends Model
{
    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function event() {
        return $this->belongsTo('App\Models\Event');
    }
}
