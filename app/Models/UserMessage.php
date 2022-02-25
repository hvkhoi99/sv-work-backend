<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model
{
    protected $guarded = [];

    public function message() {
        return $this->belongsTo('App\Models\Message');
    }

    public function recruiterProfile() {
        return $this->belongsTo('App\Models\RecruiterProfile');
    }

    public function studentProfile() {
        return $this->belongsTo('App\Models\StudentProfile');
    }

    // public function user() {
    //     return $this->belongsTo('App\User');
    // }
}
