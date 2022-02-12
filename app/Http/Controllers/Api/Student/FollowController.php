<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\RecruiterProfile;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class FollowController extends Controller
{
  public function follow(Request $request, $id)
  {
    $user = $request->user();

    $s_profile = StudentProfile::where('user_id', $user->id)->first();
    $r_profile = RecruiterProfile::where('id', $id)->first();

    if (isset($s_profile) && isset($r_profile)) {

      $exist_follow = Follow::where('s_profile_id', $s_profile->id)->where('r_profile_id', $id)->first();

      if (isset($exist_follow)) {
        $exist_follow->update([
          'is_followed' => !($exist_follow->is_followed)
        ]);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully updated.',
          'data' => $exist_follow
        ], 200);
      } else {
        $new_follow = Follow::create([
          's_profile_id' => $s_profile->id,
          'r_profile_id' => $id,
          'is_followed' => true
        ]);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully created.',
          'data' => $new_follow
        ], 200);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your student account or the recruiter was not found or has not been created.',
      ], 404);
    }
  }
}
