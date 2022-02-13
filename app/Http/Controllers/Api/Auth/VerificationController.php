<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
  public function verify($user_id, Request $request)
  {
    if (!$request->hasValidSignature()) {
      return response()->json(["msg" => "Invalid/Expired url provided."], 401);
    }

    $user = User::findOrFail($user_id);

    if (!$user->hasVerifiedEmail()) {
      $user->markEmailAsVerified();
    }

    return redirect()->to("http://localhost:3000/auth/sign-in");
  }

  public function resend(Request $request)
  {
    $user = User::where('email', $request['email'])->first();
    if ($user->hasVerifiedEmail()) {
      return response()->json([
        "status" => 0,
        "code" => 400,
        "message" => "Email already verified."
      ], 200);
    }

    $user->sendEmailVerificationNotification();

    return response()->json([
      "status" => 1,
      "code" => 200,
      "message" => "Successfully resend the email verification message.",
    ], 200);
  }
}
