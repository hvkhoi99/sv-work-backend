<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Language extends Model
{
  protected $guarded = [];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function scopeLanguage($query, $request)
  {
    if ($request->has('locales') && isset($request->locales)) {
      $query->where(DB::raw('lower(locales)'), 'like', '%' . strtolower($request->locales) . '%');
    }

    return $query;
  }
}
