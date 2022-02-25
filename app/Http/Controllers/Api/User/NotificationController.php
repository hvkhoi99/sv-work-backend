<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\RecruiterProfile;
use App\Models\StudentProfile;
use App\Models\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
  public function getRecruiterCountNotifications() {
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

  public function getStudentCountNotifications() {
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
}
