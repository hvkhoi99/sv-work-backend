<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiStudentAvatarRequest;
use App\Http\Requests\ApiStudentProfileRequest;
use App\Models\RecruiterProfile;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentProfileController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $user = $request->user();
    if (isset($user)) {
      $s_profile = StudentProfile::where('user_id', $user->id)->first();
      if (isset($s_profile)) {
        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $s_profile
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your student profile was not found or has not been created.',
          'data' => $user
        ], 404);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 401,
        'message' => 'UNAUTHORIZED'
      ], 401);
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
  public function store(ApiStudentProfileRequest $request)
  {
    $user = $request->user();
    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
      $user["r_profile"] = isset($r_profile) ? $r_profile : null;

      $s_profile = StudentProfile::where('user_id', $user->id)->first();

      if (isset($s_profile)) {
        $user["s_profile"] = $s_profile;

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Your student profile already exists.',
          'data' => $user
        ], 200);
      } else {
        $new_s_profile = StudentProfile::create([
          'email' => $request['email'],
          'first_name' => $request['first_name'],
          'last_name' => $request['last_name'],
          'avatar_link' => $request['avatar_link'],
          'date_of_birth' => $request['date_of_birth'],
          'phone_number' => $request['phone_number'],
          'nationality' => $request['nationality'],
          'address' => $request['address'],
          'gender' => $request['gender'],
          'over_view' => $request['over_view'],
          'open_for_job' => false,
          'job_title' => $request['job_title'],
          'user_id' => $user->id
        ]);

        $user["s_profile"] = $new_s_profile;

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Your student profile has been created.',
          'data' => $user
        ], 200);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 401,
        'message' => 'UNAUTHORIZED'
      ], 401);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(ApiStudentProfileRequest $request, $id)
  {
    $user = Auth::user();
    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
      $user["r_profile"] = isset($r_profile) ? $r_profile : null;

      $s_profile = StudentProfile::where('user_id', $user->id)->first();

      if (isset($s_profile)) {
        $s_profile->update($request->all());
        $user["s_profile"] = $s_profile;

        return response()->json([
          'status' => 1,
          'code' => 200,
          'mesage' => 'Your student profile was successfully updated.',
          'data' => $user
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your student profile was not found or has not been created.'
        ], 404);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 401,
        'message' => 'UNAUTHORIZED'
      ], 401);
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

  public function openJob()
  {
    $user = Auth::user();
    if (isset($user)) {
      $s_profile = StudentProfile::where('user_id', $user->id)->first();
      if (isset($s_profile)) {
        $s_profile->update([
          'open_for_job' => !($s_profile->open_for_job)
        ]);
        return response()->json([
          'status' => 1,
          'code' => 200,
          'mesage' => 'Your open job option was successfully updated.',
          'data' => $s_profile
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your student profile has not been created.'
        ], 404);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 401,
        'message' => 'UNAUTHORIZED'
      ], 401);
    }
  }

  public function changeAvatar(ApiStudentAvatarRequest $request)
  {
    $user = Auth::user();

    if (isset($user)) {
      $s_profile = StudentProfile::where('user_id', $user->id)->first();

      if (isset($s_profile)) {
        $response = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();

        $s_profile->update([
          'avatar_link' => $response
        ]);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'mesage' => 'Your student profile (avatar) was successfully updated.',
          'data' => $user
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your student profile has not been created.'
        ], 404);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 401,
        'message' => 'UNAUTHORIZED'
      ], 401);
    }
  }
}
