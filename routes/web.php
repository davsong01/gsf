<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


Route::get('/token', 'ChapterController@generate')->name('token');

Route::get('/campus', 'ChapterController@campusUpdate')->name('campus.update');
Route::post('/campus', 'ChapterController@campusView')->name('campus.view');
Route::post('/campus/{id}', 'ChapterController@campussave')->name('campus.save');
Auth::routes();
Auth::routes(['verify' => false, 'register' => false] );

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    echo "<p>Fully optimized.*</p>";
});

Route::get('/runcron', 'HomeController@cron');

Route::get('/', function(){
    echo('We are working on something cool here');
});

//Stakeholder Account
Route::get('stakeholders/login', 'StakeholderLoginController@showStakeholderLoginForm')->name('stakeholder.loginpage');
Route::post('stakeholders/login', 'StakeholderLoginController@stakeHolderLogin')->name('stakeholder.login');
Route::get('/stakeholderdashboard', 'StakeholderAccountController@index')->name('stakeholder.dashboard');
Route::get('/stakeholderprofile', 'StakeholderAccountController@profile')->name('stakeholder.profile');
Route::post('/stakeholderprofile', 'StakeholderAccountController@saveProfile')->name('stakeholder.saveprofile');
Route::get('/stakeholderlogout', 'StakeholderLoginController@logout')->name('stakeholder.logout');

//Logged in stakeholder Account

Route::middleware(['stakeholder'])->group(function(){
    Route::resource('reports', 'ReportsController');
    Route::get('deletereports/{id}', 'ReportsController@delete')->name('reports.delete');
    Route::post('rejectreports', 'ReportsController@rejectReport')->name('report.reject');
    Route::resource('stakeholderpayment', 'StakeholderPaymentController');
    Route::get('stakeholderpaymentdelete/{id}', 'StakeholderPaymentController@delete')->name('stakeholderpayment.delete');
    Route::get('downloadpop/{id}', 'StakeholderPaymentController@downloadPop')->name('pop.download');
    Route::post('/pop/export', 'StakeholderPaymentController@exportPop')->name('pop.export');
    
});

Route::get('/registration', 'HomeController@index')->name('index');
Route::get('/nec/registration/portal/pay', 'HomeController@necRegistration')->name('nec.registration');
Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay');
Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');

