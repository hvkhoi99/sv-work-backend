<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobTags extends Model
{
    protected $guarded = [];

    public function recruitments() {
        return $this->hasMany('App\Models\Recruitment');
    }
}
