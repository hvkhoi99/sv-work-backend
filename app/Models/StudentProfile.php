<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Str;

// trait Filterable
// {
//   public function scopeFilter($query, $request)
//   {
//     $params = $request->all();
//     foreach ($params as $field => $value) {
//       if ($field !==  '_token') {
//         $method = 'filter' . Str::studly($field);

//         if (!empty($value)) {
//           if (method_exists($this, $method)) {
//             $this->{$method}($query, $value);
//           }
//         }
//       }
//     }

//     return $query;
//   }
// }
class StudentProfile extends Model
{
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
  public function filterName($query, $value)
  {
    return $query
      ->where('first_name', 'LIKE', '%' . $value . '%')
      ->orWhere('last_name', 'LIKE', '%' . $value . '%');
  }
}
