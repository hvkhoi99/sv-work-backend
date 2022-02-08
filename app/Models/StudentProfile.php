<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StudentProfile extends Model
{
  // use Filterable;
  // protected $filterable = [
  //   'last_name',
  // ];
  protected $guarded = [];

  protected $casts = [
    'date_of_birth' => 'datetime:m/d/Y', // Change your format
  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function subscriptions()
  {
    return $this->hasMany(Follow::class);
  }

  // Search
  public function scopeName($query, $request)
  {
    if ($request->has('name')) {
      $query->where(DB::raw('lower(last_name)'), 'like', '%' . strtolower($request->name) . '%');
    }

    return $query;
  }

  public function scopeCareer($query, $request)
  {
    if ($request->has('career')) {
      $query->where(DB::raw('lower(job_title)'), 'like', '%' . strtolower($request->career) . '%');
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

  public function scopeGender($query, $request)
  {
    if ($request->has('gender') && isset($request->gender)) {
      $query->where('gender', $request->gender);
    }

    return $query;
  }

  // public function filterName($query, $value)
  // {
  //   return $query->where('last_name', 'LIKE', '%' . $value . '%');
  // }

  // public function filterCareer($query, $value)
  // {
  //   return $query->where('job_title', 'LIKE', '%' . $value . '%');
  // }

  // public function filterLocation($query, $value)
  // {
  //   return $query->where('address', 'LIKE', '%' . $value . '%');
  // }

  // public function filterGender($query, $value)
  // {
  //   return $query->where('gender', $value);
  // }

  // public function filterLanguage($query, $value)
  // {
  //   return $query->where('locales', 'LIKE', '%' . $value . '%');
  // }

  // public function filterEducation($query, $value)
  // {
  //   return $query->where('school', 'LIKE', '%' . $value . '%');
  // }
}
