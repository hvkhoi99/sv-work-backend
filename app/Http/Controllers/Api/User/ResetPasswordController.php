<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiResetPasswordRequest;
use App\Models\PasswordReset;
use App\Notifications\ResetPasswordRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use App\User;
use Exception;

class ResetPasswordController extends Controller
{
  const VALID_TOKEN = 60; // 60 minutes

  public function sendMail(Request $request)
  {
    try {
      $user = User::where([
        ['email', $request->email],
        ['signin_method', null]
      ])->first();
      
      if (isset($user)) {
        $passwordReset = PasswordReset::updateOrCreate([
          'email' => $user->email,
        ], [
          'token' => Str::random(60),
        ]);
  
        if ($passwordReset) {
          $user->notify(new ResetPasswordRequest($passwordReset->token));
        }
  
        return response()->json([
          'status' => true,
          'message' => __('We have e - mailed your password reset link!')
        ]);
      } else {
        return response()->json([
          'status' => false,
          'message' => __('Email doesn\'t exist. Please enter another email.')
        ]);
      }
    } catch (Exception $exception) {
      return [
        'status' => false,
        'message' => __('something went wrong!')
      ];
    }
  }

  public function resetPassword(Request $request, $token)
  {
    $passwordReset = PasswordReset::where('token', $token)->first();

    if (!$passwordReset) {
      return response()->json([
        'message' => __('The token is invalid.')
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    if (Carbon::parse($passwordReset->updated_at)->addMinutes(self::VALID_TOKEN)->isPast()) {
      $passwordReset->delete();
      return response()->json([
        'message' => 'The token is invalid.',
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $user = User::where('email', $passwordReset->email)->firstOrFail();
    $user->password = bcrypt($request->get('new_password'));
    $user->save();
    $passwordReset->delete();

    return response()->json([
      'status' => true,
      'message' => __('Password reset successful.')
    ]);
  }
}
