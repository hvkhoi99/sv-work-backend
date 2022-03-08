<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiEventRequest;
use App\Models\Event;
use App\Models\ParticipantEvent;
use App\Models\RecruiterProfile;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class StudentEventController extends Controller
{
  // list events
  public function listEvents(Request $request)
  {
    $events = Event::where('is_closed', false)->orderBy('created_at', 'desc')->get();

    foreach ($events as $event) {
      $count_participants = ParticipantEvent::where('event_id', $event->id)->get()->count();
      $event['count_participants'] = $count_participants;
    }

    $perPage = $request["_limit"];
    $current_page = LengthAwarePaginator::resolveCurrentPage();

    $new_events = new LengthAwarePaginator(
      collect($events)->forPage($current_page, $perPage)->values(),
      count($events),
      $perPage,
      $current_page,
      ['path' => url('api/event/list-event')]
    );

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $new_events
    ]);
  }

  // create event by student
  public function storeByStudent(ApiEventRequest $request)
  {
    $user = Auth::user();

    $s_profile = StudentProfile::where('user_id', $user->id)->first();

    if (isset($s_profile)) {
      $new_event = Event::create([
        'title' => $request['title'],
        'description' => $request['description'],
        'location' => $request['location'],
        'start_date' => $request['start_date'],
        'end_date' => $request['end_date'],
        'image_link' => cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath(),
        'is_closed' => false,
        's_profile_id' => $s_profile->id,
        'r_profile_id' => null
      ]);

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $new_event
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your student profile was not found.'
      ], 200);
    }
  }

  // create event by recruiter
  public function storeByRecruiter(ApiEventRequest $request)
  {
    $user = Auth::user();

    $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

    if (isset($r_profile)) {
      $response = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();

      $new_event = Event::create([
        'title' => $request['title'],
        'description' => $request['description'],
        'location' => $request['location'],
        'start_date' => $request['start_date'],
        'end_date' => $request['end_date'],
        'image_link' => $response,
        'is_closed' => false,
        's_profile_id' => null,
        'r_profile_id' => $r_profile->id
      ]);

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $new_event
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your recruiter profile was not found.'
      ], 200);
    }
  }

  // update event by id
  public function update(ApiEventRequest $request, $id)
  {
    $user = Auth::user();
    $event = Event::whereId($id)->first();
    
    if (isset($event)) {
      $last_event_image = $event->image_link;

      // student
      $s_profile = StudentProfile::where('user_id', $user->id)->first();
      if (isset($s_profile) && ($event->s_profile_id === $s_profile->id)) {
        $response = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();

        $event->update([
          'title' => $request['title'],
          'description' => $request['description'],
          'location' => $request['location'],
          'start_date' => $request['start_date'],
          'end_date' => $request['end_date'],
          $response !== $last_event_image && 'image_link' => $response,
        ]);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully updated.',
          'data' => $event
        ], 200);
      }

      // recruiter
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
      if (isset($r_profile) && ($event->r_profile_id === $r_profile->id)) {
        $response = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();

        $event->update([
          'title' => $request['title'],
          'description' => $request['description'],
          'location' => $request['location'],
          'start_date' => $request['start_date'],
          'end_date' => $request['end_date'],
          $response !== $last_event_image && 'image_link' => $response,
        ]);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully updated.',
          'data' => $event
        ], 200);
      }

      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your Student or Recruiter profile was not found.'
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Event was not found.'
      ], 200);
    }
  }

  // delete event by id
  public function delete($id)
  {
    $user = Auth::user();
    $event = Event::whereId($id)->first();

    if (isset($event)) {

      // student
      $s_profile = StudentProfile::where('user_id', $user->id)->first();
      if (isset($s_profile) && ($event->s_profile_id === $s_profile->id)) {
        $event->delete();

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully deleted.',
        ], 200);
      }

      // recruiter
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
      if (isset($r_profile) && ($event->r_profile_id === $r_profile->id)) {
        $event->delete();

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully deleted.',
        ], 200);
      }

      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your Student or Recruiter profile was not found.'
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Event was not found.'
      ], 200);
    }
  }

  public function close($id)
  {
    $user = Auth::user();
    $event = Event::whereId($id)->first();

    if (isset($event)) {

      // student
      $s_profile = StudentProfile::where('user_id', $user->id)->first();
      if (isset($s_profile) && ($event->s_profile_id === $s_profile->id)) {

        $event->update([
          'is_closed' => true
        ]);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully closed. (by Student)',
          'data' => $event
        ], 200);
      }

      // recruiter
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
      if (isset($r_profile) && ($event->r_profile_id === $r_profile->id)) {

        $event->update([
          'is_closed' => true
        ]);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully closed. (by Recruiter)',
        ], 200);
      }

      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your Student or Recruiter profile was not found.'
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Event was not found.'
      ], 200);
    }
  }

  public function join($id)
  {
    $user = Auth::user();
    $event = Event::where([
      ['id', $id],
      ['is_closed', false]
    ])->first();

    if (isset($event)) {

      // student
      $s_profile = StudentProfile::where('user_id', $user->id)->first();
      if (isset($s_profile)) {
        $exist_pe = ParticipantEvent::where('event_id', $id)->where('user_id', $s_profile->user_id)->first();

        if (isset($exist_pe)) {
          $exist_pe->update([
            'is_joined' => !($exist_pe->is_joined)
          ]);

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully '
              . ($exist_pe->is_joined
                ? 'registered to participate'
                : 'canceled participation')
              . ' in this event.',
            'data' => $exist_pe
          ], 200);
        } else {
          $new_pe = ParticipantEvent::create([
            'user_id' => $s_profile->user_id,
            'event_id' => $id,
            'is_joined' => true
          ]);

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully '
              . ($new_pe->is_joined
                ? 'registered to participate'
                : 'canceled participation')
              . ' in this event.',
            'data' => $new_pe
          ], 200);
        }
      }

      // recruiter
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
      if (isset($r_profile)) {
        $exist_pe = ParticipantEvent::where('event_id', $id)->where('user_id', $r_profile->user_id)->first();

        if (isset($exist_pe)) {
          $exist_pe->update([
            'is_joined' => !($exist_pe->is_joined)
          ]);

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully '
              . ($exist_pe->is_joined
                ? 'registered to participate'
                : 'canceled participation')
              . ' in this event.',
            'data' => $exist_pe
          ], 200);
        } else {
          $new_pe = ParticipantEvent::create([
            'user_id' => $r_profile->user_id,
            'event_id' => $id,
            'is_joined' => true
          ]);

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully '
              . ($new_pe->is_joined
                ? 'registered to participate'
                : 'canceled participation')
              . ' in this event.',
            'data' => $new_pe
          ], 200);
        }
      }

      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your Student or Recruiter profile was not found.'
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Event was not found or has been closed.'
      ], 200);
    }
  }
}
