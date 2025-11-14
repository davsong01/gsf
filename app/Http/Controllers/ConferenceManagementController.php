<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\User;
use App\Models\Hostel;
use App\Models\Chapter;
use App\Models\Payment;
use App\Mail\WelcomeMail;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CriticalEmail;
use App\Models\ConferencePlan;
use App\Services\PaymentService;
use App\Models\ConferenceEdition;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ConferenceUsersImport;
use App\Services\HostelAllocationService;
use Illuminate\Support\Facades\Validator;
use App\Services\DynamicImageGeneratorService;
use App\Services\ServicePointAllocationService;
use App\Http\Controllers\CriticalEmailController;
use App\Models\TransactionAllocationField;

class ConferenceManagementController extends Controller
{
	public $edition;

	public function index(Request $request)
	{
		// Admin
		if (auth()->user()->role == 1) {
			if (auth()->user()->conference_role == 'superadmin') {
				$editions = ConferenceEdition::with('ministry')->get();
			} else {
				$editions = ConferenceEdition::with('ministry')->where('id', $this->edition->id)->get();
			}
			
			$count = 1;
			return view('conference_management.admin.editions.index', compact('editions', 'count'));
		} else {
			$edition = (object) activeConferenceEdition();
			
			if (auth()->user()->transactions->count() > 0) {
				if (auth()->user()->isParticipant($edition) || auth()->user()->isAlumni($edition) || auth()->user()->isModerator($edition)) {
					return view('conference_management.participant.index', compact('edition'));
				}
			} else {
				return back()->with('error', 'You have not registered for any conference');
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
		$moderator = Transaction::where(['user_id' => auth()->user()->id, 'level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();
		$moderators = Transaction::where(['level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->get();
		$payment = $moderator;
		// $moderator_participants = Transaction::where(['user_id' => auth()->user()->id, 'level' => 'participant', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete','uploaded_by'=>auth()->user()->id])->get();

		if (auth()->user()->role == 1) {
			$type = $request->type;
			return view('conference_management.admin.users.create', compact('edition', 'chapters', 'hostels', 'foods', 'moderators', 'moderator', 'type'));
		}

		if (!$moderator) {
			return abort(404);
		} else {
			if ($moderator->slot_filled == $moderator->slot) {
				return back()->with('error', 'You have reached the maximum number of slots you can add');
			}
			return view('conference_management.moderator.users.create', compact('chapters', 'edition', 'payment'));
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
		$user = Transaction::with(['user', 'allocationFields'])->findOrFail($id);

		$edition = ConferenceEdition::findOrFail($request->edition);

		// Build base query once
		$plansQuery = ConferencePlan::where('status', 1)
			->where('conference_edition_id', $edition->id);

		// Clone the query BEFORE applying where for current plan
		$currentPlan = (clone $plansQuery)
			->where('id', $user->conference_plan_id)
			->first();

		// Fetch all plans
		$plans = $plansQuery->get();

		$fields = $currentPlan?->fields()->sortBy('display_order');
		$registrationFields = reformatRegistrationFields($fields);

		$filledFields = $user->allocationFields
			->pluck('value', 'key')
			->toArray();
		
		$moderator = Transaction::where(['user_id' => auth()->user()->id, 'level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();

		$chapters = Chapter::all();
		$hostels = Hostel::where('conference_edition_id', $edition->id)->orderBy('name')->get();
		$foods = Food::where('conference_edition_id', $edition->id)->orderBy('name')->get();

		if (auth()->user()->role == 1) {
			return view('conference_management.admin.users.edit', compact('user', 'hostels', 'foods', 'chapters', 'edition', 'plans', 'registrationFields','filledFields'));
		}

		if (!$moderator) {
			return abort(404);
		} else {
			if ($user->user->gender == 'Female') {
				$hostels = Hostel::where(['conference_edition_id' => $edition->id, 'level' => 'Participant', 'type' => 'Female'])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			}

			if ($user->user->gender == 'Male') {
				$hostels = Hostel::where(['conference_edition_id' => $edition->id, 'level' => 'Participant', 'type' => 'Male'])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			}

			if ($edition->foodstand_field_assignment == 'yes' && in_array($moderator->user->chapter_id, [86])) {
				$foods = Food::where(['conference_edition_id' => $edition->id, 'level' => 'Participant', 'off_campus' => 'yes'])->where('capacity', '>', 'allocation')->orderBy('name')->get();

				Food::where(['level' => 'Participant', 'conference_edition_id' => $edition->id,])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			} else {
				$foods = Food::where(['conference_edition_id' => $edition->id, 'level' => 'Participant'])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			}

			return view('conference_management.moderator.users.edit', compact('user', 'hostels', 'foods', 'chapters', 'edition', 'payment'));
		}

		return abort(404);
	}

	public function staffEdit($id, Request $request)
	{
		$edition = ConferenceEdition::find($request->edition);
		$user = User::find($id);

		if (isset($edition) && $edition->status == 'active') {
			if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') {
				$staff = User::where('role', 1)->where('conference_role', 'admin')->get();
				return view('conference_management.admin.staff.edit', compact('edition', 'user'));
			}
		} else {
			return back()->with('error', 'Conference Edition not active');
		}
	}

	public function show(Transaction $conferencemanagement, Request $request)
	{
		$chapters = Chapter::all();
		$edition = ConferenceEdition::where('id', $request->edition)->first();
		$payment = $conferencemanagement;

		if (auth()->user()->isParticipant($edition) || auth()->user()->isAlumni($edition) || auth()->user()->isNec($edition) || auth()->user()->isChoir($edition)) {
			return view('conference_management.participant.single_payment', compact('edition', 'payment', 'chapters'));
		}

		if (auth()->user()->isModerator($edition)) {
			$allparticipants = Transaction::with(['hostel', 'moderator'])->where(['uploaded_by' => auth()->user()->id, 'conference_edition_id' => $payment->conference_edition_id])->orderBy('created_at', 'desc');
			$thispayment = Transaction::with(['hostel', 'moderator'])->where(['uploaded_by' => auth()->user()->id, 'user_id' => auth()->user()->id, 'conference_edition_id' => $payment->conference_edition_id])->first();
			
			$myParticipants = clone $allparticipants;
			$myParticipantsAll = $myParticipants->get();
			$participants = $allparticipants->count();

			$pending_registration = $myParticipantsAll->where('registration_status', 'Pending')->count();
			$completed_registration = $allparticipants->where('registration_status', 'Complete')->count();
			$allparticipants = $allparticipants->get();
			$count = 1;

			return view('conference_management.moderator.index', compact('chapters', 'pending_registration', 'completed_registration', 'participants', 'myParticipantsAll', 'count', 'payment', 'edition', 'thispayment'));
		}
	}

	public function store(Request $request, User $user)
	{
		dd('under construction');
		if (auth()->user()->role == 1) {
			$validator =  Validator::make($request->all(), [
				'name' => 'required|min:3',
				'email' => 'required',
				'phone' => 'required',
				'gender' => 'required',
				'chapter' => 'required|numeric',
				'passport' => 'nullable|max:200',
				'transid' => 'nullable',
				'hostel_id' => 'required|numeric',
				'food_id' => 'required',
				'amount_paid' => 'required',
				'level' => 'required',
			]);

			if ($validator->fails()) {
				return redirect()->back()->with('errors', $validator)
					->withInput($request->all());
			}

			$data = $validator->valid();
		} else {
			$data = $request->all();
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
			if (!isset($data['uploaded_by'])) {
				$data['uploaded_by'] = auth()->user()->id;
			} else {
				$data['uploaded_by'] = $data['uploaded_by'];
			}

			// Check registration fee
			$this->checkRegFee($request->all(), $setting);
			$data['type'] = $this->getType($request);
			// Assign hostel
			// (You may want to check if gender is the same as hostel gender)

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

			// Assign Foodstand and Hostel
			$data['field_id'] = $user->campus->id;

			$hostel_allocation = HostelAllocationService::assignHostel($data);
			$service_point = ServicePointAllocationService::assignFoodStand($data);

			$data['allocated_hostel_data'] = $hostel_allocation;
			$data['allocated_service_point_data'] = $service_point;
			$payment->update([
				'hostel_allocation_number' => $hostel_allocation['hostel_allocation_number'],
				'hostel_allocation_type' => $hostel_allocation['hostel_allocation_type'],
				'service_point_allocation_number' => $service_point['service_point_allocation_number'],
				'service_point_allocation_type' => $service_point['service_point_allocation_type'],
				'hostel_id' => $hostel_allocation['hostel_id'],
				'food_id' => $service_point['service_point_allocation_id']
			]);

			// Log Email
			$data['type'] = 'welcome_mail';
			$data['amount'] = $data['amount_paid'];
			$data['family_id'] = $family_id;
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
			return redirect(route('conference.participants', ['type' => $payment->level, 'edition' => $setting]))->with('message', 'Participant successfully created');
		}

		$edition = ConferenceEdition::find($request->edition);
		$moderator = Transaction::where(['user_id' => auth()->user()->id, 'level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();

		if ($moderator) {
			$data2 = $this->validate($request, [
				'name' => 'required|min:3',
				'email' => 'required|unique:users,email',
				'phone' => 'required',
				'gender' => 'required',
				'payment_type' => 'nullable',
				'transid' => 'nullable',
			]);
			$data['type'] = 1;
			$data = array_merge($data, $data2);

			//Check if moderator has slots available
			if ($moderator->slot_filled >= $moderator->slot) {
				return back()->with('warning', 'You can no longer add participants because you have used up all available slots');
			}

			// try {
			$newuser = User::create([
				'name' => $data['name'],
				'email' => $data['email'],
				'phone' => $data['phone'],
				'gender' => $data['gender'],
				'slug' => Str::slug($data['name']),
				'chapter_id' => auth()->user()->chapter_id,
				'passport' => $data['passport'],
				'password' => $data['password']
			]);
			$data['level'] = 'Participant';

			$payment = Transaction::Create([
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

			$chapter = Chapter::with('field:id,name')->select('id', 'field_id')->where('id', $newuser->chapter_id)->first();
			$data['field_id'] = !empty($chapter->field) ? $chapter->field->id : (!empty($data['field_id']) ? $data['field_id'] : null);

			if (in_array($data['level'], ['Participant', 'Alumni', 'Nec'])) {
				$hostel_allocation = HostelAllocationService::assignHostel($data);
				$service_point = ServicePointAllocationService::assignFoodStand($data);

				$data['allocated_hostel_data'] = $hostel_allocation;
				$data['allocated_service_point_data'] = $service_point;

				$payment->update([
					'hostel_allocation_number' => $hostel_allocation['hostel_allocation_number'],
					'hostel_allocation_type' => $hostel_allocation['hostel_allocation_type'],
					'service_point_allocation_number' => $service_point['service_point_allocation_number'],
					'service_point_allocation_type' => $service_point['service_point_allocation_type'],
					'hostel_id' => $hostel_allocation['hostel_id'],
					'food_id' => $service_point['service_point_allocation_id']
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

			return redirect(route('conferencemanagement.show', ['type' => $moderator->level, 'conferencemanagement' => $payment->id, 'edition' => $payment->conference_edition_id]))->with('message', 'Participant successfully created, you have ' . ($moderator->slot - $moderator->slot_filled) . ' participant slot(s) left');
		}

		return abort(404);
	}

	public function staffStore(Request $request)
	{

		$data = $this->validate($request, [
			"name" => "required",
			"email" => "required",
			"phone" => "required",
			"sex" => "required",
			"conference_role" => "required",
			"password" => "nullable"
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
			"sex" => $data['gender'],
			"passport" => $data['passport'],
			"role" => 1,
			'slug' => Str::slug($data['name']),
			"conference_role" => $data['conference_role'],
			"password" => $password
		]);

		$user->update([
			'family_id' => PaymentService::generateStaffFamilyId($this->edition, $user),
		]);

		return redirect(route('conference.staff', ['edition' => $this->edition]))->with('message', 'Staff successfully created');
	}

	public function checkRegFee($request, $setting)
	{
		$error = 0;
		if ($request['level'] == 'Participant' and ($request['amount_paid'] < $setting->registration_fee)) {
			$error = 1;
		}

		if ($request['level'] == 'Alumni' and ($request['amount_paid'] != $setting->new_alumni_registration_fee || $request['amount_paid'] != $setting->alumni_registration_fee)) {
			$error = 1;
		}

		if ($request['level'] == 'Nec' and ($request['amount_paid'] < $setting->alumni_registration_fee)) {
			$error = 1;
		}

		if ($error == 1) {
			return back()->with('error', 'Amount is lower than registration fee for ' . $request['level']);
		}
	}

	public function update(Request $request, $id)
	{
		$payment = Transaction::with(['user', 'paymentprovider', 'edition', 'allocationFields'])->whereId($id)->first();
		$user = $payment->user;
		
		$data = $this->validate($request, [
			'name' => 'required',
			'phone' => 'required',
			'gender' => 'in:Male,Female',
			'passport' => 'nullable|max:200',
			'level' => 'required',
			'email' => 'sometimes'
		]);
		
		if ($request->has('passport')) {
			$update['passport'] = $this->uploadImage($data['passport'], 'images/passports', 400, 400);
		}
		
		$update['phone'] = $request['phone'] ?? $user->phone;
		$update['name'] = $request['name'] ?? $user->name;
		$update['gender'] = $request['gender'] ?? $user->gender;

		// handle gender change
		if ($request->has('gender') && $request['gender'] != $user->gender) {
			$hostel_allocation = HostelAllocationService::assignHostel($payment, $request->all());
			
			$data['allocated_hostel_data'] = $hostel_allocation;
			$paymentupdate['hostel_allocation_number'] = $hostel_allocation['hostel_allocation_number'];
			$paymentupdate['hostel_allocation_type'] = $hostel_allocation['hostel_allocation_type'];
			$paymentupdate['hostel_id'] = $hostel_allocation['hostel_id'];
			
			// If no hostel
			if (!isset($paymentupdate['hostel_id']) && empty($paymentupdate['hostel_id'])) {
				return back()->with('error', "There is no {$request->gender} hostel available at the moment. Changes not saved!");
			} else {
				HostelAllocationService::reduceHostelAllocation($payment);
			}
		}
		
		//handle password
		if ($request->has('password') && !empty($request->password)) {
			$update['password'] = Hash::make($request['password']);
		}

		// Moderator
		$moderator = Transaction::with(['user', 'paymentprovider', 'edition', 'allocationFields'])->where(['user_id' => auth()->user()->id, 'level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();

		if (isset($moderator) && !empty($moderator)) {
			// Hostel may not be set above, so set it
			if (!isset($update['hostel_id'])) {
				$hostel_allocation = HostelAllocationService::assignHostel($moderator, $request->all());
				
				$data['allocated_hostel_data'] = $hostel_allocation;
				$paymentupdate['hostel_allocation_number'] = $hostel_allocation['hostel_allocation_number'];
				$paymentupdate['hostel_allocation_type'] = $hostel_allocation['hostel_allocation_type'];
				$paymentupdate['hostel_id'] = $hostel_allocation['hostel_id'];

				// dd($paymentupdate['hostel_id'],$payment->level, $request['gender']);
				if (!isset($paymentupdate['hostel_id']) && empty($paymentupdate['hostel_id'])) {
					return back()->with('error', 'Sorry, there is no available hostel for you at the moment. Changes not saved!');
				} else {
					HostelAllocationService::reduceHostelAllocation($moderator);
				}
			}

			if (!isset($payment->food) && empty($payment->food)) {
				$service_point = ServicePointAllocationService::assignFoodStand($moderator);

				$data['allocated_service_point_data'] = $service_point;

				$paymentupdate['hostel_allocation_type'] = $hostel_allocation['hostel_allocation_type'];
				$paymentupdate['service_point_allocation_number'] = $service_point['service_point_allocation_number'];
				$paymentupdate['service_point_allocation_type'] = $service_point['service_point_allocation_type'];
				$paymentupdate['food_id'] = $service_point['service_point_allocation_id'];
			} else {
				$paymentupdate['food_id'] = $payment->food_id;
			}
		}

		$user->update($update);

		if(isset($paymentupdate)){
			$payment->update($paymentupdate);
		}

		return back()->with('message', 'Operation succesful');
	}

	public function adminUpdate(Request $request, $id){
		$transaction = Transaction::with('user')->whereId($id)->first();
		$user = $transaction->user;

		if (auth()->user()->role != 1 && auth()->user()->conference_role != 'superadmin') {
			return back()->with('message', 'You are not authorized to access this resource');
		}
		
		$setting = ConferenceEdition::where('status', 'active')->where('id', $transaction->conference_edition_id)->first();
		$data = $request->all();

		DB::beginTransaction();

		try {
			if ($request->has('passport')) {
				$update['passport'] = $this->uploadImage($data['passport'], 'images/passports', 400, 400);
			}

			$update['phone'] = $paymentupdate['phone'] = $data['registration_fields']['phone'] ?? $user->phone;
			$update['name'] = $paymentupdate['name'] = $data['registration_fields']['name'] ?? $user->name;
			$update['gender'] = $paymentupdate['gender'] = $data['registration_fields']['gender'] ?? $user->gender;
			$update['email'] = $paymentupdate['email'] = $data['registration_fields']['email'] ?? $user->email;

			// handle gender change, automatic hostel
			if ($update['gender'] != $user->gender) {
				$paymentArray = array_merge($data, $transaction->ToArray());
				$paymentArray['gender'] = $update['gender'];

				$paymentArray['field_id'] = $transaction?->user?->campus?->id;
				$paymentArray['setting'] = $setting;
				if ($request->has('hostel_id') && $request['hostel_id'] != $transaction->hostel_id) {
					$paymentArray['new_hostel_id'] = $request['hostel_id'] ?? null;
				}
				
				$hostel_allocation = HostelAllocationService::assignHostel($transaction, $paymentArray);

				$data['allocated_hostel_data'] = $hostel_allocation;

				$paymentupdate['hostel_allocation_number'] = $hostel_allocation['hostel_allocation_number'];
				$paymentupdate['hostel_allocation_type'] = $hostel_allocation['hostel_allocation_type'];
				$paymentupdate['hostel_id'] = $hostel_allocation['hostel_id'];

				if (!isset($paymentupdate['hostel_id']) || empty($paymentupdate['hostel_id'])) {
					DB::rollBack();
					return back()->with('error', 'Sorry, there is no available hostel for you at the moment. Changes not saved!');
				}

				HostelAllocationService::reduceHostelAllocation($transaction);
			} elseif ($request->has('hostel_id') && $request['hostel_id'] != $transaction->hostel_id) {
				$paymentArray = array_merge($data, $transaction->ToArray());
				$paymentArray['gender'] = $update['gender'] ?? $paymentArray['gender'];

				$paymentArray['field_id'] = $user?->campus?->id;
				$paymentArray['setting'] = $setting;
				$paymentArray['new_hostel_id'] = $request['hostel_id'];

				$hostel_allocation = HostelAllocationService::assignHostel($transaction, $paymentArray);

				$data['allocated_hostel_data'] = $hostel_allocation;

				$paymentupdate['hostel_allocation_number'] = $hostel_allocation['hostel_allocation_number'];
				$paymentupdate['hostel_allocation_type'] = $hostel_allocation['hostel_allocation_type'];
				$paymentupdate['hostel_id'] = $hostel_allocation['hostel_id'];

				if (!isset($paymentupdate['hostel_id']) || empty($paymentupdate['hostel_id'])) {
					DB::rollBack();
					return back()->with('error', $hostel_allocation['message'] ?? 'Sorry, there is no available hostel at the moment. Changes not saved!');
				}

				HostelAllocationService::reduceHostelAllocation($transaction);
			}


			// handle password
			if ($request->has('password') && !empty($request->password)) {
				$update['password'] = Hash::make($request['password']);
			}
			
			// handle food change
			if ($request->has('food_id') && $request['food_id'] != $transaction->food_id) {
				$paymentArray = array_merge($data, $transaction->ToArray());
				$paymentArray['field_id'] = $transaction?->user?->campus?->id;

				$paymentArray['new_food_id'] = $request['food_id'];
				$service_point = ServicePointAllocationService::assignFoodStand($paymentArray);
	
				$data['allocated_service_point_data'] = $service_point;
				$paymentupdate['service_point_allocation_number'] = $service_point['service_point_allocation_number'];
				$paymentupdate['service_point_allocation_type'] = $service_point['service_point_allocation_type'];
				$paymentupdate['food_id'] = $service_point['service_point_allocation_id'];
			} else {
				$paymentupdate['food_id'] = $transaction->food_id;
			}
			
			$user->update($update);
			
			if (!empty($paymentupdate)) {
				$transaction->update($paymentupdate);
			}

			// Update registration fields
			if(!empty($data['registration_fields'])){
				foreach ($data['registration_fields'] as $key => $value) {
					$transaction->allocationFields()
						->where('key', $key)
						->update(['value' => $value]);
				}

			}
			// END UPDATE registrationn fields
			// $user = $user->fresh();
			// $transaction = $transaction->fresh();
			
			DB::commit();

			return back()->with('message', 'Operation successful');
		} catch (\Throwable $th) {
			DB::rollBack();
			dd('hold',  $th->getMessage() . ', ' . $th->getFile() . ', ' . $th->getLine());
			return back()->with('error', $th->getMessage(). ', '.$th->getFile(). ', '.$th->getLine());
		}
	}


	public function staffUpdate(Request $request, $id)
	{

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

				$user->update($request->except(['edition', 'avatar']));
				return redirect(route('conference.staff', ['edition' => $this->edition]))->with('message', 'Staff successfully updated');
			}
		} else {
			return back()->with('error', 'Conference Edition not active');
		}
	}

	public function participants($type = '', $edition = '')
	{
		$count = 1;
		
		if (auth()->user()->role == 1) {
			$participants = Transaction::with('user')->where('conference_edition_id', $edition)->wherehas('user')->where('level', $type)->latest()->take(10)->get();
			$edition = ConferenceEdition::find($edition);
			
			return view('conference_management.admin.users.index', compact('participants', 'count', 'edition', 'type'));
		}
	}

	public function staffIndex($edition = '')
	{
		$count = 1;
		$edition = ConferenceEdition::find($edition);
		if (isset($edition) && $edition->status == 'active') {
			if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin') {
				$staff = User::where('role', 1)->get();

				return view('conference_management.admin.staff.index', compact('staff', 'count', 'edition'));
			}
		} else {
			return back()->with('error', 'Conference Edition not active');
		}
	}

	public function getCard(Request $request, $id)
	{
		// return back()->with('error', 'This feature is not available yet');
		$payment = Transaction::where('id', $id)->with('user', 'hostel')->first();

		if (!auth()->user()->completeReg($payment->edition) && auth()->user()->role <> 1) {
			return back()->with('error', 'You must complete registration before viewing this resource');
		}

		if (auth()->user()->isModerator($payment->edition)) {
			if ($payment->uploaded_by != auth()->user()->id) {
				return abort(404);
			}
		}

		// if (empty($payment->badge_location)) {
			$imageController = new DynamicImageGeneratorController();
			$imageController->generateImage($request->all(), $payment);
		// }

		return view('card.id')->with('payment', $payment)
			->with('edition', $payment->edition)
			->with('user', $payment->user);
	}

	public function resendEmail(Request $request, $id)
	{
		$payment = Transaction::find($id);
		$user = User::where('id', $payment->user_id)->first();

		$criticalEmail = CriticalEmail::where('recipient', $user->email)->where('type', 'welcome_mail')->where('status', 1)->first();

		if ($criticalEmail) {
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
		} else {
			return back()->with('error', 'No sent Email logged for user!');
		}
	}

	public function usersImportIndex(Request $request)
	{
		$edition = ConferenceEdition::find($request->edition) ?? activeConferenceEdition();

		if (auth()->user()->role == 1) {
			$type = $request->type;
			$chapters = Chapter::all();
			
			return view('conference_management.admin.users.import', compact('chapters', 'edition', 'type'));
		}

		if (auth()->user()->isModerator($edition)) {
			$payment = Transaction::where(['user_id' => auth()->user()->id, 'conference_edition_id' => $edition->id, 'registration_status' => 'Complete'])->first();

			if ($payment->slot_filled >= $payment->slot) {
				return back()->with('error', 'You have already exhausted your registration slots');
			}
			$type = 'Participant';
			return view('conference_management.moderator.users.import', compact('edition', 'type', 'payment'));
		}
	}

	public function getAdminParticipantSample(Request $request, $type)
	{

		$path = public_path() . '/frontend/exportsamples/import' . Str::lower($type) . '.xlsx';

		if (file_exists($path)) {
			return response()->download($path);
		} else {
			return back()->with('error', 'File doesnt exist');
		}
	}


	public function import(Request $request)
	{
		$edition = ConferenceEdition::find($request->edition) ?? activeConferenceEdition();
		
		if (auth()->user()->role == 1 || auth()->user()->isModerator($this->edition)) {
			$data = $this->validate($request, [
				'file' => 'required|mimes:xlsx,csv',
				'import_level' => 'required|in:Participant,Moderator,Alumni,Nec,Choir',
			]);

			$data['chapter_id'] = auth()->user()->isAdmin() ? $request->chapter_id : auth()->user()->chapter_id;
			$data['edition'] = $edition;
			
			if(auth()->user()->isAdmin()){
				if(empty($data['chapter_id'])){
					return back()->with('error', 'Chapter Id is empty');
				}
			}
			$redirectRoute = auth()->user()->isAdmin() ? route('conferenceusers.import.index', ['type' => $request->import_level, 'edition' => $request->edition]) : route('conferenceusers.import.index');

			if (!auth()->user()->isAdmin()) {
				$payment = Transaction::where([
					'user_id' => auth()->user()->id,
					'conference_edition_id' => $request->edition,
					'registration_status' => 'Complete'
				])->first();

				$sRedirectRoute = route('conferencemanagement.show', [
					'conferencemanagement' => $payment->id,
					'edition' => $request->edition
				]);
				if ($payment && $payment->slot_filled >= $payment->slot) {
					return back()->with('error', 'You have already exhausted your registration slots');
				}
			} else {
				$sRedirectRoute = route('conferenceusers.import.index', ['type' => $request->import_level, 'edition' => $request->edition]);
			}
		} else return abort(404);

		try {
			$import = new ConferenceUsersImport($data, $payment ?? null);
			Excel::import($import, $request->file('file'));

			$failures = $import->failures();

			if ($failures->isNotEmpty()) {
				$failureDetails = $failures->map(function ($failure) {
					return [
						'row' => $failure->row(),
						'data' => $failure->values(),
						'errors' => $failure->errors(),
					];
				});

				return redirect($redirectRoute)->with([
					'failures' => $failureDetails,
					'error' => 'Some rows failed to import.',
				]);
			} else {

				return redirect($sRedirectRoute)->with([
					'message' => 'Upload Successful',
				]);
			}
		} catch (\Exception $e) {
			// dd($e->getMessage(), $e->getLine(), $e->getFile());
			return redirect($redirectRoute)->with([
				'error' => 'Something went wrong, please try again: ' . $e->getMessage(),
			]);
		}
	}

	public function adminImport(Request $request)
	{
		$edition = ConferenceEdition::find($request->edition) ?? activeConferenceEdition();

		$data = $this->validate($request, [
			'file' => 'required|mimes:xlsx,csv',
			'chapter_id' => 'nullable',
			'import_level' => 'required|in:Participant,Moderator,Alumni,Nec,Choir',
		]);
		
		$data['setting'] = $edition;
		// $redirectRoute = auth()->user()->isAdmin() ? route('conferenceusers.import.index', ['type' => $request->import_level, 'edition' => $request->edition]) : route('conferenceusers.import.index');
		// $sRedirectRoute = route('conferenceusers.import.index', ['type' => $request->import_level, 'edition' => $request->edition]);

		// try {
			$import = new ConferenceUsersImport($data);
			Excel::import($import, $request->file('file'));

			$failures = $import->failures();

			if ($failures->isNotEmpty()) {
				$failureDetails = $failures->map(function ($failure) {
					return [
						'row' => $failure->row(),
						'data' => $failure->values(),
						'errors' => $failure->errors(),
					];
				});

				return redirect(route('conferenceusers.import.index', ['type' => $request->import_level, 'edition' => $request->edition]) )->with([
					'failures' => $failureDetails,
					'error' => 'Some rows failed to import.',
				]);
			} else {

				return redirect(route('conferenceusers.import.index', ['type' => $request->import_level, 'edition' => $request->edition]))->with([
					'message' => 'Upload Successful',
				]);
			}
		// } catch (\Exception $e) {
		// 	dd($e->getMessage());
		// 	return redirect(route('conferenceusers.import.index', ['type' => $request->import_level, 'edition' => $request->edition]) )->with([
		// 		'error' => 'Something went wrong, please try again: ' . $e->getMessage(),
		// 	]);
		// }
	}


	public function trashed(Request $request)
	{
		$edition = ConferenceEdition::find($request->edition);
		$count = 1;

		if (auth()->user()->role == 1) {
			$participants = User::join('transactions', 'transactions.user_id', '=', 'users.id')
				->where('transactions.conference_edition_id', $edition->id)
				->select('users.*', 'transactions.level', 'transactions.amount_paid', 'transactions.transid')
				->orderBy('users.created_at', 'desc')->onlyTrashed()->get();
			// dd($participants);
			return view('conference_management.admin.users.trashed', compact('participants', 'count', 'edition'));
		}
	}

	public function destroy(Request $request, $id)
	{

		//stop from accidentally deleted self
		if (auth()->user()->id == $id) {
			return back()->with('error', 'You cannot delete yourself');
		}

		// Reduce hostel
		$payment = Transaction::where(['id' => $request->payment_id, 'conference_edition_id' => $request->edition])->first();
		$user = User::withTrashed()->where('id', $payment->user_id)->first();

		HostelAllocationService::reduceHostelAllocation($payment);
		ServicePointAllocationService::reduceFoodStandAllocation($payment);

		if ($payment->uploaded_by) {
			$moderator = Transaction::where(['user_id' => $payment->uploaded_by, 'conference_edition_id' => $request->edition, 'level' => 'Moderator'])->first();
			if ($moderator) {
				$slot = ($moderator->slot_filled  > 0) ? $moderator->slot_filled - 1 : $moderator->slot_filled;
				$moderator->update(['slot_filled' => $slot]);
			}
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
			$payment->transid = $payment->transid . '..Flagged-' . time();

			$payment->save();
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

		$user = User::where('id', $id)->first();
		//Delete Avatar

		$this->deleteImage($user->passport);
		$user->forceDelete();

		return back()->with('message', 'Staff has been deleted forever');
	}


	public function restore($id)
	{
		if (auth()->user()->level == 'Admin') {
			$user = User::withTrashed()->where('id', $id)->firstOrFail();
			$user->restore();

			return redirect(route('users.index'))->with('message', 'Participant has been restored');
		} else return abort(404);
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

	public function ajaxPayment(Request $request)
	{
		$type = json_decode($request['metadata'], true);
		$transid = $this->generateTransactionId();

		$tempUser = app('App\Controllers\PaymentController')->initializeTransaction($request->all());
		$request['transid'] = $tempUser->transid;

		$res = [
			'tempUser' => $tempUser,
			'transid' => $transid
		];
		dd($res);
		return response()->json($res);
	}
}
