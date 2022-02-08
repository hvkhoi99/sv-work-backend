<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

  public function scopeEducation($query, $request)
  {
    if ($request->has('school') && isset($request->school)) {
      $query->where(DB::raw('lower(school)'), 'like', '%' . strtolower($request->school) . '%');
    }

    return $query;
  }
}
