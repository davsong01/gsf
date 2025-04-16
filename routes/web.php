<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NecController;
use App\Services\DynamicImageGenerator;
use App\Http\Controllers\CronController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\TempUserController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\SwitchUserController;
use App\Http\Controllers\UserEmailsController;
use App\Http\Controllers\StakeholderController;
use App\Http\Controllers\CriticalEmailController;
use App\Http\Controllers\StakeholderLoginController;
use App\Http\Controllers\ConferenceEditionController;
use App\Http\Controllers\ConferenceSettingController;
use App\Http\Controllers\StakeholderAccountController;
use App\Http\Controllers\StakeholderPaymentController;
use App\Http\Controllers\ConferenceManagementController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\DynamicImageGeneratorController;

Route::get('/queue', function () {
    // Artisan::call('queue:retry all');
    Artisan::call('queue:work --tries=2');
});

Route::get('/retry', function () {
    Artisan::call('queue:retry all');
});

Route::get('test/{data?}', [CriticalEmailController::class, 'getContent']);

// Campus Tracker routes
Route::controller(ChapterController::class)->group(function () {
    Route::get('campus-tracker', 'campusUpdate')->name('campus.tracker'); //http://127.0.0.1:8000/campus-view?chapter=1&token=387130
    Route::get('/campus-view', 'campusView')->name('campus.view');
    Route::post('/campus/{id}', 'campusSave')->name('campus.save');
});

Route::post('ajax-create-temp-details', [ConferenceManagementController::class, 'ajaxPayment']);
Auth::routes();
Auth::routes(['verify' => false, 'register' => false] );

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    echo "<p>Fully optimized.*</p>";
});

Route:: get('/runcron', [CronController::class, 'cron']);
Route::get('birthday-reminder/{days}', [CronController::class, 'birthdayReminderForNec']);

Route::get('email-cron/{pick?}', [CronController::class, 'emailCron']);
Route::get('cron/resolve-payment', [CronController::class, 'resolvePayment']);

Route::get('/conference', [ConferenceController::class, 'index']);

Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home.index');
    //Campus auto complete
    Route::get('/autocomplete-search', 'campusAutocomplete')->name('campus.suggestions');
    Route::get('/alumni-autocomplete-search', 'alumniAutoComplete')->name('alumni.suggestions');
    Route::get('/member-autocomplete-search', 'memberAutoComplete')->name('member.suggestions');
    // Campus routes
    Route::get('/people/campuses', 'chapters')->name('people.campuses');
    Route::post('search', 'search')->name('search');
    Route::POST('autocompletecampus', 'autocomplete')->name('campus.autocomplete');
    Route::any('people/singlecampus/{chapter?}', 'singleCampus')->name('campus.single');
    Route::get('campus-membersx/{id}', 'studentsByChapter')->name('members.campus');
    Route::get('campus-members/{id}', 'alumniByChapter')->name('alumni.campus');
    
    Route::post('contactcampus', 'contactCampus')->name('campus.contact');
    // Alumni routes
    Route::get('all-alumni', 'alumni')->name('people.alumni');
    Route::get('all-nec', 'nec')->name('people.nec');
    
    Route::get('all-members', 'students')->name('people.students');
    Route::get('people/{slug}', 'singleUser')->name('user.single');
    Route::get('people/{student}', 'singleStudent')->name('student.single');

    Route::POST('listing/contact', 'userContact')->name('listing.contact');
    // Student routes
    Route::POST('student/search', 'studentSearch')->name('student.search');
    // Route::POST('student/contact', 'alumniContact')->name('student.contact');

    //Events
    Route::get('/people/programs', 'programs')->name('people.programs');
    // Homepage search
    Route::get('general-search', 'generalSearch')->name('general.search');
    Route::get('search-alumni', 'alumniSearch')->name('search.alumni');
    Route::get('member/search', 'memberSearch')->name('member.search');
   
    Route::get('newalumni', 'newAlumni')->name('newalumni');
    Route::post('newalumni', 'saveNewAlumni')->name('newalumni.save');
    Route::get('conference-registration/{page?}', 'regPage')->name('conference.registration');
    // Route::post('/registration/{type}', 'registrationType')->name('registration');

    Route::get('newdonation', 'newDonation')->name('newdonation');
    Route::post('upload-alumni/{type}', [HomeController::class, 'uploadAlumni'])->name('upload-alumni');
    Route::get('upload-multiple', [HomeController::class, 'uploadMultiple'])->name('upload.multiple');
    
    Route::get('sample-listing', [HomeCOntroller::class, 'getListingSample'])->name('sample-listing');
    
});
Route::post('newdonation', [DonationController::class, 'redirectToGateway'])->name('newdonation.save');

