<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\TempUser;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\ConferenceEdition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\HostelAllocationService;
use App\Services\ServicePointAllocationService;

class ConferenceUtilityToolsController extends Controller
{
    public function utilityIndex(){
        $edition = ConferenceEdition::where('id', request()->edition)
            ->first();

        $count = Transaction::where('conference_edition_id', $edition->id)
            ->whereNotIn('status',['Complete'])
            // ->whereDoesntHave('users.payments', function ($query) use ($edition) {
            //     $query->where('registration_status', 'Complete')
            //         ->where('conference_edition_id', $edition->id);
            // })
            ->count();

        if(auth()->user()->conference_role == 'superadmin'){
            return view('conference_management.admin.editions.utility_index', compact('edition','count'));
        }
    }


    public function fixAttemptedRegistration(Request $request)
    {
        ini_set('max_execution_time', 600);

        $edition = activeConferenceEdition();
        $request['setting'] = $edition;

        if (!$edition) {
            abort(404, 'Edition not found.');
        }

        $tempusers = Transaction::where('conference_edition_id', $edition->id)
            ->whereIn('status', ['Initiated', 'abandoned'])
            ->where('transid', '!=', 'old_transid')
            ->whereNull('fix_status')
            ->get()
            ->unique('email');
        
        if(!$tempusers){
            return back()->with([
                'warning' => 'No Attempted user match'
            ]);
        }

        foreach ($tempusers as $tempuser) {
            Log::info(['Attempted Fixing Started' => $tempuser->email]);
            $adminId = auth()->user()->id ?? 0;

            try {
                $request['email'] = $tempuser->email;
                $validPayment = $this->paystackGetCustomerValidTransactionByEmail($request);

                if (isset($validPayment['transaction']) && $validPayment['success'] == true) {
                    // Go and resolve this transaction
                    $tempuser->update([
                        'old_transid' => $tempuser->transid,
                        'transid' => $validPayment['transaction']['reference']
                    ]);

                    $transactionController = new TransactionController();
                    $fakeRequest = new Request();
                    $fakeRequest->merge([
                        'reference' => $validPayment['transaction']['reference'],
                        'setting' => $request['setting']
                    ]);

                    $resolve = $transactionController->resolveTransaction($tempuser, true);

                    if($resolve['status']){
                        $tempuser->update([
                            'fix_status' => 'complete',
                        ]);
                    }
                } else {

                    $tempuser->fix_status = 'closed';

                    $tempuser->resolved_at = now();
                    $tempuser->resolved_by = $adminId ;

                    $tempuser->save();

                    Log::warning('No valid transaction for: ' . $tempuser->email);
                    Log::warning('Transaction: ' . $tempuser);
                    continue;
                }



                $tempuser->save();
            } catch (\Throwable $e) {
                DB::rollBack();
                \Log::error('Exception while processing: ' . $tempuser->email, [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]);
            }
        }

        dd('Done processing attempted registrations.');
    }


    public function paystackGetCustomerValidTransactionByEmail(Request $request)
    {
        try {
            $setting = $request['setting'] ?? activeConferenceEdition();

            $paystackSecretKey = $setting->PAYSTACK_SECRET_KEY;

            // Step 1: Get customer ID using email
            $customerResponse = Http::withToken($paystackSecretKey)
                ->get("https://api.paystack.co/customer/{$request->email}");

            if (!$customerResponse->ok() || !$customerResponse->json('data.id')) {
                return [
                    'success' => false,
                    'message' => 'Customer not found.'
                ];
            }

            $customerId = $customerResponse->json('data.id');

            // Step 2: Get transactions for this customer ID
            $transactionsResponse = Http::withToken($paystackSecretKey)
                ->get("https://api.paystack.co/transaction?customer={$customerId}");

            if (!$transactionsResponse->ok()) {
                return [
                    'success' => false,
                    'message' => 'Could not fetch transactions.'
                ];
            }

            $transactions = $transactionsResponse->json('data');

            // \Log::info(['transactions' => $transactions]);
            $filtered = collect($transactions)
                ->filter(function ($tx) use ($request) {
                    $result = $this->getSuccessFullTransaction($tx, $request['setting']);
                    // return isset($tx['metadata']['conference_edition_id'])
                    //     && ($tx['metadata']['conference_edition_id'] == $request['setting']->id || Str::contains($tx['reference'], 'GYF'))
                    //     && $tx['customer']['email'] == $request->email
                    //     && $result['status'] === true;
                    $ministryCode = strtoupper($request['setting']->ministry->code);
                    return Str::contains($tx['reference'], $ministryCode)
                        && $tx['customer']['email'] === $request->email
                        && Carbon::parse($tx['paid_at'])->gte(Carbon::parse('2025-11-01 00:00:00'))
                        && $result['status'] === true;
                })
                ->map(function ($tx) use ($request) {
                    return $this->getSuccessFullTransaction($tx, $request['setting'])['transaction'];
                })
                ->first();

            return [
                'success' => true,
                'transaction' => $filtered
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(). ' Line'. $e->getLine() .' File' . $e->getFile()
            ];
        }
    }


    public function getSuccessFullTransaction($data, $setting)
    {
        if (isset($data['transactions']) && is_array($data['transactions'])) {
            $transactions = $data['transactions'];
        } else {
            $transactions = is_array($data) && array_is_list($data) ? $data : [$data];
        }

        foreach ($transactions as $transaction) {
            if (
                // isset($transaction['status'], $transaction['metadata']['conference_edition_id']) &&
                $transaction['status'] === 'success'
                // &&
                // $transaction['metadata']['conference_edition_id'] == $setting->id
            ) {
                return [
                    'status' => true,
                    'transaction' => $transaction,
                ];
            }
        }

        return [
            'status' => false,
        ];
    }
}
