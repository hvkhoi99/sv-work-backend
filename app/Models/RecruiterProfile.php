<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class RecruiterProfile extends Model
{
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function subscriptions() {
        return $this->hasMany(Follow::class);
    }
}
