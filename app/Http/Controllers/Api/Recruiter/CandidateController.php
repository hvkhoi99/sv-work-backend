<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Jobs\PushNotificationJob;
use App\Models\Application;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Language;
use App\Models\Message;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\Models\Skill;
use App\Models\StudentProfile;
use App\Models\UserMessage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{
  public function candidateInfo($id)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $s_profile = StudentProfile::whereId($id)->first();

      if (isset($s_profile)) {
        $applied_applications = Application::where([
          // ['state', null],
          ['is_applied', true],
          ['user_id', $s_profile->user_id]
        ])->orderBy('created_at', 'desc')->get();

        $applied_jobs = [];
        foreach ($applied_applications as $application) {
          $recruitment = Recruitment::where([
            ['id', $application->recruitment_id],
            ['user_id', $user->id]
          ])->first();

          if (isset($recruitment)) {
            $recruitment = collect($recruitment)
              ->only(['id', 'title', 'is_closed']);
            $recruitment["application"] = $application;

            array_push($applied_jobs, $recruitment);
          }
        }
        $s_profile["applied_jobs"] = $applied_jobs;

        $invited_applications = Application::where([
          // ['state', null],
          ['is_invited', true],
          ['user_id', $s_profile->user_id]
        ])->orderBy('created_at', 'desc')->get();

        $invited_jobs = [];
        foreach ($invited_applications as $application) {
          $recruitment = Recruitment::where([
            ['id', $application->recruitment_id],
            ['user_id', $user->id]
          ])->first();

          if (isset($recruitment)) {
            $recruitment = collect($recruitment)
              ->only(['id', 'title', 'is_closed']);
            $recruitment["application"] = $application;

            array_push($invited_jobs, $recruitment);
          }
        }
        $s_profile["invited_jobs"] = $invited_jobs;

        $skills = Skill::where('user_id', $s_profile->user_id)->first();
        $languages = Language::where('user_id', $s_profile->user_id)->first();
        $experiences = Experience::where('user_id', $s_profile->user_id)->orderBy('created_at', 'desc')->get();
        $educations = Education::where('user_id', $s_profile->user_id)->orderBy('created_at', 'desc')->get();
        $certificates = Certificate::where('user_id', $s_profile->user_id)->orderBy('created_at', 'desc')->get();
        $s_profile["skills"] = isset($skills) ? json_decode($skills->skills) : null;
        $s_profile["languages"] = isset($languages) ? json_decode($languages->locales) : null;
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
        ], 400);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile has not been created.'
      ], 400);
    }
  }

  public function approve($recruitment_id, $candidate_id)
  {
    $user = Auth::user();

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
            'state' => true
          ]);

          // create new job notification
          $title = 'Application approved.';
          $body = [
            'job' => (object) [
              'id' => $exist_recruitment->id,
              'title' => $exist_recruitment->title,
              'user_id' => $user->id
            ],
            'company_info' => $r_profile->only([
              'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
            ]),
            'updated_at' => $exist_application->updated_at
          ];
          // Message (Notification)
          $new_notification = Message::create([
            'title' => $title,
            'body' => json_encode($body),
            'type' => 'approved-application',
            'link' => $r_profile->logo_image_link,
          ]);

          // Message_user
          if (isset($new_notification)) {
            UserMessage::create([
              'message_id' => $new_notification->id,
              's_profile_id' => $s_profile->id,
              'r_profile_id' => null,
              'is_read' => false
            ]);
          }

          // push notification
          $deviceTokens = User::whereNotNull('device_token')->whereId($s_profile->user_id)->pluck('device_token')->all();
          if (isset($deviceTokens)) {
            $title = 'Application approved.';
            $body = [
              'job' => (object) [
                'id' => $exist_recruitment->id,
                'title' => $exist_recruitment->title,
                'user_id' => $user->id
              ],
              'company_info' => $r_profile->only([
                'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
              ]),
              'type' => 'approved-application',
              'is_read' => false,
              'updated_at' => $exist_application->updated_at
            ];

            PushNotificationJob::dispatch('sendBatchNotification', [
              $deviceTokens,
              [
                'topicName' => 'approved-application',
                'title' => $title,
                'body' => $body,
                'image' => $r_profile->logo_image_link,
              ],
            ]);
          }

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => $exist_application->state
              ? 'This application has been successfully approved.'
              : 'This application has been successfully rejected.',
            'data' => $exist_application
          ], 200);
        } else {
          return response()->json([
            'status' => 0,
            'code' => 404,
            'message' => 'No applications found.'
          ], 400);
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
  }

  public function reject($recruitment_id, $candidate_id)
  {
    $user = Auth::user();

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
            'state' => false
          ]);

          // create new job notification
          $title = 'Application rejected.';
          $body = [
            'job' => (object) [
              'id' => $exist_recruitment->id,
              'title' => $exist_recruitment->title,
              'user_id' => $user->id
            ],
            'company_info' => $r_profile->only([
              'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
            ]),
            'updated_at' => $exist_application->updated_at
          ];
          // Message (Notification)
          $new_notification = Message::create([
            'title' => $title,
            'body' => json_encode($body),
            'type' => 'rejected-application',
            'link' => $r_profile->logo_image_link,
          ]);

          // Message_user
          if (isset($new_notification)) {
            UserMessage::create([
              'message_id' => $new_notification->id,
              's_profile_id' => $s_profile->id,
              'r_profile_id' => null,
              'is_read' => false
            ]);
          }

          // push notification
          $deviceTokens = User::whereNotNull('device_token')->whereId($s_profile->user_id)->pluck('device_token')->all();
          if (isset($deviceTokens)) {
            $title = 'Application rejected.';
            $body = [
              'job' => (object) [
                'id' => $exist_recruitment->id,
                'title' => $exist_recruitment->title,
                'user_id' => $user->id
              ],
              'company_info' => $r_profile->only([
                'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
              ]),
              'type' => 'rejected-application',
              'is_read' => false,
              'updated_at' => $exist_application->updated_at
            ];

            PushNotificationJob::dispatch('sendBatchNotification', [
              $deviceTokens,
              [
                'topicName' => 'rejected-application',
                'title' => $title,
                'body' => $body,
                'image' => $r_profile->logo_image_link,
              ],
            ]);
          }

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => $exist_application->state
              ? 'This application has been successfully approved.'
              : 'This application has been successfully rejected.',
            'data' => $exist_application
          ], 200);
        } else {
          return response()->json([
            'status' => 0,
            'code' => 404,
            'message' => 'No applications found.'
          ], 400);
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
  }

  public function jobsInvite()
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $recruitments = Recruitment::where([
        ['is_closed', false],
        ['user_id', $user->id]
      ])->selectRaw('id as value, title as label')->orderBy('created_at', 'desc')->get(['value', 'label']);

      if (isset($recruitments)) {

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $recruitments
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your recruitments doesn\'t exist.'
        ], 404);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile has not been created.'
      ], 404);
    }
  }

  public function getListCandidates(Request $request)
  {
    $user = Auth::user();
    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $candidates = StudentProfile::orderBy('created_at', 'desc')->get(
        [
          'id', 'avatar_link', 'first_name', 'last_name', 'job_title',
          'address', 'gender', 'user_id', 'created_at',
        ]
      )->toArray();

      $candidates = array_map(function ($candidate) {
        $skills = Skill::where('user_id', $candidate['user_id'])->first();
        if (isset($skills)) {
          $candidate['skills'] = json_decode($skills['skills']);
        } else {
          $candidate['skills'] = [];
        }
        return $candidate;
      }, $candidates);

      $perPage = $request["_limit"];
      $current_page = LengthAwarePaginator::resolveCurrentPage();

      $candidates = new LengthAwarePaginator(
        collect($candidates)->forPage($current_page, $perPage)->values(),
        count($candidates),
        $perPage,
        $current_page,
        ['path' => url(
          $user->role_id === 2
            ? 'api/recruiter/candidates/list'
            : 'api/student/recruiter/candidates/list'
        )]
      );

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $candidates
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruitments doesn\'t exist.'
      ], 200);
    }
  }
}
