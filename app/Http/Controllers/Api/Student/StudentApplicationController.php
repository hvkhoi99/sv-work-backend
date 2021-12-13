<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use App\Models\StudentProfile;
use Illuminate\Http\Request;

class StudentApplicationController extends Controller
{
    public function apply(Request $request, $id)
    {
        $user = $request->user();

        if (isset($user)) {
            $s_profile = StudentProfile::where('user_id', $user->id)->first();
            
            if (isset($s_profile) && $s_profile->open_for_job) {
                $exist_recruitment = Recruitment::whereId($id)->first();

                if (isset($exist_recruitment) && !($exist_recruitment->is_closed)) {
    
                    // $exist_invited_recruitment = Application::where('is_invited', true)->first();
    
                    // if (!isset($exist_invited_recruitment)) {
    
                        $exist_application = Application::where('recruitment_id', $id)->first();
    
                        if (isset($exist_application)) {
    
                            if ($exist_application->is_invited == false) {
    
                                $exist_application->update([
                                    'is_applied' => !($exist_application->is_applied)
                                ]);
    
                                return response()->json([
                                    'status' => 1,
                                    'code' => 200,
                                    'message' => 'Successfully updated.',
                                    'data' => $exist_application
                                ], 200);
                            } else {
                                return response()->json([
                                    'status' => 0,
                                    'code' => 405,
                                    'message' => 'You cannot take this action because your application has been approved.'
                                ], 405);
                            }
                        } else {
    
                            $new_application = Application::create([
                                'state' => null,
                                'is_invited' => null,
                                'is_applied' => true,
                                'is_saved' => false,
                                'user_id' => $user->id,
                                'recruitment_id' => $id
                            ]);
    
                            return response()->json([
                                'status' => 1,
                                'code' => 200,
                                'message' => 'Successfully applied.',
                                'data' => $new_application
                            ], 200);
                        }
                    // } else {
                    //     return response()->json([
                    //         'status' => 0,
                    //         'code' => 405,
                    //         'message' => 'You cannot take this action because your job has been opened.'
                    //     ], 405);
                    // }
                } else {
                    return response()->json([
                        'status' => 1,
                        'code' => 404,
                        'message' => 'The recruitment doesn\'t exist or closed.'
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Your student profile has not been created or your open job option is on.',
                    'data' => $user
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

    public function saveJob(Request $request, $id)
    {
        $user = $request->user();

        if (isset($user)) {
            $s_profile = StudentProfile::where('user_id', $user->id)->first();
            $exist_recruitment = Recruitment::whereId($id)->first();

            if (isset($s_profile) && isset($exist_recruitment)) {

                $exist_application = Application::where('recruitment_id', $id)->first();

                if (isset($exist_application)) {

                    $exist_application->update([
                        'is_saved' => !($exist_application->is_saved)
                    ]);

                    return response()->json([
                        'status' => 1,
                        'code' => 200,
                        'message' => 'Successfully updated.',
                        'data' => $exist_application
                    ], 200);
                } else {
                    $new_application = Application::create([
                        'state' => null,
                        'is_invited' => null,
                        'is_applied' => false,
                        'is_saved' => true,
                        'user_id' => $user->id,
                        'recruitment_id' => $id
                    ]);

                    return response()->json([
                        'status' => 1,
                        'code' => 200,
                        'message' => 'Successfully saved.',
                        'data' => $new_application
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 1,
                    'code' => 404,
                    'message' => 'The recruitment or your student profile doesn\'t exist.'
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

    public function approve(Request $request, $id)
    {
        $user = $request->user();

        if (isset($user)) {

            $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
            $exist_recruitment = Recruitment::whereId($id)->first();

            if (isset($r_profile) && isset($exist_recruitment)) {

                $exist_application = Application::where('recruitment_id', $id)->first();

                if (isset($exist_application) && $exist_application->is_applied) {

                    $exist_application->update([
                        'is_invited' => !($exist_application->is_invited)
                    ]);

                    return response()->json([
                        'status' => 1,
                        'code' => 200,
                        'message' => $exist_application->is_invited ? 'Successfully approve.' : 'Successfully reject.',
                        'data' => $exist_application
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'code' => 404,
                        'message' => 'No applications found.'
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => 1,
                    'code' => 404,
                    'message' => 'The recruitment doesn\'t exist or your recruiter profile does not exist.'
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
