<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobTags;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class StudentSearchController extends Controller
{
  public function getJobs(Request $request)
  {
    $user = $request->user('api');

    $jobs = Recruitment::query();
    $jobs = $jobs
      ->keyword($request)
      ->location($request)
      ->career($request)
      ->type($request)
      ->salary($request)
      ->closed($request)
      ->extra($request)
      ->orderBy('created_at', 'desc')
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
        ['path' => url('api/student/find/jobs')]
      );

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $new_jobs
      ]);
    }

    $perPage = $request["_limit"];
    $current_page = LengthAwarePaginator::resolveCurrentPage();

    $jobs = new LengthAwarePaginator(
      collect($jobs)->forPage($current_page, $perPage)->values(),
      count($jobs),
      $perPage,
      $current_page,
      ['path' => url('api/student/find/jobs')]
    );

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $jobs
    ]);
  }

  public function getEmployers(Request $request)
  {
    $employers = RecruiterProfile::query();
    $employers = $employers
      ->keyword($request)
      ->location($request)
      ->orderBy('created_at', 'desc')
      ->get([
        'id', 'logo_image_link', 'company_name', 'address', 'verify', 'user_id', 'created_at'
      ]);

    if (isset($employers) && $employers->count() > 0) {
      $new_employers = [];
      foreach ($employers as $employer) {
        $count_available_jobs = Recruitment::where([
          ['is_closed', false],
          ['user_id', $employer->user_id]
        ])->get()->count();
        $employer['count_available_jobs'] = $count_available_jobs;
        array_push($new_employers, $employer);
      }

      $perPage = $request["_limit"];
      $current_page = LengthAwarePaginator::resolveCurrentPage();

      $new_employers = new LengthAwarePaginator(
        collect($new_employers)->forPage($current_page, $perPage)->values(),
        count($new_employers),
        $perPage,
        $current_page,
        ['path' => url('api/student/find/employers')]
      );

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $new_employers
      ]);
    }

    $perPage = $request["_limit"];
    $current_page = LengthAwarePaginator::resolveCurrentPage();

    $employers = new LengthAwarePaginator(
      collect($employers)->forPage($current_page, $perPage)->values(),
      count($employers),
      $perPage,
      $current_page,
      ['path' => url('api/student/find/employers')]
    );

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $employers
    ]);
  }
}
