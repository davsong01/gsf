<?php

namespace App\Http\Controllers;

use App\Food;
use App\User;
use App\Hostel;
use App\Chapter;
use App\Payment;
use App\Setting;
use App\Donation;
use App\Material;
use App\Mail\WelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class ConferenceManagementController extends Controller
{
    public function index(){
		$setting = Setting::first();
		if( $setting->enable_conference == 0){
			return view('conference_management.admin.settings.edit', compact('setting'));
		}

        $chapters = Chapter::all();

		if (auth()->user()->role == 1) {
			$participants = User::where('role', '<>', 1)->get();
			$registered_participants = count($participants);
			$pending_registration = $participants->where('registration_status', 'Pending')->count();
			$total = $participants->sum('amount_paid');
			$completed_registration = count($participants->where('registration_status', 'Complete'));
			$donations = Donation::sum('amount');
			$materials = Material::count('id');

			return view('conference_management.admin.index', compact('registered_participants', 'pending_registration', 'completed_registration', 'total', 'donations', 'materials'));
			
		} elseif (auth()->user()->level <> 1 || auth()->user()->level <> 'Moderator') {

			return view('participant.index', compact('chapters'));
		} elseif (auth()->user()->level == 'Moderator') {

			$participants = User::with(['hostel', 'moderator'])->whereUploadedBy(auth()->user()->id)->orderBy('created_at', 'desc')->count();

			$pending_registration = $participants->where('registration_status', 'Pending')->whereUploadedBy(auth()->user()->id)->orderBy('created_at', 'desc')->count();

			$completed_registration = $participants->where('registration_status', 'Complete')->whereUploadedBy(auth()->user()->id)->orderBy('created_at', 'desc')->count();

			return view('moderator.index', compact('chapters', 'pending_registration', 'completed_registration', 'participants'));
		}

    }

	public function create()
	{
		$chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
		$hostels = Hostel::orderBy('name')->get();
		$foods = Food::all();

		$moderators = Payment::with('user')->whereLevel('Moderator')->get();

		if (auth()->user()->role == 1) {
			return view('conference_management.admin.users.create', compact('chapters', 'hostels', 'foods', 'moderators'));
		} 
		
		if (auth()->user()->payment->first()->level == 'Moderator') {
			if (auth()->user()->payment->first()->slot_filled == auth()->user()->payment->first()->slot) {
				return back()->with('error', 'You have reached the maximum number of slots you can add');
			}
			return view('moderator.users.create', compact('chapters'));
		}

		return back(404);
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
		if ($request['passport']) {

			$imgName = date('Y-m-d-His') . $request->passport->getClientOriginalName();
			$passport = Image::make($request->passport)->resize(500, 500);
			$passport->save('frontend/passports' . '/' . $imgName);
			$passport = 'frontend/passports/' . $imgName;
		
		} else {
			$passport = NULL;
		}


		//Store block for Admin
		if (auth()->user()->role == 1) {
			$data = $this->validate($request, [
				'name' => 'required|min:3',
				'email' => 'required|unique:users,email',
				'phone' => 'required',
				'sex' => 'required',
				'chapter' => 'required|numeric',
				'passport' => 'nullable|max:200',
				'payment_type' => 'required',
				'transid' => 'required',
				'hostel_id' => 'required|numeric',
				'password' => 'nullable',
				'payment_type' => 'required',
				'food_id' => 'required',
				'amount_paid' => 'required',
			]);
		
			if(!isset($data['uploaded_by'])){
				$data['uploaded_by'] = auth()->user()->id;
			}else{
				$data['uploaded_by'] = $data['uploaded_by'];
			}
			
			$setting = Setting::first();
			$data['password'] = $password;
			$hostel = Hostel::whereId($request->hostel_id)->first();
			$food = Food::whereId($request->food_id)->first();

			// Make sure the right people are in the right hostel
			if ($request->level == 'Participant' || $request->level == 'Alumni' || $request->level == 'Nec') {

				if ($hostel->type <> $request->sex || $hostel->level <> $request->level) {
					return back()->with('error', 'NOT SAVED. Gender or Hostel/foodstand level issue');
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
				$prefix = 'CONP';
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

				$prefix = 'CONP';
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

				$prefix = 'CONA';
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

				$prefix = 'CON';
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

				$prefix = 'CONC';
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

				$prefix = 'CONP';
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

				$prefix = 'CONP';
				$data['slot'] = 1;
				$data['level'] = 'Official';
				$data['slot_filled'] = 1;
				$data['type'] = 1;
				$data['amount_paid'] = $request->amount_paid;
			}
			$data['passport'] = $passport;
			$data['registration_status'] = 'Complete';

			try {
				$newuser =  User::create([
					'name' => $data['name'],
					'role' => 2,
					'slug' => Str::slug($data['name']),
					'email' => $data['email'],
					'phone' => $data['phone'],
					'sex' => $data['sex'],
					'chapter_id' => $data['chapter'],
					'passport' => $data['passport'],
					'password' => $data['password'],
				]);

				$newuser->update([
					'family_id' => 'GSF-' . $prefix . '-' . $newuser->id,
				]);

				if(!isset($data['uploaded_by'])){
					$data['uploaded_by'] = auth()->user()->id;
				}else{
					$data['uploaded_by'] = $request->uploaded_by;
				}
			
				$payment = Payment::Create([
					'type' => 1,
					'user_id' => $newuser->id,
					'hostel_id' => $hostel->id,
					'food_id' => $food->id,
					'level' => 'Participant',
					'uploaded_by' => $data['uploaded_by'],
					'amount_paid' => Setting::select('registration_fee')->first()->value('registration_fee'),
					'registration_status' => 'Complete',
					 
				]);

				
				//increase hostel allocation
				$hostel->allocation += 1;
				$hostel->save();

				//increase foodstand allocation
				$food->allocation += 1;
				$food->save();
			} catch (\Illuminate\Database\QueryException $ex) {
				return redirect(route('participants.create'))->with('error', $ex);
			}

			return back()->with('message', 'Participant successfully created');
		} else if (auth()->user()->payment->first()->level == 'Moderator') {
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
			if (auth()->user()->payment->first()->slot_filled >= auth()->user()->payment->first()->slot) {
				return back()->with('warning', 'You can no longer add participants because you have used up all available slots');
			}
						
			try {
				$newuser = User::create([
					'name' => $data['name'],
					'email' => $data['email'],
					'phone' => $data['phone'],
					'sex' => $data['sex'],
					'slug' => Str::slug($data['name']),
					'chapter_id' => auth()->user()->chapter,
					'passport' => $passport,
					'password' => $password
				]);

				
				
				$payment = Payment::Create([
					'type' => 1,
					'user_id' => $newuser->id,
					'level' => 'Participant',
					'uploaded_by' => auth()->user()->id,
					'amount_paid' => Setting::select('registration_fee')->first()->value('registration_fee'),
					'registration_status' => 'Complete',
					 
				]);

				$newuser->update([
					'family_id' => 'GSF-CONP-' . $newuser->id,
				]);

				auth()->user()->payment->first()->update([
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
					$request->level ?: $payment->level, // the user might be changing levels 
					$request->sex ?: $user->sex, //the user might be changing gender
					$hostels, // use the same collection for efficiency
					$request
				);

			//If user has foodstand and hostel, mark as complete else return to pending
			// dd($user, $user->hostel_id);
			if($payment->hostel_id == NULL){
				$payment->registration_status = 'Pending';
				$payment->save();

				return redirect(route('participants.index'))->with('error', 'Participant successfully added but NO hostel is available at the moment. Edit the new participant with CONFERENCE ID: '. $user->family_id. ' to try and auto assign an hostel. Alternatively, contact an admin.');
			}

			if($payment->food_id == NULL){
				$user->registration_status = 'Pending';
				$user->save();
				

				return redirect(route('participantss.index'))->with('error', 'Participant successfully added but NO Foodstand is available at the moment. Edit the new participant with CONFERENCE ID: '. $user->family_id. ' to try and auto assign a foodstand. Alternatively, contact an admin.');
			}

			return redirect(route('users.index'))->with('message', 'Participant successfully created, you have ' . (auth()->user()->slot - auth()->user()->slot_filled) . ' participant slot(s) left');
		}
		return abort(404);
	}


    public function edit($id){
        $user = Payment::with('user')->whereId($id)->first();

        $chapters = Chapter::all();

		$hostels = Hostel::all();
		$foods = Food::all();

		if (auth()->user()->role == 1) {
			return view('conference_management.admin.users.edit', compact('user', 'hostels', 'foods', 'chapters'));
		}
        
        if (auth()->user()->payment->first()->level == 'Moderator') {
			if ($user->sex == 'Female') {
				$hostels = $hostels->where('type', 'Female');;
			}

			if ($payment->sex == 'Male') {
				$hostels = $hostels->where('type', 'Male');
			}

			return view('moderator.users.edit', compact('user', 'hostels', 'foods', 'chapters'));
		}
		return abort(404);
    }

    public function update(Request $request, $id){
        $payment = Payment::with('user')->whereId($id)->first();
        
        $user = $payment->user;
      
		$data = $this->validate($request, [
			'name' => 'required',
			'phone' => 'required',
			'sex' => 'in:Male,Female',
			'passport' => 'nullable|max:200',
			'level' => 'required'
		]);

		if ($request->has('passport')) {
			if (isset($user->passport) && (file_exists( public_path() . '/frontend/passports/' . $user->passport))) {
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

		if (auth()->user()->role == 1) {
		
			//Decrease allocation in previous hostel
			$hostel = Hostel::all();
			$food = Food::all();
           
			$old_hostel = $hostel->where('id', $payment->hostel_id)->first();
			$old_food = $food->where('id', $payment->food_id)->first();
           
			if ($request->hostel_id !== $payment->hostel_id) {
				if (isset($payment->hostel_id)) {
					$old_hostel->allocation = $old_hostel->allocation - 1;
					$old_hostel->save();
				}

				//Update new hostel
				$payment->hostel_id = $request->hostel_id;
				$new_hostel = $hostel->where('id', $request->hostel_id)->first();
				$new_hostel->allocation = $new_hostel->allocation + 1;
				$new_hostel->save();
				$user->save();
			}

			if ($request->food_id !== $payment->food_id) {
				if (isset($payment->food_id)) {
					$old_food->allocation = $old_food->allocation - 1;
					$old_food->save();
				}
				//Update new hostel
				$payment->food_id = $request->food_id;
				$new_food = $food->where('id', $request->food_id)->first();
				$new_food->allocation = $new_food->allocation + 1;
				$new_food->save();
				$payment->save();
			}
      
			try {
				$user->name = $request->name;
				$user->email = $request->email;
				$user->phone = $request->phone;
				$user->sex = $request->sex;
				$payment->level = $request->level;
				$user->chapter_id = $request->chapter;
				$payment->payment_type = $request->payment_type;
				$payment->transid = $request->transid;
				$user->password = $password;
				$user->passport = $passport;
				$payment->registration_status = 'Complete';
				$user->save();
                $payment->save();
			} catch (\Illuminate\Database\QueryException $ex) {
				$error = $ex->getMessage();
                return redirect(route('conference.participants.edit', $payment->id))->with('error', $error);
			}

			return back()->with('message', 'Update successful!');

		} else if (auth()->user()->level == 'Moderator') {
			$user = User::findOrFail($user->id);
			$hostels = Hostel::orderBy('allocation', 'ASC')->get();
		
			if (
				$payment->hostel_id &&
				$user->sex == $request->sex &&
				$user->phone == $request->phone &&
				$user->name == $request->name &&
				!$request->hasFile('passport')
			) {
				return back()->with('message', ':) Great, looks like you didnt make any changes.');
			} else if (!$payment->hostel_id) { // first time users - PERFECT
				return $this->createOrUpdateHostel(
					$user,
					$request->level ?: $payment->level, // the user might be changing levels 
					$request->sex ?: $user->sex, //the user might be changing gender
					$hostels, // use the same collection for efficiency
					
					$request
				);
			} else if ($payment->hostel_id) { // user is set but is updating something[hostel, gender, level]
				return $this->createOrUpdateHostel(
					$user,
					$request->level ?: $payment->level, // the user might be changing levels 
					$request->sex ?: $user->sex, //the user might be changing gender
					$hostels, // use the same collection for efficiency
					$request
				);
			}
			return back()->with('error', ':( this is not you. It\'s us, looks like we could not complete your request, please contact an admin, let us hope he know what to do.');	
			
		}

		return abort(404);
    }

    public function participants(){
        $count = 1;
        if (auth()->user()->role == 1) {

			$participants = Payment::with('user')->wherehas('user')->orderBy('created_at', 'desc')->get();
			// $participants = Payment::with('user')->take(10)->get();
            // dd($participants);
			return view('conference_management.admin.users.index', compact('participants', 'count'));
        }
    }

    public function resendEmail($id){
        $payment = Payment::with('user')->findOrFail($id);
        $user = $payment->user;

        $data['family_id'] = $user->family_id;
		$data['name'] =  $user->name;
		$data['email'] =  $user->email;
		$data['phone'] =  $user->phone;
		$data['amount'] =  $payment->amount_paid;

		//send email to participant
		Mail::to($data['email'])->send(new WelcomeMail($data));

		return back()->with('message', 'Email resent successfully');
    }

    public function getCard($id)
	{
		$setting = Setting::first();
		$payment = Payment::with('user')->whereId($id)->first();
    
		if (auth()->user()->payment->first()->level == 'Participant') {
			if ($payment->registration_status != 'Complete') {
				return back()->with('error', 'You must complete registration before viewing this resource');
			}
		}

		if (auth()->user()->payment->first()->level == 'Moderator') {

			if ($payment->uploaded_by != auth()->user()->id) {
				return abort(404);
			}

			if ($payment->user->registration_status != 'Complete') {
				return back()->with('error', 'You must complete registration before viewing this resource');
			}
		}

		return view('card.id')->with('payment', $payment)
            ->with('setting', $setting)
            ->with('user', $payment->user);
	}

	public function trashed()
	{
		$count = 1;
		if (auth()->user()->role == 1) {
			$participants = User::with('payment')->wherehas('payment')->orderBy('created_at', 'desc')->onlyTrashed()->get();
			
			return view('conference_management.admin.users.trashed', compact('participants', 'count'));
		}
	}

	public function destroy($id){
	
		//stop from accidentally deleted self
		
		$hostel = Hostel::all();
		$foodstand = Food::all();
		$user = User::withTrashed()->with('payment')->where('id', $id)->first();

		$payment = Payment::where('user_id', $user->id)->first();
	
		if ($user->trashed()) {
			if (isset($user->passport)) {
				unlink($user->passport);
			}
			$user->forceDelete();
			$user->payment->forceDelete();

			return back()->with('message', 'Participant has been deleted forever');
		} else {

			//Get user hostel
			if ($payment->hostel) {
				
				$hostel = $hostel->where('id', $payment->hostel->id)->first();
				$hostel->allocation -= 1;
				$hostel->save();
			};

			//Get user foodstand
			if ($payment->food) {
				$food = $foodstand->where('id', $payment->food->id)->first();
				$food->allocation -= 1;
				$food->save();
			};

			$user->delete();
			$payment->delete();

			auth()->user()->payment->first()->slot_filled -= 1;
			auth()->user()->payment->first()->save();

			return back()->with('message', 'Record has been deleted!');
		}
	}

	
	public function restore($id){
		if (auth()->user()->level == 'Admin') {

			$user = User::withTrashed()->where('id', $id)->firstOrFail();

        	$user->restore();

       		return redirect(route('users.index'))->with('message', 'Participant has been restored');

		}else return abort(404);
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

}
