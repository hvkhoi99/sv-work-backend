<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Message;
use App\Models\RecruiterProfile;
use App\Models\StudentProfile;
use App\Models\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
  public function getRecruiterCountNotifications()
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
    if (isset($r_profile)) {
      $count_notifications = UserMessage::where([
        ['r_profile_id', $r_profile->id],
        ['is_read', false]
      ])->get()->count();

      return response()->json([
        'message' => 1,
        'code' => 200,
        'data' => $count_notifications
      ], 200);
    } else {
      return response()->json([
        'message' => 1,
        'code' => 200,
        'data' => 0
      ], 200);
    }
  }

  public function getStudentCountNotifications()
  {
    $user = Auth::user();

    $s_profile = StudentProfile::where('user_id', $user->id)->first();
    if (isset($s_profile)) {
      $count_notifications = UserMessage::where([
        ['s_profile_id', $s_profile->id],
        ['is_read', false]
      ])->get()->count();

      return response()->json([
        'message' => 1,
        'code' => 200,
        'data' => $count_notifications
      ], 200);
    } else {
      return response()->json([
        'message' => 1,
        'code' => 200,
        'data' => 0
      ], 200);
    }
  }

  public function getNotificationsByStudent(Request $request)
  {
    $user = Auth::user();

    $s_profile = StudentProfile::where('user_id', $user->id)->first();

    if (isset($s_profile)) {
      $notifications = DB::table('messages')
        ->join('user_messages', 'messages.id', '=', 'user_messages.message_id')
        ->select(
          'messages.id as message_id',
          'messages.type',
          'messages.body',
          'messages.title',
          'messages.link',
          'user_messages.id as user_messages_id',
          'user_messages.s_profile_id',
          'user_messages.is_read',
          'user_messages.updated_at',
          'user_messages.created_at'
        )
        ->where('s_profile_id', $s_profile->id)
        ->orderBy('created_at', 'desc')
        ->get();

      foreach ($notifications as $notification) {
        $notification->body = json_decode($notification->body);
        if ($notification->type === "invited-job") {
          $s_profile = StudentProfile::whereId($notification->s_profile_id)->first();
          if (isset($s_profile)) {
            $application = Application::where([
              'user_id' => $s_profile->user_id,
              'recruitment_id' => $notification->body->job->id
            ])->first();
            if (isset($application)) {
              $notification->is_replied = $application->state;
            } else {
              $notification->is_replied = null;
            }
          } else {
            $notification->is_replied = null;
          }
        }
      }

      $perPage = $request["_limit"];
      $current_page = LengthAwarePaginator::resolveCurrentPage();

      $notifications = new LengthAwarePaginator(
        collect($notifications)->forPage($current_page, $perPage)->values(),
        count($notifications),
        $perPage,
        $current_page,
        ['path' => url('api/student/notifications/list')]
      );

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $notifications
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your student profile has not been created.',
      ], 404);
    }
  }

  public function getUnreadNotificationsByStudent(Request $request)
  {
    $user = Auth::user();

    $s_profile = StudentProfile::where('user_id', $user->id)->first();

    if (isset($s_profile)) {
      $notifications = DB::table('messages')
        ->join('user_messages', 'messages.id', '=', 'user_messages.message_id')
        ->select(
          'messages.id as message_id',
          'messages.type',
          'messages.body',
          'messages.title',
          'messages.link',
          'user_messages.id as user_messages_id',
          'user_messages.s_profile_id',
          'user_messages.is_read',
          'user_messages.updated_at',
          'user_messages.created_at'
        )
        ->where([
          ['s_profile_id', $s_profile->id],
          ['is_read', false]
        ])
        ->orderBy('created_at', 'desc')
        ->get();

      foreach ($notifications as $notification) {
        $notification->body = json_decode($notification->body);
        if ($notification->type === "invited-job") {
          $s_profile = StudentProfile::whereId($notification->s_profile_id)->first();
          if (isset($s_profile)) {
            $application = Application::where([
              'user_id' => $s_profile->user_id,
              'recruitment_id' => $notification->body->job->id
            ])->first();
            if (isset($application)) {
              $notification->is_replied = $application->state;
            } else {
              $notification->is_replied = null;
            }
          } else {
            $notification->is_replied = null;
          }
        }
      }

      $perPage = $request["_limit"];
      $current_page = LengthAwarePaginator::resolveCurrentPage();

      $notifications = new LengthAwarePaginator(
        collect($notifications)->forPage($current_page, $perPage)->values(),
        count($notifications),
        $perPage,
        $current_page,
        ['path' => url('api/student/notifications/list-unread')]
      );

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $notifications
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your student profile has not been created.',
      ], 404);
    }
  }

  public function onMarkAsReadByStudent($id)
  {
    $user = Auth::user();

    $s_profile = StudentProfile::where('user_id', $user->id)->first();

    if (isset($s_profile)) {
      $user_messages = UserMessage::where([
        ['id', $id],
        ['s_profile_id', $s_profile->id]
      ])->first();

      $user_messages->update([
        'is_read' => !$user_messages->is_read
      ]);

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $user_messages
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Student profile has not been created.'
      ], 200);
    }
  }

  public function markAllAsReadByStudent()
  {
    $user = Auth::user();

    $s_profile = StudentProfile::where('user_id', $user->id)->first();

    if (isset($s_profile)) {
      $user_messages = UserMessage::where('s_profile_id', $s_profile->id)->update(['is_read' => true]);

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $user_messages
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Student profile has not been created.'
      ], 200);
    }
  }

  public function getNotificationsByRecruiter(Request $request)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $notifications = DB::table('messages')
        ->join('user_messages', 'messages.id', '=', 'user_messages.message_id')
        ->select(
          'messages.id as message_id',
          'messages.type',
          'messages.body',
          'messages.title',
          'messages.link',
          'user_messages.id as user_messages_id',
          'user_messages.r_profile_id',
          'user_messages.is_read',
          'user_messages.updated_at',
          'user_messages.created_at'
        )
        ->where('r_profile_id', $r_profile->id)
        ->orderBy('created_at', 'desc')
        ->get();

      foreach ($notifications as $notification) {
        $notification->body = json_decode($notification->body);
      }

      $perPage = $request["_limit"];
      $current_page = LengthAwarePaginator::resolveCurrentPage();

      $notifications = new LengthAwarePaginator(
        collect($notifications)->forPage($current_page, $perPage)->values(),
        count($notifications),
        $perPage,
        $current_page,
        ['path' => url(
          $user->role_id === 2
          ? 'api/recruiter/notifications/list'
          : 'api/student/recruiter/notifications/list'
        )]
      );

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $notifications
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile has not been created.',
      ], 404);
    }
  }

  public function getUnreadNotificationsByRecruiter(Request $request)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $notifications = DB::table('messages')
        ->join('user_messages', 'messages.id', '=', 'user_messages.message_id')
        ->select(
          'messages.id as message_id',
          'messages.type',
          'messages.body',
          'messages.title',
          'messages.link',
          'user_messages.id as user_messages_id',
          'user_messages.r_profile_id',
          'user_messages.is_read',
          'user_messages.updated_at',
          'user_messages.created_at'
        )
        ->where([
          ['r_profile_id', $r_profile->id],
          ['is_read', false]
        ])
        ->orderBy('created_at', 'desc')
        ->get();

      foreach ($notifications as $notification) {
        $notification->body = json_decode($notification->body);
      }

      $perPage = $request["_limit"];
      $current_page = LengthAwarePaginator::resolveCurrentPage();

      $notifications = new LengthAwarePaginator(
        collect($notifications)->forPage($current_page, $perPage)->values(),
        count($notifications),
        $perPage,
        $current_page,
        ['path' => url(
          $user->role_id === 2
          ? 'api/recruiter/notifications/list-unread'
          : 'api/student/recruiter/notifications/list-unread'
        )]
      );

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $notifications
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile has not been created.',
      ], 404);
    }
  }

  public function onMarkAsReadByRecruiter($id)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $user_messages = UserMessage::where([
        ['id', $id],
        ['r_profile_id', $r_profile->id]
      ])->first();

      $user_messages->update([
        'is_read' => !$user_messages->is_read
      ]);

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $user_messages
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Recruiter profile has not been created.'
      ], 200);
    }
  }

  public function markAllAsReadByRecruiter()
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $user_messages = UserMessage::where('r_profile_id', $r_profile->id)->update(['is_read' => true]);

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $user_messages
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Recruiter profile has not been created.'
      ], 200);
    }
  }
}
