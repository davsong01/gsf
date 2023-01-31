<?php
namespace App\Http\Controllers;
use PDF;

use App\User;
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
			'name' => 'required|',
			'email' => 'required|email',
			'phone' => 'required',
			'chapter' => 'nullable'
		]);
		
		//Validate individual registration
		if (isset($type['type']) && $type['type'] == '1') {
			//check amount
			$this->validate($request, [
				'gender' => 'required|',
			]);

			if ($request->amount <> ($setting->registration_fee * 100)) {
				return redirect(url('/registration/#register'))->with('error', 'Invalid amount');
			}
		}

		//Validate Fellowship registration
		if (isset($type['type']) && $type['type'] == '2') {
			if($request->participants < 2){
				return redirect(url('/registration/#register'))->with('error', 'You can only register minimum of 2 participants as a fellowship, kindly register as an individual');
			}
			//check amount
			if ($request->amount <> ($setting->registration_fee * $request->participants * 100)) {
				return redirect(url('/registration/#register'))->with('error', 'Invalid amount');
			}
		}

		//Validate Alumni Registration
		if (isset($type['type']) && $type['type'] == '3') {
			$this->validate($request, [
				'alumni_type' => 'required|in:alumni_registration_fee,new_alumni_registration_fee',
				'gender' => 'required'
			]);

			//check amount
			if ($request->amount != ($setting->$request->alumni_type * 100 )) {
				return back()->with('error', 'You cannot pay less than ' . $setting->$request->alumni_type);
			}
		}

		$type = json_decode($request['metadata'], true);
		$request['transid'] = $this->generateTransactionId();

		$tempUser = $this->createTempUser($request->all());
		$request['transid'] = $tempUser->transid;
		
		try {
			$url = $this->queryPaystack($request->all(), $setting);
			if(isset($url) && !empty($url)){
				return redirect()->away($url);
			}else{
				return redirect(url('/registration/#register'))->with('error', 'Something went wrong, Please try again');
			}
			
		} catch (\Exception $e) {
			return redirect(url('/registration/#register'))->with('error', $e . 'Transaction token has expired or details not correct. Please refresh the page and try again');
		}
	}

	public function handleGatewayCallback(Request $request)
	{
		$reference = $request->reference;
		$setting = $this->conferenceEdition();
		$conference_year = Carbon::parse($setting->start_date)->year;

		$paymentDetails = $this->verify($reference, $setting);

		// $this->verify()
		if (isset($paymentDetails) && $paymentDetails->status === 'success') {
			//get participant details
			$participant = TempUser::where('transid', $paymentDetails->reference)->whereStatus('initiated')->first();
			
			if(isset($participant) && !empty($participant)){
				$data['name'] = $participant->name ?? null;
				$data['phone'] = $participant->phone ?? null;
				$data['sex'] = $participant->gender ?? null;
				$data['type'] = $paymentDetails->metadata->type ?? null;
				$data['email'] = $participant->email;
				$data['password'] = bcrypt($participant->phone);
				$data['amount'] = $paymentDetails->amount/100;
				$data['transid'] = $participant->transid;
				$data['payment_type'] = "PAYSTACK";
				$data['conference_edition_id'] = $participant->conference_edition_id;
				$data['chapter'] = $participant->chapter_id ?? null;

				$type = $paymentDetails->metadata->type;
				$extras = $this->getExtras($type, $setting, $data['amount']);
				
				$data['slot'] = $extras['slot'] ?? null;
				$ledge = $extras['ledge'] ?? null;
				$data['level'] = $extras['level'] ?? null;
				$data['slot_filled'] = $extras['slot_filled'] ?? null;
	
				//Donations
				if (isset($paymentDetails->metadata->type) && $paymentDetails->metadata->type == '5') {
					//copy details to donations table
					$donation = Donation::Updateorcreate([
						'name' => $data['name'],
						'email' => $data['email'],
						'name' => $data['name'],
						'phone' => $data['phone'],
						'amount' => $data['amount'],
						'amount' => $data['amount'],
						'state' => $participant->state,
					]);

					$data['chapter'] = 'N/A';
					$data['type'] = $paymentDetails['data']['metadata']['type'];
					$email = [
						'recipient_name' => $data['name'],
						'recipient' => $setting->official_email,
						'subject' => $data['subject'],
						'content' => $data['content'],
						'type' => 'admin_donation_notification',
					];
					//send email to official email
					$this->logEmail($email);
					
					//delete temp user
					$participant->delete();

					//Todo: make this return redirect to
					return view('frontend.conference.donationthankyou', compact('data', 'conference_year'));
				}
				
				$user = $this->createUser($data);
				
				$payment = $this->createPayment($data, $user);

				// Assign Automatic foodstand and hostel
				if (in_array($payment->level, ['Participant', 'Alumni', 'Nec'])) {
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
					]);
				}

				$data['family_id'] = $user->family_id;
				$data['chapter'] = isset($participant->campus->name) ? $participant->campus->name : '';
				
				//update temp user
				$participant->update(['status'=>'Complete']);
				
				//send email to participant
				Mail::to($data['email'])->send(new WelcomeMail($data));

				//send email to official email
				Mail::to($this->conferenceEdition()->official_email)->send(new AdminMail($data));

				Auth::loginUsingId($payment->user->id);
				return $this->thankYouPage($data, $conference_year);
			}else{
				$payment = Payment::with('user')->where('transid', $paymentDetails->reference)->first();
				$user = $payment->user;
				
				if(isset($payment)){
					$data = [
						'name' => $payment->user->name,
						'phone' => $payment->user->phone,
						"type" => $payment->type,
						"email" => $payment->user->email,
						"amount" => $payment->amount_paid,
						"transid" => $payment->transid,
						"payment_type" => $payment->type,
						"chapter" => $payment->user->campus->name,
						"slot" => $payment->slot,
						"level" => $payment->level,
						"slot_filled" => $payment->slot_filled,
						"password" =>  $payment->user->phone,
						"family_id" => $payment->user->family_id,
						"conference_edition_id" => $payment->conference_edition_id,
						
					];
					
					// Log user in
					Auth::loginUsingId($payment->user->id);
					return $this->thankYouPage($data, $conference_year);

					// return view('frontend.conference.thankyou', compact('data', 'conference_year'));
				}else{
					return 'Payment not detected!';
				}
				// return view('frontend.conference.thankyou', compact('data', 'conference_year'));
			}
			
			
		} else {
			dd('Transaction failed! We have not received any money from you.');
		}
	}

	public function thankYouPage($data, $conference_year){
		return view('frontend.conference.thankyou', compact('data', 'conference_year'));
	}

	public function queryPaystack($request,$setting)
	{
		$transId = $this->generateTransactionId();
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
			return;
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
		$temp = TempUser::updateOrCreate(['email'=> $data['email']],[
			'name' => $data['name'],
			'transid' => $data['transid'],
			'phone' => $data['phone'],
			'type' => $type ?? null,
			'chapter_id' => $data['chapter'] ?? NULL,
			'state' => $data['state'] ?? null,
			'transid' => $data['transid'] ?? NULL,
			'status' => 'Initiated',
			'gender' => $data['gender'] ?? null,
			'conference_edition_id' => $setting->id
		]);

		return $temp;
	}

}
