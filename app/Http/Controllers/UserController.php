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
use Illuminate\Support\Facades\DB;
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
        $currentUser = auth()->user();
        $memberQuery = User::query()->where('role', '<>', 1);
        if ($currentUser->isSubAdmin() && $currentUser->isMember()) {
            $memberQuery->where('chapter_id', $currentUser->chapter_id);
        }
        $chapters = $currentUser->isAdmin()
            ? Chapter::actualCampus()->orderBy('name')->get(['id', 'name'])
            : Chapter::actualCampus()->whereKey($currentUser->chapter_id)->get(['id', 'name']);

        return view('admin.users.index', [
            'routes' => [
                'create' => route('users.create'),
                'import' => route('users.import.index'),
                'export' => route('users.export'),
                'all'    => route('users.all'),
            ],
            'isAdmin' => auth()->user()->isAdmin(),
            'studentCount' => (clone $memberQuery)->where('is_graduated', 0)->count(),
            'alumniCount' => (clone $memberQuery)->where('is_graduated', 1)->count(),
            'memberCount' => (clone $memberQuery)->count(),
            'chapters' => $chapters,
        ]);
    }

    public function allUsers(Request $request)
    {
        $user = auth()->user();

        $canEdit = $user->isAdmin() || ($user->isSubAdmin() && $user->isMember());
        $canDelete = $user->isAdmin() || ($user->isSubAdmin() && $user->isMember());

        $request['scope_chapter_id'] = ($user->isSubAdmin() && $user->isMember())
            ? $user->chapter_id
            : null;
        if (!$user->isAdmin()) {
            $request['chapter_id'] = null;
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
			$chapters = Chapter::actualCampus()->get();
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

	public function bulkTrashedAction(Request $request)
	{
		abort_unless(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember()), 403);

		$data = $request->validate([
			'action' => ['required', 'in:restore,delete'],
			'user_ids' => ['required', 'array', 'min:1'],
			'user_ids.*' => ['required', 'integer', 'distinct'],
		]);

		$query = User::onlyTrashed()->whereIn('id', $data['user_ids'])->where('role', '<>', 1);
		if (auth()->user()->isSubAdmin() && auth()->user()->isMember()) {
			$query->where('chapter_id', auth()->user()->chapter_id)->where('id', '<>', auth()->id());
		} else {
			$query->whereHas('campus');
		}

		$users = $query->get();
		abort_if($users->count() !== count($data['user_ids']), 403, 'One or more selected users are unavailable. Refresh the page and try again.');

		DB::transaction(function () use ($users, $data) {
			foreach ($users as $user) {
				if ($data['action'] === 'restore') {
					$user->restore();
					continue;
				}

				if ($user->passport) {
					$this->deleteImage($user->passport);
				}
				$user->forceDelete();
			}
		});

		$message = $data['action'] === 'restore'
			? count($users).' user(s) restored.'
			: count($users).' user(s) permanently deleted.';

		return back()->with('message', $message);
	}


	public function edit(User $user)
	{
		$chapters = Chapter::actualCampus()->get();
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
		$chapters = Chapter::actualCampus()->orderBy('name')->get(); //sort in alphabetical order
		$hostels = Hostel::orderBy('name')->get();
		$foods = Food::all();
		$chapters = Chapter::actualCampus()->get();
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

		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:users,email,'.$user->id,
			'phone' => 'required|string|max:50',
			'gender' => 'required|in:Male,Female',
			'dob' => 'nullable|date',
			'password' => 'nullable|string|min:6',
			'passport' => 'nullable|image',
			'is_graduated' => 'nullable|boolean',
			'open_to_work' => 'nullable|boolean',
			'program' => 'nullable|string|max:50',
			'course' => 'nullable|string|max:255',
			'course_duration' => 'nullable|integer|min:1|max:15',
			'matric_year' => 'nullable|string|max:20',
			'graduation_year' => 'nullable|string|max:20',
			'skills' => 'nullable|string|max:2000',
			'motto' => 'nullable|string|max:500',
			'facebook' => 'nullable|url|max:255',
			'twitter' => 'nullable|url|max:255',
		]);

		$data = $this->userService->prepareUserData($request, $user);

		$data['show_phone'] = $request->filled('show_phone') ? 1 : 0;
		$data['show_email'] = $request->filled('show_email') ? 1 : 0;

		try {
			$user->update($data);
		} catch (\Exception $e) {
			return back()->with('error', $e->getMessage());
		}

		return redirect()->route('member.profile')->with('message', 'Profile updated successfully.');

	}

	public function destroy(User $user)
	{
		if (auth()->user()->id == $user->id) {
			return back()->with('warning', 'I\'m sorry but You cannot delete your self');
		}
        // fix this later
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
		$chapters = Chapter::actualCampus()->get();
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

		$chapters = Chapter::actualCampus()->get();
		$portfolios = getCommunityPortfolios();
		$sessions = range(date('1982'), date('Y'));
		$president = $user->campus?->chapterPresident;

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
		$existingUser = filled($user->email)
			? User::whereRaw('LOWER(email) = ?', [Str::lower(trim($user->email))])->first()
			: null;

		if ($existingUser) {
			$gender = $user->gender ?? $user->sex ?? null;
			$profileDetails = array_filter([
				'name' => $user->name,
				'email' => trim((string) $user->email),
				'phone' => $user->phone,
				'gender' => $gender,
				'passport' => $user->passport,
				'chapter_id' => $user->chapter,
				'program' => $user->program,
				'course' => $user->course,
				'matric_year' => $user->matriculation_year,
				'graduation_year' => $user->graduation_year,
				'open_to_work' => $user->open_to_work,
				'slug' => Str::slug($user->name),
			], static fn ($value) => $value !== null && $value !== '');

			try {
				DB::transaction(function () use ($existingUser, $profileDetails, $user) {
					$existingUser->fill($profileDetails)->save();
					$deleted = DB::table($user->getTable())
						->where($user->getKeyName(), $user->getKey())
						->delete();

					if ($deleted !== 1) {
						throw new \RuntimeException('The pending listing could not be removed.');
					}
				});
			} catch (\Exception $e) {
				return back()->with('error', $e->getMessage());
			}

			return redirect(route('listing-pending'))->with('message', 'User details were updated.');
		}

		$data = $user->toArray();
		// Keep compatibility with pending rows created before the gender column rename.
		$data['gender'] = $data['gender'] ?? $data['sex'] ?? null;
		unset($data['sex']);
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
			$new_user->family_id = 'GSFP-' . str_pad($new_user->id, 5, '0', STR_PAD_LEFT);
			$new_user->save();
			$data['family_id'] = $new_user->family_id;

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

	public function bulkPendingAction(Request $request)
	{
		abort_unless(auth()->user()?->isAdmin(), 404);

		$data = $request->validate([
			'action' => ['required', 'in:approve,reject,delete'],
			'selected_ids' => ['required', 'array', 'min:1'],
			'selected_ids.*' => ['integer', 'distinct', 'exists:temp_members,id'],
		]);

		$items = TempMember::whereIn('id', $data['selected_ids'])->get();
		$processed = 0;
		foreach ($items as $item) {
			switch ($data['action']) {
				case 'approve':
					$this->approvePendingUser($item);
					break;
				case 'reject':
					$this->rejectPendingUser($item);
					break;
				case 'delete':
					$this->deletePendingUser($item);
					break;
			}
			$processed++;
		}

		$messages = [
			'approve' => 'approved',
			'reject' => 'rejected',
			'delete' => 'deleted',
		];
		return redirect()->route('listing-pending')->with('message', $processed . ' application(s) ' . $messages[$data['action']] . '.');
	}

}
