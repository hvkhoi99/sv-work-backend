<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiUploadPDFRequest;
use App\Models\CV;
use App\Models\StudentProfile;
use App\Models\UserCV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentCVController extends Controller
{
  public function getListCV(Request $request)
  {
    $user = Auth::user();

    $s_profile = StudentProfile::where('user_id', $user->id)->first();

    if (isset($s_profile)) {
      $_limit = $request['_limit'];
      $list_cv = DB::table('user_c_v_s')
        ->join('c_v_s', 'user_c_v_s.cv_id', '=', 'c_v_s.id')
        ->select(
          'user_c_v_s.user_id',
          'c_v_s.*'
        )
        ->where('user_id', $user->id)
        ->orderBy('updated_at', 'desc')
        ->paginate($_limit);

      return response()->json([
        'status' => 1,
        'code' => 200,
        'data' => $list_cv,
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your student profile has not been created.'
      ], 404);
    }
  }

  public function uploadCV(ApiUploadPDFRequest $request)
  {
    $user = Auth::user();

    $s_profile = StudentProfile::where('user_id', $user->id)->first();

    if (isset($s_profile)) {
      $response = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();

      $new_cv = CV::create([
        'title' => $request['title'],
        'name' => $request['name'],
        'link' => $response,
      ]);

      if (isset($new_cv)) {
        UserCV::create([
          'cv_id' => $new_cv->id,
          'user_id' => $user->id
        ]);
      }

      return response()->json([
        'status' => 1,
        'code' => 200,
        'message' => 'Your CV was successfully updated.',
        'data' => $new_cv
      ], 200);
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your student profile has not been created.'
      ], 404);
    }
  }

  public function deleteCV($id)
  {
    $user = Auth::user();

    $s_profile = StudentProfile::where('user_id', $user->id)->first();

    if (isset($s_profile)) {
      $cv = CV::whereId($id)->first();

      if (isset($cv)) {
        $cv->delete();

        return response()->json([
          'status' => 1,
          'code' => 200,
          'message' => 'Successfully deleted CV.'
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Your CV doesn\'t exist.'
        ], 404);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Your student profile has not been created.'
      ], 404);
    }
  }
}
