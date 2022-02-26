<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
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
        'user_messages.s_profile_id',
        'user_messages.is_read',
        'user_messages.updated_at'
      )
      ->where('s_profile_id', $s_profile->id)
      ->orderBy('updated_at', 'desc')
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
}
