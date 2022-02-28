<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Jobs\PushNotificationJob;
use App\Models\Application;
use App\Models\Message;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\Models\StudentProfile;
use App\Models\UserMessage;
use App\User;
use Illuminate\Support\Facades\Auth;

class StudentApplicationController extends Controller
{
  public function apply($id)
  {
    $user = Auth::user();

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
  }

  public function saveJob($id)
  {
    $user = Auth::user();

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
  }

  public function acceptInvitedJob($id)
  {
    $user = Auth::user();

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
  }

  public function rejectInvitedJob($id)
  {
    $user = Auth::user();

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
  }

  public function inviteCandidate($candidate_id, $recruitment_id)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $recruitment = Recruitment::where([
        ['id', $recruitment_id],
        ['user_id', $user->id]
      ])->where('is_closed', false)->first();
      $candidate_profile = StudentProfile::whereId($candidate_id)->first();

      if (isset($recruitment) && isset($candidate_profile)) {
        $application = Application::where([
          ['recruitment_id', $recruitment_id],
          ['user_id', $candidate_profile->user_id]
        ])->first();

        if (isset($application)) {
          // cap nhat application -> !invite

          if ($application->state === null) {
            $application->update([
              'is_invited' => !($application->is_invited)
            ]);
            $recruitment = collect($recruitment)->only(['id', 'title', 'is_closed']);
            $recruitment["application"] = $application;

            if ($application->is_invited) {
              // create new job notification
              $title = 'Invited to the job.';
              $body = [
                'job' => (object) [
                  'id' => $recruitment['id'],
                  'title' => $recruitment['title'],
                  'user_id' => $user->id
                ],
                'company_info' => $r_profile->only([
                  'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
                ]),
                'updated_at' => $application->updated_at
              ];
              // Message (Notification)
              $new_notification = Message::create([
                'title' => $title,
                'body' => json_encode($body),
                'type' => 'invited-job',
                'link' => $r_profile->logo_image_link,
              ]);

              // Message_user
              $user_messages_id = 0;
              if (isset($new_notification)) {
                $user_messages = UserMessage::create([
                  'message_id' => $new_notification->id,
                  's_profile_id' => $candidate_profile->id,
                  'r_profile_id' => null,
                  'is_read' => false
                ]);

                if (isset($user_messages)) {
                  $user_messages_id = $user_messages->id;
                }
              }

              // push notification
              $deviceTokens = User::whereNotNull('device_token')->whereId($candidate_profile->user_id)->pluck('device_token')->all();
              if (isset($deviceTokens)) {
                $title = 'Invited to the job.';
                $body = [
                  'job' => (object) [
                    'id' => $recruitment['id'],
                    'title' => $recruitment['title'],
                    'user_id' => $user->id
                  ],
                  'company_info' => $r_profile->only([
                    'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
                  ]),
                  'type' => 'invited-job',
                  'is_read' => false,
                  'updated_at' => $application->updated_at,
                  'user_messages_id' => $user_messages_id
                ];

                PushNotificationJob::dispatch('sendBatchNotification', [
                  $deviceTokens,
                  [
                    'topicName' => 'invited-job',
                    'title' => $title,
                    'body' => $body,
                    'image' => $r_profile->logo_image_link,
                  ],
                ]);
              }
            }

            return response()->json([
              'status' => 1,
              'code' => 200,
              'message' => 'Successfully ' . ($application->is_invited ? 'invited' : 'uninvited') . ' this candidate',
              'data' => $recruitment
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
          $recruitment = collect($recruitment)->only(['id', 'title', 'is_closed']);
          $recruitment["application"] = $new_application;

          if ($new_application->is_invited) {
            // create new job notification
            $title = 'Invited to the job.';
            $body = [
              'job' => (object) [
                'id' => $recruitment['id'],
                'title' => $recruitment['title'],
                'user_id' => $user->id
              ],
              'company_info' => $r_profile->only([
                'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
              ]),
              'updated_at' => $new_application->updated_at
            ];
            // Message (Notification)
            $new_notification = Message::create([
              'title' => $title,
              'body' => json_encode($body),
              'type' => 'invited-job',
              'link' => $r_profile->logo_image_link,
            ]);

            // Message_user
            $user_messages_id = 0;
            if (isset($new_notification)) {
              $user_messages = UserMessage::create([
                'message_id' => $new_notification->id,
                's_profile_id' => $candidate_profile->id,
                'r_profile_id' => null,
                'is_read' => false
              ]);

              if (isset($user_messages)) {
                $user_messages_id = $user_messages->id;
              }
            }

            // push notification
            $deviceTokens = User::whereNotNull('device_token')->whereId($candidate_profile->user_id)->pluck('device_token')->all();
            if (isset($deviceTokens)) {
              $title = 'Invited to the job.';
              $body = [
                'job' => (object) [
                  'id' => $recruitment['id'],
                  'title' => $recruitment['title'],
                  'user_id' => $user->id
                ],
                'company_info' => $r_profile->only([
                  'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
                ]),
                'type' => 'invited-job',
                'is_read' => false,
                'updated_at' => $new_application->updated_at,
                'user_messages_id' => $user_messages_id
              ];

              PushNotificationJob::dispatch('sendBatchNotification', [
                $deviceTokens,
                [
                  'topicName' => 'invited-job',
                  'title' => $title,
                  'body' => $body,
                  'image' => $r_profile->logo_image_link,
                ],
              ]);
            }
          }

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully invited this candidate',
            'data' => $recruitment
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
