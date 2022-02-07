<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Str;

// trait Filterable
// {
//   public function scopeFilter($query, $request)
//   {
//     $param = $request->all();
//     foreach ($param as $field => $value) {
//       $method = 'filter' . Str::studly($field);

//       if ($value != '') {
//         if (method_exists($this, $method)) {
//           $this->{$method}($query, $value);
//         } else {
//           if (!empty($this->filterable) && is_array($this->filterable)) {
//             if (in_array($field, $this->filterable)) {
//               $query->where($this->table . '.' . $field, $value);
//             } elseif (key_exists($field, $this->filterable)) {
//               $query->where($this->table . '.'
//                 . $this->filterable[$field], $value);
//             }
//           }
//         }
//       }
//     }

//     return $query;
//   }
// }
class StudentProfile extends Model
{
  // use Filterable;
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
  public function filterName($query, $request)
  {
    if ($request->has('name')) {
      $query->where('first_name', 'LIKE', '%' . $request->name . '%')
        ->orWhere('last_name', 'LIKE', '%' . $request->name . '%');;
    }

    return $query;
  }

  public function filterCareer($query, $request)
  {
    if ($request->has('career')) {
      $query->where('job_title', 'LIKE', '%' . $request->career . '%');
    }

    return $query;
  }

  public function filterLocation($query, $request)
  {
    if ($request->has('location')) {
      $query->where('address', 'LIKE', '%' . $request->location . '%');
    }

    return $query;
  }

  public function filterGender($query, $request)
  {
    if ($request->has('gender')) {
      $query->where('gender', $request->gender);
    }

    return $query;
  }

  // public function filterLanguage($query, $value)
  // {
  //   return $query->where('locales', 'LIKE', '%' . $value . '%');
  // }

  // public function filterEducation($query, $value)
  // {
  //   return $query->where('school', 'LIKE', '%' . $value . '%');
  // }
}
