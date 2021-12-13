<?php

namespace App;

use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role() {
        return $this->belongsTo('App\Models\Role');
    }

    public function student() {
        return $this->hasOne('App\Models\StudentProfile');
    }

    public function recruiterProfile() {
        return $this->hasOne(RecruiterProfile::class);
    }

    public function recruitments() {
        return $this->hasMany(Recruitment::class);
    }

    public function applications() {
        return $this->hasMany('App\Models\Application');
    }

    public function experiences() {
        return $this->hasMany('App\Models\Experience');
    }

    public function educations() {
        return $this->hasMany('App\Models\Education');
    }

    public function skill() {
        return $this->hasOne('App\Models\Skill');
    }

    public function certificates() {
        return $this->hasMany('App\Models\Certificate');
    }

    public function languages() {
        return $this->hasOne('App\Models\Language');
    }

    public function events() {
        return $this->hasMany('App\Models\Event');
    }

    public function participantEvents() {
        return $this->hasMany('App\Models\ParticipantEvent');
    }
}
