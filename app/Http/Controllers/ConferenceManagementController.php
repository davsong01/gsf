<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\User;
use App\Models\Hostel;
use App\Models\Chapter;
use App\Models\Payment;
use App\Models\CriticalEmail;
use App\Mail\WelcomeMail;
use App\Models\ConferenceEdition;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ConferenceUsersImport;
use App\Http\Controllers\CriticalEmailController;

class ConferenceManagementController extends Controller
{
	public $edition;

    public function index(Request $request){
		// Admin
		
		if (auth()->user()->role == 1) {
			if(auth()->user()->conference_role == 'superadmin'){
				$editions = ConferenceEdition::all();
			}else{
				$editions = ConferenceEdition::where('id', $this->edition->id)->get();
			}
			
			$count = 1;
			return view('conference_management.admin.editions.index', compact('editions','count'));
		}else{
			$edition = $this->edition ?? ConferenceEdition::where('status','active')->orderBy('created_at','DESC')->first();
			
			if(auth()->user()->payments->count() > 0){
				if(auth()->user()->isParticipant($edition) || auth()->user()->isAlumni($edition) || auth()->user()->isModerator($edition)){
					return view('conference_management.participant.index', compact('edition'));
				}
			}else{
				return back()->with('error','You have not registered for any conference');
			}
		}
    }

