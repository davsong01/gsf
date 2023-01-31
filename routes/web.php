<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/queue', function () {
    // Artisan::call('queue:retry all');
    Artisan::call('queue:work --tries=2');
});

Route::get('/retry', function () {
    Artisan::call('queue:retry all');
});
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

Route::get('/runcron', 'CronController@cron');
Route::get('/test', 'TestController@index');

Route::get('/conference', 'ConferenceController@index');
Route::get('/', 'HomeController@index')->name('home.index');

//Campus auto complete
Route::get('/autocomplete-search', 'HomeController@campusAutocomplete')->name('campus.suggestions');

// Campus routes
Route::get('/people/campuses', 'HomeController@chapters')->name('people.campuses');
Route::post('search', 'HomeController@search')->name('search');
Route::POST('autocompletecampus', 'HomeController@autocomplete')->name('campus.autocomplete');

Route::get('people/singlecampus/{chapter}', 'HomeController@singleCampus')->name('campus.single');
Route::post('contactcampus', 'HomeController@contactCampus')->name('campus.contact');

// Alumni routes
Route::get('/people/alumni', 'HomeController@alumni')->name('people.alumni');
Route::get('people/alumni/{slug}', 'HomeController@singleAlumni')->name('alumni.single');
Route::POST('alumni/search', 'HomeController@alumniSearch')->name('alumni.search');
Route::POST('alumni/contact', 'HomeController@alumniContact')->name('alumni.contact');

// Student routes
Route::get('/people/members', 'HomeController@students')->name('people.students');
Route::get('people/student/{student}', 'HomeController@singleStudent')->name('student.single');
Route::POST('student/search', 'HomeController@studentSearch')->name('student.search');
Route::POST('student/contact', 'HomeController@alumniContact')->name('student.contact');

//Events
Route::get('/people/programs', 'HomeController@programs')->name('people.programs');
// Homepage search
Route::POST('all/search', 'HomeController@generalSearch')->name('general.search');

//Stakeholder Account
Route::get('stakeholders/login', 'StakeholderLoginController@showStakeholderLoginForm')->name('stakeholder.loginpage');
Route::post('stakeholders/login', 'StakeholderLoginController@stakeHolderLogin')->name('stakeholder.login');
Route::get('/stakeholderdashboard', 'StakeholderAccountController@index')->name('stakeholder.dashboard');
Route::get('/stakeholderprofile', 'StakeholderAccountController@profile')->name('stakeholder.profile');
Route::post('/stakeholderprofile', 'StakeholderAccountController@saveProfile')->name('stakeholder.saveprofile');
Route::get('/stakeholderlogout', 'StakeholderLoginController@logout')->name('stakeholder.logout');

Route::get('newalumni', 'HomeController@newAlumni')->name('newalumni');
Route::post('newalumni', 'HomeController@saveNewAlumni')->name('newalumni.save');

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

// Registration links
Route::get('/registration', 'ConferenceController@index')->name('index');
Route::get('/nec/registration/portal/pay', 'ConferenceController@necRegistration')->name('nec.registration');
Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay');
Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');

