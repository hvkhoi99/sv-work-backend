<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Follow;
use App\Models\Hashtag;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\Models\RecruitmentTag;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function appliedJobs() {
        $user = Auth::user();

        if (isset($user)) {
            $s_profile = StudentProfile::where('user_id', $user->id)->first();

            if (isset($s_profile)) {
                $applied_jobs = [];
                $hashtags = [];

                $applications = Application::where('user_id', $user->id)->where('is_applied', true)->where('is_invited', null)->orderBy('created_at', 'desc')->get();

                foreach ($applications as $application) {
                    $applied_job = Recruitment::whereId($application->recruitment_id)->first();

                    $recruitment_tags = RecruitmentTag::where('recruitment_id', $application->recruitment_id)->get();

                    foreach ($recruitment_tags as $recruitment_tag) {
                        $hashtag = Hashtag::whereId($recruitment_tag->hashtag_id)->first()->name;
                        array_push($hashtags, $hashtag);
                    }
                    $applied_job['hashtags'] = $hashtags;
                    array_push($applied_jobs, $applied_job);
                }

                $perPage = 10;
                $current_page = LengthAwarePaginator::resolveCurrentPage();

                $new_applied_jobs = new LengthAwarePaginator(
                    collect($applied_jobs)->forPage($current_page, $perPage)->values(),
                    count($applied_jobs),
                    $perPage,
                    $current_page,
                    ['path' => url('api/student/dashboard/applied-jobs')]
                );

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $new_applied_jobs
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

    public function companyFollowed() {
        $user = Auth::user();

        if (isset($user)) {
            $s_profile = StudentProfile::where('user_id', $user->id)->first();

            if (isset($s_profile)) {
                $companies = [];
                $recruitments = [];

                $follows = Follow::where('s_profile_id', $s_profile->id)->where('is_followed', true)->orderBy('updated_at', 'desc')->get();

                foreach ($follows as $follow) {
                    $company = RecruiterProfile::whereId($follow->r_profile_id)->first();
                    $recruitments = Recruitment::where('user_id', $company->user_id)->where('is_closed', false)->orderBy('created_at', 'desc')->get();
                    $company['jobs_available'] = count($recruitments);
                    array_push($companies, $company);
                }

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $companies
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

    public function savedJobs() {
        $user = Auth::user();

        if (isset($user)) {
            $s_profile = StudentProfile::where('user_id', $user->id)->first();

            if (isset($s_profile)) {
                $saved_jobs = [];
                $hashtags = [];

                $applications = Application::where('user_id', $user->id)->where('is_saved', true)->orderBy('created_at', 'desc')->get();

                foreach ($applications as $application) {
                    $saved_job = Recruitment::whereId($application->recruitment_id)->first();

                    $recruitment_tags = RecruitmentTag::where('recruitment_id', $application->recruitment_id)->get();

                    foreach ($recruitment_tags as $recruitment_tag) {
                        $hashtag = Hashtag::whereId($recruitment_tag->hashtag_id)->first()->name;
                        array_push($hashtags, $hashtag);
                    }
                    $saved_job['hashtags'] = $hashtags;
                    array_push($saved_jobs, $saved_job);
                }

                $perPage = 10;
                $current_page = LengthAwarePaginator::resolveCurrentPage();

                $new_saved_jobs = new LengthAwarePaginator(
                    collect($saved_jobs)->forPage($current_page, $perPage)->values(),
                    count($saved_jobs),
                    $perPage,
                    $current_page,
                    ['path' => url('api/student/dashboard/saved-jobs')]
                );

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $new_saved_jobs
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

    public function invitedJobs() {
        $user = Auth::user();

        if (isset($user)) {
            $s_profile = StudentProfile::where('user_id', $user->id)->first();

            if (isset($s_profile)) {
                $invited_jobs = [];
                $hashtags = [];

                $applications = Application::where('user_id', $user->id)->where('is_invited', true)->orderBy('created_at', 'desc')->get();

                foreach ($applications as $application) {
                    $invited_job = Recruitment::whereId($application->recruitment_id)->first();

                    $recruitment_tags = RecruitmentTag::where('recruitment_id', $application->recruitment_id)->get();

                    foreach ($recruitment_tags as $recruitment_tag) {
                        $hashtag = Hashtag::whereId($recruitment_tag->hashtag_id)->first()->name;
                        array_push($hashtags, $hashtag);
                    }
                    $invited_job['hashtags'] = $hashtags;
                    array_push($invited_jobs, $invited_job);
                }

                $perPage = 10;
                $current_page = LengthAwarePaginator::resolveCurrentPage();

                $new_invited_jobs = new LengthAwarePaginator(
                    collect($invited_jobs)->forPage($current_page, $perPage)->values(),
                    count($invited_jobs),
                    $perPage,
                    $current_page,
                    ['path' => url('api/student/dashboard/invited-jobs')]
                );

                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $new_invited_jobs
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
}
