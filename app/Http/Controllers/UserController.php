<?php

namespace App\Http\Controllers;

use App\Food;
use App\User;
use App\Hostel;
use App\Chapter;
use App\Payment;
use App\Setting;
use App\Mail\WelcomeMail;
use App\ConferenceEdition;
use Illuminate\Support\Str;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Schema;
use App\Traits\UserDatatableFeaturesTrait;
use Illuminate\Database\Eloquent\Collection;


class UserController extends Controller
{
	use UserDatatableFeaturesTrait;

	public function index()
	{
		if(auth()->user()->isSubAdmin() && auth()->user()->isMember() || auth()->user()->isAdmin() ){ // Only sub admins who are members
			return view('admin.users.index');
			
		}else abort(404);
		
	}

	public function allUsers(Request $request){
		// The Traits are used in this function
		$count = 1;
		$totalData = $this->totalData(auth()->user())->count(); 
		$totalFiltered = $totalData; 

		$limit = $request->input('length');
		$start = $request->input('start');

		if(empty($request->input('search.value')))
		{    
			$users = $this->emptySearch(auth()->user(), $start, $limit);
		}
		else {
			$search = $request->input('search.value'); 

			$users = $this->results(auth()->user(), $search, $start, $limit)['users'];
			$totalFiltered = $this->results(auth()->user(), $search,$start, $limit)['totalFiltered'];
		}

		$data = array();
		if(!empty($users)){
			foreach ($users as $user){
				$avatar = $user->passport ?? "frontend/passports/avatar.jpg"; 
				$campus = $user->campus ? $user->campus->name : '';
				$nestedData['S/N'] = $count ++;
				$nestedData['email'] = $user->email;
				$nestedData['family_id'] = $user->family_id;
				$nestedData['details'] = '<strong> '.$user->name.'</strong> 
					<br><i class="fa fa-envelope"></i> '.$user->email. '
					<br><i class="fa fa-phone"></i> '.$user->phone.'
					<br><i class="fa fa-university"></i> GSF, '.$campus;
				$nestedData['avatar'] = '<img class="mr-1" style="border-radius:50%" src="'.$avatar.'" alt="avatar" height="40" width="40">';
				$nestedData['status'] = $user->status == 0 ? 'Student' : 'Alumni';
				$nestedData['role'] = $user->rolename 
					. '<br><em>' 
					. (($user->rolename <> 'Admin' && $user->rolename <> 'Member') ? $user->portfolio_session : '')
					. '<em>';

				if(auth()->user()->isSubAdmin() && auth()->user()->isMember()){ 
					$nestedData['actions'] = $this->getEditLink('users.edit', $user->id)
					. $this->getDeleteLink('users.destroy', $user->id);
				}

				if(auth()->user()->isAdmin()){
					$nestedData['actions'] = $this->getEditLink('users.edit', $user->id)
					. sprintf(
						'<a class="actions" data-toggle="tooltip" data-placement="top" title="Switch To" href="%s"><i class="fa fa-unlock actions"></i></a>', route('switchuser', $user->id))
						. $this->getDeleteLink('users.destroy', $user->id);
				}
					
			
				$data[] = $nestedData;

			}
		}
		
		$json_data = array(
			"draw"            => intval($request->input('draw')),  
			"recordsTotal"    => intval($totalData),  
			"recordsFiltered" => intval($totalFiltered), 
			"data"            => $data   
			);

		echo json_encode($json_data); 
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
		$portfolios = $this->getCommunityPortfolios();
		$sessions = range(date('1982'), date('Y'));
		
		$president = $user->campus->stakeholder ?? null;
		if($president){
			$president = $president->where('role', 'President')->first();
		}

		if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){
			if(auth()->user()->isSubAdmin() && auth()->user()->isMember() && auth()->user()->chapter_id <> $user->chapter_id){ 
				return abort(404);
			}
			return view('admin.users.edit', compact('user', 'chapters', 'portfolios', 'sessions', 'president'));
		}
		
	}

	public function create()
	{
		$chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
		$hostels = Hostel::orderBy('name')->get();
		$foods = Food::all();

		$chapters = Chapter::all();
		$portfolios = $this->getCommunityPortfolios();
		$sessions = range(date('1982'), date('Y'));

		if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){
			return view('admin.users.create', compact('chapters', 'portfolios', 'sessions'));
		}
		else return back(404);
	}

	public function store(Request $request) {
		if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){
			if(auth()->user()->isAdmin()){
				$request['chapter_id'] = $request->chapter_id;
			}else $request['chapter_id'] = auth()->user()->chapter_id;			
		}
		$this->validate($request, [
			'email' => 'unique:users,email,',
		]);
		$data = $this->validateUser($request);
	
		//Handle password
		if ($request['password']) {
			$data['password'] = Hash::make($request['password']);
		} else {
			$data['password'] = Hash::make($request['phone']);
		}

		//Handle Passport Upload
		if ($request['passport']) {
			$passport = $this->uploadImage($request->passport, 'frontend/passports/', 500, 500);

			$data['passport'] = $passport;		
		} 
		
		// Handle name change
		$data['slug'] = Str::slug($request->name);
		
		try {
			$user = User::create($data);
		} catch (\Exception $e) {
			return back()->with('error', $e->getMessage());
		}
		
		$this->createFamilyId($user);

		return redirect(route('users.index'))->with('message', 'Operation Successful');
	}

	public function update(Request $request, User $user)
	{
		if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){
			if(auth()->user()->isSubAdmin() && auth()->user()->isMember() && auth()->user()->chapter_id <> $user->chapter_id){ 
				return abort(404);
			}
			if(auth()->user()->isAdmin()){
				$request['chapter_id'] = $request->chapter_id;
			}else $request['chapter_id'] = $user->chapter_id;			
		}
		$this->validate($request, [
			"email" => "unique:users,email," . $user->id,
		]);

		$data = $this->validateUser($request);
		
		//Handle password
		if ($request['password']) {
			$data['password'] = Hash::make($request['password']);
		} else {
			$data['password'] = Hash::make($request['phone']);
		}

		//Handle Passport Upload
		if ($request['passport']) {
			$passport = $this->uploadImage($request->passport, 'frontend/passports/', 500, 500);

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
		$roles = $this->getCommunityPortfolios();
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
		$portfolios = $this->getCommunityPortfolios();
		$sessions = range(date('1982'), date('Y'));
		
		$president = $user->campus->stakeholder ?? null;
		if($president){
			$president = $president->where('role', 'President')->first();
		}

		return view('admin.users.profile', compact('chapters', 'user', 'president', 'portfolios', 'sessions'));
	}


	public function usersExport(Request $request)
	{
		$count = 1;
		$edition = ConferenceEdition::find($request->edition);
		
		if (auth()->user()->role != 1) {
			return abort(404);
		}
		
		// $user = new User();
		// $tableColumns = Schema::getColumnListing('users');
		// $columnsForeign = [
		// 	'hostel_id' => 'hostel_name',
		// 	'food_id' => 'food_name',
		// 	'chapter' => 'chapter'
		// ];

		// foreach ($user->getHidden() as $key => $value) {
		// 	if (($k = array_search($value, $tableColumns)) !== false) {
		// 		unset($tableColumns[$k]);
		// 	}
		// }
		// foreach ($columnsForeign as $key => $value) {
		// 	if (in_array($key, $tableColumns)) {
		// 		$tableColumns[array_search($key, $tableColumns)] = $value;
		// 	}
		// }
		$data = [
			'edition_id' => $edition->id,
		];
		return Excel::download(new UsersExport($data), 'users_exported.xlsx');
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
			"sex" => "nullable",
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

}
