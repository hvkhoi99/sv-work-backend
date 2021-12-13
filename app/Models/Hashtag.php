<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $guarded = [];

    public function recruitmentTag() {
        return $this->hasMany('App\Models\RecruitmentTag');
    }
}
