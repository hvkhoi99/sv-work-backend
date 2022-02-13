<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiChangePasswordRequest;
use App\Http\Requests\ApiLoginRequest;
use App\Http\Requests\ApiRegisterRequest;
use App\Models\RecruiterProfile;
use App\Models\StudentProfile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  public function register(ApiRegisterRequest $request)
  {
    $user = User::where('email', $request['email'])->first();

    if (isset($user)) {
      if ($user->hasVerifiedEmail()) {
        return response()->json([
          "status" => 0,
          "code" => 400,
          "message" => "This email address is already registered."
        ], 200);
      }

      $user->sendEmailVerificationNotification();

      return response()->json([
        'status' => 0,
        'code' => 409,
        'message' => 'This email is registered but not verified. Please verify your email to continue.'
      ], 200);
    } else {
      $role_id = $request['role_id'];
      switch ($role_id) {
        case 2:
          $name = 'Recruiter';
          break;
        default:
          $name = 'Student';
          break;
      }
      $user = User::create([
        'name' => $name,
        'email' => $request['email'],
        'password' => bcrypt($request['password']),
        'role_id' => $role_id
      ]);

      if (isset($user)) {
        $user->sendEmailVerificationNotification();

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => "You have successfully registered an account.",
          'data' => $user,
        ], 200);
      }
    }
  }

  public function login(ApiLoginRequest $request)
  {
    $login = $request->only('email', 'password');

    if (Auth::attempt($login)) {
      $user = User::whereEmail($request['email'])->first();

      if (!$user->hasVerifiedEmail()) {
        return response()->json([
          "status" => 0,
          "code" => 400,
          "message" => "Email or Password is incorrect."
        ], 400);
      }

      $user->token = $user->createToken('App')->accessToken;

      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
      $s_profile = StudentProfile::where('user_id', $user->id)->first();

      $user["r_profile"] = isset($r_profile) ? $r_profile : null;
      $user->role_id === 3 && $user["s_profile"] = isset($s_profile) ? $s_profile : null;

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $user
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Email or Password is incorrect.'
      ], 200);
    }
  }

  public function account(Request $request)
  {
    $user = $request->user('api');
    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $user
    ], 200);
  }

  public function showError()
  {
    return response()->json([
      'error' => 'Access is not allowed.'
    ], 403);
  }

  public function changePassword(ApiChangePasswordRequest $request)
  {
    $user = $request->user('api');

    if (isset($user)) {

      if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
          'status' => 0,
          'code' => 400,
          'message' => 'Current password does not match!'
        ], 400);
      }

      $user->update([
        'password' => Hash::make($request->password)
      ]);

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $user
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 401,
        'message' => 'Unauthenticated.'
      ], 401);
    }
  }

  // Google vs Facebook
  public function login_google(Request $request)
  {
    return $this->check_google($request->social_token);
  }

  public function login_facebook(Request $request)
  {
    return $this->checkFacebook($request->social_token);
  }

  public function check_google($social_token)
  {
    try {
      $verifiedIdToken = $this->auth->verifyIdToken($social_token);
      $uid = $verifiedIdToken->getClaim('sub');
      return $this->check_user_UID($uid);
    } catch (\Exception $e) {
      return response()->json(['message' => $e->getMessage()], 401);
    }
  }

  public function checkFacebook($social_token)
  {
    try {
      $verifiedIdToken = $this->auth->verifyIdToken($social_token);
      $uid = $verifiedIdToken->getClaim('sub');
      return $this->check_user_UID($uid);
    } catch (\Exception $e) {
      return response()->json(['message' => $e->getMessage()]);
    }
  }

  private function check_user_UID($uid)
  {
    $user = User::where('firebaseUID', $uid)->first();
    if (!$user) {
      $user_information = $this->auth->getUser($uid);
      // dd($user_information);
      if (!$this->is_exists_email($user_information->email)) {
        $user = User::create([
          'name' => $user_information->displayName,
          'email' => $user_information->email,
          'signin_method' => $user_information->providerData[0]->providerId,
          'phone_number' => $user_information->phoneNumber,
          'firebaseUID' => $user_information->uid,
          'avatar_url' => $user_information->photoUrl,
        ]);
      } else {
        return response()->json(['message' => 'Email already exists'], 401);
      }
    }
    $token = $user->createToken('Personal Access Client')->accessToken;
    return response()->json([
      'user' => $user,
      'access_token' => $token,
      'token_type' => 'Bearer'
    ]);
  }

  private function is_exists_email($email)
  {
    $user = User::where('email', $email)->first();
    if ($user) return true;
    return false;
  }
}
