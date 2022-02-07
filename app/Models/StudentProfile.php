<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

  // public function filterStatus($query, $value)
  // {
  //     return $query->where('status', $value);
  // }

  // public function filterType($query, $value)
  // {
  //     return $query->where('type', $value);
  // }

  // public function filterPrice($query, $value)
  // {
  //     return $query->where('price', $value);
  // }
}