Route::middleware(['auth', 'SwitchUser'])->group(function(){
    Route::get('/account', 'AccountController@index')->name('account');
    Route::resource('staff', 'StakeholderController');
    Route::get('staffs/delete/{id}', 'StakeholderController@destroy')->name('staff.delete');
    Route::resource('email', 'EmailController');

    // Official
    Route::resource('users', 'UserController');
    Route::post('allusers', 'UserController@allUsers' )->name('users.all');
    Route::get('trashedusers', 'UserController@trashedUsers')->name('users.trashed');
    Route::get('deleteusers/{id}', 'UserController@delete')->name('users.delete');
    Route::get('restoreuser/{id}', 'UserController@restore')->name('users.restore');
    Route::resource('events', 'EventController');
    Route::resource('useremails', 'UserEmailsController');

    // Profile
    Route::patch('users/profile/{user}', 'UserController@saveProfile' )->name('users.profile.save');

    //Imports
    Route::get('users/import/index', 'UserController@usersImportIndex')->name('users.import.index');
    Route::post('users/import/index', 'UserController@import')->name('users.import');
    
    //Conference management
    Route::resource('conferencemanagement', 'ConferenceManagementController');
    Route::resource('conferenceeditions', 'ConferenceEditionController');
    Route::get('conferenceeditions/{id}/edit', 'ConferenceEditionController@edit')->name('edit.conference.edition');
    Route::get('deleteedition/{id}', 'ConferenceEditionController@destroy')->name('delete.conference.edition');
    Route::get('showedition/{id}', 'ConferenceEditionController@show')->name('show.conference.edition');
    Route::resource('tempusers', 'TempUserController');

    // Participant management

    Route::resource('participants', 'ConferenceManagementController');
    Route::get('participant/delete/{id}', 'ConferenceManagementController@destroy')->name('conferenceparticipants.delete');
    Route::get('trashed/participants', 'ConferenceManagementController@trashed')->name('conferenceparticipants.trashed');

    Route::get('moderator/import/index', 'UserController@usersImportIndex')->name('moderator.conference.import.index');
    Route::get('moderator/import/index', 'UserController@usersImportIndex')->name('moderator.conference.import.index');
    Route::get('participants/import/index', 'UserController@usersImportIndex')->name('conferenceusers.import.index');
    Route::get('participants/export', 'UserController@usersExport')->name('conferenceusers.export');
    Route::post('participants/import', 'UserController@import')->name('conferenceuser.import');
    
    
    Route::get('conferenceparticipants/{type?}/{edition?}', 'ConferenceManagementController@participants')->name('conference.participants');
    Route::get('edit-conferenceparticipants/{id}/edit/{edition?}', 'ConferenceManagementController@edit')->name('conference.participants.edit');
    Route::PATCH('update-conferenceparticipants/{id}/update', 'ConferenceManagementController@update')->name('conference.participants.update');
    Route::get('resendwelcomemail/{id}/show', 'ConferenceManagementController@resendEmail')->name('participants.resendmail');

    Route::get('conferencecards/{id}', 'ConferenceManagementController@getCard')->name('participants.card');
    Route::get('user/meal/{id}', 'ConferenceManagementController@getMealTicket')->name('meal.ticket');

    
    Route::resource('conferencesettings', 'ConferenceSettingController');
    Route::get('/reset', 'ConferenceSettingController@resetData')->name('database.clear');
    
    Route::resource('hostels', 'HostelController');
    Route::get('hostels/delete/{id}', 'HostelController@destroy')->name('hostels.delete');

    Route::resource('foods', 'FoodController');
    Route::get('foods/delete/{id}', 'FoodController@destroy')->name('foods.delete');


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

    
    Route::resource('posts', 'PostController');
    Route::get('trashedposts', 'PostController@trashed')->name('posts.trashed');
    Route::get('restorepost/{id}', 'PostController@restore')->name('posts.restore');
    Route::get('posts/approve/{id}', 'PostController@approve')->name('posts.approve');
    Route::get('posts/unapprove/{id}', 'PostController@unapprove')->name('posts.unapprove');
    Route::get('posts/delete/{id}', 'PostController@destroy')->name('posts.delete');
    Route::get('posts/userdelete/{id}', 'PostController@userdelete')->name('posts.userdelete');
    Route::get('postfile/{filename}', 'PostController@getfile')->name('get.file');
    Route::post('postfilereplace', 'PostController@replaceFile')->name('file.replace');

    
    
    Route::patch('profile', 'SettingController@saveProfile')->name('profile.save');

    Route::resource('materials', 'MaterialController');
    Route::get('material/delete/{id}', 'MaterialController@destroy')->name('materials.delete');

    Route::resource('payouts', 'PayoutController');
    Route::resource('donations', 'DonationController');

    // Download sample imports
    Route::get('users-export/{type}', 'UserController@getAdminParticipantSample')->name('usersexport.sample');
    
});

 //Get signature Image
//  Route::get('stakeholdersignature/{image}', function($image){
//     $realpath = base_path() . '/uploads/signatures'. '/' .$image;
//         return response()->download($realpath);
// });

