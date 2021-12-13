<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\ParticipantEvent;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentEventController extends Controller
{
    public function join($id)
    {
        $user = Auth::user();

        if (isset($user)) {
            $s_profile = StudentProfile::where('user_id', $user->id)->first();

            if (isset($s_profile)) {
                $event = Event::whereId($id)->where('is_closed', false)->first();

                if (isset($event)) {
                    $exist_pe = ParticipantEvent::where('event_id', $id)->where('user_id', $s_profile->user_id)->first();

                    if (isset($exist_pe)) {
                        $exist_pe->delete();

                        return response()->json([
                            'status' => 1,
                            'code' => 200,
                            'message' => 'Successfully deleted.'
                        ], 200);
                    } else {
                        $new_pe = ParticipantEvent::create([
                            'user_id' => $s_profile->user_id,
                            'event_id' => $id
                        ]);
    
                        return response()->json([
                            'status' => 1,
                            'code' => 200,
                            'data' => $new_pe
                        ], 200);
                    }
                    
                } else {
                    return response()->json([
                        'status' => 0,
                        'code' => 404,
                        'message' => 'This event was not found or has finished.'
                    ], 404);
                }
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
