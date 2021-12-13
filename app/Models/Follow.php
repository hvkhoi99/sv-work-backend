<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $guarded = [];

    public function recruiterProfile() {
        return $this->belongsTo('App\Models\RecruiterProfile');
    }

    public function studentProfile() {
        return $this->belongsTo('App\Models\StudentProfile');
    }
}
