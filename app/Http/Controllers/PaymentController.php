<?php
namespace App\Http\Controllers;
use PDF;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Field;
use App\Mail\AdminMail;
use App\Models\Chapter;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Webhook;
use App\Models\Donation;
use App\Models\TempUser;
use App\Mail\WelcomeMail;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\EmailService;
use App\Services\MonnifyService;
use App\Services\PaymentService;
use App\Models\ConferenceEdition;
use App\Services\PaystackService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Services\WebhookAnalyzerService;
use Illuminate\Support\Facades\Redirect;
use App\Services\HostelAllocationService;
use App\Models\TransactionAllocationField;
use App\Services\WebhookVerificationService;
use App\Services\ServicePointAllocationService;

class PaymentController extends Controller
{
	private $conference;
	private $frontend;

	public function __construct()
	{
		$this->conference = activeConferenceEdition();
		$this->frontend = frontendTemplate();

		if (!isset($this->conference) && empty($this->conference)) {
			return true;
		} else {
			return false;
		}
	}

	public function checkout(Request $request){
		$setting = activeConferenceEdition();
		$this->frontend = frontendTemplate();
		$type = $request->type;

		$setting = $this->conferenceEdition();
		$request['setting'] = $setting;

		if(!in_array($type, [5])){
			$amount = PaymentService::calculateRegistrationAmount($request->all());
		}else{
			$amount = $request->amount;
		}

		$request['amount'] = $amount;
		
		$transaction = PaymentService::initializeTransaction($request->all());
		
		if(!$transaction['status']){
			return back()->with('error', $transaction['message'] ?? '');
		}
		
		if ($setting->lock_online_payment == 'yes') {
			return back()->with('message', 'Details received. Please make payment at the registration stand at the venue and show the registration officer your email address: ' . $request->email);
		}

		$transaction = $transaction['data'];

		// $paymentProvider = $setting->paymentprovider;

		return redirect(route('show.checkout', $transaction->id));
		// return view('frontend.conference.template' . $setting->template_id . '.checkout', compact('transaction', 'paymentProvider','setting'));
	}

