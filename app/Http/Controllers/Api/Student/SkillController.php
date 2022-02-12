<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiSkillRequest;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $user = Auth::user();


    $skills = Skill::where('user_id', $user->id)->first();

    if (isset($skills)) {
      // $array_skill = explode(',', $skills->name);
      $skills->skills = json_decode($skills->skills);

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $skills
      ], 200);
    } else {
      return response()->json([
        'status' => 1,
        'code' => 404,
        'data' => (object) [
          'skills' => [],
        ],
        'message' => 'Skills list does not exist.'
      ], 200);
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
  public function store(ApiSkillRequest $request)
  {
    $user = Auth::user();


    $skills = Skill::where('user_id', $user->id)->first();

    if (isset($skills)) {

      return response()->json([
        'status' => 1,
        'code' => 200,
        'message' => 'Your skill already exists.',
        'data' => $skills
      ], 200);
    } else {

      $new_skills = Skill::create([
        'skills' => json_encode($request['skills']),
        'user_id' => $user->id
      ]);

      $new_skills->skills = $request['skills'];

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $new_skills
      ], 200);
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
  public function update(ApiSkillRequest $request, $id)
  {
    $user = Auth::user();


    $skills = Skill::where('user_id', $user->id)->first();

    if (isset($skills)) {

      $skills->update([
        'skills' => json_encode($request['skills']),
        'user_id' => $user->id
      ]);

      $skills->skills = $request['skills'];

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $skills
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 400,
        'message' => 'Something went wrong, please try again later.'
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


    $skills = Skill::where('user_id', $user->id)->first();

    if (isset($skills)) {
      $skills->delete();

      return response()->json([
        'status' => 1,
        'code' => 200,
        'message' => 'Successfully deleted.'
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your skill was not found or has not been created.'
      ], 404);
    }
  }
}
