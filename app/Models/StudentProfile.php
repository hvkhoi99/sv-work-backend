<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// trait Filterable
// {
//   public function scopeFilter($query, $param)
//   {
//     foreach ($param as $field => $value) {
//       $method = 'filter' . Str::studly($field);

//       if ($value === '') {
//         continue;
//       }

//       if (method_exists($this, $method)) {
//         $this->{$method}($query, $value);
//         continue;
//       }

//       if (empty($this->filterable) || !is_array($this->filterable)) {
//         continue;
//       }

//       if (in_array($field, $this->filterable)) {
//         $query->where($this->table . '.' . $field, $value);
//         continue;
//       }

//       if (key_exists($field, $this->filterable)) {
//         $query->where($this->table . '.' . $this->filterable[$field], $value);
//         continue;
//       }
//     }

//     return $query;
//   }
// }

class StudentProfile extends Model
{
  // use Filterable;
  protected $filterable = [
    'last_name',
  ];
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
      $query->where('last_name', 'LIKE', '%' . $request->name . '%');
        // ->orWhere('last_name', 'LIKE', '%' . $request->name . '%');
    }

    return $query;
  }

  public function scopeCareer($query, $request)
  {
    if ($request->has('career')) {
      $query->where('job_title', 'LIKE', '%' . $request->career . '%');
    }

    return $query;
  }

  public function scopeLocation($query, $request)
  {
    if ($request->has('location')) {
      $query->where('address', 'LIKE', '%' . $request->location . '%');
    }

    return $query;
  }

  public function scopeGender($query, $request)
  {
    if ($request->has('gender')) {
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
