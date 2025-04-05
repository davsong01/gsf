<?php
namespace App\Http\Controllers;
use PDF;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\AdminMail;
use App\Models\Chapter;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Webhook;
use App\Models\Donation;
use App\Models\TempUser;
use App\Mail\WelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ConferenceEdition;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Services\WebhookAnalyzerService;
use Illuminate\Support\Facades\Redirect;
use App\Services\HostelAllocationService;
use App\Services\WebhookVerificationService;
use App\Services\ServicePointAllocationService;

class PaymentController extends Controller

{
	public function redirectToGateway(Request $request)
	{
		$setting = activeConferenceEdition();
		$this->frontend = frontendTemplate();

		if ($setting->close_registration < now()) {
			return back()->with('warning', 'Registration for this program has closed');
			// return redirect(url('/registration/#register'))->with('warning', 'Registration for this program has closed');
		}
		
		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|email',
			'phone' => 'required',
			'chapter' => 'nullable'
		]);

		$type['conference_edition_id'] = $setting->id ?? null;
		
		$metadata = json_decode($request->metadata);
		$type['type'] = $metadata->type;
		$request['metadata'] = json_encode($type);		
		
		// $type['type'] = json_decode($request->metadata)->type;
		$type = $type['type'] ?? $request->type;
		
		//Validate individual registration
		if (isset($type) && $type == '1') {
			//check amount
			
			$this->validate($request, [
				'gender' => 'required',
			]);

			if ($request->amount <> $setting->registration_fee) {
				return back()->with('error', 'Invalid amount');
			}
		}

		//Validate Fellowship registration
		if (isset($type) && $type == '2') {
			$request['amount'] = $setting->registration_fee * $request->participants;
			
			if($request->participants < 2){
				return back()->with('error', 'You can only register minimum of 2 participants as a fellowship, kindly register as an individual');
			}
			//check amount
			if ($request->amount <> ($setting->registration_fee * $request->participants)) {
				return back()->with('error', 'Invalid amount');
			}
		}
		
		//Validate Alumni Registration
		if (isset($type) && $type == '3') {
			$this->validate($request, [
				// 'amount' => 'required|in:alumni_registration_fee,new_alumni_registration_fee',
				'gender' => 'required'
			]);
			$alumni_type = $request->alumni_type;
			$request['amount'] = $setting->$alumni_type;
			//check amount
			
			if ($request->amount != $setting->$alumni_type) {
				return back()->with('error', 'You cannot pay less than ' . $setting->$alumni_type);
			}

		}
		
		//Validate Donation Registration
		if (isset($type) && $type == '5') {
			$this->validate($request, [
				"name" => "required",
				"email" => "required",
				"phone" => "required",
				"amount"=>"required"
			]);

		}
		
		$request['transid'] = $this->generateTransactionId();
		\Log::info('Transaction ID: ' . $request['transid']);
		$tempUser = $this->createTempUser($request->all());
		
		if(is_null($tempUser)){
			return back()->with('error', 'You are already a Participant in this conference');
		}

		$request['transid'] = $tempUser->transid;
		$request['conference_edition_id'] = $setting->id;
		$request['currency'] = 'NGN';
		
		if($setting->lock_online_payment == 'yes'){
			return back()->with('message', 'Details received. Please make payment at the registration stand at the venue and show the registration officer your email address: '.$request->email);
		}

		try {
			$request['amount'] = $request['amount'] * 100;
			$url = $this->queryPaystack($request->all(), $setting);
			
			if (isset($url['error'])) {
				return back()->with('error', $url['error']);
			}elseif (isset($url) && !empty($url)) {
				return redirect()->away($url);
			}
		
		} catch (\Exception $e) {
			return redirect(url('/registration/#register'))->with('error', $e . 'Transaction token has expired or details not correct. Please refresh the page and try again');
		}
	}

	public function handleGatewayCallback(Request $request, $admin="", $transfer_confirm="",$onsite_confirm="")
	{
		$reference = $request->reference;
		$setting = activeConferenceEdition();
		$conference_year = Carbon::parse($setting->start_date)->year;

		$paymentDetails = $this->verify($reference, $setting);
		
		if ((isset($paymentDetails) && $paymentDetails->status === 'success') || !empty($transfer_confirm) || !empty($onsite_confirm)) {
			//get participant details
			if(!empty($transfer_confirm) || !empty($onsite_confirm)){
				$participant = TempUser::where('transid', $request->reference)->first();
			}else{
				$participant = TempUser::where('transid', $paymentDetails->reference)->first();
			}
			
			if(empty($participant)){
				return false;
			}

			// if(isset($participant) && !empty($participant)){
			if(isset($participant) && !empty($participant)){
				$data['name'] = $participant->name ?? null;
				$data['phone'] = $participant->phone ?? null;
				$data['sex'] = $participant->gender ?? null;
				$data['type'] = isset($paymentDetails) ? $paymentDetails->metadata->type : $participant->type;
				$data['email'] = $participant->email;
				$data['password'] = bcrypt($participant->phone);
				$data['amount'] = isset($paymentDetails) ? $paymentDetails->amount/100 : $participant->amount;
				$data['transid'] = $participant->transid;
				$data['payment_type'] = "PAYSTACK";
				$data['conference_edition_id'] = $participant->conference_edition_id;
				$data['chapter'] = $participant->chapter_id ?? null;
				$data['field_id'] = $participant->field_id ?? null;
				$data['remarks'] = $participant->remarks ?? null;

				$type = isset($paymentDetails) ? $paymentDetails->metadata->type : $participant->type;
				$extras = $this->getExtras($type, $setting, $data['amount']);
				
				$data['slot'] = $extras['slot'] ?? null;
				$ledge = $extras['ledge'] ?? null;
				$data['level'] = $extras['level'] ?? null;
				$data['slot_filled'] = $extras['slot_filled'] ?? null;
				
				//Donations
				if (isset($paymentDetails->metadata->type) && $paymentDetails->metadata->type == '5') {
					//copy details to donations table
					$donation = Donation::UpdateOrCreate(['name' => $data['name'],'email' => $data['email']],[
						'name' => $data['name'],
						'email' => $data['email'],
						'phone' => $data['phone'],
						'amount' => $data['amount'],
						'remarks' => $data['remarks'] ?? null,
						'state' => $participant->state,
						'transid' => $paymentDetails->reference,
						'conference_edition_id' => $participant->conference_edition_id,
					]);

					$data['chapter'] = 'N/A';
					$data['conference_theme'] = $setting->conference_theme ?? null;
					$data['type'] = 'admin_donation_notification';
					// official email
					$email = [
						'recipient_name' => $data['name'],
						'recipient' => $setting->official_email,
						'subject' => "New Donation",
						'type' => 'admin_donation_notification',
						'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
					];
					$participant->status = 'Complete';

					$participant->save();
					$this->logEmail($email);

					//send email to donator
					$data['type'] = 'donator_notification';
					$email = [
						'subject' => 'Thank you for your donation',
						'recipient_name' => $data['name'],
						'recipient' => $data['email'],
						'type' => $data['type'],
						'amount' => $data['amount'],
						'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
					];

					$this->logEmail($email);
					//Todo: make this return redirect to
					$data['edition'] = $setting;
					
					if($admin !== 'admin'){
						return $this->donationThankYouPage($data, $conference_year);
					}else{
						return $paymentDetails;
					}

					// return view('frontend.conference.donationthankyou', compact('data', 'conference_year'));
				}
				
				$user = $this->createUser($data);
				$payment = $this->createPayment($data, $user);
				$chapter = Chapter::with('field:id,name')->select('id', 'field_id')->where('id', $data['chapter'])->first();
				$data['field_id'] = !empty($chapter->field) ? $chapter->field->id : (!empty($data['field_id']) ? $data['field_id'] : null);
				
				// Assign Automatic foodstand and hostel
				if (in_array($payment->level, ['Participant', 'Alumni', 'Nec','Moderator'])) {
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
				
				$this->createFamilyId($user, $extras['ledge']);
				
				if ($payment->level == 'Moderator') {
					$payment->update([
						'uploaded_by' => $user->id,
						// 'api_response' => isset($paymentDetails) ? json_encode($paymentDetails) : null,
					]);
				}

				$payment->update([
					'api_response' => isset($paymentDetails) ? json_encode($paymentDetails) : null,
				]);
				
				$data['family_id'] = $user->family_id;
				$data['chapter'] = isset($participant->campus->name) ? $participant->campus->name : '';
				
				//update payment user
				if(!empty($onsite_confirm)){
					$payment->update(['location'=>'On Site']);
				}

				$participant->update(['status'=>'Complete']);
				$data['type'] = 'welcome_mail';
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
				$data['type'] = 'new_registration';
				$email = [
					'subject' => 'New Registration',
					'type'=> 'new_registration',
					'recipient' => $setting->official_email,
					'chapter' => $data['chapter'],
					'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
				];
				
				//send email to official email
				$this->logEmail($email);
				if($admin !== 'admin'){
					Auth::loginUsingId($payment->user->id);	
				} 
				
				$data['edition'] = $setting;
				
				if($admin !== 'admin'){
					return $this->thankYouPage($data, $conference_year);
				} else {
					return $paymentDetails ?? $payment;
				}
			}else{
				$payment = Payment::with('user')->where('transid', $paymentDetails->reference)->first();
				
				if(isset($payment)){
					$user = $payment->user;
					$data = [
						'name' => $payment->user->name,
						'phone' => $payment->user->phone,
						"type" => $payment->type,
						"email" => $payment->user->email,
						"amount" => $payment->amount_paid,
						"transid" => $payment->transid,
						"payment_type" => $payment->type,
						"chapter" => $payment->user->campus->name ?? null,
						"slot" => $payment->slot,
						"level" => $payment->level,
						"slot_filled" => $payment->slot_filled,
						"password" =>  $payment->user->phone,
						"family_id" => $payment->user->family_id,
						"conference_edition_id" => $payment->conference_edition_id,
						"edition"=>$setting
					];
					
					// Log user in
				if($admin !== 'admin'){
						Auth::loginUsingId($payment->user->id);
					} 

				if($admin !== 'admin'){
						return $this->thankYouPage($data, $conference_year);
					} else {
						return $paymentDetails ?? $payment;
					}
					// return view('frontend.conference.thankyou', compact('data', 'conference_year'));
				}else{
				if($admin !== 'admin'){
						return redirect(route('home.index'));
					} else {
						return $paymentDetails ?? $payment;
					}
				}
				// return view('frontend.conference.thankyou', compact('data', 'conference_year'));
			}
		} else {
			if($admin !== 'admin'){
				dd('Transaction failed! We have not received any money from you.');
			} else {
				return $paymentDetails ?? $payment;
			}
		}
	}

	public function thankYouPage($data, $conference_year){
		if(isset($this->edition) && !empty($this->edition)){
            return view('frontend.conference.template'. $this->edition->template_id.'.thankyou', compact('data', 'conference_year'));
		}else{
			return view('frontend.conference.thankyou', compact('data', 'conference_year'));
		}
	}

	public function donationThankYouPage($data, $conference_year)
	{
		if (isset($this->edition) && !empty($this->edition)) {
			return view('frontend.conference.template' . $this->edition->template_id . '.donationthankyou', compact('data', 'conference_year'));
		} else {
			return view('frontend.conference.donationthankyou', compact('data', 'conference_year'));
		}
	}

	public function queryPaystack($request,$setting, $callback=null)
	{
		$url = "https://api.paystack.co/transaction/initialize";
		// Convert amount using payment mode exchange rate
		$metadata = isset($request['metadata']) ? json_decode($request['metadata'],true) : []; 

		$fields = [
			'email' => $request['email'],
			'amount' => $request['amount'],
			'reference' =>  $request['transid'],
			'callback_url' => $callback ?? url('/') . '/payment/callback',
			'currency' => $request['currency'],
			'channels' => ["card", "bank", "bank_transfer"],
			// 'channels' => ["card", "bank", "apple_pay", "ussd", "qr", "mobile_money", "bank_transfer", "eft"],
			'metadata'=> $metadata,
		];

		$fields_string = http_build_query($fields);
		//open connection
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Authorization: Bearer " . $setting->PAYSTACK_SECRET_KEY,
			"Cache-Control: no-cache",
		));

		//So that curl_exec returns the contents of the cURL; rather than echoing it
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//execute post

		$result = curl_exec($ch);
		$result = json_decode($result);
		
		try {
			return $result->data->authorization_url;
		} catch (\Exception $th) {
			\Log::error('Payment Gateway Error: ' . $th->getMessage());
			return [
				'error' => $result->message
			];

		}
	}

	public function paystackGetCustomerIdByEmail(Request $request)
	{
		$request->validate([
			'email' => 'required|email',
			'edition_id' => 'required|integer'
		]);

		try {
			$setting = activeConferenceEdition();

			$paystackSecretKey = $setting->PAYSTACK_SECRET_KEY;

			// Step 1: Get customer ID using email
			$customerResponse = Http::withToken($paystackSecretKey)
				->get("https://api.paystack.co/customer/{$request->email}");
			
			if (!$customerResponse->ok() || !$customerResponse->json('data.id')) {
				return response()->json(['success' => false, 'message' => 'Customer not found.']);
			}

			$customerId = $customerResponse->json('data.id');
			
			// Step 2: Get transactions for this customer ID
			$transactionsResponse = Http::withToken($paystackSecretKey)
				->get("https://api.paystack.co/transaction?customer={$customerId}");

			if (!$transactionsResponse->ok()) {
				return response()->json(['success' => false, 'message' => 'Could not fetch transactions.']);
			}

			$transactions = $transactionsResponse->json('data');

			$filtered = collect($transactions)->filter(function ($tx) use ($request) {
				return isset($tx['metadata']['conference_edition_id']) &&
					$tx['metadata']['conference_edition_id'] == $request->edition_id;
			})->map(function ($tx) {
				$editionId = $tx['metadata']['conference_edition_id'];
				$edition = ConferenceEdition::find($tx['metadata']['conference_edition_id']);
				$tx['conference_edition'] = $edition ? $edition->conference_theme : 'Unknown Edition';
				return $tx;
			})->values();
			
			return response()->json([
				'success' => true,
				'transactions' => $filtered
			]);

			\Log::info(['transactions' => $filtered]);

		} catch (\Exception $e) {
			return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
		}
	}
	public function verify($reference, $setting)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $reference,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . $setting->PAYSTACK_SECRET_KEY,
				"Cache-Control: no-cache",
			),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		
		curl_close($curl);

		try {
			//code...
			if ($err) {
				\Log::info("cURL Error #:" . $err);
			} else {
				$response = json_decode($response);
				
				return $response->data;
			}
		} catch (\Throwable $th) {
			
			//throw $th;
		}
	}

	public function createTempUser($data){
		$type = isset($data['metadata']) && !empty($data['metadata']) ? json_decode($data['metadata'], true) : [];
		$type = !empty($type) ? $type['type'] : null;
		
		$setting = $this->conferenceEdition();
		// Check if email already exists with a payment corresponding to this edition
		if(!in_array($type, [5])){
			$check = User::where('email', $data['email'])
				->join('payments', 'payments.user_id','=','users.id')
				->where('payments.conference_edition_id', $setting->id)
				->select('payments.*')
				->get();
			if($check->count() > 0){
				return null;
			}
		}
		
		if(isset($setting->lock_online_payment) && $setting->lock_online_payment == 'yes'){
			$location = 'On Site';
			$amount = $data['amount'];
		}
		
		$temp = TempUser::updateOrCreate(['email'=> $data['email'], 'conference_edition_id' => $setting->id],[
			'name' => $data['name'],
			'phone' => $data['phone'],
			'type' => $type ?? null,
			'chapter_id' => $data['chapter'] ?? NULL,
			'field_id' => $data['field_id'] ?? NULL,
			'state' => $data['state'] ?? null,
			'transid' => $data['transid'] ?? NULL,
			'status' => 'Initiated',
			'gender' => $data['gender'] ?? null,
			'conference_edition_id' => $setting->id,
			'location' => $location ?? null,
			'amount' => $amount ?? null,
			'remarks' => $data['remarks'] ?? null,
		]);

		return $temp;
	}

	public function dumpWebhook(Request $request){
		\Log::info(['webhook' => $request->all()]);
		$provider = $request->provider;

		if(WebhookVerificationService::verifyWebhook($provider, $request->all())['status']){
			$data = $request->all();
			
			Webhook::updateOrCreate([
				'reference' => $data['data']['reference'],
			],[
				'event_type' => $data['event'],
				'provider' => $provider,
				'reference' => $data['data']['reference'],
				'customer_email' => $data['data']['customer']['email'],
				'payload' => $request->all(),
			]);

			return response()->json([], 200);
		}
	}

	public function analyze(Request $request){
		// Get all pending payments
		$pendingPayments = Webhook::where('status', 'pending')->get();
		
		if($pendingPayments){
			foreach($pendingPayments as $payment){
				$user = TempUser::where('email', $payment->customer_email)->where('status', '!=', 'Complete')->first();
				$user->transid = $payment->reference;
				$user->save();

				if(!$user){
					return false;
				}

				$request['reference'] = $payment->reference;
				app('App\Http\Controllers\TempUserController')->requery($user->id, $request, true);
				$payment->update([
					'status' => 'processed',
					'processed_at' => now()
				]);
			}

		}
	}
}
