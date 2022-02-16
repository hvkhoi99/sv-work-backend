<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Event;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\Models\StudentProfile;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
  public function recruiters(Request $request)
  {
    $_limit = $request["_limit"];
    $recruiters = RecruiterProfile::where([
      ['verify', null],
      ['tax_code', '!=', null]
    ])->paginate($_limit);

    if (isset($recruiters)) {
      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $recruiters
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'The current listing does not have any recruiters. Please try again later.'
      ], 404);
    }
  }

  public function showRecruiter($id)
  {
    $r_profile = RecruiterProfile::where('id', $id)->first();
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
        'message' => 'Your recruiter profile was not found or has not been created.'
      ], 404);
    }
  }

  public function verifyRecruiter(Request $request, $id)
  {
    $recruiter = RecruiterProfile::whereId($id)->first();

    if (isset($recruiter)) {

      $recruiter->update($request->all());

      return response()->json([
        'status' => 1,
        'code' => 200,
        'mesage' => 'Verified recruiter.',
        'data' => $recruiter
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 400,
        'message' => 'An incident has occurred. Please try again later.'
      ], 400);
    }
  }

  public function findRecruiter(Request $request)
  {
    $searchTerms = explode(' ', $request['company_name']);
    $query = RecruiterProfile::query();

    foreach ($searchTerms as $searchTerm) {
      $query->where(function ($q) use ($searchTerm) {
        $q->where('company_name', 'like', '%' . $searchTerm . '%');
      });
    }

    $results = $query->get();

    if (isset($results)) {

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $results
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 400,
        'message' => 'An incident has occurred. Please try again later.'
      ], 400);
    }
  }

  public function dashboard()
  {
    $total_student = StudentProfile::all()->count();
    $total_recruiter = RecruiterProfile::all()->count();
    $total_application = Application::all()->count();
    $total_event = Event::all()->count();

    $student["total_student"] = $total_student;
    $recruiter["total_recruiter"] = $total_recruiter;
    $application["total_application"] = $total_application;
    $event["total_event"] = $total_event;

    $student_lastMonth_count = StudentProfile::whereMonth(
      'created_at',
      '=',
      Carbon::now()->subMonth()->month
    )->get()->count();
    $student_currentMonth_count = StudentProfile::whereMonth(
      'created_at',
      '=',
      Carbon::now()->month
    )->get()->count();

    $recruiter_lastMonth_count = RecruiterProfile::whereMonth(
      'created_at',
      '=',
      Carbon::now()->subMonth()->month
    )->get()->count();
    $recruiter_currentMonth_count = RecruiterProfile::whereMonth(
      'created_at',
      '=',
      Carbon::now()->month
    )->get()->count();

    $application_lastMonth_count = Application::whereMonth(
      'created_at',
      '=',
      Carbon::now()->subMonth()->month
    )->get()->count();
    $application_currentMonth_count = Application::whereMonth(
      'created_at',
      '=',
      Carbon::now()->month
    )->get()->count();

    $event_lastMonth_count = Event::whereMonth(
      'created_at',
      '=',
      Carbon::now()->subMonth()->month
    )->get()->count();
    $event_currentMonth_count = Event::whereMonth(
      'created_at',
      '=',
      Carbon::now()->month
    )->get()->count();

    $student_percent = $student_lastMonth_count === 0 ? 100 : round((($student_currentMonth_count / $student_lastMonth_count) * 100), 2);
    $recruiter_percent = $recruiter_lastMonth_count === 0 ? 100 : round((($recruiter_currentMonth_count / $recruiter_lastMonth_count) * 100), 2);
    $application_percent = $application_lastMonth_count === 0 ? 100 : round((($application_currentMonth_count / $application_lastMonth_count) * 100), 2);
    $event_percent = $event_lastMonth_count === 0 ? 100 :  round((($event_currentMonth_count / $event_lastMonth_count) * 100), 2);

    $student["isUp"] = $student_percent === 0.00 ? 0 : ($student_percent > 0 ? 1 : -1
    );
    $recruiter["isUp"] = $recruiter_percent === 0.00 ? 0 : ($recruiter_percent > 0 ? 1 : -1
    );
    $application["isUp"] = $application_percent === 0.00 ? 0 : ($application_percent > 0 ? 1 : -1
    );
    $event["isUp"] = $event_percent === 0.00 ? 0 : ($event_percent > 0 ? 1 : -1
    );

    $student["percent"] = $student_percent;
    $recruiter["percent"] = $recruiter_percent;
    $application["percent"] = $application_percent;
    $event["percent"] = $event_percent;

    $total["student"] = $student;
    $total["recruiter"] = $recruiter;
    $total["application"] = $application;
    $total["event"] = $event;

    if (
      isset($total_student) &&  isset($total_recruiter) &&
      isset($total_application) &&  isset($total_event)
    ) {
      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $total
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 400,
        'message' => 'An incident has occurred. Please try again later.'
      ], 200);
    }
  }

  public function chartFigure()
  {
    // now Time
    // $now = Carbon::now();

    // Last week
    // $last_last_sunday = Carbon::now()->startOfWeek()->subDays(8);
    // $last_monday = $last_last_sunday->copy()->addDay();
    // $last_tuesday = $last_monday->copy()->addDay();
    // $last_wednesday = $last_tuesday->copy()->addDay();
    // $last_thursday = $last_wednesday->copy()->addDay();
    // $last_friday = $last_thursday->copy()->addDay();
    // $last_saturday = $last_friday->copy()->addDay();

    // Current Week
    $last_sunday = Carbon::now()->startOfWeek()->subDay();
    $monday = Carbon::now()->startOfWeek();
    $tuesday = $monday->copy()->addDay();
    $wednesday = $tuesday->copy()->addDay();
    $thursday = $wednesday->copy()->addDay();
    $friday = $thursday->copy()->addDay();
    $saturday = $friday->copy()->addDay();
    $sunday = $saturday->copy()->addDay();
    // $sunday = $now->endOfWeek();

    // Total Student of Current Week
    $last_sunday_users = User::where([
      ['created_at', '>=', $last_sunday],
      ['created_at', '<', $monday]
    ])->get()->count();
    $monday_users = User::where([
      ['created_at', '>=', $monday],
      ['created_at', '<', $tuesday]
    ])->get()->count();
    $tuesday_users = User::where([
      ['created_at', '>=', $tuesday],
      ['created_at', '<', $wednesday]
    ])->get()->count();
    $wednesday_users = User::where([
      ['created_at', '>=', $wednesday],
      ['created_at', '<', $thursday]
    ])->get()->count();
    $thursday_users = User::where([
      ['created_at', '>=', $thursday],
      ['created_at', '<', $friday]
    ])->get()->count();
    $friday_users = User::where([
      ['created_at', '>=', $friday],
      ['created_at', '<', $saturday]
    ])->get()->count();
    $saturday_users = User::where([
      ['created_at', '>=', $saturday],
      ['created_at', '<', $sunday]
    ])->get()->count();

    $users_result = [
      $last_sunday_users,
      $monday_users,
      $tuesday_users,
      $wednesday_users,
      $thursday_users,
      $friday_users,
      $saturday_users
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

    // Total Recruitments of Current Week
    $last_sunday_recruitments = Recruitment::where([
      ['created_at', '>=', $last_sunday],
      ['created_at', '<', $monday]
    ])->get()->count();
    $monday_recruitments = Recruitment::where([
      ['created_at', '>=', $monday],
      ['created_at', '<', $tuesday]
    ])->get()->count();
    $tuesday_recruitments = Recruitment::where([
      ['created_at', '>=', $tuesday],
      ['created_at', '<', $wednesday]
    ])->get()->count();
    $wednesday_recruitments = Recruitment::where([
      ['created_at', '>=', $wednesday],
      ['created_at', '<', $thursday]
    ])->get()->count();
    $thursday_recruitments = Recruitment::where([
      ['created_at', '>=', $thursday],
      ['created_at', '<', $friday]
    ])->get()->count();
    $friday_recruitments = Recruitment::where([
      ['created_at', '>=', $friday],
      ['created_at', '<', $saturday]
    ])->get()->count();
    $saturday_recruitments = Recruitment::where([
      ['created_at', '>=', $saturday],
      ['created_at', '<', $sunday]
    ])->get()->count();

    $recruitments_result = [
      $last_sunday_recruitments,
      $monday_recruitments,
      $tuesday_recruitments,
      $wednesday_recruitments,
      $thursday_recruitments,
      $friday_recruitments,
      $saturday_recruitments
    ];

    // Total Applications of Current Week
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
    $saturday_applications = Application::where([
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
      "accounts" => [
        "total_users" => $users_result,
        "total_students" => $students_result,
        "total_recruiters" =>  $recruiters_result,
      ],
      "jobs" => [
        "total_recruitments" => $recruitments_result,
        "total_applications" => $applications_result
      ],
      // "events" => [
      //   "events" => $events,
      //   "participants" => $participants
      // ]
    ];

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $data,
    ], 200);
  }
}