	public function create(Request $request)
	{
		$edition = ConferenceEdition::find($request->edition);
		$chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
		$hostels = Hostel::where('conference_edition_id', $edition->id)->orderBy('name')->get();
		$foods = Food::where('conference_edition_id', $edition->id)->orderBy('name')->get();
		$type = '';
		$moderator = Payment::where(['user_id' => auth()->user()->id, 'level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();
		$moderators = Payment::where(['level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->get();
		$payment = $moderator;
		// $moderator_participants = Payment::where(['user_id' => auth()->user()->id, 'level' => 'participant', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete','uploaded_by'=>auth()->user()->id])->get();
		
		if (auth()->user()->role == 1) {
			$type = $request->type;
			return view('conference_management.admin.users.create', compact('edition','chapters', 'hostels', 'foods', 'moderators','moderator','type'));
		}

		if (!$moderator) {
			return abort(404);
		} else {
			if ($moderator->slot_filled == $moderator->slot) {
				return back()->with('error', 'You have reached the maximum number of slots you can add');
			}
			return view('conference_management.moderator.users.create', compact('chapters', 'edition','payment'));
		}
	}

	public function staffCreate(Request $request, $edition)
	{
		$edition = ConferenceEdition::find($edition);
		if (isset($edition) && $edition->status == 'active') {
			if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') {
				$staff = User::where('role', 1)->where('conference_role', 'admin')->get();
				return view('conference_management.admin.staff.create', compact('edition'));
			}
		} else {
			return back()->with('error', 'Conference Edition not active');
		}
	}

	public function edit($id, Request $request)
	{
		$user = $payment = Payment::with('user')->whereId($id)->first();
		
		$edition = ConferenceEdition::find($request->edition);
		$moderator = Payment::where(['user_id' => auth()->user()->id, 'level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();

		$chapters = Chapter::all();
		$hostels = Hostel::where('conference_edition_id', $edition->id)->orderBy('name')->get();
		$foods = Food::where('conference_edition_id', $edition->id)->orderBy('name')->get();

		if (auth()->user()->role == 1) {
			return view('conference_management.admin.users.edit', compact('user', 'hostels', 'foods', 'chapters', 'edition'));
		}

		if (!$moderator) {
			return abort(404);
		} else {
			if ($user->user->sex == 'Female') {
				$hostels = Hostel::where(['conference_edition_id' => $edition->id, 'level' => 'Participant', 'type' => 'Female'])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			}

			if ($user->user->sex == 'Male') {
				$hostels = Hostel::where(['conference_edition_id' => $edition->id, 'level' => 'Participant', 'type' => 'Male'])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			}

			if ($edition->foodstand_field_assignment == 'yes' && in_array($moderator->user->chapter_id, [86])) {
				$foods = Food::where(['conference_edition_id' => $edition->id, 'level' => 'Participant', 'off_campus' => 'yes'])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			} else {
				$foods = Food::where(['conference_edition_id' => $edition->id, 'level' => 'Participant', 'off_campus' => 'no'])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			}

			return view('conference_management.moderator.users.edit', compact('user', 'hostels', 'foods', 'chapters', 'edition','payment'));
		}

		return abort(404);
	}

	public function staffEdit($id, Request $request){
		$edition = ConferenceEdition::find($request->edition);
		$user = User::find($id);
		
		if (isset($edition) && $edition->status == 'active') {
			if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') {
				$staff = User::where('role', 1)->where('conference_role', 'admin')->get();
				return view('conference_management.admin.staff.edit', compact('edition','user'));
			}
		} else {
			return back()->with('error', 'Conference Edition not active');
		}
	}

	public function show(Payment $conferencemanagement, Request $request){
		$chapters = Chapter::all();
		
		$payment = $conferencemanagement;
		$edition = $this->edition;
		
		if (auth()->user()->isParticipant($edition) || auth()->user()->isAlumni($edition) || auth()->user()->isNec($edition) || auth()->user()->isChoir($edition)) {
			return view('conference_management.participant.single_payment', compact('edition', 'payment','chapters'));
		}
		if (auth()->user()->isModerator($edition)) {
			
			$allparticipants = Payment::with(['hostel', 'moderator'])->where(['uploaded_by'=>auth()->user()->id, 'conference_edition_id'=> $payment->conference_edition_id])->orderBy('created_at', 'desc');
			
			$thispayment = Payment::with(['hostel', 'moderator'])->where(['uploaded_by' => auth()->user()->id,'user_id' => auth()->user()->id, 'conference_edition_id' => $payment->conference_edition_id])->first();
			// dd($allparticipants->get(), $payment->conference_edition_id);
			$myParticipants = clone $allparticipants;
			$myParticipantsAll = $myParticipants->get();
			$participants = $allparticipants->count();
			
			$pending_registration = $myParticipantsAll->where('registration_status', 'Pending')->count();
			$completed_registration = $allparticipants->where('registration_status', 'Complete')->count();
			$allparticipants = $allparticipants->get();
			$count = 1;
			return view('conference_management.moderator.index', compact('chapters', 'pending_registration', 'completed_registration', 'participants', 'myParticipantsAll', 'count','payment','edition', 'thispayment'));
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
		if ($request->has('passport')) {
			$data['passport'] = $this->uploadImage($request->passport, 'images/passports', 400, 400);
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
			$family_id = $this->createFamilyId($user, $extras['ledge']);

			// Log Email
			$data['type'] = 'welcome_mail';
			$data['amount'] = $data['amount_paid'];
			$data['family_id'] = $family_id ;
			$data['chapter'] = isset($user->campus->name) ? $user->campus->name : '';
			//send email to participant
			$email = [
				'subject' => 'Thank you for registering',
				'recipient_name' => $data['name'],
				'recipient' => $data['email'],
				'type' => $data['type'],
				'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
			];
			$this->logEmail($email);
			return redirect(route('conference.participants',['type'=>$payment->level, 'edition'=>$setting]))->with('message', 'Participant successfully created');

		}
		
		$edition = ConferenceEdition::find($request->edition);
		$moderator = Payment::where(['user_id' => auth()->user()->id, 'level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();
		
		if($moderator){
			$data2 = $this->validate($request, [
				'name' => 'required|min:3',
				'email' => 'required|unique:users,email',
				'phone' => 'required',
				'sex' => 'required',
				'payment_type' => 'nullable',
				'transid' => 'nullable',
			]);
			$data['type'] = 1;
			$data = array_merge($data,$data2);
			
			//Check if moderator has slots available
			if ($moderator->slot_filled >= $moderator->slot) {
				return back()->with('warning', 'You can no longer add participants because you have used up all available slots');
			}
			
			// try {
				$newuser = User::create([
					'name' => $data['name'],
					'email' => $data['email'],
					'phone' => $data['phone'],
					'sex' => $data['sex'],
					'slug' => Str::slug($data['name']),
					'chapter_id' => auth()->user()->chapter_id,
					'passport' => $data['passport'],
					'password' => $data['password']
				]);
				$data['level'] = 'Participant';
				
				$payment = Payment::Create([
					'type' => 1,
					'user_id' => $newuser->id,
					'level' => 'Participant',
					'uploaded_by' => auth()->user()->id,
					'amount_paid' => $setting->registration_fee,
					'slot' => 1,
					'slot_filled' => 1,
					'payment_type' => $moderator->payment_type,
					'transid' => $this->generateTransactionId(),
					'registration_status' => 'Complete',
					'conference_edition_id' => $request->edition,
				]);

				if (in_array($data['level'], ['Participant', 'Alumni', 'Nec'])) {
					$hostel = $this->assignHostel($data['level'], $data['sex']);
					$food = $this->assignFoodStand($data['level'], $newuser->chapter_id);

					$data['hostel_id'] = $hostel->id ?? null;
					$data['hostel'] = $hostel->name ?? null;
					$data['food_id'] = $food->id ?? null;
					$data['foodstand'] = $food->name ?? null;

					$payment->update([
						'hostel_id' => $data['hostel_id'] ?? null,
						'food_id' => $data['food_id'] ?? null
					]);
				}

				$extras = $this->getExtras($data['type'], $setting);
				$this->createFamilyId($newuser, $extras['ledge']);
				$data['chapter'] = isset($newuser->campus->name) ? $newuser->campus->name : '';
				
				//update temp user
				$data['type'] = 'welcome_mail';
				$data['amount'] = $setting->registration_fee;
				$data['family_id'] = $newuser->family_id;
					
				//send email to participant
				$email = [
					'subject' => 'Thank you for registering',
					'recipient_name' => $data['name'],
					'recipient' => $data['email'],
					'type' => $data['type'],
					'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
				];
				
				$this->logEmail($email);
				// Mail::to($data['email'])->send(new WelcomeMail($data));
				$email = [
					'subject' => 'New Registration',
					'type' => 'new_registration',
					'recipient' => $setting->official_email,
					'chapter' => $newuser->campus,
					'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
				];

				//send email to official email
				$this->logEmail($email);
				$moderator->update([
					'slot_filled' => $moderator->slot_filled + 1,
				]);

			return redirect(route('conferencemanagement.show', ['type' => $moderator->level,'conferencemanagement' => $payment->id, 'edition' => $payment->conference_edition_id]))->with('message', 'Participant successfully created, you have ' . ($moderator->slot - $moderator->slot_filled) . ' participant slot(s) left');

		}

		return abort(404);
	}

	public function staffStore(Request $request){
		
		$data = $this->validate($request, [
			"name" => "required",
			"email" => "required",
			"phone" => "required",
			"sex" => "required",
			"conference_role" => "required",
			"password"=>"nullable"
		]);

		//Handle password
		if ($request['password']) {
			$password = $data['password'];
		} else {
			$password = $request['phone'];
		}

		$password = Hash::make($password);
		//Handle Passport Upload
		if ($request->has('avatar')) {
			$data['passport'] = $this->uploadImage($request->avatar, 'images/passports', 400, 400);
		} else {
			$data['passport'] = NULL;
		}
		
		$user = User::Create([
			"name" => $data['name'],
			"email" => $data['email'],
			"phone" => $data['phone'],
			"sex" => $data['sex'],
			"passport" => $data['passport'],
			"role" => 1,
			'slug' => Str::slug($data['name']),
			"conference_role" => $data['conference_role'],
			"password" => $password
		]);

		$user->update([
			'family_id' => $this->generateStaffFamilyId($this->edition, $user),
		]);

		return redirect(route('conference.staff', ['edition' => $this->edition]))->with('message', 'Staff successfully created');

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
		
		// Moderator
		$moderator = Payment::where(['user_id' => auth()->user()->id, 'level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();
		
		if (isset($moderator) && !empty($moderator)) {
			$edition = $this->edition;
			// Hostel may not be set above, so set it
			if(!isset($update['hostel'])){
				$update['hostel_id'] = ($this->assignHostel($payment->level, $request['sex']))->id ?? null;
				// dd($update['hostel_id'],$payment->level, $request['sex']);
				if (!isset($update['hostel_id']) && empty($update['hostel_id'])) {
					return back()->with('error', 'Sorry, there is no available hostel for you at the moment. Changes not saved!');
				} else {
					$this->reduceHostelAllocation($payment);
				}
			}
			
			if(!isset($payment->food) && empty($payment->food)){
				$update['food_id'] = ($this->assignFoodStand($payment->level, $user->chapter_id))->id ?? null;
			}else{
				$update['food_id'] = $payment->food_id;
			}
		
		}

		$user->update(Arr::except($update, ['hostel_id','food_id']));
		$payment->update(Arr::only($update, ['hostel_id','food_id']));

		return back()->with('message','Operation succesful');
    }

	public function staffUpdate(Request $request, $id){
		
		$edition = ConferenceEdition::find($request->edition);
		$user = User::find($id);
		
		if (isset($edition) && $edition->status == 'active') {
			if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') {
				//Handle password
				if ($request['password']) {
					$password = $request['password'];
				} else {
					$password = $request['phone'];
				}

				$request['password'] = Hash::make($password);
				
				//Handle Passport Upload
				if ($request->has('avatar')) {
					$request['passport'] = $this->uploadImage($request->avatar, 'images/passports', 400, 400);
				} else {
					$request['passport'] = NULL;
				}

				$user->update($request->except(['edition','avatar']));
				return redirect(route('conference.staff', ['edition' => $this->edition]))->with('message', 'Staff successfully updated');
			}
		} else {
			return back()->with('error', 'Conference Edition not active');
		}
	}

	public function reduceHostelAllocation($payment){
		if (isset($payment->hostel->id) && !empty($payment->hostel->id)) {
			$current_hostel = Hostel::find($payment->hostel->id);
			
			if($current_hostel->allocation == 0){
				return; 
			}else{
				$payment->hostel->update(['allocation'=> $payment->hostel->allocation-1]);
				return $payment->hostel;
			}
		}
	}

	public function reduceFoodStandAllocation($payment){
		if (isset($payment->food->id) && !empty($payment->food->id)) {
			$current_food = Food::find($payment->food->id);
			
			if($current_food->allocation == 0){
				return; 
			}else{
				$payment->food->update(['allocation'=> $payment->food->allocation-1]);
				return $payment->food;
			}
		}
	}

    public function participants($type='',$edition=''){
        $count = 1;
		
        if (auth()->user()->role == 1) {
			$participants = Payment::with('user')->where('conference_edition_id',$edition)->wherehas('user')->orderBy('created_at', 'desc')->where('level',$type)->get();
			$edition = ConferenceEdition::find($edition);
			
			return view('conference_management.admin.users.index', compact('participants', 'count', 'edition','type'));
        }
    }

	public function staffIndex($edition = '')
	{
		$count = 1;
		$edition = ConferenceEdition::find($edition);
		if(isset($edition) && $edition->status == 'active'){
			if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') {
				$staff = User::where('role', 1)->get();
				
				return view('conference_management.admin.staff.index', compact('staff', 'count', 'edition'));
			}
		}else{
			return back()->with('error', 'Conference Edition not active');
		}
	}

    public function getCard($id)
	{
		$payment = Payment::find($id);

		if (!auth()->user()->completeReg($payment->edition) && auth()->user()->role <> 1) {
			return back()->with('error', 'You must complete registration before viewing this resource');
		}

		if (auth()->user()->isModerator($payment->edition)) {

			if ($payment->uploaded_by != auth()->user()->id) {
				return abort(404);
			}
		}
		
		return view('card.id')->with('payment', $payment)
            ->with('edition', $payment->edition)
            ->with('user', $payment->user);
	}

	public function resendEmail(Request $request, $id){
		$payment = Payment::find($id);
		$user = User::where('id', $payment->user_id)->first();
		
		$criticalEmail = CriticalEmail::where('recipient',$user->email)->where('type','welcome_mail')->where('status',1)->first();
		
		if($criticalEmail){
			$data['type'] = $criticalEmail->type;
			$data['recipient'] = $criticalEmail->recipient;
			$data['content'] = $criticalEmail->content;
			$data['subject'] = $criticalEmail->subject;
			$data['attachments'] = $criticalEmail->attachments;

			$res = $this->sendEmail($data);
			if ($res['message'] && $res['message'] == 'success') {
				return back()->with('message', 'Email resent successfully');
			} else {
				return back()->with('error', $res['error']);
			}
		}else{
			return back()->with('error','No sent Email logged for user!');
		}
	}

	public function usersImportIndex(Request $request)
	{
		$edition = ConferenceEdition::find($request->edition);
		
		if(auth()->user()->role == 1){
			$type = $request->type;
			$chapters = Chapter::all();
			
			return view('conference_management.admin.users.import', compact('chapters','edition', 'type'));
		}

		if(auth()->user()->isModerator($edition)){
			$payment = Payment::where(['user_id' => auth()->user()->id, 'conference_edition_id' => $request->edition, 'registration_status'=>'Complete'])->first();
			
			if($payment->slot_filled >= $payment->slot){
				return back()->with('error', 'You have already exhausted your registration slots');
			}
			$type = 'Participant';
			return view('conference_management.moderator.users.import', compact('edition', 'type','payment'));
		}

	}

	public function getAdminParticipantSample(Request $request,$type){
	
		$path = public_path() . '/frontend/exportsamples/import'. Str::lower($type).'.xlsx';
		
		if (file_exists($path)) {
			return response()->download($path);
		} else {
			return back()->with('error', 'File doesnt exist');
		}
	}

	public function import(Request $request, $type)
	{
		if (auth()->user()->role == 1 || auth()->user()->isModerator($this->edition)) {
			if (auth()->user()->isAdmin()) {
				$data = $this->validate($request, [
					'file' => 'required|mimes:xlsx,csv',
				]);
				$request['chapter_id'] = $request->chapter_id;
				$request['edition'] = ConferenceEdition::where('id', $request['edition'])->first();
			} else {
				$data = $this->validate($request, [
					'file' => 'required|mimes:xlsx,csv',
					'chapter_id' => 'nullable',
				]);
				$payment = Payment::where(['user_id' => auth()->user()->id, 'conference_edition_id' => $request->edition, 'registration_status'=>'Complete'])->first();
				
				if ($payment->slot_filled >= $payment->slot) {
					return back()->with('error', 'You have already exhausted your registration slots');
				}
				$request['chapter_id'] = auth()->user()->chapter_id;
			}
		} else return abort(404);

		try {
			Excel::import(new ConferenceUsersImport($request->all()), request()->file('file'));
		} catch (\Illuminate\Database\QueryException $ex) {
			$error = $ex->getMessage();
			return back()->with('error', $error);
		}
		return back()->with('message', 'Data has been imported succesfully');
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

	public function destroy(Request $request, $id){
	
		//stop from accidentally deleted self
		if(auth()->user()->id == $id){
			return back()->with('error','You cannot delete yourself');
		}
		
		// Reduce hostel
		$payment = Payment::where(['id'=>$request->payment_id,'conference_edition_id'=>$request->edition])->first();
		$user = User::withTrashed()->where('id', $payment->user_id)->first();
		
		$this->reduceHostelAllocation($payment);
		$this->reduceFoodStandAllocation($payment);
		
		if($payment->uploaded_by){
			$moderator = Payment::where(['user_id'=>$payment->uploaded_by,'conference_edition_id'=>$request->edition])->first();
			$slot = ($moderator->slot_filled  > 0 ) ? $moderator->slot_filled - 1 : $moderator->slot_filled ;

			$moderator->update(['slot_filled'=>$slot]);
		}
		
		//Delete Avatar
		if ($user->trashed()) {
			if (isset($user->passport)) {
				unlink($user->passport);
			}
			$user->forceDelete();
			$payment->forceDelete();

			return back()->with('message', 'Participant has been deleted forever');
		} else {
			//Get user hostel
			$user->delete();
			$payment->delete();

			return back()->with('message', 'Record has been deleted!');
		}
	}

	public function destroyStaff(Request $request, $id)
	{

		//stop from accidentally deleted self
		if (auth()->user()->id == $id) {
			return back()->with('error', 'You cannot delete yourself');
		}

		$user = User::where('id',$id)->first();
		//Delete Avatar

		$this->deleteImage($user->passport);
		$user->forceDelete();

		return back()->with('message', 'Staff has been deleted forever');
		
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

	public function ajaxPayment(Request $request){
		$type = json_decode($request['metadata'], true);
		$transid = $this->generateTransactionId();

		$tempUser = app('App\Controllers\PaymentController')->createTempUser($request->all());
		$request['transid'] = $tempUser->transid;

		$res = [
			'tempUser' => $tempUser,
			'transid' => $transid
		];
		dd($res);
		return response()->json($res);
	}

}
