<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRecruitmentRequest;
use App\Models\Application;
use App\Models\Hashtag;
use App\Models\JobTags;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\Models\RecruitmentTag;
use App\Models\StudentProfile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecruitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $user = $request->user();
        // if (isset($user)) {

        // } else {
        //     return response()->json([
        //         'status' => 1,
        //         'code' => 401,
        //         'message' => 'UNAUTHORIZED',
        //     ]);
        // }
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
    public function store(ApiRecruitmentRequest $request)
    {
        $user = $request->user();

        if (isset($user)) {

            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

            if (isset($r_profile)) {

                $verify = $r_profile->verify;

                if ($verify) {

                    $new_recruiment = Recruitment::create([
                        'title' => $request['title'],
                        'position' => $request['position'],
                        'is_full_time' => $request['is_full_time'],
                        'job_category' => $request['job_category'],
                        'location' => $request['location'],
                        'description' => $request['description'],
                        'requirement' => $request['requirement'],
                        'min_salary' => $request['min_salary'],
                        'max_salary' => $request['max_salary'],
                        'benefits' => $request['benefits'],
                        'expiry_date' => $request['expiry_date'],
                        'is_closed' => false,
                        'user_id' => $user->id,
                    ]);

                    // $hashtags_id = array_map('intval', explode(',', $request['hashtags_id']));

                    // foreach ($hashtags_id as $hashtag_id) {
                    //     RecruitmentTag::create([
                    //         'recruitment_id' => $new_recruiment->id,
                    //         'hashtag_id' => $hashtag_id
                    //     ]);
                    // }

                    $hashtags = JobTags::create([
                        'hashtags' => json_encode($request['hashtags']),
                        'recruitment_id' => $new_recruiment->id
                    ]);
                    $new_recruiment["hashtags"] = $hashtags;

                    return response()->json([
                        'status' => 1,
                        'code' => 200,
                        'message' => 'Successfully created.',
                        'data' => $new_recruiment
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'code' => 401,
                        'message' => 'Your recruiter profile is not verified. Please try again later.',
                        'data' => $r_profile
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Your recruiter profile was not found or has not been created.',
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        if (isset($user)) {

            $recruitment = Recruitment::whereId($id)->where('user_id', $user->id)->first();

            if (isset($recruitment)) {

                $job_tags = JobTags::where('recruitment_id', $id)->first()->hashtags;

                $recruitment['hashtags'] = json_decode($job_tags);

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $recruitment
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Current data not available.'
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
    public function update(ApiRecruitmentRequest $request, $id)
    {
        $user = $request->user();
        if (isset($user)) {
            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
            $recruitment = Recruitment::whereId($id)->where('user_id', $user->id)->first();
            if (isset($r_profile) && isset($recruitment)) {

                $recruitment->update([
                    'title' => $request['title'],
                    'position' => $request['position'],
                    'is_full_time' => $request['is_full_time'],
                    'job_category' => $request['job_category'],
                    'location' => $request['location'],
                    'description' => $request['description'],
                    'requirement' => $request['requirement'],
                    'min_salary' => $request['min_salary'],
                    'max_salary' => $request['max_salary'],
                    'benefits' => $request['benefits'],
                    'expiry_date' => $request['expiry_date'],
                    'is_closed' => $request['is_closed']
                ]);

                // $deleted_hashtags = RecruitmentTag::where('recruitment_id', $recruitment->id)->delete();
                // if ($deleted_hashtags) {
                //     $hashtags_id = array_map('intval', explode(',', $request['hashtags_id']));

                //     foreach ($hashtags_id as $hashtag_id) {
                //         RecruitmentTag::create([
                //             'recruitment_id' => $recruitment->id,
                //             'hashtag_id' => $hashtag_id
                //         ]);
                //     }
                //     $recruitment['hashtags_id'] = $hashtags_id;
                // }
                $hashtags = JobTags::where('recruitment_id', $recruitment->id)->first();

                if (isset($hashtags)) {
                    $hashtags->update([
                        'hashtags' => json_encode($request['hashtags']),
                    ]);
                    $recruitment["hashtags"] = $hashtags;
                } else {
                    $newHashtags = JobTags::create([
                        'hashtags' => json_encode($request['hashtags']),
                        'recruitment_id' => $recruitment->id
                    ]);
                    $recruitment["hashtags"] = $newHashtags;
                    // return response()->json([
                    //     "status" => 0,
                    //     "code" => 400,
                    //     "message" => "The hashtags field cannot be left blank. Please try again."
                    // ], 400);
                }

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'mesage' => 'Your recruitment was successfully updated.',
                    'data' => $recruitment
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Current data not available.'
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
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (isset($user)) {

            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
            $recruitment = Recruitment::whereId($id)->where('user_id', $user->id)->first();
            if (isset($r_profile) && isset($recruitment)) {

                if ($recruitment->is_closed) {

                    $recruitment->delete();

                    return response()->json([
                        'status' => 1,
                        'code' => 200,
                        'message' => 'Successfully deleted.'
                        // 'data' => $recruitment_tags
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'code' => 404,
                        'message' => 'Current data not available.'
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Current data not available.'
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

    public function candidates(Request $request, $id)
    {
        $user = $request->user();

        if (isset($user)) {

            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
            $exist_recruitment = Recruitment::whereId($id)->where('user_id', $user->id)->first();

            if (isset($r_profile) && isset($exist_recruitment)) {
                $candidates = [];
                $applications = Application::where('recruitment_id', $id)->where('is_invited', null)->where('is_applied', true)->orderBy('id', 'DESC')->get();

                foreach ($applications as $application) {
                    $candidate = StudentProfile::where('user_id', $application->user_id)->first();
                    array_push($candidates, $candidate);
                }

                $perPage = 10;
                $current_page = LengthAwarePaginator::resolveCurrentPage();

                $new_recruiments = new LengthAwarePaginator(
                    collect($candidates)->forPage($current_page, $perPage)->values(),
                    count($candidates),
                    $perPage,
                    $current_page,
                    ['path' => url('api/student/recruiter/recruitment/' . $id . '/candidates')]
                );

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $new_recruiments
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Current data not available or your recruiter profile doesn\'t exist.'
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

    public function close($id)
    {
        $user = Auth::user();
        if (isset($user)) {
            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

            if (isset($r_profile)) {
                $recruitment = Recruitment::whereId($id)->where('user_id', $r_profile->user_id)->first();

                if (isset($recruitment)) {
                    $recruitment->update([
                        'is_closed' => !($recruitment->is_closed)
                    ]);

                    return response()->json([
                        'status' => 1,
                        'code' => 200,
                        'mesage' => 'Your recruitment was successfully updated.',
                        'data' => $recruitment
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'code' => 404,
                        'message' => 'Current data not available.'
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Your recruiter profile has not been created.'
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
