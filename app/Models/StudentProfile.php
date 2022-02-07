<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Filterable
{
  public function scopeFilter($query, $request)
  {
    $params = $request->all();
    foreach ($params as $field => $value) {
      if ($field !==  '_token') {
        $method = 'filter' . Str::studly($field);

        if (!empty($value)) {
          if (method_exists($this, $method)) {
            $this->{$method}($query, $value);
          }
        }
      }
    }

    return $query;
  }
}

class StudentProfile extends Model
{
  use Filterable;
  // protected $filterable = [
  //   'first_name',
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
  // public function scopeName($query, $request)
  // {
  //   if ($request->has('name')) {
  //     $query->where('last_name', 'LIKE', '%' . $request->name . '%');
  //       // ->orWhere('last_name', 'LIKE', '%' . $request->name . '%');
  //   }

  //   return $query;
  // }

  // public function scopeCareer($query, $request)
  // {
  //   if ($request->has('career')) {
  //     $query->where('job_title', 'LIKE', '%' . $request->career . '%');
  //   }

  //   return $query;
  // }

  // public function scopeLocation($query, $request)
  // {
  //   if ($request->has('location')) {
  //     $query->where('address', 'LIKE', '%' . $request->location . '%');
  //   }

  //   return $query;
  // }

  // public function scopeGender($query, $request)
  // {
  //   if ($request->has('gender')) {
  //     $query->where('gender', $request->gender);
  //   }

  //   return $query;
  // }
  public function filterName($query, $value)
  {
    return $query
      ->where('last_name', 'LIKE', '%' . $value . '%');
  }

  public function filterCareer($query, $value)
  {
    return $query->where('job_title', 'LIKE', '%' . $value . '%');
  }

  public function filterLocation($query, $value)
  {
    return $query->where('address', 'LIKE', '%' . $value . '%');
  }

  public function filterGender($query, $value)
  {
    return $query->where('gender', $value);
  }
}
