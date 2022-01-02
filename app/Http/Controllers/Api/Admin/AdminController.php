<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Event;
use App\Models\RecruiterProfile;
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
        $recruiters = RecruiterProfile::where('verify', null)->paginate($_limit);

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
        $user = Auth::user();

        if (isset($user)) {

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
        } else {
            return response()->json([
                'status' => 0,
                'code' => 401,
                'message' => 'UNAUTHORIZED'
            ], 401);
        }
    }

    public function findRecruiter(Request $request)
    {
        $user = $request->user();

        if (isset($user)) {

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
        } else {
            return response()->json([
                'status' => 0,
                'code' => 401,
                'message' => 'UNAUTHORIZED'
            ], 401);
        }
    }

    public function dashboard()
    {
        $user = Auth::user();

        if (isset($user)) {

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
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 400,
                    'message' => 'An incident has occurred. Please try again later.'
                ], 400);
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
