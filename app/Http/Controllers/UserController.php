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
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Collection;


class UserController extends Controller
{

	public function usersImportIndex()
	{
		return view('admin.users.import');
	}

	public function choirImportIndex()
	{
		return view('admin.choir.import');
	}

	public function medicalImportIndex()
	{
		return view('admin.medic.import');
	}

	public function moderatorsImportIndex()
	{
		return view('admin.moderator.import');
	}

	public function alumnisImportIndex()
	{
		return view('admin.alumni.import');
	}

	public function necsImportIndex()
	{
		return view('admin.nec.import');
	}

	public function officialsImportIndex()
	{
		return view('admin.official.import');
	}

	public function	getAdminParticipantSample($type){
		if($type == 'Participant'){
			$realpath = 'frontend/exportsamples/importparticipantsample.xlsx';
		}

		if($type == 'Moderator'){

			$realpath = 'frontend/exportsamples/importmoderatorsample.xlsx';
			
		}

		if($type == 'Alumni'){

			$realpath = 'frontend/exportsamples/importalumnisample.xlsx';
			
		}

        return response()->download($realpath);
	}
	public function index()
	{
		$count = 1;
		if (auth()->user()->level == 'Admin') {

			$participants = User::where('level', '<>', 'Admin')->orderBy('created_at', 'desc')->get();

			return view('admin.users.index', compact('participants', 'count'));
		} else if (auth()->user()->level == 'Moderator') {
			$participants = User::with(['hostel', 'moderator'])->whereUploadedBy(auth()->user()->id)->orderBy('created_at', 'asc')->get();

			return view('moderator.users.index', compact('participants', 'count'));
		}

		return abort(404);
	}

	public function trashed()
	{
		$count = 1;
		if (auth()->user()->level == 'Admin') {
			$participants = User::onlyTrashed()->where('level', '<>', 'Admin')->orderBy('created_at', 'desc')->get();

			return view('admin.users.trashed', compact('participants', 'count'));
		}
	}

	public function getChoir()
	{
		$count = 1;
		if (auth()->user()->level == 'Admin') {
			$participants = User::with(['hostel', 'moderator'])->whereLevel('Choir')->orderBy('created_at', 'desc')->get();

			return view('admin.choir.index', compact('participants', 'count'));
		}

		return abort(404);
	}

	public function getMedical()
	{
		$count = 1;
		if (auth()->user()->level == 'Admin') {
			$participants = User::with(['hostel', 'moderator'])->whereLevel('Medical')->orderBy('created_at', 'desc')->get();

			return view('admin.medic.index', compact('participants', 'count'));
		}

		return abort(404);
	}

	public function getNec()
	{
		$count = 1;
		if (auth()->user()->level == 'Admin') {
			$participants = User::with(['hostel', 'moderator'])->whereLevel('Nec')->orderBy('created_at', 'desc')->get();

			return view('admin.nec.index', compact('participants', 'count'));
		}

		return abort(404);
	}

	public function getOfficial()
	{
		$count = 1;
		if (auth()->user()->level == 'Admin') {
			$participants = User::with(['hostel', 'moderator'])->whereLevel('Official')->orderBy('created_at', 'desc')->get();

			return view('admin.official.index', compact('participants', 'count'));
		}

		return abort(404);
	}


