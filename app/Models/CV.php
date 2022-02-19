<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CV extends Model
{
    protected $guarded = [];

    public function userCVs() {
        return $this->hasMany('App\Models\UserCV');
    }
}
