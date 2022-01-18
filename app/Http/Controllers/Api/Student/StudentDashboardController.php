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
  public function appliedJobs(Request $request)
  {
    $user = Auth::user();

    if (isset($user)) {
      $s_profile = StudentProfile::where('user_id', $user->id)->first();

      if (isset($s_profile)) {
        $applied_jobs = [];

        $applications = Application::where([
          ['is_applied', true],
          ['user_id', $user->id]
        ])->orderBy('updated_at', 'desc')->get();

        foreach ($applications as $application) {
          $applied_job = Recruitment::whereId($application->recruitment_id)
          ->first()
          ->only(
            [
              'id', 'title', 'job_category', 'location', 'min_salary', 'max_salary', 
              'is_closed', 'user_id', 'created_at', 'updated_at'
            ]
          );
          // $applied_job = collect($applied_job)->only(
          //   [
          //     'id', 'title', 'job_category', 'location', 'min_salary', 'max_salary', 
          //     'is_closed', 'user_id', 'created_at', 'updated_at'
          //   ]
          // );
          $applied_job["status"] = $application->state;

          // $company_info = RecruiterProfile::whereId($applied_job->user_id)->first();
          // $applied_job["company_info"] = collect($company_info)
          //   ->only(['id', 'logo_image_link', 'company_name', 'verify']);

          array_push($applied_jobs, $applied_job);
        }

        $perPage = $request["_limit"];
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

  public function companyFollowed(Request $request)
  {
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

        $perPage = $request["_limit"];
        $current_page = LengthAwarePaginator::resolveCurrentPage();

        $new_companies = new LengthAwarePaginator(
          collect($companies)->forPage($current_page, $perPage)->values(),
          count($companies),
          $perPage,
          $current_page,
          ['path' => url('api/student/dashboard/followed-jobs')]
        );

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $new_companies
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

  public function savedJobs(Request $request)
  {
    $user = Auth::user();

    if (isset($user)) {
      $s_profile = StudentProfile::where('user_id', $user->id)->first();

      if (isset($s_profile)) {
        $saved_jobs = [];


        $applications = Application::where('user_id', $user->id)->where('is_saved', true)->orderBy('created_at', 'desc')->get();

        foreach ($applications as $application) {
          $saved_job = Recruitment::whereId($application->recruitment_id)->first();

          $recruitment_tags = RecruitmentTag::where('recruitment_id', $application->recruitment_id)->get();

          $hashtags = [];
          foreach ($recruitment_tags as $recruitment_tag) {
            $hashtag = Hashtag::whereId($recruitment_tag->hashtag_id)->first()->name;
            array_push($hashtags, $hashtag);
          }
          $saved_job['hashtags'] = $hashtags;
          array_push($saved_jobs, $saved_job);
        }

        $perPage = $request["_limit"];
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

  public function invitedJobs(Request $request)
  {
    $user = Auth::user();

    if (isset($user)) {
      $s_profile = StudentProfile::where('user_id', $user->id)->first();

      if (isset($s_profile)) {
        $invited_jobs = [];

        $applications = Application::where('user_id', $user->id)->where('state', null)->where('is_invited', true)->orderBy('created_at', 'desc')->get();

        foreach ($applications as $application) {
          $invited_job = Recruitment::whereId($application->recruitment_id)->first();

          $recruitment_tags = RecruitmentTag::where('recruitment_id', $application->recruitment_id)->get();

          $hashtags = [];
          foreach ($recruitment_tags as $recruitment_tag) {
            $hashtag = Hashtag::whereId($recruitment_tag->hashtag_id)->first()->name;
            array_push($hashtags, $hashtag);
          }
          $invited_job['hashtags'] = $hashtags;
          array_push($invited_jobs, $invited_job);
        }

        $perPage = $request["_limit"];
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
