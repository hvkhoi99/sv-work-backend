<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiEventRequest;
use App\Models\Event;
use App\Models\ParticipantEvent;
use App\Models\RecruiterProfile;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class RecruiterEventController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $user = Auth::user();

    if (isset($user)) {
      $events = Event::where('is_closed', false)->orderBy('created_at', 'desc')->get();

      foreach ($events as $event) {
        $participants = ParticipantEvent::where('event_id', $event->id)->get();
        $event['participants'] = count($participants);
      }

      $perPage = 10;
      $current_page = LengthAwarePaginator::resolveCurrentPage();

      $new_events = new LengthAwarePaginator(
        collect($events)->forPage($current_page, $perPage)->values(),
        count($events),
        $perPage,
        $current_page,
        ['path' => url('api/recruiter/manage-event/index')]
      );

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $new_events
      ]);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 401,
        'message' => 'UNAUTHORIZED'
      ], 401);
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(ApiEventRequest $request)
  {
    $user = Auth::user();

    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

      if (isset($r_profile)) {
        $new_event = Event::create([
          'title' => $request['title'],
          'description' => $request['description'],
          'location' => $request['location'],
          'start_date' => $request['start_date'],
          'end_date' => $request['end_date'],
          'image_link' => $request['image_link'],
          'is_closed' => false,
          'user_id' => $user->id
        ]);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $new_event
        ]);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your recruiter profile was not found or has not been created.'
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

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $event = Event::whereId($id)->first();

      if (isset($event)) {
        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $event
        ]);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Event was not found or has not been created.'
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

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(ApiEventRequest $request, $id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

      if (isset($r_profile)) {
        $event = Event::whereId($id)->where('user_id', $user->id)->first();

        if (isset($event)) {

          $event->update($request->all());

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully updated.'
          ]);
        } else {
          return response()->json([
            'status' => 0,
            'code' => 404,
            'message' => 'Event was not found or has not been created.'
          ], 404);
        }
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your recruiter profile was not found or has not been created.'
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

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

      if (isset($r_profile)) {
        $event = Event::whereId($id)->where('user_id', $user->id)->first();

        if (isset($event)) {

          $event->delete();

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully deleted.'
          ]);
        } else {
          return response()->json([
            'status' => 0,
            'code' => 404,
            'message' => 'Event was not found or has not been created.'
          ], 404);
        }
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your recruiter profile was not found or has not been created.'
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

  public function dashboardIndex()
  {
    $user = Auth::user();

    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();
      if (isset($r_profile)) {
        $availableEvents = Event::where('user_id', $r_profile->user_id)->where('is_closed', false)->get();
        $closedEvents = Event::where('user_id', $r_profile->user_id)->where('is_closed', true)->get();
        $data['availableEvents'] = count($availableEvents);
        $data['closedEvents'] = count($closedEvents);
        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $data,
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your recruiter profile does not exist.'
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

  public function posted()
  {
    $user = Auth::user();

    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

      if (isset($r_profile)) {
        $postedEvents = Event::where('user_id', $r_profile->user_id)->where('is_closed', false)->orderBy('created_at', 'desc')->get();

        foreach ($postedEvents as $event) {
          $participants = ParticipantEvent::where('event_id', $event->id)->get();
          $event['participants'] = count($participants);
        }

        $perPage = 10;
        $current_page = LengthAwarePaginator::resolveCurrentPage();

        $new_postedEvents = new LengthAwarePaginator(
          collect($postedEvents)->forPage($current_page, $perPage)->values(),
          count($postedEvents),
          $perPage,
          $current_page,
          ['path' => url('api/recruiter/manage-event/index')]
        );

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $new_postedEvents,
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your recruiter profile does not exist.'
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

  public function closed()
  {
    $user = Auth::user();

    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

      if (isset($r_profile)) {
        $closedEvents = Event::where('user_id', $r_profile->user_id)->where('is_closed', true)->orderBy('created_at', 'desc')->get();

        foreach ($closedEvents as $event) {
          $participants = ParticipantEvent::where('event_id', $event->id)->get();
          $event['participants'] = count($participants);
        }

        $perPage = 10;
        $current_page = LengthAwarePaginator::resolveCurrentPage();

        $new_closedEvents = new LengthAwarePaginator(
          collect($closedEvents)->forPage($current_page, $perPage)->values(),
          count($closedEvents),
          $perPage,
          $current_page,
          ['path' => url('api/recruiter/manage-event/index')]
        );

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $new_closedEvents,
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your recruiter profile does not exist.'
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

  public function close($id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $r_profile = RecruiterProfile::where('user_id', $user->id)->first();

      if (isset($r_profile)) {
        $exist_event = Event::whereId($id)->where('user_id', $r_profile->user_id)->first();

        if (isset($exist_event)) {
          $exist_event->update([
            'is_closed' => !($exist_event->is_closed)
          ]);

          return response()->json([
            'status' => 1,
            'code' => 200,
            'message' => 'Successfully updated.'
          ], 200);
        } else {
          return response()->json([
            'status' => 0,
            'code' => 404,
            'message' => 'Your event does not exist.'
          ], 404);
        }
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your recruiter profile does not exist.'
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
