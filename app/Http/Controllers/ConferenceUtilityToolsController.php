<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\TempUser;
use Illuminate\Http\Request;
use App\Models\ConferenceEdition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\HostelAllocationService;
use App\Services\ServicePointAllocationService;

class ConferenceUtilityToolsController extends Controller
{
    public function utilityIndex(){
        $edition = ConferenceEdition::where('id', request()->edition)
            ->first();
        
        $count = TempUser::where('conference_edition_id', $edition->id)
            ->where('fix_status','pending')
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
        $edition = ConferenceEdition::find($request->edition);
        $request['setting'] = $edition;

        if (!$edition) {
            abort(404, 'Edition not found.');
        }

        $tempusers = TempUser::where('conference_edition_id', $edition->id)
            ->whereIn('status', ['Initiated', 'abandoned', 'Complete'])
            ->whereIn('fix_status', ['pending'])
            ->whereDoesntHave('users.payments', function ($query) use ($edition) {
                $query->where('registration_status', 'Complete')
                    ->where('conference_edition_id', $edition->id);
            })
            ->take(10)
            ->get()
            ->unique('email');
            
        if(!$tempusers){
            return back()->with([
                'warning' => 'No Attempted user match'
            ]);
        }

        foreach ($tempusers as $tempuser) {
            \Log::info(['Fixing Started' => $tempuser->email]);

            DB::beginTransaction();

            try {
                $request['email'] = $tempuser->email;
                $validPayment = $this->paystackGetCustomerValidTransactionByEmail($request);
                
                if (isset($validPayment['transaction']) && $validPayment['success'] == true) {
                    $valid = $validPayment['transaction'];
                    $amount = ($valid['amount'] / $request['setting']->registration_fee);
                    $type = $valid['metadata']['type'];
                    $extras = $this->getExtras($type, $request['setting'], $amount);
                    $slot = $extras['slot'] ?? null;
                    $level = $extras['level'] ?? null;
                    $slot_filled = $extras['slot_filled'] ?? null;

                    $data = [
                        'name'  => $tempuser->name,
                        'email' => $tempuser->email,
                        'phone' => $tempuser->phone,
                        'level' => $level,
                        'type' => $type,
                        'chapter_id' => $tempuser->chapter_id ?? null,
                        'chapter' => $tempuser->chapter_id ?? null,
                        'sex' => $tempuser->gender,
                        'registration_status' => 'Complete',
                        'slot' => $slot,
                        'slot_filled' => $slot_filled,
                        'amount_paid' => $amount,
                        'payment_type' => 'PAYSTACK',
                        'transid' => $valid['reference'],
                        'password' => bcrypt($tempuser->phone),
                        'conference_edition_id' => $request['setting']->id
                    ];

                    $user = app('App\Http\Controllers\Controller')->createUser($data);

                    if (!$user) {
                        DB::rollBack();
                        \Log::error('User creation failed for: ' . $tempuser->email);
                        continue;
                    }

                    $payment = app('App\Http\Controllers\Controller')->createPayment($data, $user);

                    if (!$payment) {
                        DB::rollBack();
                        \Log::error('Payment creation failed for: ' . $tempuser->email);
                        continue;
                    }

                    $chapter = Chapter::with('field:id,name')->select('id', 'field_id')->where('id', $data['chapter_id'])->first();
                    $data['field_id'] = $chapter->field->id ?? $data['field_id'] ?? null;
                    $data['setting'] = $request['setting'];

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
                        'food_id' => $service_point['service_point_allocation_id'],
                        'api_response' => isset($valid) ? json_encode($valid) : null,
                    ]);

                    $this->createFamilyId($user, $extras['ledge']);

                    $tempuser->update([
                        'status' => 'Complete',
                        'transid' => $valid['reference'],
                        'amount' => $amount,
                    ]);

                    if ($payment->level == 'Moderator') {
                        $payment->update([
                            'uploaded_by' => $user->id,
                        ]);
                    }

                    DB::commit();
                    $tempuser->fix_status = 'completed';
                    $tempuser->save();
                    // $tempuser->update([
                    //     'fix_status' => 'complete'
                    // ]);

                    \Log::info('Committed: ' . $payment);
                } else {
                    DB::rollBack();

                    $tempuser->fix_status = 'checked';
                    $tempuser->save();
                 
                    \Log::warning('No valid transaction for: ' . $tempuser->email);
                    \Log::warning('tempuser: ' . $tempuser);
                    continue;
                }

                $tempuser->fix_status = 'completed';
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

        return back()->with([
            'message' => 'Attempted registrations processed.'
        ]);
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
                    return isset($tx['metadata']['conference_edition_id'])
                        && $tx['metadata']['conference_edition_id'] == $request['setting']->id
                        && $tx['customer']['email'] == $request->email
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
                isset($transaction['status'], $transaction['metadata']['conference_edition_id']) &&
                $transaction['status'] === 'success' &&
                $transaction['metadata']['conference_edition_id'] == $setting->id
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