<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Language;
use App\Models\RecruiterProfile;
use App\Models\Skill;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{
  public function candidateInfo(Request $request, $id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

      if (isset($r_profile)) {
        $s_profile = StudentProfile::whereId($id)->first();

        if (isset($s_profile)) {
          $skill = Skill::where('user_id', $s_profile->user_id)->first();
          $language = Language::where('user_id', $s_profile->user_id)->first();
          $experiences = Experience::where('user_id', $s_profile->user_id)->orderBy('created_at', 'desc')->get();
          $educations = Education::where('user_id', $s_profile->user_id)->orderBy('created_at', 'desc')->get();
          $certificates = Certificate::where('user_id', $s_profile->user_id)->orderBy('created_at', 'desc')->get();
          $s_profile["skills"] = isset($skill) ?  $skill->name : null;
          $s_profile["languages"] = isset($language) ? $language->locale : null;
          $s_profile["experiences"] = $experiences;
          $s_profile["educations"] = $educations;
          $s_profile["certificates"] = $certificates;

          return response()->json([
            'status' => 1,
            'code' => 200,
            'data' => $s_profile
          ], 200);
        } else {
          return response()->json([
            'status' => 0,
            'code' => 404,
            'message' => 'Candidate\'s profile has not been created.'
          ], 404);
        }
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your recruiter profile has not been created.'
        ], 404);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 401,
        'message' => 'UNAUTHORIZED'
      ], 401);
    }
  }
}
