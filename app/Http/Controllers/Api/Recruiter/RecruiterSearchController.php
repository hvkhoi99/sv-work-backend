<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecruiterSearchController extends Controller
{
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

    $results = DB::table('student_profiles')
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
      )
      ->name($request)->get();

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $results
    ], 200);
  }
}
