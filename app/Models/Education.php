<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
  protected $guarded = [];

  protected $casts = [
    'from_date' => 'datetime:m/d/Y', // Change your format
    'to_date' => 'datetime:m/d/Y', // Change your format
  ];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  // Search
  // public function scopeEducation($query, $request, $user_id)
  // {
  //   if ($request->has('school')) {
  //     $query->where([
  //       ['school', 'LIKE', '%' . $request->school . '%'],
  //       ['user_id', $user_id]
  //     ]);
  //   }

  //   return $query;
  // }
}
