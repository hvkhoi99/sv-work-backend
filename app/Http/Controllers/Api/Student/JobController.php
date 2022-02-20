<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobTags;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
  public function showJob(Request $request, $id)
  {
    $user = $request->user('api');

    $recruitment = Recruitment::whereId($id)->first();

    if (isset($recruitment)) {
      if (isset($user)) {
        $application = Application::where([
          ['recruitment_id', $recruitment->id],
          ['user_id', $user->id]
        ])->first();
      } else {
        $application = (object) [
          'id' => 0,
          'state' => null,
          'is_invited' => false,
          'is_applied' => false,
          'is_saved' => false
        ];
      }

      $company_info = RecruiterProfile::where([
        ['user_id', $recruitment->user_id]
      ])->first();

      $recruitment["application"] = $application;

      if (isset($company_info)) {
        $recruitment["company_info"] = collect($company_info)
          ->only(['id', 'logo_image_link', 'company_name', 'verify']);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $recruitment,
          // 'user' => $user
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Current data (application or company info) not available.'
        ], 404);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Current data not available.'
      ], 404);
    }
  }

  public function getJobsByRecruiterId(Request $request, $id)
  {
    $user = $request->user('api');
    $r_profile = RecruiterProfile::whereId($id)->first();

    if (isset($r_profile)) {
      $jobs = Recruitment::where([
        ['is_closed', false],
        ['user_id', $r_profile->user_id]
      ])
        ->orderBy('updated_at', 'desc')
        ->get(
          [
            'id', 'title', 'location', 'job_category', 'min_salary', 'max_salary',
            'expiry_date', 'is_closed', 'user_id', 'created_at',
          ]
        );

      if (isset($jobs) && count($jobs) > 0) {
        $new_jobs = [];
        foreach ($jobs as $job) {
          // company info
          $company_info = RecruiterProfile::where([
            ['user_id', $job->user_id]
          ])->first();

          // count application
          $count_applications = Application::where([
            ['is_applied', true],
            ['recruitment_id', $job->id]
          ])->get()->count();

          // status between student and job
          if (isset($user)) {
            $application = Application::where([
              ['user_id', $user->id],
              ['recruitment_id', $job->id]
            ])->first();
          } else {
            $application = (object) [
              'id' => 0,
              'state' => null,
              'is_invited' => false,
              'is_applied' => false,
              'is_saved' => false
            ];
          }

          $hashtags = JobTags::where('recruitment_id', $job->id)->first()->hashtags;
          $job['hashtags'] = json_decode($hashtags);

          if (isset($company_info)) {
            $job["company_info"] = collect($company_info)
              ->only(['id', 'logo_image_link', 'company_name', 'verify']);
          }

          $job["count_applications"] = $count_applications;
          $job["application"] = isset($application)
            ? $application
            : (object) [
              'id' => 0,
              'state' => null,
              'is_invited' => false,
              'is_applied' => false,
              'is_saved' => false
            ];
          array_push($new_jobs, $job);
        }

        $perPage = $request["_limit"];
        $current_page = LengthAwarePaginator::resolveCurrentPage();

        $new_jobs = new LengthAwarePaginator(
          collect($new_jobs)->forPage($current_page, $perPage)->values(),
          count($new_jobs),
          $perPage,
          $current_page,
          ['path' => url('api/job/getJobsByRecruiterId/' . $id)]
        );

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $new_jobs
        ], 200);
      }

      $perPage = $request["_limit"];
      $current_page = LengthAwarePaginator::resolveCurrentPage();

      $jobs = new LengthAwarePaginator(
        collect($jobs)->forPage($current_page, $perPage)->values(),
        count($jobs),
        $perPage,
        $current_page,
        ['path' => url('api/job/getJobsByRecruiterId/' . $id)]
      );

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $jobs
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile has not been created.'
      ], 200);
    }
  }
}
