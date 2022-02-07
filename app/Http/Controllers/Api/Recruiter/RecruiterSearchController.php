<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Filterable;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class RecruiterSearchController extends Controller
{
  use Filterable;

  public function getCandidateSearch(Request $request) {
    $candidates = StudentProfile::filter($request)->get();

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $candidates
    ], 200);
  }
}
