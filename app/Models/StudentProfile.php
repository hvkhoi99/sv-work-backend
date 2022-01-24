<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'datetime:m/d/Y', // Change your format
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function subscriptions() {
        return $this->hasMany(Follow::class);
    }
}
