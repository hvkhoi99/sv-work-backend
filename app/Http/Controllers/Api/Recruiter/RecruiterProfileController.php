<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRecruiterProfileRequest;
use App\Http\Requests\ApiStudentAvatarRequest;
use App\Jobs\PushNotificationJob;
use App\Models\Follow;
use App\Models\Message;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\Models\StudentProfile;
use App\Models\UserMessage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecruiterProfileController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
    if (isset($r_profile)) {
      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $r_profile
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile was not found or has not been created.',
      ], 404);
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(ApiRecruiterProfileRequest $request)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
    $s_profile = StudentProfile::where('user_id', $user->id)->first();
    $user->role_id === 3 && $user["s_profile"] = isset($s_profile) ? $s_profile : null;

    if (isset($r_profile)) {
      $user["r_profile"] = $r_profile;

      return response()->json([
        'status' => 1,
        'code' => 200,
        'message' => 'Your recruiter profile already exists.',
        'data' => $user
      ], 200);
    } else {
      $new_r_profile = RecruiterProfile::create([
        'contact_email' => $request['contact_email'],
        'company_name' => $request['company_name'],
        'logo_image_link' => "https://res.cloudinary.com/dakhi21gx/image/upload/v1644765118/company-avatar.png",
        // 'description_image_link' => $request['description_image_link'],
        'description' => $request['description'],
        'phone_number' => $request['phone_number'],
        'verify' => null,
        'address' => $request['address'],
        'company_size' => $request['company_size'],
        'company_industry' => $request['company_industry'],
        'tax_code' => $request['tax_code'],
        'user_id' => $user->id
      ]);

      if (isset($new_r_profile)) {
        $user["r_profile"] = $new_r_profile;
        // create new job notification
        $title = 'Employer sends a request to verify company profile.';
        $body = [
          'company_info' => $new_r_profile->only([
            'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
          ]),
          'updated_at' => $new_r_profile->updated_at
        ];
        // Message (Notification)
        $new_notification = Message::create([
          'title' => $title,
          'body' => json_encode($body),
          'type' => 'verify-company-profile',
          'link' => $new_r_profile->logo_image_link,
        ]);

        // Message_user
        $user_messages_id = 0;
        if (isset($new_notification)) {
          $user_messages = UserMessage::create([
            'message_id' => $new_notification->id,
            's_profile_id' => null,
            'r_profile_id' => null,
            'admin_id' => 1,
            'is_read' => false
          ]);

          if (isset($user_messages)) {
            $user_messages_id = $user_messages->id;
          }
        }

        // push notification
        $deviceTokens = User::whereNotNull('device_token')->where('role_id', 1)->pluck('device_token')->all();
        if (isset($deviceTokens)) {
          $title = 'Employer sends a request to verify company profile.';
          $body = [
            'company_info' => $new_r_profile->only([
              'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
            ]),
            'type' => 'verify-company-profile',
            'is_read' => false,
            'updated_at' => $new_r_profile->updated_at,
            'user_messages_id' => $user_messages_id
          ];

          PushNotificationJob::dispatch('sendBatchNotification', [
            $deviceTokens,
            [
              'topicName' => 'verify-company-profile',
              'title' => $title,
              'body' => $body,
              'image' => $new_r_profile->logo_image_link,
            ],
          ]);
        }
      }

      return response()->json([
        'status' => 1,
        'code' => 200,
        'message' => 'Your recruiter profile was successfully created.',
        'data' => $user
      ], 200);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    $user = Auth::user();
    $s_profile = StudentProfile::where('user_id', $user->id)->first();
    $r_profile = RecruiterProfile::where('id', $id)->first();
    if (isset($r_profile)) {

      if (isset($s_profile)) {
        $follow = Follow::where('s_profile_id', $s_profile->id)->where('r_profile_id', $id)->first();
        if (isset($follow)) {
          $r_profile['is_followed'] = $follow->is_followed;
        } else {
          $r_profile['is_followed'] = false;
        }
      } else {
        $r_profile['is_followed'] = false;
      }

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $r_profile
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile was not found or has not been created.'
      ], 404);
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(ApiRecruiterProfileRequest $request, $id)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
    $s_profile = StudentProfile::where('user_id', $user->id)->first();
    $user->role_id === 3 && $user["s_profile"] = isset($s_profile) ? $s_profile : null;

    if (isset($r_profile)) {
      $verify = $request["verify"];

      if ($verify) {
        return response()->json([
          'status' => 0,
          'code' => 400,
          'message' => 'You cannot verify your own profile. Please try again later.'
        ], 400);
      } else {
        $last_tax_code = $r_profile->tax_code;
        $user["r_profile"] = $r_profile;

        if ($request['tax_code'] !== $last_tax_code) {
          $r_profile->update([
            'contact_email' => $request['contact_email'],
            'company_name' => $request['company_name'],
            'phone_number' => $request['phone_number'],
            'verify' => null,
            'address' => $request['address'],
            'company_size' => $request['company_size'],
            'company_industry' => $request['company_industry'],
            'tax_code' => $request['tax_code'],
          ]);

          // create new job notification
          $title = 'Employer sends a request to verify company profile.';
          $body = [
            'company_info' => $r_profile->only([
              'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
            ]),
            'updated_at' => $r_profile->updated_at
          ];
          // Message (Notification)
          $new_notification = Message::create([
            'title' => $title,
            'body' => json_encode($body),
            'type' => 'verify-company-profile',
            'link' => $r_profile->logo_image_link,
          ]);

          // Message_user
          $user_messages_id = 0;
          if (isset($new_notification)) {
            $user_messages = UserMessage::create([
              'message_id' => $new_notification->id,
              's_profile_id' => null,
              'r_profile_id' => null,
              'admin_id' => 1,
              'is_read' => false
            ]);

            if (isset($user_messages)) {
              $user_messages_id = $user_messages->id;
            }
          }

          // push notification
          $deviceTokens = User::whereNotNull('device_token')->where([
            // ['id', '!=', $r_profile->user_id],
            ['role_id', 1]
          ])->pluck('device_token')->all();
          if (isset($deviceTokens)) {
            $title = 'Employer sends a request to verify company profile.';
            $body = [
              'company_info' => $r_profile->only([
                'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
              ]),
              'type' => 'verify-company-profile',
              'is_read' => false,
              'updated_at' => $r_profile->updated_at,
              'user_messages_id' => $user_messages_id
            ];

            PushNotificationJob::dispatch('sendBatchNotification', [
              $deviceTokens,
              [
                'topicName' => 'verify-company-profile',
                'title' => $title,
                'body' => $body,
                'image' => $r_profile->logo_image_link,
              ],
            ]);
          }
        } else {
          $r_profile->update($request->all());
        }

        return response()->json([
          'status' => 1,
          'code' => 200,
          'mesage' => 'Your recruiter profile was successfully updated.',
          'data' => $user
        ], 200);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile was not found or has not been created.'
      ], 404);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }

  public function updateDescription(Request $request, $id)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
    $s_profile = StudentProfile::where('user_id', $user->id)->first();
    $user["s_profile"] = isset($s_profile) && $user->role_id === 3 ? $s_profile : null;

    if (isset($r_profile)) {
      // $r_profile->update([
      //   'contact_email' => $r_profile->contact_email,
      //   'company_name' => $r_profile->company_name,
      //   'logo_image_link' => $r_profile->logo_image_link,
      //   'phone_number' => $r_profile->phone_number,
      //   'verify' => $r_profile->verify,
      //   'address' => $r_profile->address,
      //   'company_size' => $r_profile->company_size,
      //   'company_industry' => $r_profile->company_industry,
      //   'tax_code' => $r_profile->tax_code,
      //   'description' => $request['description']
      // ]);
      $r_profile->update($request->all());

      $user["r_profile"] = $r_profile;

      return response()->json([
        'status' => 1,
        'code' => 200,
        'mesage' => 'Your recruiter profile was successfully updated.',
        'data' => $user
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile was not found or has not been created.'
      ], 404);
    }
  }

  public function changeRecruiterAvatar(ApiStudentAvatarRequest $request)
  {
    $user = Auth::user();
    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $response = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();

      $r_profile->update([
        'logo_image_link' => $response
      ]);

      // create new job notification
      $title = 'Employer updated avatar profile.';
      $body = [
        'company_info' => $r_profile->only([
          'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
        ]),
        'updated_at' => $r_profile->updated_at
      ];
      // Message (Notification)
      $new_notification = Message::create([
        'title' => $title,
        'body' => json_encode($body),
        'type' => 'update-avatar',
        'link' => $r_profile->logo_image_link,
      ]);

      // Message_user
      $list_students = DB::table('student_profiles')
        ->join('follows', 'student_profiles.id', '=', 'follows.s_profile_id')
        ->select(
          'student_profiles.id as s_profile_id',
          'student_profiles.user_id',
          'follows.r_profile_id'
        )
        ->where('r_profile_id', $r_profile->id)
        ->get()
        ->toArray();

      $list_id = array_values(array_unique(array_column($list_students, 's_profile_id')));
      if (isset($new_notification)) {
        foreach ($list_id as $id) {
          UserMessage::create([
            'message_id' => $new_notification->id,
            's_profile_id' => $id,
            'r_profile_id' => null,
            'admin_id' => null,
            'is_read' => false
          ]);
        }
      }

      $list_user_id = array_values(array_unique(array_column($list_students, 'user_id')));
      // push notification
      $deviceTokens = User::whereNotNull('device_token')->whereIn('id', $list_user_id)->pluck('device_token')->all();
      if (isset($deviceTokens)) {
        $title = 'Employer updated avatar profile.';
        $body = [
          'company_info' => $r_profile->only([
            'id', 'company_name', 'verify', 'logo_image_link', 'user_id'
          ]),
          'type' => 'update-avatar',
          'is_read' => false,
          'updated_at' => $r_profile->updated_at
        ];

        PushNotificationJob::dispatch('sendBatchNotification', [
          $deviceTokens,
          [
            'topicName' => 'update-avatar',
            'title' => $title,
            'body' => $body,
            'image' => $r_profile->logo_image_link,
          ],
        ]);
      }

      return response()->json([
        'status' => 1,
        'code' => 200,
        'mesage' => 'Your recruiter profile (avatar) was successfully updated.',
        'data' => $r_profile
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile has not been created.'
      ], 404);
    }
  }

  public function checkVerified(Request $request)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {

      return response()->json([
        'status' => 1,
        'code' => 200,
        // 'data' => isset($r_profile->verify) ? $r_profile->verify : false,
        'data' => $r_profile->verify
      ], 200);
    } else {

      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile has not been created.'
      ], 200);
    }
  }
}