Route::controller(StakeholderLoginController::class)->group(function () {
    //Stakeholder Account
    Route::get('stakeholders/login', 'showStakeholderLoginForm')->name('stakeholder.loginpage');
    Route::post('stakeholders/login', 'stakeHolderLogin')->name('stakeholder.login');
    Route::get('/stakeholderlogout', 'logout')->name('stakeholder.logout');
});

Route::controller(StakeholderAccountController::class)->group(function () {
    Route::get('/stakeholderdashboard', 'index')->name('stakeholder.dashboard');
    Route::get('/stakeholderprofile', 'profile')->name('stakeholder.profile');
    Route::post('/stakeholderprofile', 'saveProfile')->name('stakeholder.saveprofile');
});

//Logged in stakeholder Account
Route::middleware(['stakeholder'])->group(function(){
    Route::resource('reports', ReportsController::class);
    Route::controller(ReportsController::class)->group(function () {
        Route::get('deletereports/{id}', 'delete')->name('reports.delete');
        Route::post('rejectreports', 'rejectReport')->name('report.reject');
    });
    
    Route::resource('stakeholderpayment', StakeholderPaymentController::class);

    Route::controller(StakeholderPaymentController::class)->group(function () {
        Route::get('stakeholderpaymentdelete/{id}', 'delete')->name('stakeholderpayment.delete');
        Route::get('downloadpop/{id}', 'downloadPop')->name('pop.download');
        Route::post('/pop/export', 'exportPop')->name('pop.export');
    });
});

// Registration links
Route::get( '/registration', [ConferenceController::class, 'index'])->name('index');
Route::get('/nec/registration/portal/pay', [ConferenceController::class, 'necRegistration'])->name('nec.registration');

Route::post('/pay', [PaymentController::class, 'redirectToGateway'])->name('pay');
Route::get('/payment/callback', [PaymentController::class, 'handleGatewayCallback']);
Route::any('/payment/webhook', [PaymentController::class, 'dumpWebhook']);
Route::get('/payment/analyze-webhook', [PaymentController::class, 'analyze']);
Route::get('/payment/donation-callback', [DonationController::class, 'handleDonationGatewayCallback']);


