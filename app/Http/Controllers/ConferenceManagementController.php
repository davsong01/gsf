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
			$edition = $this->edition ?? ConferenceEdition::where('status','active')->orderBy('created_at','DESC')->first();
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
		if (auth()->user()->isParticipant($edition) || auth()->user()->isAlumni($edition) || auth()->user()->isNec($edition) || auth()->user()->isChoir($edition)) {
			return view('conference_management.participant.single_payment', compact('edition', 'payment','chapters'));
		}
		if (auth()->user()->isModerator($edition)) {
			$allparticipants = Payment::with(['hostel', 'moderator'])->where(['uploaded_by'=>auth()->user()->id, 'conference_edition_id'=> $payment->conference_edition_id])->orderBy('created_at', 'desc');
			// dd($allparticipants->get(), $payment->conference_edition_id);
			$myParticipants = clone $allparticipants;
			$myParticipantsAll = $myParticipants->get();
			$participants = $allparticipants->count();
			
			$pending_registration = $myParticipantsAll->where('registration_status', 'Pending')->count();
			$completed_registration = $allparticipants->where('registration_status', 'Complete')->count();
			$allparticipants = $allparticipants->get();
			$count = 1;
			return view('conference_management.moderator.index', compact('chapters', 'pending_registration', 'completed_registration', 'participants', 'myParticipantsAll', 'count','payment','edition'));
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

		$moderator = Payment::where(['user_id'=>auth()->user()->id,'level'=>'Moderator','conference_edition_id'=>$request->edition,'registration_status'=>'Complete'])->first();
		
		if(!$moderator){
			return abort(404);
		}else{
			if ($user->user->sex == 'Female') {
				$hostels = Hostel::where(['conference_edition_id'=>$edition->id,'level'=>'Participant','type'=>'Female'])->where('capacity','>','allocation')->orderBy('name')->get();
			}
			
			if ($user->user->sex == 'Male') {
				$hostels = Hostel::where(['conference_edition_id'=>$edition->id,'level'=>'Participant','type'=>'Male'])->where('capacity','>','allocation')->orderBy('name')->get();
			}

			if($edition->foodstand_field_assignment == 'yes' && in_array($moderator->user->chapter_id, [86])){
				$foods = Food::where(['conference_edition_id'=>$edition->id,'level'=>'Participant','off_campus'=>'yes'])->where('capacity','>','allocation')->orderBy('name')->get();
			}else{
				$foods = Food::where(['conference_edition_id'=>$edition->id,'level'=>'Participant','off_campus'=>'no'])->where('capacity','>','allocation')->orderBy('name')->get();
			}

			return view('conference_management.moderator.users.edit', compact('user', 'hostels', 'foods', 'chapters','edition'));
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