Route::middleware(['auth', 'SwitchUser'])->group(function(){
    Route::get('/account', 'AccountController@index')->name('account');

    Route::get('/reset', function () {
        Artisan::call('migrate:refresh --seed');
        return back()->with('message', 'You have successfully cleared the whole application');
    })->name('database.clear');

    // Official
    Route::group([], function(){
        Route::get('/official/import/index', 'UserController@officialsImportIndex')->name('officials.import.index');
        Route::get('/official/export', 'UserController@officialsExport')->name('officials.export');
        Route::post('/official/import', 'UserController@import')->name('officials.import');
		});

    // nec
    Route::group([], function(){
        Route::get('/nec/import/index', 'UserController@necsImportIndex')->name('necs.import.index');
        Route::get('/nec/export', 'UserController@necsExport')->name('necs.export');
        Route::post('/nec/import', 'UserController@import')->name('necs.import');
		});

    // Alumni
    Route::group([], function(){
        Route::get('/alumnis/import/index', 'UserController@alumnisImportIndex')->name('alumnis.import.index');
        Route::get('/alumnis/export', 'UserController@alumnisExport')->name('alumnis.export');
        Route::post('/alumnis/import', 'UserController@import')->name('alumnis.import');
		});

    // Moderators
    Route::group([], function(){
        Route::get('/moderators/import/index', 'UserController@moderatorsImportIndex')->name('moderators.import.index');
        Route::get('/moderators/export', 'UserController@moderatorsExport')->name('moderators.export');
        Route::post('/moderators/import', 'UserController@import')->name('moderators.import');
		});
		
    // Medical
    Route::group([], function(){
        Route::get('/medical/import/index', 'UserController@medicalImportIndex')->name('medical.import.index');
        Route::get('/medical/export', 'UserController@medicalExport')->name('medical.export');
        Route::post('/medical/import', 'UserController@import')->name('medical.import');
		});
		
    //Admin Only
    Route::group([], function(){
        Route::get('/choir/import/index', 'UserController@choirImportIndex')->name('choir.import.index');
        Route::get('/choir/export', 'UserController@choirExport')->name('choir.export');
        Route::post('/choir/import', 'UserController@import')->name('choir.import');
    });
    //Admin Only
    Route::group([], function(){
        Route::get('/users/import/index', 'UserController@usersImportIndex')->name('users.import.index');
        Route::get('moderator/import/index', 'UserController@usersImportIndex')->name('moderator.users.import.index');
        Route::get('moderator/import/index', 'UserController@usersImportIndex')->name('moderator.users.import.index');
        Route::get('/users/export', 'UserController@usersExport')->name('users.export');
        Route::post('/users/import', 'UserController@import')->name('users.import');
        Route::resource('staff', 'StakeholderController');
        Route::get('staffs/delete/{id}', 'StakeholderController@destroy')->name('staff.delete');

        
    });
    Route::resource('users', 'UserController');
    Route::resource('tempusers', 'TempUserController');
    Route::get('users/delete/{id}', 'UserController@destroy')->name('users.delete');
    Route::get('trashed/users', 'UserController@trashed')->name('users.trashed');
    Route::get('restore/users/{id}', 'UserController@restore')->name('users.restore');

    Route::get('medical', 'UserController@getMedical')->name('user.medical');
    Route::get('choir', 'UserController@getChoir')->name('user.choir');
    Route::get('choir/{id}/edit', 'UserController@editChoir')->name('choir.edit');
    Route::get('medic/{id}/edit', 'UserController@editMedic')->name('medic.edit');
    Route::resource('officials', 'OfficialController');
    Route::resource('chapters', 'ChapterController');
    Route::get('newtoken/{id}', 'ChapterController@generateNewToken')->name('chapter.newtoken');
    Route::resource('fields', 'FieldController');
    Route::get('fields/delete/{id}', 'FieldController@destroy')->name('fields.delete');
    Route::resource('zones', 'ZoneController');
    Route::get('zones/delete/{id}', 'ZoneController@destroy')->name('zones.delete');
    Route::get('chapters/delete/{chapter}', 'ChapterController@destroy')->name('chapters.delete');
    Route::get('/chapter/exporting', 'ChapterController@chaptersExport')->name('chapters.export');
    Route::get('officials/delete/{official}', 'OfficialController@delete')->name('officials.delete');
    Route::get('moderators/delete/{id}', 'ModeratorController@destroy')->name('moderators.delete');

    Route::get('nec', 'UserController@getNec')->name('user.nec');
    Route::get('nec/{id}/edit', 'UserController@editNec')->name('nec.edit');
    Route::get('nec/{id}/edit', 'UserController@editOfficial')->name('official.edit');
    
    Route::get('/switch/{id}', 'SwitchUserController@index')->name('switchuser');
    Route::get('/stopswitching', 'SwitchUserController@stopSwitching')->name('stop.switchuser');
    Route::resource('moderators', 'ModeratorController');
  

    Route::resource('alumni', 'AlumniController');
    Route::get('alumnis/delete/{id}', 'AlumniController@destroy')->name('alumni.delete');
    Route::resource('participants', 'AccountController')->except([
        'create', 'store', 'destroy', 'index', 'show'
	]);

    Route::resource('hostels', 'HostelController');

    Route::get('user/card/{id}', 'AccountController@getCard')->name('user.card');
    Route::get('user/meal/{id}', 'AccountController@getMealTicket')->name('meal.ticket');
    
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
    Route::resource('donations', 'DonationController');

    // Download sample imports
    Route::get('users-export/{type}', 'UserController@getAdminParticipantSample')->name('usersexport.sample');
    
});

 //Get signature Image
 Route::get('stakeholdersignature/{image}', function($image){
    $realpath = base_path() . '/uploads/signatures'. '/' .$image;
        return response()->download($realpath);
});

