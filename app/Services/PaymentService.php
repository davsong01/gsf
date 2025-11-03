<?php
namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionAllocationField;

class PaymentService {
    public static function generateTransactionId()
    {
        return date('Ymd') . '-' . strtoupper(Str::random(6));
    }

    public static function calculateRegistrationAmount($data)
    {
        $type = $data['type'];
        $setting = $data['setting'];

        switch ($type) {
            // Individual Registration
            case '1':
                $expectedAmount = $setting->registration_fee;
                break;

            // Fellowship Registration
            case '2':
                $participants = (int) $data['participants'];
                $expectedAmount = $setting->registration_fee * $participants;
                break;

            // Alumni Registration
            case '3':
                $alumniType = $data->alumni_type;
                $expectedAmount = $setting->$alumniType ?? 0;

                break;
        }

        return $expectedAmount;
    }

    public static function initializeTransaction(array $data)
    {
        $type = $data['type'];
        $setting = $data['setting'];

        $exists = Transaction::where('email', $data['email'])
            ->where('conference_edition_id', $setting->id)
            ->where('status', 'Complete')
            ->exists();

        if ($exists) {
            return [
                'status' => false,
                'message' => 'Registration already exists.'
            ];
        }

        $location =  $data['location'] ?? null;
        $amount = $data['amount'];
        
        if ($setting->lock_online_payment == 'yes') {
            $location = 'On Site';
            $amount = $data['amount'] ?? ($setting->registration_fee ?? 0);
        } else {
            $paymentProvider = $setting->paymentprovider;
            $location = 'Online';

            if ($paymentProvider && $paymentProvider->customer_pays_provider_charge) {
                $provider_charge = $paymentProvider->provider_charge;
                $data['provider_charge'] = $provider_charge;
                $data['total_amount'] = $provider_charge + $data['amount'];
            }
        }

        $extras = self::getExtras($type, $setting, $data['amount']);
        $data['transid'] = strtoupper($setting->ministry->code) .'-'. PaymentService::generateTransactionId();
        
        try {
            DB::beginTransaction();

            $transaction = Transaction::updateOrCreate(
                [
                    'email' => $data['email'],
                    'conference_edition_id' => $setting->id,
                    'provider_charge' => $data['provider_charge'] ?? null,
                ],
                [
                    'name' => $data['name'],
                    'provider_charge' => $data['provider_charge'] ?? null,
                    'payment_provider_id' => $data['payment_provider_id'] ?? $paymentProvider->id,
                    'amount_paid' => $amount,
                    'total_amount' => $data['total_amount'] ?? $amount,
                    'phone' => $data['phone'],
                    'type' => $type,
                    'transid' => $data['transid'] ?? null,
                    'status' => 'Initiated',
                    'gender' => $data['gender'] ?? null,
                    'conference_edition_id' => $setting->id,
                    'location' => $location,
                    'remarks' => $data['remarks'] ?? null,

                    'registration_status' => 'Pending',
                    'slot' => $extras['slot'],
                    'slot_filled' => $extras['slot_filled'] ?? 0,
                    'level' => $extras['level'],
                ]
            );

            $allocatableFields = [
                'field_id',
                'chapter_id',
                'chapter',
                'state',
                'region_id',
                'district_id',
                'assembly_id'
            ];

            $options = !empty($setting->ministry->fields)
                ? $setting->ministry->fields->pluck('name')->toArray()
                : [];

            $filteredFields = [];

            if (in_array('field_id', $options, true) && !isset($data['field_id'])) {
                if (!empty($data['chapter'])) {
                    $fieldId = DB::table('chapters')
                        ->where('id', $data['chapter'])
                        ->value('field_id');

                    if ($fieldId) {
                        $filteredFields['field_id'] = $fieldId;
                    }
                }
            }

            foreach ($allocatableFields as $key) {
                if (in_array($key, $options, true) && isset($data[$key])) {
                    $filteredFields[$key] = $data[$key];
                }
            }

            if (!empty($filteredFields)) {
                foreach ($filteredFields as $key => $value) {
                    TransactionAllocationField::updateOrCreate(
                        [
                            'transaction_id' => $transaction->id,
                            'key' => $key,
                        ],
                        [
                            'value' => $value,
                        ]
                    );
                }
            }

            DB::commit();

            return [
                'status' => true,
                'data' => $transaction->load('allocationFields')
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => 'Transaction initialization failed: ' . $e->getMessage()
            ];
        }
    }

    public static function getExtras($type, $setting, $amount = null)
    {
        if (isset($type) && $type == '1') {
            $data['slot'] = 1;
            $data['ledge'] = $setting->reg_prefix . 'P-';
            $data['level'] = 'Participant';
            $data['slot_filled'] = 1;
        }

        if (isset($type) && $type == '2') {
            $data['slot'] = $amount / $setting->registration_fee;
            $data['ledge'] = $setting->reg_prefix . 'M-';
            $data['level'] = 'Moderator';
            $data['slot_filled'] = 1;
        }

        if (isset($type) && $type == '3') {
            $data['slot'] = 1;
            $data['ledge'] = $setting->reg_prefix . 'A-';
            $data['level'] = 'Alumni';
            $data['slot_filled'] = 1;
        }

        if (isset($type) && $type == '4') {
            $data['slot'] = 1;
            $data['ledge'] = $setting->reg_prefix . 'N-';
            $data['level'] = 'Nec';
            $data['slot_filled'] = 1;
        }
        if (isset($type) && $type == '5') {
            $data['slot'] = 1;
            $data['ledge'] = $setting->reg_prefix . 'O-';
            $data['level'] = 'Official';
            $data['slot_filled'] = 1;
        }

        if (isset($type) && $type == '6') {
            $data['slot'] = 1;
            $data['ledge'] = $setting->reg_prefix . 'C-';
            $data['level'] = 'Choir';
            $data['slot_filled'] = 1;
        }

        if (isset($type) && $type == '7') {
            $data['slot'] = 1;
            $data['ledge'] = $setting->reg_prefix . 'M-';
            $data['level'] = 'Medical';
            $data['slot_filled'] = 1;
        }
        return $data;
    }

    public static function getType($request)
    {
        if ($request->level == 'Participant') {
            $type = 1;
        }
        if ($request->level == 'Moderator') {
            $type = 2;
        }
        if ($request->level == 'Alumni') {
            $type = 3;
        }
        if ($request->level == 'Nec') {
            $type = 4;
        }
        if ($request->level == 'Official') {
            $type = 5;
        }
        if ($request->level == 'Choir') {
            $type = 6;
        }

        if ($request->level == 'Medical') {
            $type = 7;
        }

        return $type;
    }

    public static function createUser($transaction)
    {
        $user = User::firstOrNew(['email' => $transaction->email]);
        
        $user->fill([
            'name' => $transaction->name,
            'phone' => $transaction->phone,
            'sex' => $transaction->gender ?? $user->sex,
            'chapter_id' => $transaction->chapter->id ?? $user->chapter_id,
            'passport' => $user->passport,
            'slug' => Str::slug($transaction->name),
            'role' => $user->role ?? 2,
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