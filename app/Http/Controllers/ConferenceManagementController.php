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
use App\Services\EmailService;
use App\Services\ExcelService;
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
use App\Models\TransactionAllocationField;
use App\Services\DynamicImageGeneratorService;
use App\Services\ServicePointAllocationService;
use App\Http\Controllers\CriticalEmailController;
use App\Services\UserService;

class ConferenceManagementController extends Controller
{
	public $edition;

	public function index(Request $request)
	{
		// Admin
        $loginStatus = $request->login_status ?? null;
        $user = auth()->user();

		if ($user->role == 1) {
			if ($user->conference_role == 'superadmin') {
				$editions = ConferenceEdition::with('ministry')->latest()->get();
			} else {
				$editions = ConferenceEdition::with('ministry')->where('id', $this->edition->id)->get();
			}

			return view('conference_management.admin.editions.index', compact('editions'));
		} else {

			$edition = (object) activeConferenceEdition();

			if ($user->transactions->count() > 0) {
                if (getRegistrationUserType(['participant'], $edition)){
                        $view = 'conference_management.participant.index';
                }elseif(getRegistrationUserType(['moderator'], $edition)){
                    $view = 'conference_management.participant.index';
                }

                if($loginStatus){
                    $user->update(['last_login' => now()]);
                }

                return view($view, compact('edition'));

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

		$moderator = Transaction::where(['user_id' => auth()->user()->id, 'registration_user_type' => 'moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();
		$moderators = Transaction::where(['level' => 'Moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->get();
		$payment = $moderator;

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
        $transaction = Transaction::with(['user', 'allocationFields'])->findOrFail($id);
		$edition = ConferenceEdition::findOrFail($request->edition);
        $moderator = Transaction::where(['user_id' => auth()->user()->id, 'registration_user_type' => 'moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();

        $chapters = Chapter::all();
		$hostels = Hostel::where('conference_edition_id', $edition->id)->orderBy('name')->get();
		$foods = Food::where('conference_edition_id', $edition->id)->orderBy('name')->get();

		// Build base query once
		$plansQuery = ConferencePlan::where('status', 1)
			->where('conference_edition_id', $edition->id);

		// Clone the query BEFORE applying where for current plan
		$currentPlan = (clone $plansQuery)
			->where('id', $transaction->conference_plan_id)
			->first();

		// Fetch all plans
		$plans = $plansQuery->get();

		$fields = $moderator ? $currentPlan?->fields()->where('name', '!=', 'no_of_participants')->sortBy('display_order') : $currentPlan?->fields()->sortBy('display_order');

		$registrationFields = reformatRegistrationFields($fields);

		$filledFields = $transaction->allocationFields
			->pluck('value', 'key')
			->toArray();

		if (auth()->user()->role == 1) {
			return view('conference_management.admin.users.edit', compact('transaction', 'hostels', 'foods', 'chapters', 'edition', 'plans', 'registrationFields','filledFields'));
		}

		if (!$moderator) {
			return abort(404);
		} else {
			if ($transaction->user->gender == 'Female') {
				$hostels = Hostel::where(['conference_edition_id' => $edition->id, 'level' => 'Participant', 'type' => 'Female'])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			}

			if ($transaction->user->gender == 'Male') {
				$hostels = Hostel::where(['conference_edition_id' => $edition->id, 'level' => 'Participant', 'type' => 'Male'])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			}

			if ($edition->foodstand_field_assignment == 'yes' && in_array($moderator->user->chapter_id, [86])) {
				$foods = Food::where(['conference_edition_id' => $edition->id, 'level' => 'Participant', 'off_campus' => 'yes'])->where('capacity', '>', 'allocation')->orderBy('name')->get();

				Food::where(['level' => 'Participant', 'conference_edition_id' => $edition->id,])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			} else {
				$foods = Food::where(['conference_edition_id' => $edition->id, 'level' => 'Participant'])->where('capacity', '>', 'allocation')->orderBy('name')->get();
			}

			return view('conference_management.moderator.users.edit', compact('transaction', 'hostels', 'foods', 'chapters', 'edition', 'registrationFields', 'filledFields'));
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
        $user = auth()->user();
        $payment = $thispayment = $conferencemanagement;

        if (getRegistrationUserType(['Participant','Alumni', 'Nec','Choir'], $edition)){
			return view('conference_management.participant.single_payment', compact('edition', 'payment', 'chapters'));
		}
        if (getRegistrationUserType(['moderator'], $edition)) {
            $userId = $user->id;

            $baseQuery = Transaction::with(['hostel', 'moderator'])
                ->where('conference_edition_id', $edition->id)
                ->orderBy('created_at', 'desc');

            // Current user's payment
            $thispayment = $conferencemanagement;

            $allParticipantsCollection = $baseQuery->where('uploaded_by', $userId)->orWhere('id', $payment->id)->get();

            // Counts from collection (no extra DB queries)
            $participants = $allParticipantsCollection->count();

            $pending_registration = $allParticipantsCollection
                ->where('registration_status', 'Pending')
                ->count();

            $completed_registration = $allParticipantsCollection
                ->where('registration_status', 'Complete')
                ->count();

            return view('conference_management.moderator.index', [
                'chapters' => $chapters,
                'pending_registration' => $pending_registration,
                'completed_registration' => $completed_registration,
                'participants' => $participants,
                'myParticipantsAll' => $allParticipantsCollection,
                'edition' => $edition,
                'thispayment' => $thispayment,
            ]);
        }
	}

	public function store(Request $request, $source = null)
	{
        $user = auth()->user();
        $isImport = $source == 'import';

		if ($user->role == 1 && !$isImport) {
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

			return redirect()->back()->with('errors', $validator)->withInput($request->all());

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

		//Handle Passport Upload
		if ($request->has('passport')) {
			$data['passport'] = $this->uploadImage($request->passport, 'images/passports', 400, 400);
		} else {
			$data['passport'] = NULL;
		}

		$setting = ConferenceEdition::find($request->edition);
		$data['conference_edition_id'] = $setting->id;

        if (!isset($data['uploaded_by'])) {
            $data['uploaded_by'] = $user->id;
        } else {
            $data['uploaded_by'] = $data['uploaded_by'];
        }

        $isAdmin = $user->role == 1 ? true : false;
		$plan = ConferencePlan::where('status', 1)->where('conference_edition_id', $setting->id)->where('level', 'Participant')->first();

		if(!$plan){
            if($isImport){
                return [
                    'status' => false,
                    'error' => 'No Participant plan found, please contact support',
                ];
            }

			return back()->with('error', 'No Participant plan found, please contact support');
		}

		$moderator = Transaction::where(['user_id' => auth()->user()->id, 'registration_user_type' => 'moderator', 'conference_edition_id' => $request->edition, 'registration_status' => 'Complete'])->first();

        $allocationFields = $isAdmin ? $plan->fields() : $moderator->allocationFields;

        $fields = !empty($allocationFields)
            ? $allocationFields->whereNotIn('key',['name','email','phone', 'participants','no_of_participants'])->pluck('value', 'key')->toArray()
            : [];

        // merge correctly
        $data = array_merge($fields, $data);

        if(!$isAdmin){
            if ($moderator->slot_filled >= $moderator->slot) {
                if($isImport){
                    return [
                        'status' => false,
                        'error' => 'You can no longer add participants because you have used up all available slots',
                    ];
                }

                return back()->with('warning', 'You can no longer add participants because you have used up all available slots');
            }
        }

        DB::beginTransaction();

        try {
            $data['plan']    = $plan;
            $data['amount']  = $plan->price;
            $data['total_amount']  = $plan->price;
            $data['setting'] = $setting;
            $data['setting'] = $setting;
            $data['provider_charge'] = 0;
            $data['transaction_source'] = $isAdmin ? 'admin' : 'moderator';
            $data['payment_provider_id'] = $moderator->payment_provider_id ?? null;

            $transaction = PaymentService::initializeTransaction($data);

            if (!$transaction['status']) {
                DB::rollBack();

                if($isImport){
                    return [
                        'status' => false,
                        'message' => $transaction['message'] ?? '',
                    ];
                }

                return back()->with('error', $transaction['message'] ?? '');
            }

            $transaction = $transaction['data'];
            $newUser = PaymentService::createUser($transaction);

            $data['allocated_hostel_data'] = HostelAllocationService::assignHostel($transaction);
            $data['allocated_service_point_data'] = ServicePointAllocationService::assignFoodStand($transaction);

            $transaction->update([
                'hostel_allocation_number' => $data['allocated_hostel_data']['hostel_allocation_number'] ?? null,
                'hostel_allocation_type'   => $data['allocated_hostel_data']['hostel_allocation_type'] ?? null,
                'service_point_allocation_number' => $data['allocated_service_point_data']['service_point_allocation_number'] ?? null,
                'service_point_allocation_type'   => $data['allocated_service_point_data']['service_point_allocation_type'] ?? null,
                'hostel_id' => $data['allocated_hostel_data']['hostel_id'] ?? null,
                'food_id'   => $data['allocated_service_point_data']['service_point_allocation_id'] ?? null,
            ]);

            // Generate Family ID
            $familyId = PaymentService::generateFamilyId($newUser, $setting);

            $newUser->update([
                'passport' => $data['passport'] ?? "frontend/passports/avatar.jpg",
                'family_id' => $familyId
            ]);

            // Final Transaction Update
            $transaction->update([
                'status'              => 'Complete',
                'registration_status' => 'Complete',
                'user_id'             => $newUser->id ?? null,
                // 'uploaded_by' => $moderator->uploaded_by ?? null
            ]);

            // Update moderator slot
            if(!$isAdmin){
                $moderator->update([
                    'slot_filled' => $moderator->slot_filled + 1,
                ]);
            }


            // Send Emails
            $transaction->user = $newUser;
            EmailService::sendRegistrationEmails($transaction);

            DB::commit();

            if($isImport){
                return [
                    'status' => true,
                    'message' => 'Participant successfully uploaded',
                ];
            }

            return redirect(route('conferencemanagement.show', ['conferencemanagement' => $moderator->id, 'edition' => $edition->id ?? $setting->id]))->with('message', 'Participant successfully created, you have ' . ($moderator->slot - $moderator->slot_filled) . ' participant slot(s) left');
        } catch (\Throwable $e) {
            DB::rollBack();

            if($isImport){
                return [
                    'status' => false,
                    'message' => 'An error occured',
                ];
            }

            throw $e; // optional: or return error message
        }

        if($isImport){
            return [
                'status' => false,
                'message' => 'Something went wrong',
            ];
        }
		return abort(404);
	}

	public function staffStore(Request $request)
	{

		$data = $this->validate($request, [
			"name" => "required",
			"email" => "required",
			"phone" => "required",
			"gender" => "required",
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
			"gender" => $data['gender'],
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
		$transaction = Transaction::with('user')->whereId($id)->first();
		$user = $transaction->user;
        $isAdmin = false;

		$setting = ConferenceEdition::where('status', 'active')->where('id', $transaction->conference_edition_id)->first();
		$data = $request->all();

        if (auth()->user()->role == 1 || auth()->user()->conference_role == 'superadmin') {
			$isAdmin = true;
		}

		DB::beginTransaction();

		try {
			if ($request->has('passport')) {
				$update['passport'] = $this->uploadImage($data['passport'], 'images/passports', 400, 400);
			}

			$update['phone'] = $paymentupdate['phone'] = $data['registration_fields']['phone'] ?? $user->phone;
			$update['name'] = $paymentupdate['name'] = $data['registration_fields']['name'] ?? $user->name;
			$update['gender'] = $paymentupdate['gender'] = $data['registration_fields']['gender'] ?? $user->gender;

			// handle gender change, automatic hostel
			if ($update['gender'] != $user->gender || ($isAdmin && $request->hostel_id != $transaction->hostel_id )) {
				$paymentArray = array_merge($data, $transaction->ToArray());
				$paymentArray['gender'] = $update['gender'];

				$paymentArray['field_id'] = $transaction?->user?->campus?->id;
				$paymentArray['setting'] = $setting;

                if($isAdmin && $request->has('hostel_id') && $request['hostel_id'] != $transaction->hostel_id){
					$paymentArray['new_hostel_id'] = $request['hostel_id'] ?? null;
                }

                $oldHostel = $transaction->hostel;

				$hostel_allocation = HostelAllocationService::assignHostel($transaction, $paymentArray);

				$data['allocated_hostel_data'] = $hostel_allocation;

				$paymentupdate['hostel_allocation_number'] = $hostel_allocation['hostel_allocation_number'];
				$paymentupdate['hostel_allocation_type'] = $hostel_allocation['hostel_allocation_type'];
				$paymentupdate['hostel_id'] = $hostel_allocation['hostel_id'];

                if (!$hostel_allocation['status'] && empty($hostel_allocation['hostel_id'])) {
					DB::rollBack();
					return back()->with('error', $hostel_allocation['message'] ?? 'There is no available hostel at the moment. Changes not saved!');
				}

                if (!isset($hostel_allocation['reason']) && $oldHostel && $oldHostel->id != $hostel_allocation['hostel_id']) {
                    HostelAllocationService::reduceHostelAllocation($oldHostel);
                }
			}

			// handle password
			if ($request->has('password') && !empty($request->password)) {
				$update['password'] = Hash::make($request['password']);
			}

			// handle food change
			if ($request->has('food_id') && $request['food_id'] != $transaction->food_id && $isAdmin) {
                $oldServicePoint = $transaction->food;
				$paymentArray = array_merge($data, $transaction->ToArray());
				$paymentArray['field_id'] = $transaction?->user?->campus?->id;

				$paymentArray['new_food_id'] = $request['food_id'];
				$service_point = ServicePointAllocationService::assignFoodStand($transaction, $paymentArray);

				$data['allocated_service_point_data'] = $service_point;
				$paymentupdate['service_point_allocation_number'] = $service_point['service_point_allocation_number'];
				$paymentupdate['service_point_allocation_type'] = $service_point['service_point_allocation_type'];
				$paymentupdate['food_id'] = $service_point['service_point_allocation_id'];

                if (!isset($service_point['reason']) && $oldServicePoint && $oldServicePoint->id != $service_point['service_point_allocation_id'] && $isAdmin) {
                    ServicePointAllocationService::reduceFoodStandAllocation($oldServicePoint);
                }
			} else {
				$paymentupdate['food_id'] = $transaction->food_id;
			}

			$user->update($update);

			if (!empty($paymentupdate)) {
				$transaction->update($paymentupdate);
			}


			// Update registration fields
            if (
                $isAdmin
                && isset($data['registration_fields']['no_of_participants'])
                && $data['registration_fields']['no_of_participants'] > $transaction->slot_filled
                && $transaction->registration_user_type === 'moderator'
            ) {
                $transaction->update([
                    'slot' => $data['registration_fields']['no_of_participants']
                ]);
            }

			if (!empty($data['registration_fields'])) {
				foreach ($data['registration_fields'] as $key => $value) {
					$transaction->allocationFields()->updateOrCreate(
						[
							'key' => $key,
							'transaction_id' => $transaction->id,
						],
						['value' => $value]
					);
				}
			}
			// END UPDATE registrationn fields
			DB::commit();

			return back()->with('message', 'Operation successful');
		} catch (\Throwable $th) {
			DB::rollBack();
			return back()->with('error', $th->getMessage() . ', ' . $th->getFile() . ', ' . $th->getLine());
		}

		return back()->with('message', 'Operation succesful');
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

	public function participants($type = '', $edition = '', $slug = '')
	{
		if (auth()->user()->role == 1) {
			$participants = Transaction::with('user')->where('conference_edition_id', $edition)->wherehas('user')->where('level', $type);

            if(request()->filled('registration_user_type')){
                $participants->where('registration_user_type', request()->registration_user_type);
            }

            $participants = $participants->latest()->take(10)->get();
            $import_type = $slug;

			$edition = ConferenceEdition::find($edition);

			return view('conference_management.admin.users.index', compact('participants', 'edition', 'type', 'import_type'));
		}
	}

    public function exportParticipants(Request $request){
        $payload = [
            'edition' => ConferenceEdition::where('id', $request->edition)->first(),
            'plan' => $request->plan,
        ];

        $exportData = UserService::exportConferenceParticipantsData($payload);
        $name = $payload['edition']->conference_theme . ' participants.xlsx';

        return ExcelService::download(
            $exportData['data']->toArray(),
            array_values($exportData['headers']),
            $name
        );

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
        $user = auth()->user();

		if ($payment->registration_status != 'Complete'  && $user->role <> 1) {
			return back()->with('error', 'You must complete registration before viewing this resource');
		}

		if (getRegistrationUserType(['moderator'], $payment->edition)) {
			if ($payment->uploaded_by != $user->id) {
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

		$criticalEmail = CriticalEmail::where('recipient', $user->email)->where('type', 'conference_registration_welcome_mail')->where('status', 1)->first();

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

	public function usersImportIndex(Request $request){
		$edition = ConferenceEdition::find($request->edition) ?? activeConferenceEdition();
		$type = $request->type;
        $isModerator = getRegistrationUserType(['moderator'], $edition);

		// Build base query once
		$currentPlan = ConferencePlan::where('status', 1)
			->where('conference_edition_id', $edition->id)->where('level', $type)->first();

		$fields = $currentPlan?->fields()->where('name', '!=', 'no_of_participants')->sortBy('display_order');
        $user = auth()->user();

		if ($user->role == 1) {
			$type = $request->type;
			$import_type = $request->import_type;
			$chapters = Chapter::all();

			return view('conference_management.admin.users.import', compact('chapters', 'edition', 'type', 'import_type', 'fields'));
		}

		if (getRegistrationUserType(['moderator'], $edition)) {
			$transaction = Transaction::where(['user_id' => $user->id, 'conference_edition_id' => $edition->id, 'registration_status' => 'Complete'])->first();

			if ($transaction->slot_filled >= $transaction->slot) {
				return back()->with('error', 'You have already exhausted your registration slots');
			}

			return view('conference_management.moderator.users.import', compact('edition', 'type', 'transaction', 'fields'));
		}
	}

	public function getAdminParticipantSample(Request $request, $type)
	{
		$edition = ConferenceEdition::findOrFail($request->edition);

        $isModerator = getRegistrationUserType(['moderator'], $edition);

		// Build base query once
		$currentPlan = ConferencePlan::where('status', 1)
			->where('conference_edition_id', $edition->id);

        if(!empty($request->import_type)){
            $currentPlan = $currentPlan->where('slug', $request->import_type);
        }else{
			$currentPlan = $currentPlan->where('level', $type);
        }

		$currentPlan = $currentPlan->first();

		$fields = $isModerator ? $currentPlan?->fields()->where('name', '!=', 'no_of_participants')->sortBy('display_order') : $currentPlan?->fields()->sortBy('display_order');

		$fields = $fields
			->groupBy('type')
			->map(fn($group) => $group->pluck('name')->toArray())
			->toArray();

		// Prepare headers for Excel (flattened)
		$headers = [];
		foreach ($fields as $type => $names) {
			foreach ($names as $name) {
				$headers[] = $name;
			}
		}

		$sample = [];
		foreach ($fields as $type => $names) {
			foreach ($names as $name) {
				$sample[$name] = generateSampleValue($type, $name);
			}
		}

		return ExcelService::download([$sample], $headers, Str::lower($request->type) . 'sample.xlsx');
	}


    public function import(Request $request)
	{
        $user = auth()->user();
        $isModerator = false;

        if($user->role == 2){
            $moderator = Transaction::where(['user_id' => $user->id, 'conference_edition_id' => $this->edition->id, 'status' => 'Complete', 'registration_status' => 'Complete', 'registration_user_type' => 'moderator'])->first();
            $isModerator = true;
        }

		if (auth()->user()->role == 1 || getRegistrationUserType(['moderator'], $this->edition)) {
			$this->validate($request, [
				'file' => 'required|mimes:xlsx,csv',
				'import_level' => 'required',
			]);
		}

		$participants = ExcelService::import($request->file('file'));

		foreach($participants as $participant){
			request()->merge($participant);
			$store = $this->store(request(), 'import');

            if(!$store['status']) continue;
		}

        if (!$isModerator) {
            return redirect()->route('conference.participants', [
                'type' => $request->type,
                'edition' => $request->edition,
                'slug' => $request->import_type,
            ])->with('message', 'Upload Successful');
        }

        $moderator = $moderator->fresh();

        return redirect(route('conferencemanagement.show', ['conferencemanagement' => $moderator->id, 'edition' => $this->edition->id]))->with('message', 'Participant successfully created, you have ' . ($moderator->slot - $moderator->slot_filled) . ' participant slot(s) left');
	}

	// public function adminImport(Request $request)
	// {
	// 	$edition = ConferenceEdition::find($request->edition) ?? activeConferenceEdition();

	// 	$data = $this->validate($request, [
	// 		'file' => 'required|mimes:xlsx,csv',
	// 		'chapter_id' => 'nullable',
	// 		'import_level' => 'required|in:Participant,Moderator,Alumni,Nec,Choir',
	// 	]);

	// 	$data['setting'] = $edition;
	// 	$redirectRoute = auth()->user()->isAdmin() ? route('conferenceusers.import.index', ['type' => $request->import_level, 'edition' => $request->edition]) : route('conferenceusers.import.index');

	// 	return redirect(route('conferenceusers.import.index', ['type' => $request->import_level, 'edition' => $request->edition]))->with([
	// 		'message' => 'Upload Successful',
	// 	]);
	// }


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

    public function destroy(Request $request, $id){
        // Prevent deleting self
        if (auth()->user()->id == $id) {
            return back()->with('error', 'You cannot delete yourself');
        }

        DB::beginTransaction();

        try {
            $payment = Transaction::where([
                'id' => $request->payment_id,
                'conference_edition_id' => $request->edition
            ])->firstOrFail();

            $user = User::withTrashed()->where('id', $payment->user_id)->firstOrFail();

            // Reduce allocations
            HostelAllocationService::reduceHostelAllocation($payment->hostel);
            ServicePointAllocationService::reduceFoodStandAllocation($payment->food);

            // Adjust moderator slot if uploaded by a moderator
            if ($payment->uploaded_by) {
                $moderator = Transaction::where([
                    'user_id' => $payment->uploaded_by,
                    'conference_edition_id' => $request->edition,
                    'registration_user_type' => 'moderator'
                ])->first();

                if ($moderator) {
                    $moderator->slot_filled = max($moderator->slot_filled - 1, 0);
                    $moderator->save();
                }
            }

            // Delete user passport if exists
            if ($user->passport && file_exists($user->passport)) {
                unlink($user->passport);
            }

            // Determine deletion type
            if ($user->trashed() || $payment->uploaded_by) {
                // Force delete
                $user->forceDelete();
                $payment->forceDelete();
            } else {
                // Soft delete
                $user->delete();
                $payment->transid = $payment->transid . '..Flagged-' . time();
                $payment->save();
                $payment->delete();
            }

            DB::commit();
            return back()->with('message', 'Participant record has been deleted!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
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
