<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobTags;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
  public function getTopRecruiters()
  {
    $users = User::where('role_id', '!=', 1)
      ->withCount('recruitments')
      ->orderBy('recruitments_count', 'desc')
      ->take(9)
      ->get();

    if (isset($users)) {
      // $recruitments->transform(function ($recruitment) {
      //     $recruitment->user = User::whereHas('recruitments', function ($q) use ($recruitment) {
      //         $q->where('id', $recruitment->id);
      //     })
      //         ->take(10)
      //         ->get();
      //     return $recruitment;
      // });
      // $users = $users->reverse();
      $new_users = [];
      foreach ($users as $user) {
        $recruiter = RecruiterProfile::where('user_id', $user->id)->first();
        if (isset($recruiter)) {
          $recruiter = collect($recruiter)->only(['id', 'logo_image_link', 'company_name', 'user_id']);
          $recruiter["recruitments_count"] = $user->recruitments_count;
          array_push($new_users, $recruiter);
        }
      }
      // $halved = array_chunk($new_users, ceil(count($new_users)/2));
      // $halved = array_reverse($halved);
      // $users->split(ceil($users->count()/2))->toArray();
      $data = array();
      if (isset($new_users[6])) {
        if (isset($new_users[8])) {
          $data[0] = array(
            $new_users[6],
            $new_users[8]
          );
        } else {
          $data[0] = array(
            $new_users[6]
          );
        }
      } else {
        $data[0] = [];
      }

      if (isset($new_users[2])) {
        if (isset($new_users[4])) {
          $data[1] = array(
            $new_users[2],
            $new_users[4]
          );
        } else {
          $data[1] = array(
            $new_users[2]
          );
        }
      } else {
        $data[1] = [];
      }

      if (isset($new_users[0])) {
        $data[2] = array(
          $new_users[0]
        );
      } else {
        $data[2] = [];
      }

      if (isset($new_users[1])) {
        if (isset($new_users[3])) {
          $data[3] = array(
            $new_users[1],
            $new_users[3]
          );
        } else {
          $data[3] = array(
            $new_users[1]
          );
        }
      } else {
        $data[3] = [];
      }

      if (isset($new_users[5])) {
        if (isset($new_users[7])) {
          $data[4] = array(
            $new_users[5],
            $new_users[7]
          );
        } else {
          $data[4] = array(
            $new_users[5]
          );
        }
      } else {
        $data[4] = [];
      }

      return response()->json([
        "status" => 1,
        "code" => 200,
        "data" => array_values($data),
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Data was not found. Please try again later.'
      ], 404);
    }
  }

  public function getTopRecruitments(Request $request)
  {
    $user = $request->user('api');

    $jobs = Recruitment::where('is_closed', false)
      // ->orderBy('created_at', 'desc')
      // ->take(4)
      ->get(
        [
          'id', 'title', 'location', 'job_category', 'min_salary', 'max_salary',
          'expiry_date', 'is_closed', 'user_id', 'created_at',
        ]
      );

    if (isset($jobs) && count($jobs) > 0) {
      // $new_jobs = [];
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
        // array_push($new_jobs, $job);
      }

      $jobs = collect($jobs)->toArray();
      usort($jobs, fn ($a, $b) => -1 * strcmp($a['count_applications'], $b['count_applications']));

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => array_slice($jobs, 0, 4),
        // 'data' => $jobs
      ], 200);
    }

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $jobs
    ], 200);
  }

  public function getTotalJobs()
  {
    $total_jobs = Recruitment::where('is_closed', false)->get()->count();
    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $total_jobs
    ], 200);
  }
}
