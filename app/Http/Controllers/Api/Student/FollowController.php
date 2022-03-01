<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Jobs\PushNotificationJob;
use App\Models\Follow;
use App\Models\Message;
use App\Models\RecruiterProfile;
use App\Models\StudentProfile;
use App\Models\UserMessage;
use App\User;
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

        if ($exist_follow->is_followed) {
          // create new job notification
          $title = 'Follow Company.';
          $body = [
            'student_info' => $s_profile->only([
              'id', 'first_name', 'last_name', 'gender', 'avatar_link', 'user_id'
            ]),
            'updated_at' => $exist_follow->updated_at
          ];
          // Message (Notification)
          $new_notification = Message::create([
            'title' => $title,
            'body' => json_encode($body),
            'type' => 'follow-company',
            'link' => $s_profile->avatar_link,
          ]);

          // Message_user
          $user_messages_id = 0;
          if (isset($new_notification)) {
            $user_messages = UserMessage::create([
              'message_id' => $new_notification->id,
              's_profile_id' => null,
              'r_profile_id' => $r_profile->id,
              'admin_id' => null,
              'is_read' => false
            ]);

            if (isset($user_messages)) {
              $user_messages_id = $user_messages->id;
            }
          }

          // push notification
          $deviceTokens = User::whereNotNull('device_token')->whereId($r_profile->user_id)->pluck('device_token')->all();
          if (isset($deviceTokens)) {
            $title = 'Follow Company.';
            $body = [
              'student_info' => $s_profile->only([
                'id', 'first_name', 'last_name', 'gender', 'avatar_link', 'user_id'
              ]),
              'type' => 'follow-company',
              'is_read' => false,
              'updated_at' => $exist_follow->updated_at,
              'user_messages_id' => $user_messages_id
            ];

            PushNotificationJob::dispatch('sendBatchNotification', [
              $deviceTokens,
              [
                'topicName' => 'follow-company',
                'title' => $title,
                'body' => $body,
                'image' => $s_profile->avatar_link,
              ],
            ]);
          }
        }

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

        if ( isset($new_follow) && $new_follow->is_followed) {
          // create new job notification
          $title = 'Follow Company.';
          $body = [
            'student_info' => $s_profile->only([
              'id', 'first_name', 'last_name', 'gender', 'avatar_link', 'user_id'
            ]),
            'updated_at' => $new_follow->updated_at
          ];
          // Message (Notification)
          $new_notification = Message::create([
            'title' => $title,
            'body' => json_encode($body),
            'type' => 'follow-company',
            'link' => $s_profile->avatar_link,
          ]);

          // Message_user
          $user_messages_id = 0;
          if (isset($new_notification)) {
            $user_messages = UserMessage::create([
              'message_id' => $new_notification->id,
              's_profile_id' => null,
              'r_profile_id' => $r_profile->id,
              'admin_id' => null,
              'is_read' => false
            ]);

            if (isset($user_messages)) {
              $user_messages_id = $user_messages->id;
            }
          }

          // push notification
          $deviceTokens = User::whereNotNull('device_token')->whereId($r_profile->user_id)->pluck('device_token')->all();
          if (isset($deviceTokens)) {
            $title = 'Follow Company.';
            $body = [
              'student_info' => $s_profile->only([
                'id', 'first_name', 'last_name', 'gender', 'avatar_link', 'user_id'
              ]),
              'type' => 'follow-company',
              'is_read' => false,
              'updated_at' => $new_follow->updated_at,
              'user_messages_id' => $user_messages_id
            ];

            PushNotificationJob::dispatch('sendBatchNotification', [
              $deviceTokens,
              [
                'topicName' => 'follow-company',
                'title' => $title,
                'body' => $body,
                'image' => $s_profile->avatar_link,
              ],
            ]);
          }
        }

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
