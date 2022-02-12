<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiExperienceRequest;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExperienceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $user = Auth::user();

    $_limit = $request['_limit'];
    $experiences = Experience::where('user_id', $user->id)
      ->orderBy('updated_at', 'desc')
      ->paginate($_limit);

    if (isset($experiences)) {
      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $experiences
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Experience list does not exist.'
      ], 404);
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
  public function store(ApiExperienceRequest $request)
  {
    $user = Auth::user();


    $new_experience = Experience::create([
      'position' => $request['position'],
      'company' => $request['company'],
      'from_date' => $request['from_date'],
      'to_date' => $request['to_date'],
      'current_job' => $request['current_job'],
      'description' => $request['description'],
      'user_id' => $user->id
    ]);

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $new_experience
    ], 200);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
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
  public function update(ApiExperienceRequest $request, $id)
  {
    $user = Auth::user();


    $experience = Experience::whereId($id)->where('user_id', $user->id)->first();

    if (isset($experience)) {
      $experience->update($request->all());

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $experience
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 400,
        'message' => 'Your experience was not found or has not been created.'
      ], 400);
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


    $experience = Experience::whereId($id)->where('user_id', $user->id)->first();

    if (isset($experience)) {
      $experience->delete();

      return response()->json([
        'status' => 1,
        'code' => 200,
        'message' => 'Successfully deleted.'
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your experience was not found or has not been created.'
      ], 404);
    }
  }
}
