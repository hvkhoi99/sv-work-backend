<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    protected $guarded = [];

    public function recruitments() {
        return $this->hasMany('App\Models\Recruitment');
    }
}
