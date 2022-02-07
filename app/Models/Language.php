<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
  protected $guarded = [];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  // Search
  public function scopeLanguage($query, $request)
  {
    if ($request->has('locales')) {
      $query->where('locales', 'LIKE', '%' . $request->locales . '%');
    }

    return $query;
  }
}
