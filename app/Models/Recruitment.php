<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\City;
use App\Models\JobCategory;
use App\User;

class Recruitment extends Model
{
    protected $guarded = [];

    // public function city() {
    //     return $this->belongsTo('App\Models\City');
    // }

    public function recruitmentTag() {
        return $this->hasMany('App\Models\RecruitmentTag');
    }

    public function jobCategory() {
        return $this->belongsTo(JobCategory::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function applications() {
        return $this->hasMany('App\Models\Application');
    }
}
