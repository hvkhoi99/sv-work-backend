<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiLanguageRequest;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
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

      $languages = Language::where('user_id', $user->id)->first();

      if (isset($languages)) {
        // $array_language = explode(',', $languages->locale);
        $languages->locales = json_decode($languages->locales);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $languages
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Languages list does not exist.'
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
  public function store(ApiLanguageRequest $request)
  {
    $user = Auth::user();

    if (isset($user)) {

      $languages = Language::where('user_id', $user->id)->first();

      if (isset($languages)) {

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Your language already exists.',
          'data' => $languages
        ], 200);
      } else {

        $new_language = Language::create([
          'locales' => json_encode($request['locales']),
          'user_id' => $user->id
        ]);

        $new_language->locales = $request['locales'];

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $new_language
        ], 200);
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
  public function update(ApiLanguageRequest $request, $id)
  {
    $user = Auth::user();

    if (isset($user)) {

      $languages = Language::where('user_id', $user->id)->first();

      if (isset($languages)) {

        $languages->update([
          'locales' => json_encode($request['locales']),
          'user_id' => $user->id
        ]);

        $languages->locales = $request['locales'];

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully updated',
          'data' => $languages
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 400,
          'message' => 'Something went wrong, please try again later.'
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

      $languages = Language::where('user_id', $user->id)->first();

      if (isset($languages)) {

        $languages->delete();

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully deleted.'
        ], 200);
      } else {

        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your language was not found or has not been created.'
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
