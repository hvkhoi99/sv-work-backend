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
            return response()->json([
                'status' => 0,
                'code' => 409,
                'message' => 'That email address is already registered. You sure you don\'t have an account?'
            ], 409);
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

            return response()->json([
                'status' => 1,
                'code' => 200,
                'data' => $user,
            ], 200);
        }
    }

    public function login(ApiLoginRequest $request)
    {
        $login = $request->only('email', 'password');

        if (Auth::attempt($login)) {
            $user = User::whereEmail($request['email'])->first();
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
                'code' => 401,
                'message' => 'Email or Password is incorrect.'
            ], 401);
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
        $user = Auth::user();

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
                'message' => 'UNAUTHORIZED'
            ], 401);
        }
    }
}
