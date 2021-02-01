<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Auth::routes();
Auth::routes(['verify' => false, 'register' => false] );

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    echo "<p>Fully optimized.*</p>";
});

Route::get('/tac', function () {
   return view('tac');
})->name('tac');

Route::get('/', 'HomeController@index')->name('index');
Route::get('/nec/registration/portal/pay', 'HomeController@necRegistration')->name('nec.registration');
Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay');
Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');


Route::middleware(['auth', 'SwitchUser'])->group(function(){
    Route::get('/account', 'AccountController@index')->name('account');

    // nec
    Route::group([], function(){
        Route::get('/nec/import/index', 'UserController@necsImportIndex')->name('necs.import.index');
        Route::get('/nec/export', 'UserController@necsExport')->name('necs.export');
        Route::post('/nec/import', 'UserController@necsImport')->name('necs.import');
		});

    // Alumni
    Route::group([], function(){
        Route::get('/alumnis/import/index', 'UserController@alumnisImportIndex')->name('alumnis.import.index');
        Route::get('/alumnis/export', 'UserController@alumnisExport')->name('alumnis.export');
        Route::post('/alumnis/import', 'UserController@alumnisImport')->name('alumnis.import');
		});

    // Moderators
    Route::group([], function(){
        Route::get('/moderators/import/index', 'UserController@moderatorsImportIndex')->name('moderators.import.index');
        Route::get('/moderators/export', 'UserController@moderatorsExport')->name('moderators.export');
        Route::post('/moderators/import', 'UserController@moderatorsImport')->name('moderators.import');
		});
		
    //Admin Only
    Route::group([], function(){
        Route::get('/medical/import/index', 'UserController@medicalImportIndex')->name('medical.import.index');
        Route::get('/medical/export', 'UserController@medicalExport')->name('medical.export');
        Route::post('/medical/import', 'UserController@medicalImport')->name('medical.import');
		});
		
    //Admin Only
    Route::group([], function(){
        Route::get('/choir/import/index', 'UserController@choirImportIndex')->name('choir.import.index');
        Route::get('/choir/export', 'UserController@choirExport')->name('choir.export');
        Route::post('/choir/import', 'UserController@choirImport')->name('choir.import');
    });
		//Admin Only
		Route::group([], function(){
			Route::get('/users/import/index', 'UserController@usersImportIndex')->name('users.import.index');
			Route::get('/users/export', 'UserController@usersExport')->name('users.export');
			Route::post('/users/import', 'UserController@usersImport')->name('users.import');
		});
    Route::resource('users', 'UserController');
    Route::get('users/delete/{id}', 'UserController@destroy')->name('users.delete');
    Route::get('trashed/users', 'UserController@trashed')->name('users.trashed');
    Route::get('restore/users', 'UserController@trashed')->name('users.restore');

    Route::get('medical', 'UserController@getMedical')->name('user.medical');
    Route::get('choir', 'UserController@getChoir')->name('user.choir');
    Route::get('choir/{id}/edit', 'UserController@editChoir')->name('choir.edit');
    Route::get('medic/{id}/edit', 'UserController@editMedic')->name('medic.edit');
    Route::get('official', 'UserController@getOfficial')->name('user.official');
    Route::get('nec', 'UserController@getNec')->name('user.nec');
    Route::get('nec/{id}/edit', 'UserController@editNec')->name('nec.edit');
    Route::get('nec/{id}/edit', 'UserController@editOfficial')->name('official.edit');
    
    Route::get('/switch/{id}', 'SwitchUserController@index')->name('switchuser');
    Route::get('/stopswitching', 'SwitchUserController@stopSwitching')->name('stop.switchuser');
    Route::resource('moderators', 'ModeratorController');
    Route::get('moderators/delete/{id}', 'ModeratorController@destroy')->name('moderators.delete');

    Route::resource('alumni', 'AlumniController');
    Route::get('alumnis/delete/{id}', 'AlumniController@destroy')->name('alumni.delete');
    Route::resource('participants', 'AccountController')->except([
			'create', 'store', 'destroy', 'index', 'show'
	]);

    Route::resource('hostels', 'HostelController');

    Route::get('user/card/{id}', 'AccountController@getCard')->name('user.card');
    Route::get('user/meal/{id}', 'AccountController@getMealTicket')->name('meal.ticket');

    // Route::get('id', function(){
    //     return view('card.id');
    // });
    
    Route::get('hostels/delete/{id}', 'HostelController@destroy')->name('hostels.delete');

    Route::resource('foods', 'FoodController');
    Route::get('foods/delete/{id}', 'FoodController@destroy')->name('foods.delete');

    Route::resource('posts', 'PostController');
    Route::get('trashedposts', 'PostController@trashed')->name('posts.trashed');
    Route::get('restorepost/{id}', 'PostController@restore')->name('posts.restore');
    Route::get('posts/approve/{id}', 'PostController@approve')->name('posts.approve');
    Route::get('posts/unapprove/{id}', 'PostController@unapprove')->name('posts.unapprove');
    Route::get('posts/delete/{id}', 'PostController@destroy')->name('posts.delete');
    Route::get('posts/userdelete/{id}', 'PostController@userdelete')->name('posts.userdelete');
    Route::get('postfile/{filename}', 'PostController@getfile')->name('get.file');
    Route::post('postfilereplace', 'PostController@replaceFile')->name('file.replace');

    Route::resource('settings', 'SettingController');
    
    Route::patch('profile', 'SettingController@saveProfile')->name('profile.save');

    Route::resource('materials', 'MaterialController');
    Route::get('material/delete/{id}', 'MaterialController@destroy')->name('materials.delete');

    Route::resource('payouts', 'PayoutController');
  
});

