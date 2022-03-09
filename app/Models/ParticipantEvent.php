<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantEvent extends Model
{
    protected $guarded = [];
    // protected $casts = [
    //     'from_date' => 'datetime:m/d/Y', // Change your format
    //     'to_date' => 'datetime:m/d/Y', // Change your format
    // ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function event() {
        return $this->belongsTo('App\Models\Event');
    }
}
