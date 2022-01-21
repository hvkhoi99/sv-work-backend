<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\RecruiterProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
  public function companyInfo($id)
  {
    $user = Auth::user();

    if (isset($user)) {
      $company_info = RecruiterProfile::whereId($id)->first();

      if (isset($company_info)) {
        $company_info = collect($company_info)->only([
          'id', 'company_name', 'company_industry', 'address', 'company_size',
          'contact_email', 'description', 'logo_image_link', 'phone_number',
          'verify'
        ]);

        $follow_status = Follow::where([
          ['r_profile_id', $id],
          ['s_profile_id', $user->id]
        ])->first();

        $is_followed = isset($follow_status) ? $follow_status->is_followed : false;
        $company_info['is_followed'] = $is_followed;

        return response()->json([
          'status' => 1,
          'code' => 200,
          'data' => $company_info
        ], 200);
      } else {
        return response()->json([
          'status' => 0,
          'code' => 404,
          'message' => 'No information found for this company.'
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
