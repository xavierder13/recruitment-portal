<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Authentication Route
Route::prefix('auth')->group(function(){
    Route::get('/init', [
        'uses' => 'API\AuthController@init',
        'as' => 'auth.init'
    ])->middleware('auth:api');

    Route::post('/login', [
        'uses' => 'API\AuthController@login',
        'as' => 'auth.login'
    ]);

    Route::post('/register', [
        'uses' => 'API/AuthController@register',
        'as' => 'auth.register'
    ]);

    Route::get('/logout', [
        'uses' => 'API\AuthController@logout',
        'as' => 'auth.logout'
    ])->middleware('auth:api');
});

// User Routes
Route::group(['prefix' => 'user', 'middleware' => ['auth:api', 'user.maintenance']], function(){
    Route::get('/index', [
        'uses' => 'API\UserController@index',
        'as' => 'user.index',
    ]);

    Route::get('/create', [
        'uses' => 'API\UserController@create',
        'as' => 'user.create',
    ]);

    Route::post('/store', [
        'uses' => 'API\UserController@store',
        'as' => 'user.store',
    ]);

    Route::get('/edit/{id}', [
        'uses' => 'API\UserController@edit',
        'as' => 'user.edit',
    ]);

    Route::post('/update/{id}', [
        'uses' => 'API\UserController@update',
        'as' => 'user.update',
    ]);

    Route::post('/update_profile/{id}', [
        'uses' => 'API\UserController@update_profile',
        'as' => 'user.update_profile',
    ]);

    Route::post('/delete', [
        'uses' => 'API\UserController@delete',
        'as' => 'user.delete',
    ]);

    Route::get('/roles_permissions', [
        'uses' => 'API\UserController@userRolesPermissions',
        'as' => 'user.roles_permissions',
    ]);

});

//Permissions
Route::group(['prefix' => 'permission', 'middleware' => ['auth:api', 'permission.maintenance']], function(){
    Route::get('/index', [
        'uses' => 'API\PermissionController@index',
        'as' => 'permission.index',
    ]);
    Route::get('/create', [
        'uses' => 'API\PermissionController@create',
        'as' => 'permission.create',
    ]);
    Route::post('/store', [
        'uses' => 'API\PermissionController@store',
        'as' => 'permission.store',
    ]);
    Route::post('/edit', [
        'uses' => 'API\PermissionController@edit',
        'as' => 'permission.edit',
    ]);
    Route::post('/update/{id}', [
        'uses' => 'API\PermissionController@update',
        'as' => 'permission.update',
    ]);
    Route::post('/delete', [
        'uses' => 'API\PermissionController@delete',
        'as' => 'permission.delete',
    ]);

});

//Roles
Route::group(['prefix' => 'role', 'middleware' => ['auth:api', 'role.maintenance']], function(){
    Route::get('/index', [
        'uses' => 'API\RoleController@index',
        'as' => 'role.index',
    ]);
    Route::get('/create', [
        'uses' => 'API\RoleController@create',
        'as' => 'role.create',
    ]);
    Route::post('/store', [
        'uses' => 'API\RoleController@store',
        'as' => 'role.store',
    ]);
    Route::post('/edit', [
        'uses' => 'API\RoleController@edit',
        'as' => 'role.edit',
    ]);
    Route::post('/update/{id}', [
        'uses' => 'API\RoleController@update',
        'as' => 'role.update',
    ]);
    Route::post('/delete', [
        'uses' => 'API\RoleController@delete',
        'as' => 'role.delete',
    ]);

});

// Branch Routes
Route::group(['prefix' => 'branch', 'middleware' => ['auth:api', 'branch.maintenance']], function(){
  Route::get('/index', [
      'uses' => 'API\BranchController@index',
      'as' => 'branch.index',
  ]);
  Route::get('/create', [
      'uses' => 'API\BranchController@create',
      'as' => 'branch.create',
  ]);
  Route::post('/store', [
      'uses' => 'API\BranchController@store',
      'as' => 'branch.store',
  ]);
  Route::post('/edit', [
      'uses' => 'API\BranchController@edit',
      'as' => 'branch.edit',
  ]);
  Route::post('/update/{id}', [
      'uses' => 'API\BranchController@update',
      'as' => 'branch.update',
  ]);
  Route::post('/delete', [
      'uses' => 'API\BranchController@delete',
      'as' => 'branch.delete',
  ]);
});

