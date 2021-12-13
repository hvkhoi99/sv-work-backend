<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function participantEvents() {
        return $this->hasMany('App\Models\ParticipantEvent');
    }
}
