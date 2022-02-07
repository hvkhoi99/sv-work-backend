<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

trait Filterable
{
  public function scopeFilter($query, $request)
  {
    $params = $request->all();
    foreach ($params as $field => $value) {
      if ($field !==  '_token') {
        $method = 'filter' . Str::studly($field);

        if (!empty($value)) {
          if (method_exists($this, $method)) {
            $this->{$method}($query, $value);
          }
        }
      }
    }

    return $query;
  }
}

class RecruiterSearchController extends Controller
{
  use Filterable;

  public function filterName($query, $value)
  {
    return $query
      ->where('last_name', 'LIKE', '%' . $value . '%');
  }

  public function filterCareer($query, $value)
  {
    return $query->where('job_title', 'LIKE', '%' . $value . '%');
  }

  public function filterLocation($query, $value)
  {
    return $query->where('address', 'LIKE', '%' . $value . '%');
  }

  public function filterLanguage($query, $value)
  {
    return $query->where('locales', 'LIKE', '%' . $value . '%');
  }

  public function filterGender($query, $value)
  {
    return $query->where('gender', $value);
  }

  public function filterEducation($query, $value)
  {
    return $query->where('school', 'LIKE', '%' . $value . '%');
  }

  public function getCandidateSearch(Request $request)
  {
    // $candidates = StudentProfile::filter($request)->get();
    // $candidates = StudentProfile::where([
    //   ['first_name', 'LIKE', '%'.$request['name'].'%'],
    //   ['last_name', 'LIKE', '%'.$request['name'].'%'],
    //   ['job_title', 'LIKE', '%'.$request['career'].'%'],
    //   ['address', 'LIKE', '%'.$request['location'].'%'],
    //   ['gender', $request['gender']],
    // ])->get();

    // $candidates = StudentProfile::query();
    // $candidates->name($request);
    // $candidates = $candidates->get();

    // $candidates = array_filter($candidates, function ($candidate) {
    //   return $candidate[""]
    // })

    $results =
      DB::table('student_profiles')
      ->join('languages', 'student_profiles.user_id', '=', 'languages.user_id')
      ->join('education', 'student_profiles.user_id', '=', 'education.user_id')
      ->select(
          'student_profiles.id',
          'student_profiles.avatar_link',
          'student_profiles.first_name',
          'student_profiles.last_name',
          'student_profiles.address',
          'student_profiles.job_title',
          'student_profiles.gender',
          'student_profiles.created_at',
        'languages.locales',
        'education.school',
      )->filter($request)->get();

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $results
    ], 200);
  }
}
