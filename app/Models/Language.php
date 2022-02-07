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
}
