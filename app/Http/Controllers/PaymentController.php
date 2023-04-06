<?php
namespace App\Http\Controllers;
use PDF;

use App\User;
use App\Chapter;
use App\Payment;
use App\Setting;
use App\Donation;
use App\TempUser;
use Carbon\Carbon;
use App\Mail\AdminMail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class PaymentController extends Controller

{
	public function redirectToGateway(Request $request)
	{
		$setting = $this->conferenceEdition();
		
		if ($setting->close_registration < now()) {
			return redirect(url('/registration/#register'))->with('warning', 'Registration for this program has closed');
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
		if (isset($type['type']) && $type['type'] == '1') {
			//check amount
			$this->validate($request, [
				'gender' => 'required',
			]);

			if ($request->amount <> ($setting->registration_fee)) {
				return redirect(url('/registration/#register'))->with('error', 'Invalid amount');
			}
		}

		//Validate Fellowship registration
		if (isset($type) && $type == '2') {
			$request['amount'] = $setting->registration_fee * $request->participants;
			
			if($request->participants < 2){
				return redirect(url('/registration/#register'))->with('error', 'You can only register minimum of 2 participants as a fellowship, kindly register as an individual');
			}
			//check amount
			if ($request->amount <> ($setting->registration_fee * $request->participants)) {
				return redirect(url('/registration/#register'))->with('error', 'Invalid amount');
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
		$setting = $this->conferenceEdition();
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

				$type = isset($paymentDetails) ? $paymentDetails->metadata->type : $participant->type;
				$extras = $this->getExtras($type, $setting, $data['amount']);
				
				$data['slot'] = $extras['slot'] ?? null;
				$ledge = $extras['ledge'] ?? null;
				$data['level'] = $extras['level'] ?? null;
				$data['slot_filled'] = $extras['slot_filled'] ?? null;
				
				//Donations
				if (isset($paymentDetails->metadata->type) && $paymentDetails->metadata->type == '5') {
					//copy details to donations table
					$donation = Donation::Create([
						'name' => $data['name'],
						'email' => $data['email'],
						'name' => $data['name'],
						'phone' => $data['phone'],
						'amount' => $data['amount'],
						'amount' => $data['amount'],
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

				// Assign Automatic foodstand and hostel
				if (in_array($payment->level, ['Participant', 'Alumni', 'Nec','Moderator'])) {
					$hostel = $this->assignHostel($data['level'], $data['sex']);
					$food = $this->assignFoodStand($data['level'], $data['chapter']);

					$data['hostel_id'] = $hostel->id ?? null;
					$data['hostel'] = $hostel->name ?? null;
					$data['food_id'] = $food->id ?? null;
					$data['foodstand'] = $food->name ?? null;

					$payment->update([
						'hostel_id' => $data['hostel_id'] ?? null,
						'food_id' => $data['food_id'] ?? null
					]);
				}

				$this->createFamilyId($user, $extras['ledge']);

				if ($payment->level == 'Moderator') {
					$payment->update([
						'uploaded_by' => $user->id,
						'response' => isset($paymentDetails) ? json_encode($paymentDetails) : null,
					]);
				}

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
	public function queryPaystack($request,$setting)
	{
		$url = "https://api.paystack.co/transaction/initialize";
		// Convert amount using payment mode exchange rate
		$metadata = isset($request['metadata']) ? json_decode($request['metadata'],true) : []; 

		$fields = [
			'email' => $request['email'],
			'amount' => $request['amount'],
			'reference' =>  $request['transid'],
			'callback_url' => url('/') . '/payment/callback',
			'currency' => $request['currency'],
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
			'state' => $data['state'] ?? null,
			'transid' => $data['transid'] ?? NULL,
			'status' => 'Initiated',
			'gender' => $data['gender'] ?? null,
			'conference_edition_id' => $setting->id,
			'location' => $location ?? null,
			'amount' => $amount ?? null,
		]);

		return $temp;
	}

}
