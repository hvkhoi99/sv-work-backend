<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class RecruiterSearchController extends Controller
{
  public function getCandidateSearch(Request $request) {
    $param = $request->all();
    $candidates = StudentProfile::filter($param);

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $candidates
    ], 200);
  }
}