// Position Routes
Route::group(['prefix' => 'position', 'middleware' => ['auth:api', 'position.maintenance']], function(){
  Route::get('/index', [
      'uses' => 'API\PositionController@index',
      'as' => 'position.index',
  ]);
  Route::get('/create', [
      'uses' => 'API\PositionController@create',
      'as' => 'position.create',
  ]);
  Route::post('/store', [
      'uses' => 'API\PositionController@store',
      'as' => 'position.store',
  ]);
  Route::post('/edit', [
      'uses' => 'API\PositionController@edit',
      'as' => 'position.edit',
  ]);
  Route::post('/update/{id}', [
      'uses' => 'API\PositionController@update',
      'as' => 'position.update',
  ]);
  Route::post('/delete', [
      'uses' => 'API\PositionController@delete',
      'as' => 'position.delete',
  ]);
  
});

//Activity Logs
Route::group(['prefix' => 'activity_logs', 'middleware' => ['auth:api', 'activity.logs']], function(){
  Route::get('/index', [
    'uses' => 'API\ActivityLogController@activity_logs',
    'as' => 'activity_logs.index',
  ]);
});

// Job Vacancy
Route::group(['prefix' => 'job_vacancy', 'middleware' => ['auth:api', 'jobvacancy.maintenance']], function(){
	Route::post('/store', [
		'uses' => 'API\JobVacancyController@store',
		'as' => 'job_vacancy.store',
	]);

	Route::get('/view', [
		'uses' => 'API\JobVacancyController@view',
		'as' => 'job_vacancy.view',
	]);

	Route::get('/delete_job_vacancy/{id}', [
		'uses' => 'API\JobVacancyController@delete_job_vacancy',
		'as' => 'job_vacancy.delete_job_vacancy'
	]);

	Route::get('/edit_job_vacancy/{id}', [
		'uses' => 'API\JobVacancyController@edit_job_vacancy',
		'as' => 'job_vacancy.edit_job_vacancy'
	]);

	Route::post('/update', [
		'uses' => 'API\JobVacancyController@update',
		'as' => 'job_vacancy.update'
	]);
});

