<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\Recruiter\CandidateController;
use App\Http\Controllers\Api\Recruiter\RecruiterDashboardController;
use App\Http\Controllers\Api\Recruiter\RecruiterEventController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Recruiter\RecruiterProfileController;
use App\Http\Controllers\Api\Recruiter\RecruitmentController;
use App\Http\Controllers\Api\Student\CertificateController;
use App\Http\Controllers\Api\Student\CompanyController;
use App\Http\Controllers\Api\Student\EducationController;
use App\Http\Controllers\Api\Student\ExperienceController;
use App\Http\Controllers\Api\Student\FollowController;
use App\Http\Controllers\Api\Student\JobController;
use App\Http\Controllers\Api\Student\LanguageController;
use App\Http\Controllers\Api\Student\SkillController;
use App\Http\Controllers\Api\Student\StudentApplicationController;
use App\Http\Controllers\Api\Student\StudentDashboardController;
use App\Http\Controllers\Api\Student\StudentEventController;
use App\Http\Controllers\Api\Student\StudentProfileController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

//Auth
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/account', [UserController::class, 'account'])->name('account');
Route::get('/error', [UserController::class, 'showError'])->name('showError');

// Admin - Student - Recruiter 
Route::group([
  'middleware' => 'auth:api',
], function () {
  // Change Password
  Route::prefix('auth/password')->group(function () {
    Route::put('change', [UserController::class, 'changePassword']);
  });


  // Admin
  Route::group([
    'name' => 'admin.',
    'prefix' => 'admin',
    'middleware' => 'role:admin'
  ], function () {
    // Total 
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Recruiters
    Route::get('recruiters', [AdminController::class, 'recruiters'])->name('recruiters');

    // Change password
    Route::post('/password/change', [UserController::class, 'changePassword']);

    // Admin -> Recruiter
    Route::prefix('recruiter')->group(function () {
      Route::get('{id}', [AdminController::class, 'showRecruiter']);
      Route::post('search-by-name', [AdminController::class, 'findRecruiter']);
      Route::post('{id}', [AdminController::class, 'verifyRecruiter']);
    });
  });

  // Student
  Route::group([
    'name' => 'student.',
    'prefix' => 'student',
    'middleware' => 'role:student'
  ], function () {

    // Student Account
    Route::post('/password/change', [UserController::class, 'changePassword']);

    // Student Profile
    Route::prefix('profile')->group(function () {
      Route::get('index', [StudentProfileController::class, 'index']);
      Route::post('store', [StudentProfileController::class, 'store']);
      Route::put('{id}', [StudentProfileController::class, 'update']);
      Route::put('over-view/{id}', [StudentProfileController::class, 'updateStudentOverview']);
      Route::post('job', [StudentProfileController::class, 'openJob']);
      Route::post('avatar/change', [StudentProfileController::class, 'changeAvatar']);
    });

    // Student Experiences
    Route::prefix('experience')->group(function () {
      Route::get('index', [ExperienceController::class, 'index']);
      Route::post('store', [ExperienceController::class, 'store']);
      Route::put('{id}', [ExperienceController::class, 'update']);
      Route::delete('{id}', [ExperienceController::class, 'destroy']);
    });

    // Student Education
    Route::prefix('education')->group(function () {
      Route::get('index', [EducationController::class, 'index']);
      Route::post('store', [EducationController::class, 'store']);
      Route::put('{id}', [EducationController::class, 'update']);
      Route::delete('{id}', [EducationController::class, 'destroy']);
    });

    // Student Skill
    Route::prefix('skill')->group(function () {
      Route::get('index', [SkillController::class, 'index']);
      Route::post('store', [SkillController::class, 'store']);
      Route::put('{id}', [SkillController::class, 'update']);
      Route::delete('{id}', [SkillController::class, 'destroy']);
    });

    // Student Certificate
    Route::prefix('certificate')->group(function () {
      Route::get('index', [CertificateController::class, 'index']);
      Route::post('store', [CertificateController::class, 'store']);
      Route::put('{id}', [CertificateController::class, 'update']);
      Route::delete('{id}', [CertificateController::class, 'destroy']);
    });

    // Student Language
    Route::prefix('language')->group(function () {
      Route::get('index', [LanguageController::class, 'index']);
      Route::post('store', [LanguageController::class, 'store']);
      Route::put('{id}', [LanguageController::class, 'update']);
      Route::delete('{id}', [LanguageController::class, 'destroy']);
    });

    // Job 
    Route::prefix('job')->group(function () {
      Route::get('{id}', [JobController::class, 'showJob']);
    });

    // Company
    Route::prefix('company')->group(function () {
      Route::get('{id}', [CompanyController::class, 'companyInfo']);
    });

    // Student -> Dashboard
    Route::prefix('dashboard')->group(function () {
      Route::get('applied-jobs', [StudentDashboardController::class, 'appliedJobs']);
      Route::get('company-followed', [StudentDashboardController::class, 'companyFollowed']);
      Route::get('saved-jobs', [StudentDashboardController::class, 'savedJobs']);
      Route::get('invited-jobs', [StudentDashboardController::class, 'invitedJobs']);
    });

    // Student Application (apply, save)
    Route::prefix('recruitment')->group(function () {
      Route::put('{id}/apply', [StudentApplicationController::class, 'apply']);
      Route::put('{id}/save', [StudentApplicationController::class, 'saveJob']);
      Route::put('{id}/accept-invited', [StudentApplicationController::class, 'acceptInvitedJob']);
      Route::put('{id}/reject-invited', [StudentApplicationController::class, 'rejectInvitedJob']);
    });

    // Event
    Route::prefix('event')->group(function () {
      Route::post('{id}', [StudentEventController::class, 'join']);
    });

    // Student -> Recruiter
    Route::group([
      'name' => 'recruiter.',
      'prefix' => 'recruiter'
    ], function () {

      // Student -> Recruiter -> Recruitment
      Route::prefix('recruitment')->group(function () {
        Route::get('{id}', [RecruitmentController::class, 'show']);
        Route::post('store', [RecruitmentController::class, 'store']);
        Route::put('{id}', [RecruitmentController::class, 'update']);
        Route::delete('{id}', [RecruitmentController::class, 'destroy']);
        Route::put('{id}/close', [RecruitmentController::class, 'close']);

        // Application (invite)
        Route::put('{id}/candidate/{candidateId}/approve', [CandidateController::class, 'approve']);
        Route::get('{id}/candidates', [RecruitmentController::class, 'candidates']);
      });

      // Student -> Recruiter -> Dashboard
      Route::prefix('dashboard')->group(function () {
        Route::get('index', [RecruiterDashboardController::class, 'index']);
        Route::get('available-recruitments', [RecruiterDashboardController::class, 'availableRecruitments']);
        Route::get('closed-recruitments', [RecruiterDashboardController::class, 'closedRecruitments']);
      });

      // Student -> Recruiter -> Profile
      Route::prefix('profile')->group(function () {
        Route::get('index', [RecruiterProfileController::class, 'index']);
        Route::post('store', [RecruiterProfileController::class, 'store']);
        Route::get('{id}', [RecruiterProfileController::class, 'show']);
        Route::put('{id}', [RecruiterProfileController::class, 'update']);
        Route::put('{id}/updateDescription', [RecruiterProfileController::class, 'updateDescription']);
        Route::post('avatar/change', [RecruiterProfileController::class, 'changeRecruiterAvatar']);
      });

      // Student -> Recruiter -> Follow
      Route::post('{id}/follow', [FollowController::class, 'follow']);

      // Event
      Route::prefix('event')->group(function () {
        Route::get('{id}', [RecruiterEventController::class, 'show']);
        Route::post('store', [RecruiterEventController::class, 'store']);
        Route::put('{id}', [RecruiterEventController::class, 'update']);
        Route::delete('{id}', [RecruiterEventController::class, 'destroy']);
        Route::post('{id}/close', [RecruiterEventController::class, 'close']);
      });

      // Manage event
      Route::prefix('manage-event')->group(function () {
        Route::get('index', [RecruiterEventController::class, 'dashboardIndex']);
        Route::get('posted', [RecruiterEventController::class, 'posted']);
        Route::get('closed', [RecruiterEventController::class, 'closed']);
      });

      // Candidate
      Route::prefix('candidate')->group(function () {
        Route::get('{id}', [CandidateController::class, 'candidateInfo']);
        Route::get('jobsInvite/list', [CandidateController::class, 'jobsInvite']);
        Route::post('inviteCandidate', [StudentApplicationController::class, 'inviteCandidate']);
      });
    });
  });

  // Recruiter
  Route::group([
    'name' => 'recruiter.',
    'prefix' => 'recruiter',
    'middleware' => 'role:recruiter'
  ], function () {

    // Recruiter account
    Route::post('/password/change', [UserController::class, 'changePassword']);

    // Recruiter Profile
    Route::prefix('profile')->group(function () {
      Route::get('index', [RecruiterProfileController::class, 'index']);
      Route::post('store', [RecruiterProfileController::class, 'store']);
      Route::post('{id}', [RecruiterProfileController::class, 'edit']);
      Route::put('{id}', [RecruiterProfileController::class, 'update']);
      Route::put('{id}/updateDescription', [RecruiterProfileController::class, 'updateDescription']);
      Route::post('avatar/change', [RecruiterProfileController::class, 'changeRecruiterAvatar']);
    });

    // Recruiter -> Recruitment
    Route::prefix('recruitment')->group(function () {
      Route::get('{id}', [RecruitmentController::class, 'show']);
      Route::post('store', [RecruitmentController::class, 'store']);
      Route::put('{id}', [RecruitmentController::class, 'update']);
      Route::delete('{id}', [RecruitmentController::class, 'destroy']);
      Route::put('{id}/close', [RecruitmentController::class, 'close']);

      // Application (invite)
      Route::put('{id}/candidate/{candidateId}/approve', [CandidateController::class, 'approve']);
      Route::get('{id}/candidates', [RecruitmentController::class, 'candidates']);
    });

    // Recruiter -> Dashboard
    Route::prefix('dashboard')->group(function () {
      Route::get('index', [RecruiterDashboardController::class, 'index']);
      Route::get('available-recruitments', [RecruiterDashboardController::class, 'availableRecruitments']);
      Route::get('closed-recruitments', [RecruiterDashboardController::class, 'closedRecruitments']);
    });

    // Event
    Route::prefix('event')->group(function () {
      Route::get('{id}', [RecruiterEventController::class, 'show']);
      Route::post('store', [RecruiterEventController::class, 'store']);
      Route::put('{id}', [RecruiterEventController::class, 'update']);
      Route::delete('{id}', [RecruiterEventController::class, 'destroy']);
      Route::post('{id}/close', [RecruiterEventController::class, 'close']);
    });

    // Manage event
    Route::prefix('manage-event')->group(function () {
      Route::get('index', [RecruiterEventController::class, 'dashboardIndex']);
      Route::get('posted', [RecruiterEventController::class, 'posted']);
      Route::get('closed', [RecruiterEventController::class, 'closed']);
    });

    // Candidate
    Route::prefix('candidate')->group(function () {
      Route::get('{id}', [CandidateController::class, 'candidateInfo']);
      Route::get('jobsInvite/list', [CandidateController::class, 'jobsInvite']);
      Route::post('inviteCandidate', [StudentApplicationController::class, 'inviteCandidate']);
    });
  });

  // User
  Route::get('events', [RecruiterEventController::class, 'index']);
});

// Test
Route::get('test', [TestController::class, 'test']);
Route::post('upload', [TestController::class, 'upload']);

// Home
Route::get('getTopRecruiters', [HomeController::class, 'getTopRecruiters']);
