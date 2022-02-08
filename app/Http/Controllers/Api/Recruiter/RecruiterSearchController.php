<?php

namespace App\Http\Controllers\Api\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\Language;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

// use Illuminate\Support\Facades\DB;

class RecruiterSearchController extends Controller
{
  public function getCandidateSearch(Request $request)
  {
    $user = Auth::user();
    $candidates = StudentProfile::query();
    $candidates = $candidates->name($request)->career($request)->location($request)->gender($request)
      ->get(
        [
          'id', 'avatar_link', 'first_name', 'last_name', 'job_title',
          'address', 'gender', 'user_id', 'created_at',
        ]
      )->toArray();

    $languages = Language::query();
    $languages = $languages->language($request)->get()->toArray();
    $educations = Education::query();
    $educations = $educations->education($request)->get()->toArray();
    // $new_languages = array_map(function ($language) {
    //   return $language['user_id'];
    // }, $languages);
    // $new_languages = array_values(array_unique($new_languages, SORT_REGULAR));

    $new_candidates = [];
    if (count($candidates) > 0 && count($languages) > 0 && count($educations) > 0) {
      foreach ($candidates as $candidate) {
        $is_exist_language = in_array($candidate['user_id'], array_column($languages, 'user_id'));
        $is_exist_education = in_array($candidate['user_id'], array_column($educations, 'user_id'));
        $index = array_search($candidate['user_id'], array_column($languages, 'user_id'));
        if ($is_exist_language && $is_exist_education && ($index > -1)) {
          $language['locales'] = json_decode($languages[$index]['locales']);
          $language['user_id'] = $languages[$index]['user_id'];
          $candidate['language'] =  $language;
          array_push($new_candidates, $candidate);
        }
      }
    }

    $perPage = $request["_limit"];
    $current_page = LengthAwarePaginator::resolveCurrentPage();

    $new_pagination_candidates = new LengthAwarePaginator(
      collect($new_candidates)->forPage($current_page, $perPage)->values(),
      count($new_candidates),
      $perPage,
      $current_page,
      ['path' => url(
        $user->role_id === 2
          ? 'api/recruiter/find/candidate'
          : 'api/student/recruiter/find/candidate'
      )]
    );

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

    return response()->json([
      'status' => 1,
      'code' => 200,
      'data' => $new_pagination_candidates,
      // 'data1' => $languages
      // 'type' => gettype($languages)
    ], 200);
  }
}