// Job Applicant
Route::group(['prefix' => 'job_applicant', 'middleware' => ['auth:api', 'applicant.maintenance']], function(){
	Route::get('/get_applicants_old', [
		'uses' => 'API\ApplicantController@get_applicants_old',
		'as' => 'job_applicant.get_applicants_old',
	]);
  Route::get('/get_applicants_new', [
		'uses' => 'API\ApplicantController@get_applicants_new',
		'as' => 'job_applicant.get_applicants_new',
	]);

	Route::get('/view_applicants/{id}', [
		'uses' => 'API\ApplicantController@view_applicants',
		'as' => 'job_applicant.view_applicants'
	]);

  Route::get('/view_applicants_new/{id}', [
		'uses' => 'API\ApplicantController@view_applicants_new',
		'as' => 'job_applicant.view_applicants_new'
	]);

	Route::get('/delete_applicant/{id}', [
		'uses' => 'API\ApplicantController@delete_applicant',
		'as' => 'job_applicant.delete_application'
	]);

    Route::post('/export_applicants', [
        'uses' => 'API\ApplicantController@export_applicants',
        'as' => 'job_applicant.export_applicants',
    ]);

    Route::post('/export_applicants_new', [
        'uses' => 'API\ApplicantController@export_applicants_new',
        'as' => 'job_applicant.export_applicants_new',
    ]);

    Route::post('/export_total_number_of_applicants', [
        'uses' => 'API\ApplicantController@export_total_number_of_applicants',
        'as' => 'job_applicant.export_total_number_of_applicants',
    ]);

	Route::post('/update_status', [
		'uses' => 'API\ApplicantController@update_status',
		'as' => 'job_applicant.update_status',
	]);

    Route::post('/file/download', [
        'uses' => 'API\ApplicantController@download_file',
        'as' => 'applicant.file.download',
    ]);

    Route::get('/applicants_today_list', [
        'uses' => 'API\ApplicantController@applicants_today_list',
        'as' => 'job_applicant.applicants_today_list',
    ]);

    Route::get('/screening_list', [
        'uses' => 'API\ApplicantController@screening_list',
        'as' => 'job_applicant.screening_list',
    ]);

    Route::get('/initial_interview_list', [
        'uses' => 'API\ApplicantController@initial_interview_list',
        'as' => 'job_applicant.initial_interview_list',
    ]);

    Route::get('/iq_test_list', [
        'uses' => 'API\ApplicantController@iq_test_list',
        'as' => 'job_applicant.iq_test_list',
    ]);

    Route::get('/bi_list', [
        'uses' => 'API\ApplicantController@bi_list',
        'as' => 'job_applicant.bi_list',
    ]);

    Route::get('/final_interview_list', [
        'uses' => 'API\ApplicantController@final_interview_list',
        'as' => 'job_applicant.final_interview_list',
    ]);

    Route::get('/orientation_list', [
        'uses' => 'API\ApplicantController@orientation_list',
        'as' => 'job_applicant.orientation_list',
    ]);

    Route::get('/get_all_status_count', [
        'uses' => 'API\ApplicantController@get_all_status_count',
        'as' => 'job_applicant.get_all_status_count',
    ]);

});

Route::get('/job_applicant/delete_applicants_old', [
    'uses' => 'API\ApplicantController@delete_applicants_old',
    'as' => 'job_applicant.delete_applicants_old',
]);

// Jobapplicant File Routes
Route::group(['prefix' => 'jobapplicant_file', 'middleware' => ['auth:api', 'applicant_file.maintenance']], function(){
    Route::get('/get_applicant_files/{id}', [
        'uses' => 'API\ApplicantFileController@get_applicant_files',
        'as' => 'job_applicant_file.get_applicant_files',
    ]);

    Route::post('/store', [
        'uses' => 'API\ApplicantFileController@store',
        'as' => 'job_applicant_file.store',
    ]);

    Route::post('/delete', [
        'uses' => 'API\ApplicantFileController@delete',
        'as' => 'job_applicant_file.delete',
    ]);
    
});

// Public API
Route::group(['prefix' => 'public_api'], function(){
	Route::get('/job_vacancy_public', [
		'uses' => 'API\JobVacancyController@job_vacancy_public',
		'as' => 'public_api.job_vacancy_public',
	]);

	Route::get('/all_job_vacancy_public', [
		'uses' => 'API\JobVacancyController@all_job_vacancy_public',
		'as' => 'public_api.all_job_vacancy_public',
	]);

	Route::post('/search_branches', [
		'uses' => 'API\JobVacancyController@search_branches',
		'as' => 'public_api.search_branches',
	]);

	Route::post('/get_job_vacancy_public', [
		'uses' => 'API\JobVacancyController@get_job_vacancy_public',
		'as' => 'public_api.get_job_vacancy_public',
	]);

	Route::post('/submit_application', [
		'uses' => 'API\ApplicantController@submit_application',
		'as' => 'job_applicant.submit_application',
	]);

	Route::get('/branches', [
		'uses' => 'API\JobVacancyController@get_branches',
		'as' => 'public_api.branches',
	]);

	Route::post('/get_job_details', [
		'uses' => 'API\JobVacancyController@get_job_details',
		'as' => 'public_api.get_job_details',
	]);

	Route::get('/test', [
		'uses' => 'API\JobVacancyController@test',
		'as' => 'public_api.test',
	]);

});


