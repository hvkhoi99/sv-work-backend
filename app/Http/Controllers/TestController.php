<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Follow;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\Models\StudentProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{
  public function test(Request $request)
  {
    // $days = array_rand(array_flip(range(1, 20)), 10);
    // $my_array = array(
    //     (object) [
    //         'value' => 'php 1',
    //         'label' => 'php 1'
    //     ],
    //     (object) [
    //         'value' => 'php 2',
    //         'label' => 'php 2'
    //     ],
    //     (object) [
    //         'value' => 'php 3',
    //         'label' => 'php 3'
    //     ]
    // );

    // $my_array = array_map(function($o) { return collect($o)->only(['label']); }, $my_array);

    // $company_info = RecruiterProfile::whereId(7)->first();

    // if (isset($company_info)) {
    //   $company_info = collect($company_info)->only([
    //     'id', 'company_name', 'company_industry', 'address', 'company_size',
    //     'contact_email', 'description', 'logo_image_link', 'phone_number',
    //     'verify'
    //   ]);

    //   $follow_status = Follow::where([
    //     ['r_profile_id', 7],
    //     ['s_profile_id', 6]
    //   ])->first();

    //   $is_followed = isset($follow_status) ? $follow_status->is_followed : false;
    //   $company_info['is_followed'] = $is_followed;

    //   return response()->json([
    //     'status' => 1,
    //     'code' => 200,
    //     'data' => $company_info
    //   ], 200);
    // } else {
    //   return response()->json([
    //     'status' => 0,
    //     'code' => 404,
    //     'message' => 'No information found for this company.'
    //   ], 404);
    // }
    // Stats::where('created_at', '>', Carbon::now()->startOfWeek())
    //  ->where('created_at', '<', Carbon::now()->endOfWeek())
    //  ->get();
    // $wednesday = $now->endOfWeek(Carbon::WEDNESDAY)->format('m-d-Y');
    
    // now Time
    // $now = Carbon::now();
    
    // Last week
    $last_last_sunday = Carbon::now()->startOfWeek()->subDays(8);
    $last_monday = $last_last_sunday->copy()->addDay();
    $last_tuesday = $last_monday->copy()->addDay();
    $last_wednesday = $last_tuesday->copy()->addDay();
    $last_thursday = $last_wednesday->copy()->addDay();
    $last_friday = $last_thursday->copy()->addDay();
    $last_saturday = $last_friday->copy()->addDay();
    
    // Current Week
    $last_sunday = $last_saturday->copy()->addDay();
    $monday = Carbon::now()->startOfWeek();
    $tuesday = $monday->copy()->addDay();
    $wednesday = $tuesday->copy()->addDay();
    $thursday = $wednesday->copy()->addDay();
    $friday = $thursday->copy()->addDay();
    $saturday = $friday->copy()->addDay();
    $sunday = $saturday->copy()->addDay();
    // $sunday = $now->endOfWeek();

    // Total Student of Last Week
    $last_sunday_students = StudentProfile::where([
      ['created_at', '>=', $last_last_sunday],
      ['created_at', '<', $last_monday]
    ])->get()->count();
    $monday_students = StudentProfile::where([
      ['created_at', '>=', $last_monday],
      ['created_at', '<', $last_tuesday]
    ])->get()->count();
    $tuesday_students = StudentProfile::where([
      ['created_at', '>=', $last_tuesday],
      ['created_at', '<', $last_wednesday]
    ])->get()->count();
    $wednesday_students = StudentProfile::where([
      ['created_at', '>=', $last_wednesday],
      ['created_at', '<', $last_thursday]
    ])->get()->count();
    $thursday_students = StudentProfile::where([
      ['created_at', '>=', $last_thursday],
      ['created_at', '<', $last_friday]
    ])->get()->count();
    $friday_students = StudentProfile::where([
      ['created_at', '>=', $last_friday],
      ['created_at', '<', $last_saturday]
    ])->get()->count();
    $saturday_students = StudentProfile::where([
      ['created_at', '>=', $last_saturday],
      ['created_at', '<', $last_sunday]
    ])->get()->count();

    $students_result_last_week = [
      $last_sunday_students,
      $monday_students,
      $tuesday_students,
      $wednesday_students,
      $thursday_students,
      $friday_students,
      $saturday_students
    ];

    // Total Recruiter of Last Week
    $last_sunday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $last_last_sunday],
      ['created_at', '<', $last_monday]
    ])->get()->count();
    $monday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $last_monday],
      ['created_at', '<', $last_tuesday]
    ])->get()->count();
    $tuesday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $last_tuesday],
      ['created_at', '<', $last_wednesday]
    ])->get()->count();
    $wednesday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $last_wednesday],
      ['created_at', '<', $last_thursday]
    ])->get()->count();
    $thursday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $last_thursday],
      ['created_at', '<', $last_friday]
    ])->get()->count();
    $friday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $last_friday],
      ['created_at', '<', $last_saturday]
    ])->get()->count();
    $saturday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $last_saturday],
      ['created_at', '<', $last_sunday]
    ])->get()->count();

    $recruiters_result_last_week = [
      $last_sunday_recruiters,
      $monday_recruiters,
      $tuesday_recruiters,
      $wednesday_recruiters,
      $thursday_recruiters,
      $friday_recruiters,
      $saturday_recruiters
    ];

    // Total Application of Last Week
    $last_sunday_applications = Application::where([
      ['created_at', '>=', $last_last_sunday],
      ['created_at', '<', $last_monday]
    ])->get()->count();
    $monday_applications = Application::where([
      ['created_at', '>=', $last_monday],
      ['created_at', '<', $last_tuesday]
    ])->get()->count();
    $tuesday_applications = Application::where([
      ['created_at', '>=', $last_tuesday],
      ['created_at', '<', $last_wednesday]
    ])->get()->count();
    $wednesday_applications = Application::where([
      ['created_at', '>=', $last_wednesday],
      ['created_at', '<', $last_thursday]
    ])->get()->count();
    $thursday_applications = Application::where([
      ['created_at', '>=', $last_thursday],
      ['created_at', '<', $last_friday]
    ])->get()->count();
    $friday_applications = Application::where([
      ['created_at', '>=', $last_friday],
      ['created_at', '<', $last_saturday]
    ])->get()->count();
    $saturday_applications = RecruiterProfile::where([
      ['created_at', '>=', $last_saturday],
      ['created_at', '<', $last_sunday]
    ])->get()->count();

    $applications_result_last_week = [
      $last_sunday_applications,
      $monday_applications,
      $tuesday_applications,
      $wednesday_applications,
      $thursday_applications,
      $friday_applications,
      $saturday_applications
    ];

    // Total Student of Current Week
    $last_sunday_students = StudentProfile::where([
      ['created_at', '>=', $last_sunday],
      ['created_at', '<', $monday]
    ])->get()->count();
    $monday_students = StudentProfile::where([
      ['created_at', '>=', $monday],
      ['created_at', '<', $tuesday]
    ])->get()->count();
    $tuesday_students = StudentProfile::where([
      ['created_at', '>=', $tuesday],
      ['created_at', '<', $wednesday]
    ])->get()->count();
    $wednesday_students = StudentProfile::where([
      ['created_at', '>=', $wednesday],
      ['created_at', '<', $thursday]
    ])->get()->count();
    $thursday_students = StudentProfile::where([
      ['created_at', '>=', $thursday],
      ['created_at', '<', $friday]
    ])->get()->count();
    $friday_students = StudentProfile::where([
      ['created_at', '>=', $friday],
      ['created_at', '<', $saturday]
    ])->get()->count();
    $saturday_students = StudentProfile::where([
      ['created_at', '>=', $saturday],
      ['created_at', '<', $sunday]
    ])->get()->count();

    $students_result = [
      $last_sunday_students,
      $monday_students,
      $tuesday_students,
      $wednesday_students,
      $thursday_students,
      $friday_students,
      $saturday_students
    ];

    // Total Recruiter of Current Week
    $last_sunday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $last_sunday],
      ['created_at', '<', $monday]
    ])->get()->count();
    $monday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $monday],
      ['created_at', '<', $tuesday]
    ])->get()->count();
    $tuesday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $tuesday],
      ['created_at', '<', $wednesday]
    ])->get()->count();
    $wednesday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $wednesday],
      ['created_at', '<', $thursday]
    ])->get()->count();
    $thursday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $thursday],
      ['created_at', '<', $friday]
    ])->get()->count();
    $friday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $friday],
      ['created_at', '<', $saturday]
    ])->get()->count();
    $saturday_recruiters = RecruiterProfile::where([
      ['created_at', '>=', $saturday],
      ['created_at', '<', $sunday]
    ])->get()->count();

    $recruiters_result = [
      $last_sunday_recruiters,
      $monday_recruiters,
      $tuesday_recruiters,
      $wednesday_recruiters,
      $thursday_recruiters,
      $friday_recruiters,
      $saturday_recruiters
    ];

    // Total Application of Current Week
    $last_sunday_applications = Application::where([
      ['created_at', '>=', $last_sunday],
      ['created_at', '<', $monday]
    ])->get()->count();
    $monday_applications = Application::where([
      ['created_at', '>=', $monday],
      ['created_at', '<', $tuesday]
    ])->get()->count();
    $tuesday_applications = Application::where([
      ['created_at', '>=', $tuesday],
      ['created_at', '<', $wednesday]
    ])->get()->count();
    $wednesday_applications = Application::where([
      ['created_at', '>=', $wednesday],
      ['created_at', '<', $thursday]
    ])->get()->count();
    $thursday_applications = Application::where([
      ['created_at', '>=', $thursday],
      ['created_at', '<', $friday]
    ])->get()->count();
    $friday_applications = Application::where([
      ['created_at', '>=', $friday],
      ['created_at', '<', $saturday]
    ])->get()->count();
    $saturday_applications = RecruiterProfile::where([
      ['created_at', '>=', $saturday],
      ['created_at', '<', $sunday]
    ])->get()->count();

    $applications_result = [
      $last_sunday_applications,
      $monday_applications,
      $tuesday_applications,
      $wednesday_applications,
      $thursday_applications,
      $friday_applications,
      $saturday_applications
    ];

    $data = (object) [
      "last_week" => [
        "students_data" => $students_result_last_week,
        "recruiters_data" => $recruiters_result_last_week,
        "applications_result" => $applications_result_last_week,
      ],
      "currentWeek" => [
        "students_data" => $students_result,
        "recruiters_data" => $recruiters_result,
        "applications_result" => $applications_result,
      ]
    ];

    return response()->json([
      'data' => $data,
    ], 200);
  }

  public function upload(Request $request)
  {
    // if ($file = $request->file('file')) {
    //   $file_name = $file->getClientOriginalName();
    // }
    $response = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();
    dd($response);
  }
}
