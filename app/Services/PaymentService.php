<?php
namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TransactionAllocationField;

class PaymentService {
    public static function generateTransactionId()
    {
        return date('Ymd') . '-' . strtoupper(Str::random(6));
    }

    public static function initializeTransaction(array $data)
    {
        $setting = $data['setting'];
        $plan = $data['plan'];

        $exists = Transaction::where('email', $data['email'])
            ->where('conference_edition_id', $setting->id)
            ->where('status', 'Complete')
            ->exists();

        if ($exists) {
            Log::notice('Conference payment initialization skipped because a completed registration already exists.', [
                'conference_edition_id' => $setting->id,
                'conference_plan_id' => $plan->id,
            ]);

            return [
                'status' => false,
                'message' => 'Registration already exists.'
            ];
        }

        $isModerator = !empty($data['transaction_source']) && $data['transaction_source'] == 'moderator';

        $location =  $data['location'] ?? null;
        $amount = $data['amount'] ?? $plan->price;

        if ($setting->lock_online_payment == 'yes') {
            $location = 'On Site';
        } else {
            if(!$isModerator){
                $paymentProvider = $setting->paymentprovider;
                $location = 'Online';

                if ($paymentProvider && $paymentProvider->customer_pays_provider_charge) {
                    // Gross up the registration amount so that a percentage fee
                    // charged on the full Paystack transaction leaves the
                    // fellowship with the full registration fee.
                    $rateBasisPoints = (int) round(((float) $paymentProvider->provider_charge) * 100);
                    if ($rateBasisPoints >= 10000) {
                        return [
                            'status' => false,
                            'message' => 'Provider charge must be below 100%. Update the payment provider settings.',
                        ];
                    }

                    $baseAmountCents = (int) round(((float) $amount) * 100);
                    $totalAmountCents = $rateBasisPoints > 0
                        ? (int) ceil(($baseAmountCents * 10000) / (10000 - $rateBasisPoints))
                        : $baseAmountCents;

                    $data['provider_charge'] = ($totalAmountCents - $baseAmountCents) / 100;
                    $data['total_amount'] = $totalAmountCents / 100;
                }
            }
        }

        $extras = self::getExtras($plan, $setting, $data['amount']);
        $data['transid'] = strtoupper($setting->ministry->code) .'-'. PaymentService::generateTransactionId();

        try {
            DB::beginTransaction();
            $transaction = Transaction::where('transid', $data['transid'])->first();

            if (!$transaction) {

                $transaction = Transaction::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'gender' => $data['gender'] ?? null,
                    'remarks' => $data['remarks'] ?? null,
                    'conference_edition_id' => $setting->id,
                    'payment_provider_id' => $data['payment_provider_id'] ?? $paymentProvider->id,
                    'provider_charge' => $data['provider_charge'] ?? null,
                    'amount_paid' => $amount,
                    'total_amount' => $data['total_amount'] ?? $amount,
                    'type' => $plan->id,
                    'conference_plan_id' => $plan->id,
                    'transid' => $data['transid'],
                    'status' => 'Initiated',
                    'location' => $location,
                    'registration_status' => 'Pending',
                    'slot' => $extras['slot'],
                    'slot_filled' => $extras['slot_filled'] ?? 0,
                    'level' => $extras['level'],
                    'uploaded_by' => $data['uploaded_by'] ?? null,
                    'registration_user_type' => $extras['slot'] > 1 ? 'moderator' : 'participant',
                ]);
            }

            $allocatableFields = $plan->fields()->pluck('name')->toArray();
            $filteredFields = [];
            
            // Auto-fill field_id if missing but chapter exists
            if (in_array('field_id', $allocatableFields, true) && empty($data['field_id']) && !empty($data['chapter'])) {
                $fieldId = DB::table('chapters')->where('id', $data['chapter'])->value('field_id');
                if ($fieldId) {
                    $filteredFields['field_id'] = $fieldId;
                }
            }

            // Filter and prepare valid fields
            foreach ($allocatableFields as $key) {
                if (in_array($key, $allocatableFields, true) && isset($data[$key])) {
                    $filteredFields[$key] = $data[$key];
                }
            }

            // Insert or update allocation fields
            foreach ($filteredFields as $key => $value) {
                TransactionAllocationField::updateOrCreate(
                    [
                        'transaction_id' => $transaction->id,
                        'key' => $key,
                    ],
                    [
                        'value' => is_array($value) ? implode(', ', array_map('strval', $value)) : $value,
                    ]
                );
            }

            DB::commit();

            Log::info('Conference payment transaction initialized.', [
                'transaction_id' => $transaction->id,
                'transaction_reference' => $transaction->transid,
                'conference_edition_id' => $setting->id,
                'conference_plan_id' => $plan->id,
                'payment_provider_id' => $transaction->payment_provider_id,
                'amount_paid' => $transaction->amount_paid,
                'provider_charge' => $transaction->provider_charge,
                'total_amount' => $transaction->total_amount,
            ]);

            return [
                'status' => true,
                'data' => $transaction->load('allocationFields')
            ];
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Conference payment transaction initialization rolled back.', [
                'transaction_reference' => $data['transid'] ?? null,
                'conference_edition_id' => $setting->id ?? null,
                'conference_plan_id' => $plan->id ?? null,
                'payment_provider_id' => $data['payment_provider_id'] ?? null,
                'amount_paid' => $amount ?? null,
                'total_amount' => $data['total_amount'] ?? $amount ?? null,
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Transaction initialization failed: ' . $e->getMessage(),
            ];
        }
    }

    public static function getExtras($plan, $setting, $amount = null)
    {
        $data['slot'] = 1;
        $data['slot_filled'] = 1;

        if($plan->type == 'multiple'){
            $data['slot'] = $amount / $plan->price;
        }

        $data['level'] = $plan->level;
        $data['ledge'] = $setting->reg_prefix . strtoupper(substr($plan->level, 0, 1)) . '-';

        return $data;
    }

    public static function createUser($transaction)
    {
        $user = User::firstOrNew(['email' => $transaction->email]);

        $user->fill([
            'name' => $transaction->name,
            'phone' => $transaction->phone,
            'gender' => $transaction->gender ?? $user->gender,
            'chapter_id' => $transaction->chapter->id ?? $user->chapter_id,
            'passport' => $user->passport,
            'slug' => Str::slug($transaction->name),
            'role' => $user->role ?? 2,
            'status' => 'active',
        ]);

        if (!$user->exists || $user->isDirty('phone')) {
            $user->password = bcrypt($transaction->phone);
        }

        $user->save();

        return $user;
    }


    public static function generateFamilyId($user, $setting)
    {
        $prefix = strtoupper(substr($setting->reg_prefix ?? 'DEF', 0, 3));
        $prefix = strtoupper($setting->ministry->code) . $prefix .'-'. $user->id;

        return $prefix;
    }

    public static function generateStaffFamilyId($edition, $user)
    {
        return  'GSF' . $edition->reg_prefix . '-' . $user->id;
    }

}
