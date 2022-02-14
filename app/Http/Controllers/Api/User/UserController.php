<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiChangePasswordRequest;
use App\Http\Requests\ApiLoginRequest;
use App\Http\Requests\ApiRegisterRequest;
use App\Models\RecruiterProfile;
use App\Models\StudentProfile;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  public function __construct()
  {
    $this->auth = app('firebase.auth');
    // $this->middleware('auth:api', ['except' => ['login', 'login_google', 'login_facebook']]);
  }

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
    return $this->check_google($request);
  }

  public function login_facebook(Request $request)
  {
    return $this->checkFacebook($request);
  }

  public function check_google(Request $request)
  {
    try {
      $verifiedIdToken = $this->auth->verifyIdToken($request->social_token);
      $uid = $verifiedIdToken->claims()->get('sub');
      return $this->check_user_UID($request, $uid);
      // return $uid;
    } catch (\Exception $e) {
      return response()->json(['message' => $e->getMessage()], 401);
    }
  }

  public function checkFacebook(Request $request)
  {
    try {
      $verifiedIdToken = $this->auth->verifyIdToken($request->social_token);
      $uid = $verifiedIdToken->claims()->get('sub');
      return $this->check_user_UID($request, $uid);
    } catch (\Exception $e) {
      return response()->json(['message' => $e->getMessage()]);
    }
  }

  private function check_user_UID(Request $request, $uid)
  {
    $user = User::where('firebaseUID', $uid)->first();
    if (!$user) {
      $user_information = $this->auth->getUser($uid);
      // dd($user_information);
      if (!$this->is_exists_email($user_information->email)) {
        $role_id = $request['role_id'];
        $new_user = User::create([
          'name' => $user_information->displayName,
          'email' => $user_information->email,
          'signin_method' => $user_information->providerData[0]->providerId,
          'firebaseUID' => $user_information->uid,
          'role_id' => $role_id,
        ]);
        switch ($role_id) {
          case 2:
            $r_profile = RecruiterProfile::create([
              'contact_email' => $user_information->email,
              'company_name' => $user_information->displayName,
              'logo_image_link' => $user_information->photoUrl,
              'phone_number' => $user_information->phoneNumber,
              'description' => "",
              'address' => "",
              'company_size' => 0,
              'company_industry' => "",
              'tax_code' => "",
              'verify' => null,
              'user_id' => $new_user->id
            ]);
            $new_user["r_profile"] = $r_profile;
            $new_user["s_profile"] = null;
            break;
          case 3:
            $s_profile = StudentProfile::create([
              'email' => $user_information->email,
              'last_name' => $user_information->displayName,
              'avatar_link' => $user_information->photoUrl,
              'phone_number' => $user_information->phoneNumber,
              'open_for_job' => false,
              'date_of_birth' => Carbon::now(),
              'nationality' => "",
              'address' => "",
              'gender' => null,
              'over_view' => "",
              'open_for_job' => false,
              'job_title' => "",
              'user_id' => $new_user->id
            ]);
            $new_user["s_profile"] = $s_profile;
            $new_user["r_profile"] = null;
            break;
          default:
            break;
        }

        $new_user->token = $new_user->createToken('Personal Access Client')->accessToken;

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully signed in with Google account. (Email successfully registered)',
          'data' => $new_user,
          // 'access_token' => $token,
          // 'token_type' => 'Bearer'
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 400,
          'message' => 'Email already exists.'
        ], 200);
      }
    }
    $user->token = $user->createToken('Personal Access Client')->accessToken;

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
    $s_profile = StudentProfile::where('user_id', $user->id)->first();

    $user["r_profile"] = isset($r_profile) ? $r_profile : null;
    $user->role_id === 3 && $user["s_profile"] = isset($s_profile) ? $s_profile : null;

    return response()->json([
      'status' => 1,
      'code' => 200,
      'message' => 'Successfully signed in with Google account. (Re-Login with exist email)',
      'data' => $user,
      // 'access_token' => $token,
      // 'token_type' => 'Bearer'
    ], 200);
  }

  private function is_exists_email($email)
  {
    $user = User::where('email', $email)->first();
    if ($user) return true;
    return false;
  }
}