	public function store(Request $request, User $user)
	{

		//Handle password
		if ($request['password']) {
			$password = Hash::make($request['password']);
		} else {
			$password = Hash::make($request['phone']);
		}

		//Handle Passport Upload
		//get filename with extensionz 
		$imgName = date('Y-m-d-His') . $request->passport->getClientOriginalName();
		$passport = Image::make($request->passport)->resize(500, 500);
		$passport->save('frontend/passports' . '/' . $imgName);
		$passport = 'frontend/passports/' . $imgName;


		//Store block for Admin
		if (auth()->user()->level == 'Admin') {
			$data = $this->validate($request, [
				'name' => 'required|min:3',
				'email' => 'required|unique:users,email',
				'phone' => 'required',
				'amount_paid' => 'required',
				'sex' => 'required',
				'chapter' => 'required|numeric',
				'passport' => 'required|max:200',
				'payment_type' => 'required',
				'transid' => 'required',
				'hostel_id' => 'required|numeric',
				'password' => 'nullable',
				'payment_type' => 'required',
				'food_id' => 'required',
			]);


			$setting = Setting::first();
			$data['password'] = $password;
			$hostel = Hostel::whereId($request->hostel_id)->first();
			$food = Food::whereId($request->food_id)->first();

			// Make sure the right people are in the right hostel
			if ($request->level == 'Participant' || $request->level == 'Alumni' || $request->level == 'Nec') {

				if ($hostel->type <> $request->sex || $hostel->level <> $request->level) {
					return back()->with('error', 'NOT SAVED. Please check the hostel you are trying to assign to this participant');
				}
			}

			if ($request->level == 'Participant' || $request->level == 'Alumni'  || $request->level == 'Medical'  || $request->level == 'Nec' || $request->level == 'Choir') {

				if ($food->level <> $request->level || $food->level <> $request->level) {

					return back()->with('error', 'NOT SAVED. Please check the food stand you are trying to assign to this participant');
				}
			}

			if ($request->level == 'Official' || $request->level == 'Medical' || $request->level == 'Official') {

				if ($hostel->level <> $request->level) {
					return back()->with('error', 'NOT SAVED. Please check the hostel you are trying to assign to this participant');
				}
			}

			//Fill slot, slot filled, type, others for participant
			if ($request->level == 'Participant') {
				$prefix = 'AOP';
				$data['slot'] = 1;
				$data['level'] = 'Participant';
				$data['slot_filled'] = 1;
				$data['type'] = 1;

				if ($request->amount_paid < $setting->registration_fee) {
					return back()->with('error', 'Participant cannot pay less than registration fee');
				}

				$data['amount_paid'] = $request->amount_paid;
			}

			//Fill slot, slot filled, type, others for moderator
			if ($request->level == 'Moderator') {

				if ($request->amount_paid < $setting->registration_fee) {
					return back()->with('error', 'Moderator cannot pay less than registration fee');
				}

				$prefix = 'AOP';
				$data['slot'] = $request->amount_paid / $setting->registration_fee;
				$data['level'] = 'Moderator';
				$data['slot_filled'] = 1;
				$data['type'] = 2;
				$data['amount_paid'] = $request->amount_paid;
			}

			if ($request->level == 'Alumni') {

				if ($request->amount_paid < $setting->alumni_fee) {
					return back()->with('error', 'Alumni cannot pay less than alumni minimum fee');
				}

				$prefix = 'AOA';
				$data['slot'] = 1;
				$data['level'] = 'Alumni';
				$data['slot_filled'] = 1;
				$data['type'] = 3;
				$data['amount_paid'] = $request->amount_paid;
			}

			if ($request->level == 'Nec') {

				if ($request->amount_paid < $setting->alumni_fee) {
					return back()->with('error', 'Nec cannot pay less than alumni minimum fee');
				}

				$prefix = 'AON';
				$data['slot'] = 1;
				$data['level'] = 'Nec';
				$data['slot_filled'] = 1;
				$data['type'] = 4;
				$data['amount_paid'] = $request->amount_paid;
			}

			if ($request->level == 'Choir') {

				if ($request->amount_paid < $setting->registration_fee) {
					return back()->with('error', 'Nec cannot pay less than registration fee');
				}

				$prefix = 'AOC';
				$data['slot'] = 1;
				$data['level'] = 'Choir';
				$data['slot_filled'] = 1;
				$data['type'] = 5;
				$data['amount_paid'] = $request->amount_paid;
			}


			if ($request->level == 'Medical') {

				if ($request->amount_paid < $setting->registration_fee) {
					return back()->with('error', 'Medical personnel cannot pay less than registration fee');
				}

				$prefix = 'AOP';
				$data['slot'] = 1;
				$data['level'] = 'Medical';
				$data['slot_filled'] = 1;
				$data['type'] = 1;
				$data['amount_paid'] = $request->amount_paid;
			}

			if ($request->level == 'Official') {

				if ($request->amount_paid < $setting->registration_fee) {
					return back()->with('error', 'Official personnel cannot pay less than registration fee');
				}

				$prefix = 'AOP';
				$data['slot'] = 1;
				$data['level'] = 'Official';
				$data['slot_filled'] = 1;
				$data['type'] = 1;
				$data['amount_paid'] = $request->amount_paid;
			}
			$data['passport'] = $passport;
			$data['registration_status'] = 'Complete';

			try {
				$newuser =  User::create($data);

				$newuser->update([
					'conference_number' => 'GSF-' . $prefix . '-' . $newuser->id,
				]);

				//increase hostel allocation
				$hostel->allocation += 1;
				$hostel->save();

				//increase foodstand allocation
				$food->allocation += 1;
				$food->save();
			} catch (\Illuminate\Database\QueryException $ex) {
				return back()->with('error', $ex);
			}

			return back()->with('message', 'Participant successfully created');
		} else if (auth()->user()->level == 'Moderator') {
			$data = $this->validate($request, [
				'name' => 'required|min:3',
				'email' => 'required|unique:users,email',
				'phone' => 'required',
				'sex' => 'required',
				'passport' => 'required|max:200',
				'password' => 'nullable',
				'payment_type' => 'nullable',
				'transid' => 'nullable',
			]);

			//Check if moderator has slots available
			if (auth()->user()->slot_filled >= auth()->user()->slot) {
				return back()->with('warning', 'You can no longer add participants because you have used up all available slots');
			}
						
			try {
				$newuser = User::create([
					'name' => $data['name'],
					'email' => $data['email'],
					'phone' => $data['phone'],
					'sex' => $data['sex'],
					'chapter' => auth()->user()->chapter,
					'passport' => $passport,
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

			$user = $newuser;
			$request->level = 'Participant';
			$hostels = Hostel::orderBy('allocation', 'ASC')->get();
				
				//Algorithm to assign hostel and food stand here
				$this->createOrUpdateHostel(
					$user,
					$request->level ?: $user->level, // the user might be changing levels 
					$request->sex ?: $user->sex, //the user might be changing gender
					$hostels, // use the same collection for efficiency
					$request
				);

			//If user has foodstand and hostel, mark as complete else return to pending
			// dd($user, $user->hostel_id);
			if($user->hostel_id == NULL){
				$user->registration_status = 'Pending';
				$user->save();

				return redirect(route('users.index'))->with('error', 'Participant successfully added but NO hostel is available at the moment. Edit the new participant with CONFERENCE ID: '. $user->conference_number. ' to try and auto assign an hostel. Alternatively, contact an admin.');
			}

			if($user->food_id == NULL){
				$user->registration_status = 'Pending';
				$user->save();
				

				return redirect(route('users.index'))->with('error', 'Participant successfully added but NO Foodstand is available at the moment. Edit the new participant with CONFERENCE ID: '. $user->conference_number. ' to try and auto assign a foodstand. Alternatively, contact an admin.');
			}

			return redirect(route('users.index'))->with('message', 'Participant successfully created, you have ' . (auth()->user()->slot - auth()->user()->slot_filled) . ' participant slot(s) left');
		}
		return abort(404);
	}

	/**
	 * Allocate the next avialable hostel to the $user
	 * @param App\User $user the user needing the allocation **NOTE** [$user eloguent object it passed, to implement the save method]
	 * @param string $level the level of the user to query or the $request->new_level and sort the hostel by level
	 * @param string $gender the gender of the user to query or the $request->new_gender and sort the hostel by gender
	 * @param Illuminate\Support\Collection $hostel_collection for eager loading already queried hostel is passed
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */

	private function createOrUpdateHostel(User $user, string $level, string $gender, Collection $hostel_collection, Request $request)
	{
		$collection = $hostel_collection->where('level', $level == 'Moderator' ?'Participant':$level)->where('type', $gender)
			->sortBy('allocation'); // sort by the lowest allocation
		$message = ['key' => 'error', 'value' => ':(, this is not you it\'s us, looks like there is no hostel available.'];

		$this->createNewFood($user); // it doesnt matter where you place this, it excutes once - CORRECT
	
		// Iterate through the collection
		$iterator = 0;
		$collection->each(
			function ($item, $key) use ($user, $hostel_collection, $collection, $request, &$iterator, &$message) {
				$iterator++;
				if ($item->capacity != $item->allocation) {
					// check if user has an associated hostel
					$user_hostel = $hostel_collection
						->where('id', $user->hostel_id)->first(); // you want to make sure you are querying the global as you can get
					$user_hostel // find the hostel from the sorted
						? $user_hostel->allocation-- : null; // , this is the only way to reduce the allocation effectively
					$user_hostel ? $user_hostel->save() : null; // and reduce by one

					$item->allocation++; // increase the numbers of allocation in the corresponding hostel
					$item->save(); // remember to save the hostel 

					$user->hostel_id = $item->id; // update the user hostel_id if required
					$user->sex = $request->sex ?: $user->sex; // gender
					$user->name = $request->name ?: $user->name; // name
					$user->phone = $request->phone ?: $user->phone; // phone
					$user->password = $request->password ? Hash::make($request->password) : $user->password; // password

					// Handle passport upload
					if ($request->hasFile('passport') && $request->file('passport')->isValid()) {
						$imgName = $request->passport->getClientOriginalName();
						$passport = Image::make($request->passport)->resize(500, 500);
						$passport->save('frontend/passports/' . date('Y-m-d-His') . $imgName);
						$image_path = $passport->dirname . '/' . $passport->basename;

						if (file_exists($user->passport))
							unlink($user->passport);

						$passport->destroy();
						$user->passport = $image_path;
					}
					if ($user->registration_status != 'Complete')
						$user->registration_status = 'Complete';

					$user->save(); // save the changes if any
					$message['key'] = 'message';
					$message['value'] = '&#128515;, update was successful';
					return false;
				}
				if ($item->capacity == $item->allocation && $collection->count() == $iterator) { // the last loop
					$message['key'] = 'error';
					$message['value'] = ':(, this is not you it\'s us, looks like there is no hostel available.';
					return false;
				}
			}
		);
		return redirect()->route('account')->with($message['key'], $message['value']);
	}

	/**
	 * Allocate the next avialable food to the $user
	 * @param App\User $user the user needing the allocation **NOTE** [$user eloguent object it passed, to implement the save method]
	 */
	private function createNewFood(User $user)
	{
		$collection = Food::where('level', $user->level == 'Moderator'? 'Participant': $user->level)
			->orderBy('allocation', 'ASC')->get(); // sort by the lowest allocation
		// Iterate through the collection
		$iterator = 0;

		if (!$user->food_id)
			$collection->each(function ($item) use (&$iterator, $user, $collection) {
				$iterator++;
				if ($item->capacity != $item->allocation) {
					$user->food_id = $item->id;
					$user->save();

					$item->allocation++;
					$item->save();
					return false; // break outta each
				} else if ($item->capacity == $item->allocation && $collection->count() == $iterator) { // the last loop
					return false; // breaks outta each
				}
			});
	}
	public function show($id)
	{
		//
	}

	public function edit(User $user)
	{

		$chapters = Chapter::all();

		$hostels = Hostel::all();
		$foods = Food::all();

		if (auth()->user()->level == 'Admin') {
			return view('admin.users.edit', compact('user', 'hostels', 'foods', 'chapters'));
		}

		if (auth()->user()->level == 'Moderator') {
			if ($user->sex == 'Female') {
				$hostels = $hostels->where('type', 'Female');;
			}

			if ($user->sex == 'Male') {
				$hostels = $hostels->where('type', 'Male');
			}

			return view('moderator.users.edit', compact('user', 'hostels', 'foods', 'chapters'));
		}
		return abort(404);
	}

	public function editChoir($id)
	{
		$user = User::findorfail($id);

		$chapters = Chapter::all();
		$hostels = Hostel::all();
		$foods = Food::all();

		if (auth()->user()->level == 'Admin') {
			return view('admin.choir.edit', compact('user', 'hostels', 'foods', 'chapters'));
		}
	}

	public function necImportIndex()
	{
		return view('admin.nec.import');
	}

	public function create()
	{
		$chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
		$hostels = Hostel::all();
		$foods = Food::all();
		$moderators = User::whereLevel('Moderator')->get();

		if (auth()->user()->level == 'Admin') {
			return view('admin.users.create', compact('chapters', 'hostels', 'foods', 'moderators'));
		} else if (auth()->user()->level == 'Moderator') {
			if (auth()->user()->slot_filled == auth()->user()->slot) {
				return back()->with('error', 'You have reached the maximum number of slots you can add');
			}
			return view('moderator.users.create', compact('chapters'));
		}

		return back(404);
	}

	public function editMedic($id)
	{
		$user = User::findorfail($id);

		$chapters = Chapter::all();
		$hostels = Hostel::all();
		$foods = Food::all();

		if (auth()->user()->level == 'Admin') {
			return view('admin.medic.edit', compact('user', 'hostels', 'foods', 'chapters'));
		}
	}

	public function editAlumni($id)
	{
		$user = User::findorfail($id);

		$chapters = Chapter::all();
		$hostels = Hostel::all();
		$foods = Food::all();

		if (auth()->user()->level == 'Admin') {
			return view('admin.alumni.edit', compact('user', 'hostels', 'foods', 'chapters'));
		}
	}


	public function update(Request $request, User $user)
	{

		$data = $this->validate($request, [
			'name' => 'required',
			'phone' => 'required',
			'sex' => 'in:Male,Female',
			'passport' => 'nullable|max:200'
		]);

		if ($request->has('passport')) {
			if (isset($user->passport)) {
				unlink($user->passport);
			}

			$imgName = date('Y-m-d-His') . $request->passport->getClientOriginalName();

			$passport = Image::make($request->passport)->resize(500, 500);
			$passport->save('frontend/passports' . '/' . $imgName);
			$passport = 'frontend/passports/' . $imgName;
		} else {
			$passport  = $user->passport;
		}

		//handle password
		if ($request['password']) {
			$password = Hash::make($request['password']);
		} else {
			$password = $user->password;
		}

		if (auth()->user()->level == 'Admin') {
		
			//Decrease allocation in previous hostel
			$hostel = Hostel::all();
			$food = Food::all();

			$old_hostel = $hostel->where('id', $user->hostel_id)->first();
			$old_food = $food->where('id', $user->food_id)->first();

			if ($request->hostel_id !== $user->hostel_id) {
				if (isset($user->hostel_id)) {
					$old_hostel->allocation = $old_hostel->allocation - 1;
					$old_hostel->save();
				}

				//Update new hostel
				$user->hostel_id = $request->hostel_id;
				$new_hostel = $hostel->where('id', $request->hostel_id)->first();
				$new_hostel->allocation = $new_hostel->allocation + 1;
				$new_hostel->save();
				$user->save();
			}

			if ($request->food_id !== $user->food_id) {
				if (isset($user->food_id)) {
					$old_food->allocation = $old_food->allocation - 1;
					$old_food->save();
				}
				//Update new hostel
				$user->food_id = $request->food_id;
				$new_food = $food->where('id', $request->food_id)->first();
				$new_food->allocation = $new_food->allocation + 1;
				$new_food->save();
				$user->save();
			}

			try {
				$user->name = $request->name;
				$user->email = $request->email;
				$user->phone = $request->phone;
				$user->sex = $request->sex;
				$user->chapter = $request->chapter;
				$user->payment_type = $request->payment_type;
				$user->transid = $request->transid;
				$user->password = $password;
				$user->passport = $passport;
				$user->registration_status = 'Complete';
				$user->save();
			} catch (\Illuminate\Database\QueryException $ex) {
				$error = $ex->getMessage();
				return back()->with('error', $ex);
			}

			return redirect()->back()->with('message', 'Update successful!');
		} else if (auth()->user()->level == 'Moderator') {
			$user = User::findOrFail($user->id);
			$hostels = Hostel::orderBy('allocation', 'ASC')->get();
		
			if (
				$user->hostel_id &&
				$user->sex == $request->sex &&
				$user->phone == $request->phone &&
				$user->name == $request->name &&
				!$request->hasFile('passport')
			) {
				return back()->with('message', ':) Great, looks like you didnt make any changes.');
			} else if (!$user->hostel_id) { // first time users - PERFECT
				return $this->createOrUpdateHostel(
					$user,
					$request->level ?: $user->level, // the user might be changing levels 
					$request->sex ?: $user->sex, //the user might be changing gender
					$hostels, // use the same collection for efficiency
					$request
				);
			} else if ($user->hostel_id) { // user is set but is updating something[hostel, gender, level]
				return $this->createOrUpdateHostel(
					$user,
					$request->level ?: $user->level, // the user might be changing levels 
					$request->sex ?: $user->sex, //the user might be changing gender
					$hostels, // use the same collection for efficiency
					$request
				);
			}
			return back()->with('error', ':( this is not you. It\'s us, looks like we could not complete your request, please contact the admin, let us hope he know what to do.');	
			
		}

		return abort(404);
	}


	public function destroy($id)
	{
		//stop from accidentally deleted self
		if (auth()->user()->id == $id) {
			return back()->with('warning', 'I\'m sorry but You cannot delete your self');
		}
		$hostel = Hostel::all();
		$foodstand = Food::all();
		$user = User::withTrashed()->where('id', $id)->firstOrFail();

		if ($user->trashed()) {
			if (isset($user->passport)) {
				unlink($user->passport);
			}
			$user->forceDelete();

			return back()->with('message', 'Participant has been deleted forever');
		} else {

			//Get user hostel
			if ($user->hostel) {
				$hostel = $hostel->where('id', $user->hostel->id)->first();
				$hostel->allocation -= 1;
				$hostel->save();
			};

			//Get user foodstand
			if ($user->food) {
				$food = $foodstand->where('id', $user->food->id)->first();
				$food->allocation -= 1;
				$food->save();
			};

			$user->delete();

			auth()->user()->slot_filled -= 1;
			auth()->user()->save();

			return back()->with('message', 'Record has been deleted!');
		}
	}

	public function usersExport()
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
		foreach ($columnsForeign as $key => $value) {
			if (in_array($key, $tableColumns)) {
				$tableColumns[array_search($key, $tableColumns)] = $value;
			}
		}
		return Excel::download(new UsersExport($tableColumns), 'users_exported.xlsx');
	}

	public function import(Request $request)
	{
		$data = $this->validate($request, [
			'file' => 'required|mimes:xlsx,csv',
			'import_level' => 'required|in:Participant,Moderator,Nec,Admin,Alumni,Offical,Choir,Medic'
		]);

		Excel::import($request->import_level, request()->file('file')) {
			$count = getActiveSheet()->getHighestRow();
		}
			

		try {
			Excel::import(new UsersImport($request->import_level), request()->file('file'));
		} catch (\Illuminate\Database\QueryException $ex) {
			$error = $ex->getMessage();
			return back()->with('error', $error);
		}
		return back()->with('message', 'Data has been imported succesfully');
	}

	public function choirExport()
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
		foreach ($columnsForeign as $key => $value) {
			if (in_array($key, $tableColumns)) {
				$tableColumns[array_search($key, $tableColumns)] = $value;
			}
		}

		return (new UsersExport($tableColumns))->level('Choir')->download('choir_exported.xlsx');
	}

	public function medicalExport()
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
		foreach ($columnsForeign as $key => $value) {
			if (in_array($key, $tableColumns)) {
				$tableColumns[array_search($key, $tableColumns)] = $value;
			}
		}

		return (new UsersExport($tableColumns))->level('Medical')->download('medical_exported.xlsx');
	}


