<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\DB;

class RecruiterProfile extends Model
{
  protected $guarded = [];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function subscriptions()
  {
    return $this->hasMany(Follow::class);
  }

  public function userMessages()
  {
    return $this->hasMany('App\Models\UserMessage');
  }

  public function events()
  {
    return $this->hasMany('App\Models\Event');
  }

  // Search Name
  public function scopeKeyword($query, $request)
  {
    if ($request->has('keyword') && isset($request->keyword)) {
      $query->where(DB::raw('lower(company_name)'), 'like', '%' . strtolower($request->keyword) . '%');
    }

    return $query;
  }

  public function scopeLocation($query, $request)
  {
    if ($request->has('location') && isset($request->location)) {
      $query->where(DB::raw('lower(address)'), 'like', '%' . strtolower($request->location) . '%');
    }

    return $query;
  }
}
