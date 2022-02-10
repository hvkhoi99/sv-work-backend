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

  // Search Name
  public function scopeKeyword($query, $request)
  {
    if ($request->has('keyword')) {
      $query->where(DB::raw('lower(company_name)'), 'like', '%' . strtolower($request->keyword) . '%');
    }

    return $query;
  }

  public function scopeLocation($query, $request)
  {
    if ($request->has('location')) {
      $query->where(DB::raw('lower(address)'), 'like', '%' . strtolower($request->location) . '%');
    }

    return $query;
  }
}
