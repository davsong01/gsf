<?php

namespace App\Http\Controllers;

use App\Food;
use App\User;
use App\Hostel;
use App\Chapter;
use App\Payment;
use App\Mail\WelcomeMail;
use App\ConferenceEdition;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ConferenceManagementController extends Controller
{
	private $edition;

	public function __construct(Request $request){
		$this->edition = ConferenceEdition::find($request->edition);
		if (!isset($this->edition) && empty($this->edition)) {
			// $edition = ConferenceEdition::orderBy('created_at', 'DESC')->first();
			return redirect(route('conferencemanagement.index'));
		}
	}

    public function index(Request $request){
		
		// Admin
		if (auth()->user()->role == 1) {
			$editions = ConferenceEdition::all();
			
			$count = 1;
			return view('conference_management.admin.editions.index', compact('editions','count'));
		}else{
			$edition = $this->edition ?? ConferenceEdition::orderBy('created_at','DESC')->first();

			if(auth()->user()->isParticipant($edition) || auth()->user()->isAlumni($edition) || auth()->user()->isModerator($edition)){
				return view('conference_management.participant.index', compact('edition'));
			}
		}
		
    }

	public function create(Request $request)
	{
		$edition = ConferenceEdition::find($request->edition);
		$chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
		$hostels = Hostel::where('conference_edition_id', $edition->id)->orderBy('name')->get();
		$foods = Food::where('conference_edition_id', $edition->id)->orderBy('name')->get();

		$moderators = Payment::with('user')->where('conference_edition_id',$edition->id)->whereLevel('Moderator')->get();

		if (auth()->user()->role == 1) {
			return view('conference_management.admin.users.create', compact('edition','chapters', 'hostels', 'foods', 'moderators'));
		} 
		
		if (auth()->user()->payment->first()->level == 'Moderator') {
			if (auth()->user()->payment->first()->slot_filled == auth()->user()->payment->first()->slot) {
				return back()->with('error', 'You have reached the maximum number of slots you can add');
			}
			return view('moderator.users.create', compact('chapters'));
		}

		return back(404);
	}

	public function show(Payment $conferencemanagement, Request $request){
		$chapters = Chapter::all();
		
		$payment = $conferencemanagement;
		$edition = $this->edition;
		if (auth()->user()->isParticipant($edition) || auth()->user()->isAlumni($edition) || auth()->user()->Nec($edition) || auth()->user()->Choir($edition)) {
			return view('conference_management.participant.single_payment', compact('edition', 'payment','chapters'));
		}
		if (auth()->user()->isModerator($edition)) {
			$allparticipants = Payment::with(['hostel', 'moderator'])->where('uploaded_by', auth()->user()->id)->orderBy('created_at', 'desc');
			$myParticipants = clone $allparticipants;
			$myParticipantsAll = $myParticipants->get();
			$participants = $allparticipants->count();
			$pending_registration = $allparticipants->where('registration_status', 'Pending')->count();
			$completed_registration = $allparticipants->where('registration_status', 'Complete')->count();
			$allparticipants = $allparticipants->get();
			$count = 1;
			return view('conference_management.moderator.index', compact('chapters', 'pending_registration', 'completed_registration', 'participants', 'myParticipantsAll', 'count'));
		}
	}

	public function store(Request $request, User $user)
	{
		if (auth()->user()->role == 1) {
			$data = $this->validate($request, [
				'name' => 'required|min:3',
				'email' => 'required|unique:users,email',
				'phone' => 'required',
				'sex' => 'required',
				'chapter' => 'required|numeric',
				'passport' => 'nullable|max:200',
				'transid' => 'nullable',
				'hostel_id' => 'required|numeric',
				'food_id' => 'required',
				'amount_paid' => 'required',
				'level' => 'required',
			]);
		}
		//Handle password
		if ($request['password']) {
			$data['password'] = Hash::make($request['password']);
		} else {
			$data['password']  = Hash::make($request['phone']);
		}
		
		if ($request['transid']) {
			$data['transid'] = $request['transid'];
		} else {
			$data['transid'] = $this->generateTransactionId();
		}

		//Handle Passport Upload
		if ($request['passport']) {
			$data['passport'] = $this->uploadImage($data['passport'], 'images/passports', 400, 400);
			
		} else {
			$data['passport'] = NULL;
		}
		
		$setting = ConferenceEdition::find($request->edition);
		$data['conference_edition_id'] = $setting->id;
		//Store block for Admin
		if (auth()->user()->role == 1) {	
			if(!isset($data['uploaded_by'])){
				$data['uploaded_by'] = auth()->user()->id;
			}else{
				$data['uploaded_by'] = $data['uploaded_by'];
			}
			
			// Check registration fee
			$this->checkRegFee($request->all(), $setting);
			$data['type'] = $this->getType($request);
			// Assign hostel
			// (You may want to check if gender is the same as hostel gender)
			// Assign Foodstand and Hostel
			
			$data['hostel_id'] = $this->assignHostel($data['level'], $data['sex'], $setting);
			$data['food_id'] = $this->assignFoodStand($data['level'], $data['chapter'],$setting);
			
			// Extras
			$extras = $this->getExtras($data['type'], $setting);
			$data['slot'] = $extras['slot'];
			$data['level'] = $extras['level'];
			$data['slot_filled'] = $extras['slot_filled'];
			$data['amount_paid'] = $request->amount_paid;
			$data['registration_status'] = 'Complete';
			$data['payment_type'] = 'Admin';
			
			$user = $this->createUser($data);
			$payment = $this->createPayment($data, $user);
			$this->createFamilyId($user, $extras['ledge']);
			
			return redirect(route('conference.participants',['type'=>$payment->level, 'edition'=>$setting]))->with('message', 'Participant successfully created');

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
					'passport' => $data['passport']
				]);

				$payment = Payment::Create([
					'type' => 1,
					'user_id' => $newuser->id,
					'level' => 'Participant',
					'uploaded_by' => auth()->user()->id,
					'amount_paid' => $setting->registration_fee,
					'registration_status' => 'Complete',
					 
				]);
				
				$extras = $this->getExtras($data['type'], $setting);
				$this->createFamilyId($newuser, $extras['ledge']);
				
				$payment->update([
					'slot_filled' => $payment->slot_filled + 1,
				]);

			} catch (\Illuminate\Database\QueryException $ex) {
				return back()->with('error', $ex);
			}

			$user = $newuser;
			$data['level'] = 'Participant';
			
			$hostels = Hostel::orderBy('allocation', 'ASC')->get();
				
				//Algorithm to assign hostel and food stand here
				// $this->createOrUpdateHostel(
				// 	$user,
				// 	$request->level ?: $payment->level, // the user might be changing levels 
				// 	$request->sex ?: $user->sex, //the user might be changing gender
				// 	$hostels, // use the same collection for efficiency
				// 	$request
				// );

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

		dd($request->all());

		return abort(404);
	}

	public function checkRegFee($request, $setting){
		$error = 0;
		if($request['level'] == 'Participant' and ($request['amount_paid'] < $setting->registration_fee)){
			$error = 1;
		}
	
		if ($request['level'] == 'Alumni' and ($request['amount_paid'] != $setting->new_alumni_registration_fee || $request['amount_paid'] != $setting->alumni_registration_fee)) {
			$error = 1;
		}

		if ($request['level'] == 'Nec' and ($request['amount_paid'] < $setting->alumni_registration_fee)) {
			$error = 1;
		}

		if($error == 1){
			return back()->with('error', 'Amount is lower than registration fee for '. $request['level']);
		}
	}	

    public function edit($id, Request $request){
        $user = Payment::with('user')->whereId($id)->first();
		$edition = ConferenceEdition::find($request->edition);
		
        $chapters = Chapter::all();
		$hostels = Hostel::where('conference_edition_id', $edition->id)->orderBy('name')->get();
		$foods = Food::where('conference_edition_id', $edition->id)->orderBy('name')->get();

		if (auth()->user()->role == 1) {
			return view('conference_management.admin.users.edit', compact('user', 'hostels', 'foods', 'chapters','edition'));
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
			$update['passport'] = $this->uploadImage($data['passport'], 'images/passports', 400, 400);
		} 
		
		$update['phone'] = $request['phone'] ?? $user->phone;
		$update['name'] = $request['name'] ?? $user->name;
		$update['sex'] = $request['sex'] ?? $user->sex;
		
		// handle gender change
		if ($request->has('sex') && $request['sex'] != $user->sex) {
			$update['hostel_id'] = ($this->assignHostel($payment->level, $request['sex']))->id ?? null;
			// If no hostel
			if (!isset($update['hostel_id']) && empty($update['hostel_id'])) {
				return back()->with('error', 'Sorry, there is no available hostel for you at the moment. Changes not saved!');
			}else{
				$this->reduceHostelAllocation($payment);
			}
		}
		
		//handle password
		if ($request->has('password') && !empty($request->password)) {
			$update['password'] = Hash::make($request['password']);
		} 

		// All Participants update
		if (auth()->user()->role != 1) {
			
			$user->update(Arr::except($update, ['hostel_id']));
			$payment->update(Arr::only($update, ['hostel_id']));
		}
		
		//Moderator
		// Admin and moderator, what happens to normal participant?
		if (auth()->user()->role == 1) {
			//Decrease allocation in previous hostel
			$update['hostel_id'] = ($this->assignHostel($payment->level, $request['sex']))->id ?? null;

			// $hostel = Hostel::all();
			// $food = Food::all();
			
			// $old_hostel = $hostel->where('id', $payment->hostel_id)->first();
			// $old_food = $food->where('id', $payment->food_id)->first();
			
			// if ($request->hostel_id !== $payment->hostel_id) {
			// 	if (isset($payment->hostel_id)) {
			// 		$old_hostel->allocation = $old_hostel->allocation - 1;
			// 		$old_hostel->save();
			// 	}

			// 	//Update new hostel
			// 	$payment->hostel_id = $request->hostel_id;
			// 	$new_hostel = $hostel->where('id', $request->hostel_id)->first();
			// 	$new_hostel->allocation = $new_hostel->allocation + 1;
			// 	$new_hostel->save();
			// 	$user->save();
			// }

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
				return $this->createOrUpdateHostel($user,$request->level ?: $payment->level,$request->sex ?: $user->sex,$hostels,$request);
			} else if ($payment->hostel_id) { // user is set but is updating something[hostel, gender, level]
				return $this->createOrUpdateHostel($user,$request->level ?: $payment->level,$request->sex ?: $user->sex,$hostels,$request);
			}

			return back()->with('error', ':( this is not you. It\'s us, looks like we could not complete your request, please contact an admin, let us hope he know what to do.');	
			
		}

		return back()->with('message','Operation succesful');
    }

	public function reduceHostelAllocation($payment){
		$current_hostel = Hostel::find($payment->hostel->id);

		if($current_hostel->allocation == 0){
			return; 
		}else{
			$payment->hostel->update(['allocation'=> $payment->hostel->allocation-1]);
			return $payment->hostel;
		}
	}

	// public function reduceFoodStandAllocation($user)
	// {
	// 	$user->payment->food->update(['allocation' => $user->payment->food->allocation - 1]);
	// 	return $user->payment->food;
	// }

    public function participants($type='',$edition=''){
        $count = 1;
        if (auth()->user()->role == 1) {
			$participants = Payment::with('user')->where('conference_edition_id',$edition)->wherehas('user')->orderBy('created_at', 'desc')->where('level',$type)->get();
			$edition = ConferenceEdition::find($edition);
		
			return view('conference_management.admin.users.index', compact('participants', 'count', 'edition','type'));
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
		$payment = Payment::find($id);
		if (!auth()->user()->completeReg($this->edition)) {
			return back()->with('error', 'You must complete registration before viewing this resource');
		}

		if (auth()->user()->isModerator($this->edition)) {

			if ($payment->uploaded_by != auth()->user()->id) {
				return abort(404);
			}
		}
		
		return view('card.id')->with('payment', $payment)
            ->with('edition', $this->edition)
            ->with('user', $payment->user);
	}

	public function trashed(Request $request)
	{
		$edition = ConferenceEdition::find($request->edition);
		$count = 1;
		
		if (auth()->user()->role == 1) {
			$participants = User::join('payments', 'payments.user_id', '=', 'users.id')
			->where('payments.conference_edition_id', $edition->id)
			->select('users.*','payments.level', 'payments.amount_paid')
				->orderBy('users.created_at', 'desc')->onlyTrashed()->get();
				// dd($participants);
			return view('conference_management.admin.users.trashed', compact('participants', 'count','edition'));
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
