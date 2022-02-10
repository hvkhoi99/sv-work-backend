<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\City;
use App\Models\JobCategory;
use App\User;
use Illuminate\Support\Facades\DB;

class Recruitment extends Model
{
  protected $guarded = [];

  protected $casts = [
    'expiry_date' => 'datetime:m/d/Y', // Change your format
  ];

  // public function city() {
  //     return $this->belongsTo('App\Models\City');
  // }

  public function recruitmentTag()
  {
    return $this->hasMany('App\Models\RecruitmentTag');
  }

  public function jobCategory()
  {
    return $this->belongsTo(JobCategory::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function applications()
  {
    return $this->hasMany('App\Models\Application');
  }

  public function jobtag()
  {
    return $this->hasOne('App\Models\JobTags');
  }

  // Search Jobs
  public function scopeKeyword($query, $request)
  {
    if ($request->has('keyword')) {
      $query->where(DB::raw('lower(title)'), 'like', '%' . strtolower($request->keyword) . '%');
    }

    return $query;
  }

  public function scopeLocation($query, $request)
  {
    if ($request->has('location') && isset($request->location)) {
      $query->where(DB::raw('lower(location)'), 'like', '%' . strtolower($request->location) . '%');
    }

    return $query;
  }

  public function scopeCareer($query, $request)
  {
    if ($request->has('career') && isset($request->career)) {
      $query->where(DB::raw('lower(position)'), 'like', '%' . strtolower($request->career) . '%');
    }

    return $query;
  }

  public function scopeType($query, $request)
  {
    if ($request->has('type') && isset($request->type)) {
      $query->where('is_full_time', $request->type);
    }

    return $query;
  }
  
  public function scopeSalary($query, $request)
  {
    if ($request->has('salary') && isset($request->salary)) {
      $salary = explode('~', $request->salary);
      $query->where([
        ['min_salary', '>=', (int)$salary[0]],
        ['max_salary', '<=', (int)$salary[1]]
      ]);
    }
    
    return $query;
  }
  
  public function scopeClosed($query, $request)
  {
    if ($request->has('closed') && isset($request->closed)) {
      $query->where('is_closed', $request->closed);
    }

    return $query;
  }

  public function scopeExtra($query, $request)
  {
    if ($request->has('extra') && isset($request->extra)) {
      $query->where(DB::raw('lower(job_category)'), 'like', '%' . strtolower($request->extra) . '%');
    }

    return $query;
  }
}
