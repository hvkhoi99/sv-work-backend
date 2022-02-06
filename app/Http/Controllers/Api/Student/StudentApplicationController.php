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

        if (isset($exist_recruitment)) {
          $exist_application = Application::where([
            ['user_id', $user->id],
            ['recruitment_id', $id]
          ])->first();

          if (isset($exist_application)) {
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
          return response()->json([
            'status' => 0,
            'code' => 404,
            'message' => 'The recruitment doesn\'t exist.'
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
        $exist_application = Application::where([
          ['user_id', $user->id],
          ['recruitment_id', $id]
        ])->first();

        if (isset($exist_application)) {
          $exist_application->update([
            'is_saved' => !($exist_application->is_saved)
          ]);

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully ' . ($exist_application->is_saved ? 'saved' : 'un-save') . ' job.',
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
          'status' => 0,
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

  public function acceptInvitedJob($id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $s_profile = StudentProfile::where('user_id', $user->id)->first();

      if (isset($s_profile)) {
        $exist_recruitment = Recruitment::whereId($id)->first();

        if (isset($exist_recruitment)) {
          $exist_application = Application::where([
            ['user_id', $user->id],
            ['recruitment_id', $id]
          ])->first();

          if (isset($exist_application)) {
            $exist_application->update([
              'state' => true
            ]);

            return response()->json([
              'status' => 1,
              'code' => 200,
              'message' => 'Successfully accepted job offer.',
              'data' => $exist_application
            ], 200);
          } else {
            return response()->json([
              'status' => 0,
              'code' => 404,
              'message' => 'The application doesn\'t exist.'
            ], 404);
          }
        } else {
          return response()->json([
            'status' => 0,
            'code' => 404,
            'message' => 'The recruitment doesn\'t exist.'
          ], 404);
        }
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your student profile doesn\'t exist.'
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

  public function rejectInvitedJob($id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $s_profile = StudentProfile::where('user_id', $user->id)->first();

      if (isset($s_profile)) {
        $exist_recruitment = Recruitment::whereId($id)->first();

        if (isset($exist_recruitment)) {
          $exist_application = Application::where([
            ['user_id', $user->id],
            ['recruitment_id', $id]
          ])->first();

          if (isset($exist_application)) {
            $exist_application->update([
              'state' => false
            ]);

            return response()->json([
              'status' => 1,
              'code' => 200,
              'message' => 'Successfully rejected job offer.',
              'data' => $exist_application
            ], 200);
          } else {
            return response()->json([
              'status' => 0,
              'code' => 404,
              'message' => 'The application doesn\'t exist.'
            ], 404);
          }
        } else {
          return response()->json([
            'status' => 0,
            'code' => 404,
            'message' => 'The recruitment doesn\'t exist.'
          ], 404);
        }
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your student profile doesn\'t exist.'
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

  public function inviteCandidate($recruitment_id, $candidate_id)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $recruitment = RecruiterProfile::where('user_id', $user->id)->where('is_closed', false)->first();
      $candidate_profile = StudentProfile::whereId($candidate_id)->first();

      if (isset($recruitment) && isset($candidate_profile)) {
        $application = Application::where([
          ['recruitment_id', $recruitment_id],
          ['user_id', $candidate_profile->user_id]
        ])->first();

        if (isset($application)) {
          // cap nhat application -> !invite

          if ($application->state === null) {
            $data = $application->update([
              $application->is_invited = !($application->is_invited)
            ]);
  
            return response()->json([
              'status' => 1,
              'code' => 200,
              'message' => 'Successfully ' . ($application->is_invited ? 'invited' : 'uninvited') . ' this candidate',
              'data' => $data
            ], 200);
          } else {
            return response()->json([
              'status' => 0,
              'code' => 400,
              'message' => 'This candidate did or is doing this job.'
            ], 200);
          }

        } else {
          // tao moi application -> invite = true
          $new_application = Application::create([
            'state' => null,
            'is_invited' => true,
            'is_applied' => false,
            'is_saved' => false,
            'user_id' => $candidate_profile->user_id,
            'recruitment_id' => $recruitment_id
          ]);

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully invited this candidate',
            'data' => $new_application
          ], 200);
        }
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your recruitment (or closed) or Candidate profile doesn\'t exist.'
        ], 404);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile doesn\'t exist.'
      ], 404);
    }
  }
}
