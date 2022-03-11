<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    protected $guarded = [];

    // protected $casts = [
    //     'from_date' => 'datetime:m/d/Y', // Change your format
    //     'to_date' => 'datetime:m/d/Y', // Change your format
    // ];

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

    // Search Events
    public function scopeEvent($query, $request)
    {
        if ($request->has('event')) {
            $query->where(DB::raw('lower(title)'), 'like', '%' . strtolower($request->event) . '%');
        }

        return $query;
    }

    public function scopeLocation($query, $request)
    {
        if ($request->has('location') && isset($request->location)) {
            $query->where(DB::raw('lower(location)'), 'like', '%' . strtolower($request->location) . '%');
        }

        return $query;
    }

    public function scopeStart($query, $request)
    {
        if ($request->has('start') && isset($request->start)) {
            $query->whereDate('start_date', '=', $request->start);
        }

        return $query;
    }
}
