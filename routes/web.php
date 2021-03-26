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
    Artisan::call('route:clear');
    echo "<p>Fully optimized.*</p>";
});


Route::get('/tac', function () {
   return view('tac');
})->name('tac');

Route::get('/test', 'HomeController@temp')->name('temp');
Route::get('/', 'HomeController@index')->name('index');
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
    });
    Route::resource('users', 'UserController');
    Route::resource('tempusers', 'TempUserController');
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

