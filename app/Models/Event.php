<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];

    protected $casts = [
        'from_date' => 'datetime:m/d/Y', // Change your format
        'to_date' => 'datetime:m/d/Y', // Change your format
    ];

    public function student_profile()
    {
        return $this->belongsTo('App\Models\StudentProfile');
    }

    public function recruiter_profile()
    {
        return $this->belongsTo('App\Models\RecruiterProfile');
    }

    public function participant_events()
    {
        return $this->hasMany('App\Models\ParticipantEvent');
    }
}
