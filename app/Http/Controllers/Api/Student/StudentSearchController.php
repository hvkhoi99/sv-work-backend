<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobTags;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentSearchController extends Controller
{
  public function getJobs(Request $request)
  {
    $user = Auth::user();

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
          'id', 'title', 'location', 'min_salary', 'max_salary',
          'expiry_date', 'is_closed', 'user_id', 'created_at',
        ]
      )->toArray();

    if (count($jobs) > 0) {
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
        $application = Application::where([
          ['user_id', $user->id],
          ['recruitment_id', $job->id]
        ])->first();

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
      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $new_jobs
      ]);
    }
    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $jobs
    ]);
  }
}
