<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\RecruiterProfile;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
  public function showJob($id)
  {
    $user = Auth::user();

    $recruitment = Recruitment::whereId($id)->first();

    if (isset($recruitment)) {
      if ($user) {
        $application = Application::where([
          ['recruitment_id', $recruitment->id],
          ['user_id', $user->id]
        ])->first();
      } else {
        $application = (object) [
          'id' => 0,
          'state' => null,
          'is_invited' => false,
          'is_applied' => false,
          'is_saved' => false
        ];
      }

      $company_info = RecruiterProfile::where([
        ['user_id', $recruitment->user_id]
      ])->first();

      $recruitment["application"] = $application;

      if (isset($company_info)) {
        $recruitment["company_info"] = collect($company_info)
          ->only(['id', 'logo_image_link', 'company_name', 'verify']);

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $recruitment
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'Current data (application or company info) not available.'
        ], 404);
      }
    } else {
      return response()->json([
        'status' => 0,
        'code' => 404,
        'message' => 'Current data not available.'
      ], 404);
    }
  }
}
