<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\RecruiterProfile;
use Illuminate\Http\Request;

class TestController extends Controller
{
  public function test(Request $request)
  {
    // $days = array_rand(array_flip(range(1, 20)), 10);
    // $my_array = array(
    //     (object) [
    //         'value' => 'php 1',
    //         'label' => 'php 1'
    //     ],
    //     (object) [
    //         'value' => 'php 2',
    //         'label' => 'php 2'
    //     ],
    //     (object) [
    //         'value' => 'php 3',
    //         'label' => 'php 3'
    //     ]
    // );

    // $my_array = array_map(function($o) { return collect($o)->only(['label']); }, $my_array);

    $company_info = RecruiterProfile::where([
      ['id', 7]
    ])->first();

    if (isset($company_info)) {
      $company_info = collect($company_info)->only([
        'id', 'company_name', 'company_industry', 'address', 'company_size',
        'contact_email', 'description', 'logo_image_link', 'phone_number',
        'verify'
      ]);

      $follow_status = Follow::where([
        ['r_profile_id', 7],
        ['s_profile_id', 6]
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

    // return response()->json([
    //   'data' => $my_array
    // ], 200);
  }
}