	public function moderatorsExport()
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
		foreach ($columnsForeign as $key => $value) {
			if (in_array($key, $tableColumns)) {
				$tableColumns[array_search($key, $tableColumns)] = $value;
			}
		}

		return (new UsersExport($tableColumns))->level('Moderator')->download('moderator_exported.xlsx');
	}


	public function alumnisExport()
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
		foreach ($columnsForeign as $key => $value) {
			if (in_array($key, $tableColumns)) {
				$tableColumns[array_search($key, $tableColumns)] = $value;
			}
		}

		return (new UsersExport($tableColumns))->level('Alumni')->download('alumnis_exported.xlsx');
	}

	public function necsExport()
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
		foreach ($columnsForeign as $key => $value) {
			if (in_array($key, $tableColumns)) {
				$tableColumns[array_search($key, $tableColumns)] = $value;
			}
		}

		return (new UsersExport($tableColumns))->level('Nec')->download('necs_exported.xlsx');
	}

	public function officialsExport()
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
		foreach ($columnsForeign as $key => $value) {
			if (in_array($key, $tableColumns)) {
				$tableColumns[array_search($key, $tableColumns)] = $value;
			}
		}

		return (new UsersExport($tableColumns))->level('Official')->download('officials_exported.xlsx');
	}
}
