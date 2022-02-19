<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCV extends Model
{
    protected $guarded = [];

    public function cv() {
        return $this->belongsTo('App\Models\CV');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