Route::middleware(['auth', 'SwitchUser'])->group(function(){
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::get('/home', [AccountController::class, 'index'])->name('home');
    Route::resource('staff', StakeholderController::class);
    Route::get('staffs/delete/{id}', [StakeholderController::class, 'destroy'])->name('staff.delete');
    Route::resource('email', EmailController::class);

    // Official
    Route::resource('nec', NecController::class);
    
    Route::get('archived-nec', [NecController::class, 'archivedNec'])->name('archive.nec.index');
    Route::post('process-archive-nec', [NecController::class, 'archiveNec'])->name('nec.archive');
    
    Route::get('nec-delete/{id}', [NecController::class, 'delete'])->name('nec.delete');
    Route::resource('users', UserController::class);
    Route::get('donations-all', [DonationController::class, 'allDonations'])->name('donations.all');
    Route::get('listing-pending', [UserController::class, 'pendingListing'])->name('listing-pending');
    // Route::get('trashedusers', 'trashedUsers')->name('users.trashed');
    // Route::get('deleteusers/{id}', 'delete')->name('users.delete');

    Route::controller(UserController::class)->group(function () {
        Route::post('allusers', 'allUsers' )->name('users.all');
        Route::post('allpending', 'allPendingUsers' )->name('users.pending');
        Route::get('approve-pending/{user}', 'approvePendingUser' )->name('user.single.approve');
        Route::get('approve-pending/{user}', 'approvePendingUser' )->name('user.single.approve');
        Route::get('reject-pending/{user}', 'rejectPendingUser' )->name('user.single.reject');
        Route::get('delete-pending/{user}', 'deletePendingUser' )->name('user.single.delete');
        Route::get('trashedusers', 'trashedUsers')->name('users.trashed');
        Route::get('deleteusers/{id}', 'delete')->name('users.delete');
        Route::get('restoreuser/{id}', 'restore')->name('users.restore');
        Route:: patch('users/profile/{user}', 'saveProfile')->name('users.profile.save');
        Route::get('users/import/index', 'usersImportIndex')->name('users.import.index');
        Route::post('users/import/index', 'import')->name('users.import');

        Route::get('/official/import/index', 'officialsImportIndex')->name('officials.import.index');
        Route::get('/official/export', 'officialsExport')->name('officials.export');
        Route::post('/official/import', 'import')->name('officials.import');
    });
    Route::resource('events', EventController::class);
    Route::resource('useremails', UserEmailsController::class);

    Route::controller(ConferenceEditionController::class)->group(function () {
        Route::get('conferenceeditions/{id}/edit', 'edit')->name('edit.conference.edition');
        Route::get('deleteedition/{id}', 'destroy')->name('delete.conference.edition');
        Route::get('showedition/{id}', 'show')->name('show.conference.edition');
        Route::get('clone-edition/{id}', 'clone')->name('clone.conference.edition');
        Route::get('chart-data/{id}', 'chart')->name('conference.edition.chart');
    });
    Route::resource('conferenceeditions', ConferenceEditionController::class);

    //Conference management
    Route::resource('conferencemanagement', ConferenceManagementController::class);
    Route::resource('tempusers', TempUserController::class);

    Route::post('fetch-transaction', [PaymentController::class, 'paystackGetCustomerIdByEmail'])->name('admin.transactions.fetch');

    Route::controller(TempUserController::class)->group(function () {
        Route::get('tempusers-transfer-confirm/{id}', 'confirmTransfer')->name('tempusers.transfer.confirm');
        Route::get('tempusers-onsite-confirm/{id}', 'confirmOnSiteTransfer')->name('tempusers.onsite.confirm');

        Route::get('requery/{id}', 'requery')->name('tempusers.requery');
        Route::post('verify-multiple-payments', 'requeryMultiple')->name('tempusers.requery-multiple');
        Route::get('set-and-verify-reference/{reference}/{temp_id}', 'setAndVerifyReference')->name('set-and-verify-reference');
    });

    // Participant management
    Route::resource('participants', ConferenceManagementController::class);
    Route::resource('criticalEmail', CriticalEmailController::class);

    Route::controller(CriticalEmailController::class)->group(function () {
        Route::get('CriticalEmail-delete/{id}','destroy')->name('CriticalEmail.delete');
    });

    Route::controller(ConferenceManagementController::class)->group(function () {
        // Import conference users
        Route::get('participants/import/index', 'usersImportIndex')->name('conferenceusers.import.index');
        Route::get('conference-users-import/{type}', 'getAdminParticipantSample')->name('conference.usersexport.sample');
        Route::post('participants-import', 'import')->name('conferenceusers.import');
        Route::post('conference-users-export/{type}', 'import')->name('conferenceuser.import');
        Route::post('admin-conference-users-export/{type}', 'adminImport')->name('admin.conferenceuser.import');
        Route::get('conferenceparticipants/{type?}/{edition?}', 'participants')->name('conference.participants');
        Route::get('create-conferenceparticipants/{edition?}', 'create')->name('conference.participants.create');
        Route::post('store-conferenceparticipants/{edition?}', 'store')->name('conference.participants.store');
        Route::get('edit-conferenceparticipants/{id}/edit/{edition?}', 'edit')->name('conference.participants.edit');
        Route::PATCH('update-conferenceparticipants/{id}/update', 'update')->name('conference.participants.update');
        Route::PATCH('admin-update-conferenceparticipants/{id}/update', 'adminUpdate')->name('conference.participants.admin.update');
        Route::get('resendwelcomemail/{id}/show', 'resendEmail')->name('participants.resendmail');
        Route::get('participant/delete/{id}', 'destroy')->name('conferenceparticipants.delete');
        Route::get('trashed/participants', 'trashed')->name('conferenceparticipants.trashed');

        Route::get('conferencestaff/{edition?}', 'staffIndex')->name('conference.staff');
        Route::get('conferencestaff-create/{edition?}', 'staffCreate')->name('conference.staff.create');
        Route::post('conferencestaff-store/{edition?}', 'staffStore')->name('conference.staff.store');
        Route::get('conferencestaff-edit/{id}/{edition?}', 'staffEdit')->name('conference.staff.edit');
        Route::PATCH('conferencestaff-update/{id}', 'staffUpdate')->name('conference.staff.update');
        Route::get('conferencestaff.delete/{id}', 'destroyStaff')->name('conferencestaff.delete');

        Route::get('conferencecards/{id}', 'getCard')->name('participants.card');
        Route::get('user/meal/{id}', 'getMealTicket')->name('meal.ticket');
        
    });

    Route::controller(DynamicImageGeneratorController::class)->group(function () {
        Route::post('generate-template-preview/{edition_id}', 'generateTemplatePreview')->name('template.preview');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('conferenceusers/export', 'usersExport')->name('conferenceusers.export');
        // Route::get('moderator/import/index', 'usersImportIndex')->name('moderator.conference.import.index');
        // Route::get('participants/import/index', 'usersImportIndex')->name('conferenceusers.import.index');
    });
   
    Route::resource('hostels', HostelController::class);
    Route::controller(HostelController::class)->group(function () {
        Route::get('hostels/delete/{id}', 'destroy')->name('hostels.delete');
        Route::get('hostel-export/{id}', 'participantExport')->name('hostelusers.export');
        Route::get('hostels-repair-allocation', 'repairHostelAllocation')->name('hostels.repair.allocation');
        Route::get('hostels-auto-allocate/{edition}', 'autoAllocateHostel')->name('hostels.auto.allocate');
        
        Route::post('hostels-merger', 'hostelMerger')->name('hostels.merge');
        Route::post('get-available-hostels', 'getAvailableHostels')->name('get.available.hostels');
    });

    Route::controller(FoodController::class)->group(function () {
        Route::get('service-point-repair-allocation', 'repairServicePointAllocation')->name('servicepoint.repair.allocation');
        Route::post('service-point-merger', 'servicePointMerger')->name('servicepoint.merge');
        Route::post('get-available-service-points', 'getAvailableServicePoints')->name('get.available.service_point');
        Route::get('sp-auto-allocate/{edition}', 'autoAllocateServicePoint')->name('sp.auto.allocate');
    });

    Route::get('food-export/{id}', [FoodController::class, 'participantExport'])->name('foodusers.export');
    Route::resource('conferencesettings', ConferenceSettingController::class);
    Route::get('/reset', [ConferenceSettingController::class, 'resetData'])->name('database.clear');
    
    Route::resource('foods', FoodController::class);
    Route::get('foods/delete/{id}', [FoodController::class, 'destroy'])->name('foods.delete');

    Route::controller(UserController::class)->group(function () {
        Route::get('/nec/import/index', 'necsImportIndex')->name('necs.import.index');
        Route::get('/nec/export', 'necsExport')->name('necs.export');
        Route::post('/nec/import', 'import')->name('necs.import');
    
        Route::get('/alumnis/import/index', 'alumnisImportIndex')->name('alumnis.import.index');
        Route::get('/alumnis/export', 'alumnisExport')->name('alumnis.export');
        Route::post('/alumnis/import', 'import')->name('alumnis.import');
    
        Route::get('/moderators/import/index', 'moderatorsImportIndex')->name('moderators.import.index');
        Route::get('/moderators/export', 'moderatorsExport')->name('moderators.export');
        Route::post('/moderators/import', 'import')->name('moderators.import');
        Route::get('/medical/import/index', 'medicalImportIndex')->name('medical.import.index');
        Route::get('/medical/export', 'medicalExport')->name('medical.export');
        Route::post('/medical/import', 'import')->name('medical.import');
    
        Route::get('/choir/import/index', 'choirImportIndex')->name('choir.import.index');
        Route::get('/choir/export', 'choirExport')->name('choir.export');
        Route::post('/choir/import', 'import')->name('choir.import');
    
        Route::get('medical', 'getMedical')->name('user.medical');
        Route::get('choir', 'getChoir')->name('user.choir');
        Route::get('choir/{id}/edit', 'editChoir')->name('choir.edit');
        Route::get('medic/{id}/edit', 'editMedic')->name('medic.edit');

        // Route::get('nec', 'getNec')->name('user.nec');
        Route::get('nec/{id}/edit', 'editNec')->name('nec.edit');
        Route::get('nec/{id}/edit', 'editOfficial')->name('official.edit');

        Route::get('users-export/{type}', 'getAdminParticipantSample')->name('usersexport.sample');
    });
    
    Route::resource('officials', OfficialController::class);

    Route::resource('chapters', ChapterController::class);
    Route::controller(ChapterController::class)->group(function () {
        Route::get('newtoken/{id}', 'generateNewToken')->name('chapter.newtoken');
        Route::get('chapters/delete/{chapter}', 'destroy')->name('chapters.delete');
        Route::get('/chapter/exporting', 'chaptersExport')->name('chapters.export');
        Route::get('/token', 'generate')->name('token');
        // Route::get('/campus', 'campusUpdate')->name('campus.update');
        // Route::post('/campus', 'campusView')->name('campus.view');
        // Route::post('/campus/{id}', 'campussave')->name('campus.save');
    });

    Route::resource('fields', FieldController::class);
    Route::controller(FieldController::class)->group(function () {
        Route::get('fields/delete/{id}', 'destroy')->name('fields.delete');
    });

    Route::resource('zones', ZoneController::class);
    Route::get('zones/delete/{id}', [ZoneController::class, 'destroy'])->name('zones.delete');
  
    Route::get('officials/delete/{official}', [OfficialController::class, 'delete'])->name('officials.delete');
    Route::resource('moderators', ModeratorController::class);
    Route::get('moderators/delete/{id}', [ModeratorController::class, 'destroy'])->name('moderators.delete');

    Route::controller(SwitchUserController::class)->group(function () {
        Route::get('/switch/{id}', 'index')->name('switchuser');
        Route::get('/stopswitching', 'stopSwitching')->name('stop.switchuser');
    });

    Route::resource('alumni', AlumniController::class);
    Route::get('alumnis/delete/{id}', [AlumniController::class, 'destroy'])->name('alumni.delete');
    Route::resource('participants', AccountController::class)->except([
        'create', 'store', 'destroy', 'index', 'show'
	]);

    Route::resource('posts', PostController::class);
    Route::controller(PostController::class)->group(function () {
        Route::get('trashedposts', 'trashed')->name('posts.trashed');
        Route::get('restorepost/{id}', 'restore')->name('posts.restore');
        Route::get('posts/approve/{id}', 'approve')->name('posts.approve');
        Route::get('posts/unapprove/{id}', 'unapprove')->name('posts.unapprove');
        Route::get('posts/delete/{id}', 'destroy')->name('posts.delete');
        Route::get('posts/userdelete/{id}', 'userdelete')->name('posts.userdelete');
        Route::get('postfile/{filename}', 'getfile')->name('get.file');
        Route::post('postfilereplace', 'replaceFile')->name('file.replace');
    });

    Route::patch('profile', [SettingController::class, 'saveProfile'])->name('profile.save');

    Route::resource('materials', MaterialController::class);
    Route::get('material/delete/{id}', [MaterialController::class, 'destroy'])->name('materials.delete');

    Route::resource('payouts', PayoutController::class);
    Route::resource('donations', DonationController::class);

});

//Get signature Image
//  Route::get('stakeholdersignature/{image}', function($image){
//     $realpath = base_path() . '/uploads/signatures'. '/' .$image;
//         return response()->download($realpath);
// });

