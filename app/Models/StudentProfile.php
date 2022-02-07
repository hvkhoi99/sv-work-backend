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
}
