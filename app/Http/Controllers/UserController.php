<?php

namespace App\Http\Controllers;

use App\Food;
use App\User;
use App\Hostel;
use App\Chapter;
use App\Setting;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Schema;


class UserController extends Controller
{

	public function importExport()
	{
		return view('admin.users.import');
	}


	public function index()
	{
		$count = 1;
		if (auth()->user()->level == 'Admin') {
			$participants = User::with(['hostel', 'moderator'])->whereLevel('Participant')->orwhere('level', 'Moderator')->orderBy('created_at', 'desc')->get();

			return view('admin.users.index', compact('participants', 'count'));
		} else if (auth()->user()->level == 'Moderator') {
			$participants = User::with(['hostel', 'moderator'])->whereUploadedBy(auth()->user()->id)->orderBy('created_at', 'desc')->get();

			return view('moderator.users.index', compact('participants', 'count'));
		}

		return abort(404);
	}

	public function create()
	{
		$chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order

		if (auth()->user()->level == 'Admin') {
			return view('admin.users.create');
		} else if (auth()->user()->level == 'Moderator') {
			return view('moderator.users.create', compact('chapters'));
		}

		return back(404);
	}

	public function store(Request $request, User $user)
	{

		//Validate request
		$data = $this->validate($request, [
			'name' => 'required|min:3',
			'chapter' => 'required|numeric',
			'email' => 'required|unique:users,email',
			'phone' => 'required',
			'sex' => 'required',
			'passport' => 'required|max:200',
			'password' => 'nullable',

		]);

		//Handle password
		if ($request['password']) {
			$password = Hash::make($request['password']);
		} else $password = Hash::make($request['phone']);

		//Handle Passport Upload
		//get filename with extension
		$imgName = $request->passport->getClientOriginalName();
		$passport = Image::make($request->passport)->resize(500, 500);
		$passport->save('frontend/passports' . '/' . $imgName);
		$passportPath = 'frontend/passports' . '/' . $imgName;


		//Store block for Admin
		if (auth()->user()->level == 'Admin') {
		} else if (auth()->user()->level == 'Moderator') {
			//Check if moderator has slots available
			if (auth()->user()->slot_filled >= auth()->user()->slot) {
				return back()->with('warning', 'You can no longer add participants because you have used up all available slots');
			}

			//Assign hostel and food stand here


			try {
				$newuser = User::create([
					'name' => $data['name'],
					'email' => $data['email'],
					'phone' => $data['phone'],
					'sex' => $data['sex'],
					'chapter' => $data['chapter'],
					'passport' => $passportPath,
					'type' => 1,
					'level' => 'Participant',
					'uploaded_by' => auth()->user()->id,
					'password' =>  $password,
					'amount_paid' => Setting::select('registration_fee')->first()->value('registration_fee'),
					'registration_status' => 'Complete'
				]);

				$newuser->update([
					'conference_number' => 'GSF-AOP-' . $newuser->id,
				]);

				auth()->user()->update([
					'slot_filled' => auth()->user()->slot_filled + 1,
				]);
			} catch (\Illuminate\Database\QueryException $ex) {
				return back()->with('error', $ex);
			}

			return back()->with('message', 'Participant successfully created, you have ' . (auth()->user()->slot - auth()->user()->slot_filled) . ' participant slot(s) left');
		}
		return abort(404);
	}
	public function show($id)
	{
		//
	}

	public function edit(User $user)
	{
		$chapters = Chapter::all();

		if (auth()->user()->level == 'Admin') {
			if ($user->sex == 'Female') {
				$hostels = Hostel::whereType('Female')->whereLevel('Participant')->get();
			}

			if ($user->sex == 'Male') {
				$hostels = Hostel::whereType('Male')->whereLevel('Participant')->get();
			}

			$foods = Food::all();
			return view('admin.users.edit', compact('user', 'hostels', 'foods', 'chapters'));
		}
		return abort(404);
	}

	public function update(Request $request, User $user)
	{

		if (auth()->user()->level == 'Admin') {
			//handle password
			if ($request['password']) {
				$request['password'] = Hash::make($request['password']);
			} else $request['password'] = $user->password;

			try {
				$user->update($request->all());
			} catch (\Illuminate\Database\QueryException $ex) {
				$error = $ex->getMessage();
				return back()->with('error', $ex);
			}

			return redirect()->back()->with('message', 'Update successful!');
		} else if (auth()->user()->level == 'Moderator') {
			//Moderator updates here
			dd($user, $request->all());
		}

		return abort(404);
	}


	public function destroy($id)
	{
		$user = User::findOrFail($id);

		$user->delete();

		return back()->with('message', 'Record has been deleted forever!');
	}

	/**
	 * Download tables from storage
	 * @return Excel::download
	 */
	public function export()
	{

		$user = new User();
		$tableColumns = Schema::getColumnListing('users');
		$columnsForeign = [
			'hostel_id' => 'hostel_name',
			'food_id' => 'food_name',
			'chapter' => 'chapter'
		];

		foreach ($user->getHidden() as $key => $value) {
			if (($k = array_search($value, $tableColumns)) !== false) {
				unset($tableColumns[$k]);
			}
		}
		foreach($columnsForeign as $key => $value){
				if(in_array($key, $tableColumns)){
					$tableColumns[array_search($key, $tableColumns)] = $value;
				}
			}
		return Excel::download(new UsersExport($tableColumns), 'users_exported.xlsx');
	}

	public function import(Request $request)
	{
		$data = $this->validate($request, [
			'file' => 'required|mimes:xlsx,csv',
		]);


		try {
			// Excel::import(new ImportAllocations, request()->file('file'));
		} catch (\Illuminate\Database\QueryException $ex) {
			$error = $ex->getMessage();
			return back()->with('error', $error);
		}
		return redirect(route('allocation.index'))->with('message', 'Data has been imported succesfully');
	}

}
