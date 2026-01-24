<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\User;
use App\Models\Hostel;
use App\Models\Chapter;
use App\Models\TempMember;
use App\Exports\UserExport;
use Illuminate\Support\Str;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\ConferenceEdition;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\StakeholderDesignation;
use App\Traits\UserDatatableFeaturesTrait;

class UserController extends Controller
{
	use UserDatatableFeaturesTrait;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $this->userService->authorizeUserAction(auth()->user());

        return view('admin.users.index', [
            'routes' => [
                'create' => route('users.create'),
                'import' => route('users.import.index'),
                'export' => route('users.export'),
                'all'    => route('users.all'),
            ],
            'isAdmin' => auth()->user()->isAdmin(),
        ]);
    }

    public function allUsers(Request $request)
    {
        $user = auth()->user();

        $canEdit = $user->isAdmin() || ($user->isSubAdmin() && $user->isMember());
        $canDelete = $user->isAdmin() || ($user->isSubAdmin() && $user->isMember());

        if($user->isAdmin()){
            $request['chapter_id'] = null;
        }

        if(($user->isSubAdmin() && $user->isMember())){
            $request['chapter_id'] = $user->chapter_id;
        }

        $request['canDelete'] = $canDelete;
        $request['canEdit'] = $canEdit;

        $request['canSwitch'] = $user->isAdmin();

        $json_data = $this->userService->getAllUsers( $request->all());

        return response()->json($json_data);
    }


	public function pendingListing()
	{
		if (auth()->user()->isAdmin()) {
			$pending = TempMember::with('campus')->orderBy('created_at','DESC')->get();
			$chapters = Chapter::where('id', '!=', 86)->get();
			$portfolios = getCommunityPortfolios();

			$counter = 1;
			return view('admin.listings.index', compact('pending','counter','chapters', 'portfolios'));
		} else abort(404);
	}

	public function trashedUsers()
	{
		$count = 1;
		if(auth()->user()->isSubAdmin() && auth()->user()->isMember()){
			$users = User::onlyTrashed()
				->where('chapter_id', auth()->user()->chapter_id)
				->where('role','<>', 1)->orderBy('created_at', 'desc')
				->where('id', '<>', auth()->user()->id)
				->get();
				return view('admin.users.trashed', compact('users', 'count'));
		}

		if(auth()->user()->isAdmin()){
			$users = User::onlyTrashed()->wherehas('campus')->where('role','<>', 1)->orderBy('created_at', 'desc')->get();
			return view('admin.users.trashed', compact('users', 'count'));
		}


	}


	public function edit(User $user)
	{
		$chapters = Chapter::all();
		$portfolios = getCommunityPortfolios();
        $campusDesignations = StakeholderDesignation::select('id','name')->where('type', 'chapter_executive')->orderBy('order')->get();
		$sessions = range(date('1982'), date('Y'));
        $isAdmin = true;
		$president = $user->campus->chapterPresident ?? null;

        if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){
            if(auth()->user()->isSubAdmin() && auth()->user()->isMember() && auth()->user()->chapter_id <> $user->chapter_id){
				return abort(404);
			}
			return view('admin.users.edit', compact('user', 'chapters', 'portfolios', 'sessions', 'president','campusDesignations', 'isAdmin'));
		}

	}

	public function create(Request $request)
	{
		$chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
		$hostels = Hostel::orderBy('name')->get();
		$foods = Food::all();
		$chapters = Chapter::all();
		$portfolios = getCommunityPortfolios();
        $campusDesignations = StakeholderDesignation::select('id','name')->where('type', 'chapter_executive')->orderBy('order')->get();

		$sessions = range(date('1982'), date('Y'));

		if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){
			return view('admin.users.edit', compact('chapters', 'portfolios', 'sessions','campusDesignations'));
		}

		else return back(404);
	}

	// public function store(Request $request) {
	// 	if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){
	// 		if(auth()->user()->isAdmin()){
	// 			$request['chapter_id'] = $request->chapter_id;
	// 		}else $request['chapter_id'] = auth()->user()->chapter_id;
	// 	}
	// 	$this->validate($request, [
	// 		'email' => 'unique:users,email,',
	// 	]);
	// 	$data = $this->validateUser($request);

	// 	//Handle password
	// 	if ($request['password']) {
	// 		$data['password'] = Hash::make($request['password']);
	// 	} else {
	// 		$data['password'] = Hash::make($request['phone']);
	// 	}

	// 	//Handle Passport Upload
	// 	if ($request['passport']) {
	// 		$passport = $this->uploadImage($request->passport, 'frontend/passports', 500, 500);

	// 		$data['passport'] = $passport;
	// 	}

	// 	// Handle name change
	// 	$data['slug'] = Str::slug($request->name);

	// 	try {
	// 		$user = User::create($data);
	// 	} catch (\Exception $e) {
	// 		return back()->with('error', $e->getMessage());
	// 	}

	// 	$this->createFamilyId($user);

	// 	return redirect(route('users.index'))->with('message', 'Operation Successful');
	// }
    public function store(Request $request)
    {
        $this->userService->authorizeUserAction(auth()->user());

        $request['chapter_id'] = auth()->user()->isAdmin() ? $request->chapter_id : auth()->user()->chapter_id;

        $request->validate([
            'email' => 'unique:users,email',
        ]);

        $data = $this->userService->prepareUserData($request);

        try {
            $this->userService->createUser($data);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('users.index')->with('message', 'User created successfully.');
    }

	public function update(Request $request, User $user)
    {
        $this->userService->authorizeUserAction($user);
        // check permissions like in store
        // Ensure chapter_id is set correctly based on user role
        $request['chapter_id'] = auth()->user()->isAdmin() ? $request->chapter_id : $user->chapter_id;

        // Validate email uniqueness
        $request->validate([
            'email' => 'unique:users,email,' . $user->id,
        ]);

        // Prepare data using the service
        $data = $this->userService->prepareUserData($request, $user);

        try {
            $this->userService->updateUser($user, $data);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        
        return redirect()->route('users.index')->with('message', 'User updated successfully.');
    }

	public function saveProfile(Request $request, User $user){

		if($user->id <> auth()->user()->id){
			return abort(404);
		}
		$request['chapter_id'] = $user->chapter_id;
		$request['role'] = $user->role;

		$this->validate($request, [
			'email' => 'unique:users,email,'.$user->id,
		]);

		$data = $this->validateUser($request);

		//Handle password
		if ($request['password']) {
			$data['password'] = Hash::make($request['password']);
		} else {
			$data['password'] = $user->password;
		}

		if ($request->show_phone) {
			$data['show_phone'] = 1;
		} else $data['show_phone'] = 0;

		if ($request->show_email) {
			$data['show_email'] = 1;
		} else $data['show_email'] = 0;

		//Handle Passport Upload
		if ($request['passport']) {
			$passport = $this->uploadImage($request->passport, 'images/passports', 500, 500);
			$data['passport'] = $passport;
		}

		// Handle name change
		$data['slug'] = Str::slug($request->name);

		try {
			$user->update($data);
		} catch (\Exception $e) {
			return back()->with('error', $e->getMessage());
		}

		return back()->with('message', 'Operation Successful');

	}

	public function destroy(User $user)
	{
		if (auth()->user()->id == $user->id) {
			return back()->with('warning', 'I\'m sorry but You cannot delete your self');
		}

		if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){

			if(auth()->user()->isSubAdmin() && auth()->user()->isMember() && auth()->user()->chapter_id <> $user->chapter_id){
				return abort(404);
			}
			$user->delete();
			return back()->with('message', 'Record has been deleted!');
		}

	}

	public function delete($id){
		$user = User::withTrashed()->whereId($id)->first();
		if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){

			if(auth()->user()->isSubAdmin() && auth()->user()->isMember() && auth()->user()->chapter_id <> $user->chapter_id){
				return abort(404);
			}

			if (isset($user->passport)) {
				$this->deleteImage($user->passport);
			}

			$user->forceDelete();

			return back()->with('message', 'Record deleted forever');
		}


	}

	public function restore($id){
		$user = User::withTrashed()->whereId($id)->first();
		if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){

			if(auth()->user()->isSubAdmin() && auth()->user()->isMember() && auth()->user()->chapter_id <> $user->chapter_id){
				return abort(404);
			}

			$user->restore();
			return redirect(route('users.index'))->with('message', 'Record has been restored');
		}

	}

	public function usersImportIndex()
	{
		$chapters = Chapter::all();
		$roles = getCommunityPortfolios();
		return view('admin.users.import', compact('chapters', 'roles'));
	}

	public function import(Request $request)
	{
		if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){
			if(auth()->user()->isAdmin()){
				$request['chapter_id'] = $request->chapter_id;
			}else $request['chapter_id'] = auth()->user()->chapter_id;
		}else return abort(404);

		$data = $this->validate($request, [
			'type' => 'required|numeric',
			'chapter_id' => 'required|numeric',
			'file' => 'required|mimes:xlsx,csv',
		]);

		try {
			Excel::import(new UsersImport($data), request()->file('file'));
		} catch (\Illuminate\Database\QueryException $ex) {
			$error = $ex->getMessage();
			return back()->with('error', $error);
		}
		return back()->with('message', 'Data has been imported succesfully');
	}

	public function show(User $user)
	{
		if($user->id <> auth()->user()->id){
			return abort(404);
		}

		$chapters = Chapter::all();
		$portfolios = getCommunityPortfolios();
		$sessions = range(date('1982'), date('Y'));

		$president = $user->campus->stakeholder ?? null;
		if($president){
			$president = $president->where('role', 'President')->first();
		}

		return view('admin.users.profile', compact('chapters', 'user', 'president', 'portfolios', 'sessions'));
	}


	public function userExport(Request $request)
	{
		$count = 1;
		$edition = ConferenceEdition::find($request->edition);

		if (auth()->user()->role != 1) {
			return abort(404);
		}

		$data = [
			'edition_id' => $edition->id,
		];
		return Excel::download(new UserExport($data), 'users_exported.xlsx');
	}

	public function createEmail(){
		return view('admin.users.import');
	}

	private function validateUser($request){

		$data = $this->validate($request, [
			"name" => "required",
			"email" => "required",
			"show_email" => "nullable",
			"slug" => "nullable",
			"phone" => "required",
			"show_phone" => "nullable",
			"gender" => "nullable",
			"status" => "numeric",
			"matric_year" => "required|string",
			"graduation_year" => "nullable|string",
			"password" => "nullable",
			"chapter_id" => "required|numeric",
			"role" => "required|numeric",
			"portfolio_session" => "nullable",
			"course" => "required",
			"skills" => "nullable",
			"course_duration" => "required|numeric",
			"facebook" => "nullable|url",
			"twitter" => "nullable|url",
			"passport" => "nullable",
			"dob" => "nullable",
			"program" => "required",
			"open_to_work" => 'required|numeric'
		]);

		return $data;

	}

	public function approvePendingUser(TempMember $user){
		$data = $user->toArray();
		// dd(Carbon::parse($user->date_of_birth));
		$data['password'] = Hash::make($user->phone);
		$data['passport'] = $user->passport;
		$data['matric_year'] = $user->matriculation_year;
		// $data['dob'] = $user->date_of_birth;
		$data['slug'] = Str::slug($user->name);
		$data['chapter_id'] = $user->chapter;
		$data['status'] = $user->is_graduated ?? 0;
		$data['role'] = 2;
		unset($data['chapter']);
		unset($data['marital_status']);
		unset($data['matriculation_year']);
		unset($data['date_of_birth']);
		unset($data['id']);

		try {
			$new_user = User::create($data);
			$data['family_id'] = $this->createFamilyId($new_user);

			$data['type'] = 'approve_listing';
				//send email to participant
			$email = [
				'subject' => 'Welcome to GSF Directory Website',
				'recipient_name' => $data['name'],
				'recipient' => $data['email'],
				'type' => $data['type'],
				'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
			];

			$this->logEmail($email);

			$user->delete();
		} catch (\Exception $e) {
			return back()->with('error', $e->getMessage());
		}



		return redirect(route('listing-pending'))->with('message', 'Approval succesful');
	}

	public function rejectPendingUser(TempMember $user)
	{
		$data = $user->toArray();

		try {
			$data['type'] = 'reject_listing';
			//send email to participant
			$email = [
				'subject' => 'Approval to GSF Directory failed!',
				'recipient_name' => $data['name'],
				'recipient' => $data['email'],
				'type' => $data['type'],
				'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
			];

			$this->logEmail($email);

			$user->delete();
		} catch (\Exception $e) {
			return back()->with('error', $e->getMessage());
		}

		return redirect(route('listing-pending'))->with('message', 'Listing rejected');
	}

	public function deletePendingUser(TempMember $user){
		$user->delete();
		return back()->with('message', 'Delete Successful');
	}

}