// Route::get('/switch/{id}', 'SwitchUserController@index')->name('switchuser')->middleware('SwitchUser');
// Route::get('/stopswitching', 'SwitchUserController@stopSwitching')->name('stop.switchuser');
//Admin Only Routes
// Route::group(['middleware' => ['auth', 'isAdmin', 'SwitchUser']], function () {
//     Route::get('recommendations', 'AppraisalController@recommendations')->name('recommendations.index');
//     Route::resource('user', 'UserController');
//     Route::resource('designation', 'DesignationController');
//     Route::resource('position', 'PositionController');

//     //Route For Reports
//     Route::get('reports/performance', 'ReportController@getPerformanceAppraisals')->name('reports.appraisals');
//     Route::get('reports/all', 'ReportController@index')->name('reports.index');
//     Route::get('reports/leadership', 'ReportController@leadership')->name('reports.leadership');
//     Route::get('reports/technical', 'ReportController@technical')->name('reports.technical');
//     Route::get('reports/qualifications', 'ReportController@qualifications')->name('reports.qualification');
//     Route::get('reports/professionals', 'ReportController@professionals')->name('reports.professional');
//      Route::get('reports/nontechnical', 'ReportController@professionals')->name('reports.nontechnical');

//     //Single reports
//     Route::get('leadership/{id}', 'ReportController@leadershipSingle')->name('leadership.show');
//     Route::get('technical/{id}', 'ReportController@technicalSingle')->name('technical.show');
//     Route::get('qualification/{id}', 'ReportController@qualificationSingle')->name('qualification.show');
//     Route::get('certification/{id}', 'ReportController@certificationSingle')->name('certification.show');

//     Route::post('reports', 'ReportController@destroy')->name('reports.destroy');
//     Route::post('reports/{id}', 'ReportController@show')->name('reports.show');
   
//     Route::get('import-export', 'UserController@importExport');
//     Route::post('import', 'UserController@import');
//     Route::post('importusers', 'UserController@import');
//     Route::get('export', 'UserController@export')->name('export');
		
//     Route::resource('role', 'RoleController');
//     Route::get('roles/import-export', 'RoleController@importExport');
//     Route::post('import-roles', 'RoleController@import');
//     Route::post('importroles', 'RoleController@import');
//     Route::get('roles/export', 'RoleController@export')->name('roles.export');
//     // Route::get('appraisals/import-export', 'AppraisalController@importExport');
//     // Route::post('import-appraisals', 'AppraisalController@import');
//     // Route::post('importappraisals', 'AppraisalController@import');
//     // Route::get('appraisals/export', 'AppraisalController@export');
		
//     Route::resource('allocation', 'AllocationController');
//     Route::get('allocations/import-export', 'AllocationController@importExport');
//     Route::post('import-allocations', 'AllocationController@import');
//     Route::post('importallocations', 'AllocationController@import');
//     Route::get('allocations/export', 'AllocationController@export')->name('allocations.export');
		
//     Route::resource('department', 'DepartmentController');
//     Route::get('departments/import-export', 'DepartmentController@importExport');
//     Route::post('import-departments', 'DepartmentController@import');
//     Route::post('importdepartments', 'DepartmentController@import');
//     Route::get('departments/export', 'DepartmentController@export')->name('departments.export');
		
//     Route::resource('question', 'QuestionController');
//     Route::get('questions/import-export', 'QuestionController@importExport');
//     Route::post('import-questions', 'QuestionController@import');
//     Route::post('importquestions', 'QuestionController@import');
//     Route::get('questions/export', 'QuestionController@export')->name('questions.export');
		
//     Route::get('designations/import-export', 'DesignationController@importExport');
//     Route::post('import-designations', 'DesignationController@import');
//     Route::post('importdesignations', 'DesignationController@import');
//     Route::get('designations/export', 'DesignationController@export')->name('designations.export');
		
//     Route::resource('unit', 'UnitController');
//     Route::get('units/import-export', 'UnitController@importExport');
//     Route::post('import-units', 'UnitController@import');
//     Route::post('importunits', 'UnitController@import');
//     Route::get('units/export', 'UnitController@export');
		
//     Route::get('positions/import-export', 'PositionController@importExport');
//     Route::post('import-positions', 'PositionController@import');
//     Route::post('importpositions', 'PositionController@import');
//     Route::get('positions/export', 'PositionController@export')->name('positions.export');
		
//     Route::resource('category', 'CategoryController');
//     Route::get('categories/import-export', 'CategoryController@importExport');
//     Route::post('import-categories', 'CategoryController@import');
//     Route::post('importcategories', 'CategoryController@import');
// 		Route::get('categories/export', 'CategoryController@export')->name('categories.export');
		
// 		Route::resource('company', 'CompanyController');
// 		Route::get('companies/export/', 'CompanyController@export');
// 	});


