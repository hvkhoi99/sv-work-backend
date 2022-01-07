<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Language;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
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

  public function approve($recruitment_id, $candidate_id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
      $exist_recruitment = Recruitment::where([
        ['id', $recruitment_id],
        ['user_id', $user->id]
      ])->first();

      if (isset($r_profile) && isset($exist_recruitment)) {
        $s_profile = StudentProfile::whereId($candidate_id)->first();

        if (isset($s_profile)) {
          $exist_application = Application::where([
            ['recruitment_id', $exist_recruitment->id],
            ['user_id', $s_profile->user_id],
          ])->first();

          if (isset($exist_application) && $exist_application->is_applied) {

            $exist_application->update([
              'is_invited' => !($exist_application->is_invited)
            ]);

            return response()->json([
              'status' => 1,
              'code' => 200,
              'message' => $exist_application->is_invited ? 'Successfully approve.' : 'Successfully reject.',
              'data' => $exist_application
            ], 200);
          } else {
            return response()->json([
              'status' => 0,
              'code' => 404,
              'message' => 'No applications found.'
            ], 404);
          }
        } else {
          return response()->json([
            'status' => 0,
            'code' => 404,
            'message' => 'The candidate\'s profile could not be found.'
          ], 404);
        }
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'The recruitment doesn\'t exist or your recruiter profile does not exist.'
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
