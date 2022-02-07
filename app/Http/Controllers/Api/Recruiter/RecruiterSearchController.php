<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class RecruiterSearchController extends Controller
{
  public function getCandidateSearch(Request $request) {
    // $candidates = StudentProfile::filter($request)->get();
    // $candidates = StudentProfile::where([
    //   ['first_name', 'LIKE', '%'.$request['name'].'%'],
    //   ['last_name', 'LIKE', '%'.$request['name'].'%'],
    //   ['job_title', 'LIKE', '%'.$request['career'].'%'],
    //   ['address', 'LIKE', '%'.$request['location'].'%'],
    //   ['gender', $request['gender']],
    // ])->get();
    $candidates = StudentProfile::query();
    $candidates->filterName($request);
    $candidates = $candidates->get();

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $candidates
    ], 200);
  }
}
