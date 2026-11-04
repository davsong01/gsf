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
use App\Models\ConferencePlan;
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

		$plan = ConferencePlan::where('status', 1)->where('id', $request->conference_plan_id)->where('conference_edition_id', $setting->id)->first();

		if(!$plan){
			return back()->with('message', 'Invalid registration plan');
		}

		$setting = $this->conferenceEdition();
        // dd($setting);
		$request['setting'] = $setting;

		if(!in_array($plan->type, ['donation'])){
			$request['amount'] = $plan->price;
		}

		if (in_array($plan->type, ['multiple'])) {
			$request['amount'] = $plan->price * ($request->no_of_participants ?? $request->participants);
		}

		$request['payment_provider_id'] = $setting->payment_provider_id;

		$request['plan'] = $plan;

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
		$transaction->load('conferenceplan');
        // dd($transaction);
		// $transaction->update(['transid' => strtoupper($setting->ministry->code) . '-' . PaymentService::generateTransactionId()]); //No need to generate new transid here since it's already generated during initialization. This was causing issues with requerying the transaction after payment verification.
		$paymentProvider = $transaction->paymentprovider;

		return view('frontend.site.conferences.checkout', compact('transaction', 'paymentProvider', 'setting'));
	}

	public function retryCheckout(Request $request, Transaction $transaction)
	{
		$transaction->load(['edition.ministry', 'conferenceplan', 'allocationFields']);

		if ($transaction->status === 'Complete') {
			return redirect('/payment/verify/' . rawurlencode($transaction->transid));
		}

		$setting = $transaction->edition;
		$plan = $transaction->conferenceplan;
		abort_unless($setting && $plan, 404);

		$registrationFields = $transaction->allocationFields->pluck('value', 'key')->toArray();
		$retryData = array_merge($registrationFields, [
			'setting' => $setting,
			'plan' => $plan,
			'name' => $transaction->name,
			'email' => $transaction->email,
			'phone' => $transaction->phone,
			'gender' => $transaction->gender,
			'remarks' => $transaction->remarks,
			'amount' => $transaction->amount_paid,
			'payment_provider_id' => $setting->payment_provider_id ?: $transaction->payment_provider_id,
			'location' => 'Online',
		]);

		$newTransaction = PaymentService::initializeTransaction($retryData);
		if (empty($newTransaction['status'])) {
			return redirect()->route('show.checkout', $transaction->id)
				->with('error', $newTransaction['message'] ?? 'A new payment attempt could not be created.')
				->with('payment_failed', true);
		}

		return redirect()->route('show.checkout', $newTransaction['data']->id)
			->with('message', 'A fresh payment attempt is ready. Review the total and continue to payment.');
	}

	// public function handleGatewayCallback(Request $request, $admin = "", $transfer_confirm = "", $onsite_confirm = "")
	public function handleGatewayCallback(Request $request, $reference)
	{
		$admin = $request->admin;

		$transaction = Transaction::with(['user', 'paymentprovider', 'edition','conferenceplan', 'allocationFields'])
			->where('transid', $reference)
			->first();

		if (!$transaction) {
			\Log::warning('Payment callback received for an unknown transaction.', [
				'transaction_reference' => $reference,
			]);
			return response()->json(['error' => 'Transaction not found'], 404);
		}

		$setting = $transaction->edition ?? $request['setting'] ?? activeConferenceEdition();
		$extraData = [
			'conference_year' => $setting && $setting->start_date
				? Carbon::parse($setting->start_date)->year
				: Carbon::now()->year,
		];

		\Log::info('Payment callback received for a conference transaction.', [
			'transaction_id' => $transaction->id,
			'transaction_reference' => $transaction->transid,
			'payment_provider_id' => $transaction->payment_provider_id,
			'payment_provider' => $transaction->paymentprovider->slug ?? null,
			'transaction_status' => $transaction->status,
		]);

		// Already completed
		if ($transaction->status === 'Complete') {
			return $admin !== 'admin'
				? $this->thankYouPage($transaction, $extraData)
				: $transaction;
		}

		try {
			$verify = $this->verify($transaction);
		} catch (\Throwable $e) {
			\Log::error('Payment gateway verification threw an exception.', [
				'transaction_id' => $transaction->id,
				'transaction_reference' => $transaction->transid,
				'payment_provider_id' => $transaction->payment_provider_id,
				'exception_class' => get_class($e),
				'exception_message' => $e->getMessage(),
			]);

			$verify = ['status' => false, 'message' => null, 'provider_reference' => null];
		}

		$transaction->update([
			'api_response' => $verify['message'] ?? null,
			'provider_reference' => $verify['provider_reference'] ?? null,
		]);

		// Stop early if verification failed and no manual confirmation
		if (!$verify['status'] && empty($transfer_confirm) && empty($onsite_confirm)) {
			\Log::warning('Payment verification failed; conference registration remains pending.', [
				'transaction_id' => $transaction->id,
				'transaction_reference' => $transaction->transid,
				'payment_provider_id' => $transaction->payment_provider_id,
				'expected_total_amount' => $transaction->total_amount,
			]);
			if ($admin !== 'admin') {
				$providerMessage = $verify['verification_message'] ?? null;
				return redirect()->route('show.checkout', $transaction->id)
					->with('error', 'We could not verify this payment with the provider. Your registration is still pending.' . ($providerMessage ? ' Provider response: ' . $providerMessage : ''))
					->with('payment_failed', true);
			}
			return $transaction;
		}

		\Log::info('Payment gateway verification accepted for conference transaction.', [
			'transaction_id' => $transaction->id,
			'transaction_reference' => $transaction->transid,
			'payment_provider_id' => $transaction->payment_provider_id,
		]);

		try {
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
		// if (in_array($transaction->level, ['Participant', 'Alumni', 'Nec', 'Moderator'])) {
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
		// }

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

		\Log::info('Conference registration completed after payment verification.', [
			'transaction_id' => $transaction->id,
			'transaction_reference' => $transaction->transid,
			'payment_provider_id' => $transaction->payment_provider_id,
			'user_id' => $user->id ?? null,
		]);

		// Send emails
		$transaction->user = $user;
        $transaction->verification_response = $verify['message'] ?? null;

		EmailService::sendRegistrationEmails($transaction);

		// Auto-login (if needed)
		if ($admin !== 'admin') {
			Auth::loginUsingId($user->id);
		}

		return $admin !== 'admin'
			? $this->thankYouPage($transaction, $extraData)
			: $transaction;
		} catch (\Throwable $e) {
			\Log::critical('Payment was verified, but conference callback processing failed before returning a result.', [
				'transaction_id' => $transaction->id,
				'transaction_reference' => $transaction->transid,
				'payment_provider_id' => $transaction->payment_provider_id,
				'transaction_status' => $transaction->fresh()->status,
				'exception_class' => get_class($e),
				'exception_message' => $e->getMessage(),
			]);

			throw $e;
		}
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


	public function thankYouPage($transaction, $extraData){
		$transaction->fresh();

		if(!empty($transaction->edition)){
            return view('frontend.site.conferences.thankyou', compact('transaction', 'extraData'));
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

	public function requery(Request $request)
	{
		$transaction = Transaction::where('transid', $request->transid)->first();
		$provider = $transaction->paymentprovider->slug;

		if ($provider == 'paystack') {
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
