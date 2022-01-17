<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentApplicationController extends Controller
{
  public function apply($id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $s_profile = StudentProfile::where('user_id', $user->id)->first();

      if (isset($s_profile) && $s_profile->open_for_job) {
        $exist_recruitment = Recruitment::whereId($id)->first();

        if (isset($exist_recruitment) && !($exist_recruitment->is_closed)) {

          // $exist_invited_recruitment = Application::where('is_invited', true)->first();

          // if (!isset($exist_invited_recruitment)) {

          $exist_application = Application::where('recruitment_id', $id)->first();

          if (isset($exist_application)) {

            if ($exist_application->user_id === $user->id) {

              if ($exist_application->state !== true) {
                $exist_application->update([
                  'is_applied' => !($exist_application->is_applied)
                ]);

                return response()->json([
                  'status' => 1,
                  'code' => 200,
                  'message' => 'Successfully updated.',
                  'data' => $exist_application
                ], 200);
              } else {
                return response()->json([
                  'status' => 0,
                  'code' => 405,
                  'message' => 'You cannot take this action because your application has been approved.'
                ], 405);
              }
            } else {
              $new_application = Application::create([
                'state' => null,
                'is_invited' => false,
                'is_applied' => true,
                'is_saved' => false,
                'user_id' => $user->id,
                'recruitment_id' => $id
              ]);

              return response()->json([
                'status' => 1,
                'code' => 200,
                'message' => 'Successfully applied.',
                'data' => $new_application
              ], 200);
            }
          } else {

            $new_application = Application::create([
              'state' => null,
              'is_invited' => false,
              'is_applied' => true,
              'is_saved' => false,
              'user_id' => $user->id,
              'recruitment_id' => $id
            ]);

            return response()->json([
              'status' => 1,
              'code' => 200,
              'message' => 'Successfully applied.',
              'data' => $new_application
            ], 200);
          }
          // } else {
          //     return response()->json([
          //         'status' => 0,
          //         'code' => 405,
          //         'message' => 'You cannot take this action because your job has been opened.'
          //     ], 405);
          // }
        } else {
          return response()->json([
            'status' => 1,
            'code' => 404,
            'message' => 'The recruitment doesn\'t exist or closed.'
          ], 404);
        }
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your student profile has not been created or your open job option is on.',
          'data' => $user
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

  public function saveJob($id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $s_profile = StudentProfile::where('user_id', $user->id)->first();
      $exist_recruitment = Recruitment::whereId($id)->first();

      if (isset($s_profile) && isset($exist_recruitment)) {

        $exist_application = Application::where('recruitment_id', $id)->first();

        if (isset($exist_application)) {

          $exist_application->update([
            'is_saved' => !($exist_application->is_saved)
          ]);

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully saved/unsave job.',
            'data' => $exist_application
          ], 200);
          
        } else {
          $new_application = Application::create([
            'state' => null,
            'is_invited' => false,
            'is_applied' => false,
            'is_saved' => true,
            'user_id' => $user->id,
            'recruitment_id' => $id
          ]);

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully saved (created).',
            'data' => $new_application
          ], 200);
        }
      } else {
        return response()->json([
          'status' => 1,
          'code' => 404,
          'message' => 'The recruitment or your student profile doesn\'t exist.'
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
