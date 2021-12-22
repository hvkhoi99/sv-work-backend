<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRecruiterProfileRequest;
use App\Models\Follow;
use App\Models\RecruiterProfile;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecruiterProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if (isset($user)) {
            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
            if (isset($r_profile)) {
                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $r_profile
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Your recruiter profile was not found or has not been created.',
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 0,
                'code' => 404,
                'message' => 'UNAUTHORIZED'
            ], 404);
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
    public function store(ApiRecruiterProfileRequest $request)
    {
        $user = Auth::user();
        if (isset($user)) {
            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
            $s_profile = StudentProfile::where('user_id', $user->id)->first();
            $user["s_profile"] = isset($s_profile) ? $s_profile : null;

            if (isset($r_profile)) {
                $user["r_profile"] = $r_profile;

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'message' => 'Your recruiter profile already exists.',
                    'data' => $user
                ], 200);
            } else {
                $new_r_profile = RecruiterProfile::create([
                    'contact_email' => $request['contact_email'],
                    'company_name' => $request['company_name'],
                    'logo_image_link' => $request['logo_image_link'],
                    // 'description_image_link' => $request['description_image_link'],
                    'description' => $request['description'],
                    'phone_number' => $request['phone_number'],
                    'verify' => null,
                    'address' => $request['address'],
                    'company_size' => $request['company_size'],
                    'company_industry' => $request['company_industry'],
                    'tax_code' => $request['tax_code'],
                    'user_id' => $user->id
                ]);

                $user["r_profile"] = $new_r_profile;

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'message' => 'Your recruiter profile was successfully created.',
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
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $s_profile = StudentProfile::where('user_id', $user->id)->first();
        $r_profile = RecruiterProfile::where('id', $id)->first();
        if (isset($r_profile)) {

            if (isset($s_profile)) {
                $follow = Follow::where('s_profile_id', $s_profile->id)->where('r_profile_id', $id)->first();
                if (isset($follow)) {
                    $r_profile['is_followed'] = $follow->is_followed;
                } else {
                    $r_profile['is_followed'] = false;
                }
            } else {
                $r_profile['is_followed'] = false;
            }

            return response()->json([
                'status' => 1,
                'code' => 200,
                'data' => $r_profile
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'code' => 404,
                'message' => 'Your recruiter profile was not found or has not been created.'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ApiRecruiterProfileRequest $request, $id)
    {
        $user = Auth::user();
        if (isset($user)) {
            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
            $s_profile = StudentProfile::where('user_id', $user->id)->first();
            $user["s_profile"] = isset($s_profile) ? $s_profile : null;

            if (isset($r_profile)) {
                $r_profile->update($request->all());

                $user["r_profile"] = $r_profile;

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'mesage' => 'Your recruiter profile was successfully updated.',
                    'data' => $user
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Your recruiter profile was not found or has not been created.'
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
}
