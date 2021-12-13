<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentTag extends Model
{
    protected $guarded = [];

    public function hashtag() {
        return $this->belongsTo('App\Models\Hashtag');
    }

    public function recruitment() {
        return $this->belongsTo('App\Models\Recruitment');
    }
}


