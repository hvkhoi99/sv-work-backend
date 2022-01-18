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

    if (isset($user)) {

      $recruitment = Recruitment::whereId($id)->first();

      if (isset($recruitment)) {
        $application = Application::where([
          ['recruitment_id', $recruitment->id],
          ['user_id', $user->id]
        ])->first();
        $company_info = RecruiterProfile::whereId($recruitment->user_id)->first();

        if ( isset($application) && isset($company_info)) {

          $recruitment["application"] = $application;
          
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
    } else {
      return response()->json([
        'status' => 0,
        'code' => 401,
        'message' => 'UNAUTHORIZED'
      ], 401);
    }
  }
}
