<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecruiterSearchController extends Controller
{
  public function getCandidateSearch(Request $request)
  {
    // $candidates = StudentProfile::query();
    // $candidates = $candidates->name($request)->career($request)->location($request)->gender($request)->get(
    //   ['id', 'avatar_link', 'first_name', 'last_name', 'job_title', 'address', 'created_at']
    // );

    $languages = Language::query();
    $languages = $languages->language($request)->get(['user_id'])->toArray();
    $languages = array_values(array_unique($languages, SORT_REGULAR));

    $new_languages = [];
    foreach ($languages as $language) {
      array_push($new_languages, $language->user_id);
    }
    // array_values(array_unique($array));
    //array_unique($array, SORT_REGULAR);

    // if (count($candidates) > 0 && count($languages) > 0) {
    //   $candidates = array_filter($candidates, function ($candidate) {
    //     if (in_array($candidate->user_id, $languages)) {
    //       return $candidate;
    //     }
    //   });
    // }

    // $results =
    //   // DB::table('student_profiles')
    //   StudentProfile::join('languages', 'student_profiles.user_id', '=', 'languages.user_id')
    //   ->join('education', 'student_profiles.user_id', '=', 'education.user_id')
    //   ->select(
    //     'student_profiles.id',
    //     'student_profiles.avatar_link',
    //     'student_profiles.first_name',
    //     'student_profiles.last_name',
    //     'student_profiles.address',
    //     'student_profiles.job_title',
    //     'student_profiles.gender',
    //     'student_profiles.created_at',
    //     // ['languages.locales' => json_decode('languages.locales')],
    //     'languages.locales',
    //     'education.school',
    //   )->get();

    // $results = $results->name($request)->get();

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $new_languages,
      // 'type' => gettype($languages)
    ], 200);
  }
}
