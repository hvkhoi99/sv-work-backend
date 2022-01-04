<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Hashtag;
use App\Models\JobTags;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\Models\RecruitmentTag;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class RecruiterDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (isset($user)) {
            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
            $recruitments = Recruitment::where('user_id', $user->id)->get();
            // $recruitments->transform(function ($recruitment) {
            //     $recruitment->applicants = Application::whereHas('recruitments', function ($q) use ($recruitment) {
            //         $q->where('recruitment_id', $recruitment->id);
            //     })
            //         ->get();
            //     return $recruitment;
            // });
            $total_applicants = 0;
            foreach ($recruitments as $recruitment) {
                $count_applicant = Application::where('recruitment_id', $recruitment->id)->where('is_applied', true)->count();
                if (isset($count_applicant)) {
                    $total_applicants += $count_applicant;
                }
            }

            if (isset($r_profile)) {
                $availableJobs = Recruitment::where('user_id', $r_profile->user_id)->where('is_closed', false)->get();
                $closedJobs = Recruitment::where('user_id', $r_profile->user_id)->where('is_closed', true)->get();
                $data['profile'] = $r_profile;
                $data['availableJobs'] = count($availableJobs);
                $data['closedJobs'] = count($closedJobs);
                $data['total_applicants'] = $total_applicants;
                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $data,
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Your recruiter profile does not exist.'
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

    public function availableRecruitments(Request $request)
    {
        $user = $request->user();
        if (isset($user)) {
            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
            $recruitments = Recruitment::where('user_id', $user->id)->where('is_closed', "<>", true)->orderBy('created_at', 'DESC')->get();

            if (isset($r_profile) && isset($recruitments)) {
                foreach ($recruitments as $rec) {
                    $applicant = Application::where('recruitment_id', $rec->id)->get();
                    $rec['applicants'] = count($applicant);
                    // $recruitments_tag = RecruitmentTag::where('recruitment_id', $rec->id)->get();

                    // $hashtags = [];
                    // foreach ($recruitments_tag as $rec_tag) {
                    //     $hashtag = Hashtag::whereId($rec_tag->hashtag_id)->first();
                    //     array_push($hashtags, $hashtag);
                    // }
                    // array_push($hashtags, $recruitments_tag);
                    $hashtags = JobTags::where('recruitment_id', $rec->id)->first()->hashtags;
                    $rec['hashtags'] = json_decode($hashtags);
                }

                $perPage = $request["_limit"];
                $current_page = LengthAwarePaginator::resolveCurrentPage();

                $new_recruiments = new LengthAwarePaginator(
                    collect($recruitments)->forPage($current_page, $perPage)->values(),
                    $recruitments->count(),
                    $perPage,
                    $current_page,
                    ['path' => url('api/student/recruiter/dashboard/available-recruitments')]
                );

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $new_recruiments,
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Current data not available or your recruiter profile does not exist.'
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

    public function closedRecruitments(Request $request)
    {
        $user = $request->user();
        if (isset($user)) {
            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
            $recruitments = Recruitment::where('user_id', $user->id)->where('is_closed', true)->orderBy('updated_at', 'DESC')->get();

            if (isset($r_profile) && isset($recruitments)) {

                foreach ($recruitments as $rec) {
                    $applicant = Application::where('recruitment_id', $rec->id)->get();
                    $rec['applicants'] = count($applicant);
                    $recruitments_tag = RecruitmentTag::where('recruitment_id', $rec->id)->get();

                    $hashtags = [];
                    foreach ($recruitments_tag as $rec_tag) {
                        $hashtag = Hashtag::whereId($rec_tag->hashtag_id)->first();
                        array_push($hashtags, $hashtag);
                    }
                    // array_push($hashtags, $recruitments_tag);
                    $rec['hashtags'] = $hashtags;
                }

                $perPage = $request["_limit"];
                $current_page = LengthAwarePaginator::resolveCurrentPage();

                $new_recruiments = new LengthAwarePaginator(
                    collect($recruitments)->forPage($current_page, $perPage)->values(),
                    $recruitments->count(),
                    $perPage,
                    $current_page,
                    ['path' => url('api/student/recruiter/dashboard/closed-recruitments')]
                );

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $new_recruiments,
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
}