	public function showCheckout(Transaction $transaction){
		$setting = $transaction->edition;
		$paymentProvider = $transaction->paymentprovider;

		return view('frontend.conference.template' . $setting->template_id . '.checkout', compact('transaction', 'paymentProvider', 'setting'));
	}

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

	
	public function handleGatewayCallback(Request $request, $admin = "", $transfer_confirm = "", $onsite_confirm = "")
	{
		$reference = $request->reference;
		$setting = $request['setting'] ?? activeConferenceEdition();
		$extraData = [
			'conference_year' => Carbon::parse($setting->start_date)->year,
		];

		$transaction = Transaction::with(['user', 'paymentprovider', 'edition', 'allocationFields'])
			->where('transid', $reference)
			->first();

		if (!$transaction) {
			return response()->json(['error' => 'Transaction not found'], 404);
		}

		// Already completed
		if ($transaction->status === 'Complete') {
			return $admin !== 'admin'
				? $this->thankYouPage($transaction, $extraData)
				: $transaction;
		}

		$verify = $this->verify($transaction);

		$transaction->update([
			'api_response' => $verify['message'] ?? null,
		]);

		// Stop early if verification failed and no manual confirmation
		if (!$verify['status'] && empty($transfer_confirm) && empty($onsite_confirm)) {
			if ($admin !== 'admin') {
				return back()->with('error', 'Transaction failed! We have not received any payment.');
			}
			return $transaction;
		}

		// If donation
		if ($transaction->purpose == 'donation') {
			$this->processDonation($transaction, $setting, $admin);
			return $admin !== 'admin'
				? $this->donationThankYouPage($transaction, $extraData)
				: $transaction;
		}

		// Normal registration
		if ($transaction->purpose != 'donation') {
			$user = PaymentService::createUser($transaction);
		}else{
			$user = null;
		}

		$data = [];

		// Hostel and service point allocations
		if (in_array($transaction->level, ['Participant', 'Alumni', 'Nec', 'Moderator'])) {
			$data['allocated_hostel_data'] = HostelAllocationService::assignHostel($transaction);
			$data['allocated_service_point_data'] = ServicePointAllocationService::assignFoodStand($transaction);

			$transaction->update([
				'hostel_allocation_number' => $data['allocated_hostel_data']['hostel_allocation_number'] ?? null,
				'hostel_allocation_type' => $data['allocated_hostel_data']['hostel_allocation_type'] ?? null,
				'service_point_allocation_number' => $data['allocated_service_point_data']['service_point_allocation_number'] ?? null,
				'service_point_allocation_type' => $data['allocated_service_point_data']['service_point_allocation_type'] ?? null,
				'hostel_id' => $data['allocated_hostel_data']['hostel_id'] ?? null,
				'food_id' => $data['allocated_service_point_data']['service_point_allocation_id'] ?? null,
			]);
		}

		// Generate Family ID
		$familyId = PaymentService::generateFamilyId($user, $setting);
		$user->update(['family_id' => $familyId]);

		// Moderator special case
		if ($transaction->level === 'Moderator') {
			$transaction->update(['uploaded_by' => $user->id]);
		}

		// Final transaction update
		$transaction->update([
			'status' => 'Complete',
			'registration_status' => 'Complete',
			'user_id' => $user->id ?? null,
		]);

		if(in_array($transaction->level, ['Moderator'])){
			$transaction->update([
				'uploaded_by' => $user->id ?? null,
			]);
		}

		// Send emails
		$transaction->user = $user;
		$this->sendRegistrationEmails($transaction);

		// Auto-login (if needed)
		if ($admin !== 'admin') {
			Auth::loginUsingId($user->id);
		}

		return $admin !== 'admin'
			? $this->thankYouPage($transaction, $extraData)
			: $transaction;
	}

	/**
	 * Process donation-type transactions.
	 */
	protected function processDonation($transaction, $setting, $admin)
	{
		$emailData['transaction'] = $transaction;

		// Admin notification
		$emailData['type'] = 'admin_donation_notification';
		EmailService::logEmail($emailData);

		// Donor notification
		$emailData['type'] = 'donator_notification';
		EmailService::logEmail($emailData);

		$transaction->update([
			'status' => 'Complete',
		]);

		return true;
	}

	/**
	 * Send all registration-related emails.
	 */
	protected function sendRegistrationEmails($transaction)
	{
		$emailData['transaction'] = $transaction;

		// Welcome email to participant
		$emailData['type'] = 'welcome_mail';
		EmailService::logEmail($emailData);

		// Notify admin/new registration
		$emailData['type'] = 'new_registration';
		EmailService::logEmail($emailData);
	}


	public function thankYouPage($transaction, $extraData){
		$transaction->fresh();
		
		if(!empty($transaction->edition)){
            return view('frontend.conference.template'. $transaction->edition->template_id.'.thankyou', compact('transaction', 'extraData'));
		}else{
			return view('frontend.conference.thankyou', compact('data', 'extraData'));
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


	public function verify($transaction)
	{
		$provider = $transaction->paymentprovider->slug;
		
		if($provider == 'paystack'){
			return PaystackService::verify($transaction);
		}

		if ($provider == 'monnify') {
			return MonnifyService::verify($transaction);
		}
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
				$user = Transaction::where('email', $payment->customer_email)->where('status', '!=', 'Complete')->first();
				$user->transid = $payment->reference;
				$user->save();

				if(!$user){
					return false;
				}

				$request['reference'] = $payment->reference;
				app('App\Http\Controllers\TransactionController')->requery($user->id, $request, true);
				$payment->update([
					'status' => 'processed',
					'processed_at' => now()
				]);
			}

		}
	}
}
