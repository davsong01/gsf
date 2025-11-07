<?php

namespace App\Services;

use App\Models\Hostel;
use App\Models\Payment;
use App\Models\ConferenceEdition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class HostelAllocationService
{
    public static function assignHostel($transaction, $newData = [])
    {
        $defaultResponse = [
            'status' => false,
            'message' => 'Hostel could not be allocated',
            'hostel_id' => null,
            'hostel_allocation_number' => null,
            'hostel_allocation_type' => null,
            'hostel_name' => null,
        ];

        try {
            $setting = $transaction->edition;
            $level = $transaction->level === 'Moderator' ? 'Participant' : $transaction->level;
            $gender = $newData['sex'] ?? $transaction->gender;
            $conference_edition_id = $transaction->conference_edition_id;
            
            DB::beginTransaction();

            // --- CASE 1: Admin manually set hostel ---
            if (!empty($newData['new_hostel_id'])) {
                $hostel = Hostel::where('id', $newData['new_hostel_id'])
                    ->where('conference_edition_id', $conference_edition_id)
                    ->where('type', $gender)
                    ->whereRaw('allocation < capacity')
                    ->first();

                if (!$hostel) {
                    DB::rollBack();
                    return [
                        ...$defaultResponse,
                        'message' => 'Selected hostel not available or already full.',
                    ];
                }

                $allocationNumber = $hostel->allocation + 1;
                $hostel->update(['allocation' => $allocationNumber]);

                DB::commit();
                return [
                    ...$defaultResponse,
                    'status' => true,
                    'message' => 'Hostel allocated successfully (admin).',
                    'hostel_id' => $hostel->id,
                    'hostel_name' => $hostel->name,
                    'hostel_allocation_number' => $allocationNumber,
                    'hostel_allocation_type' => 'admin',
                ];
            }

            // --- CASE 2: Automatic allocation ---
            $allocationType = $setting->hostel_assignment_type ?? null;
            $chapterField = $transaction->allocationFields->where('key', 'chapter')->first();
            $fieldField = $transaction->allocationFields->where('key', 'field_id')->first();

            $chapter_id = $chapterField->value ?? null;
            $field_id = $fieldField->value ?? null;
            $hostel = null;

            if (in_array($level, ['Official', 'Medical'])) {
                $hostel = Hostel::where([
                    'level' => $level,
                    'conference_edition_id' => $conference_edition_id,
                ])->first();

                if (!$hostel) {
                    DB::rollBack();
                    return [
                        ...$defaultResponse,
                        'message' => "No hostel found for {$level}.",
                    ];
                }
            } else {
                switch ($allocationType) {
                    case 'full-random':
                        $hostel = Hostel::where([
                            'type' => $gender,
                            'conference_edition_id' => $conference_edition_id,
                        ])
                            ->whereRaw('allocation < capacity')
                            ->inRandomOrder()
                            ->first();
                        break;

                    case 'random':
                        $hostel = Hostel::where([
                            'type' => $gender,
                            'level' => $level,
                            'conference_edition_id' => $conference_edition_id,
                        ])
                            ->whereRaw('allocation < capacity')
                            ->inRandomOrder()
                            ->first();
                        break;

                    case 'based_on_chapter':
                        $hostel = Hostel::where([
                            'type' => $gender,
                            'conference_edition_id' => $conference_edition_id,
                        ])
                            ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [json_encode((string) $chapter_id)])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_field':
                        $hostel = Hostel::where([
                            'type' => $gender,
                            'conference_edition_id' => $conference_edition_id,
                        ])
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [json_encode((string) $field_id)])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_chapter_with_category':
                        $hostel = Hostel::where([
                            'type' => $gender,
                            'conference_edition_id' => $conference_edition_id,
                        ])
                            ->where('level', $level)
                            ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [json_encode((string) $chapter_id)])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_field_with_category':
                        $hostel = Hostel::where([
                            'type' => $gender,
                            'conference_edition_id' => $conference_edition_id,
                        ])
                            ->where('level', $level)
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [json_encode((string) $field_id)])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;
                }

                // --- fallback: random allocation ---
                if (!$hostel) {
                    $hostel = Hostel::where([
                        'type' => $gender,
                        'level' => $level,
                        'conference_edition_id' => $conference_edition_id,
                    ])
                        ->whereRaw('allocation < capacity')
                        ->inRandomOrder()
                        ->first();

                    if (!$hostel) {
                        DB::rollBack();
                        return [
                            ...$defaultResponse,
                            'message' => 'No available hostel found for your category or fallback type.',
                        ];
                    }

                    $allocationType = 'fallback-random';
                }
            }

            // --- Assign hostel ---
            $allocationNumber = $hostel->allocation + 1;
            $hostel->update(['allocation' => $allocationNumber]);

            $hostelNumber = self::generateHostelNumber($hostel);

            DB::commit();
            return [
                ...$defaultResponse,
                'status' => true,
                'message' => 'Hostel allocated successfully.',
                'hostel_id' => $hostel->id,
                'hostel_name' => $hostel->name,
                'hostel_allocation_number' => $hostelNumber,
                'hostel_allocation_type' => $allocationType,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Hostel assignment failed', [
                'transaction_id' => $transaction->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'hostel_id' => null,
                'hostel_allocation_number' => null,
                'hostel_allocation_type' => null,
                'hostel_name' => null,
            ];
        }
    }

    static function generateHostelNumber($hostel)
    {
        $name = trim($hostel->name ?? '');
        $allocation = $hostel->allocation ?? 0;

        if (mb_strlen($name) < 3) {
            return 'HST-' . $hostel->id . '-' . $allocation;
        }

        $first_two_letters = mb_substr($name, 0, 2);
        $last_letter = mb_substr($name, -1);

        $number = strtoupper($first_two_letters . $last_letter) . '-' . $allocation;
        $number = str_replace(' ', '', $number);

        // \Log::info(['hostel number' => $number, 'hostel' => $hostel]);
        return $number;
    }


    public static function reduceHostelAllocation($transaction)
    {
        if (isset($transaction->hostel->id) && !empty($transaction->hostel->id)) {
            $current_hostel = Hostel::find($transaction->hostel->id);

            if ($current_hostel->allocation == 0) {
                return;
            } else {
                $transaction->hostel->update(['allocation' => $transaction->hostel->allocation - 1]);
                return $transaction->hostel;
            }
        }
    }

    static function hostelMerger($request)
    {
        return DB::transaction(function () use ($request) {
            $hostel = Hostel::findOrFail($request->deallocate);
            $hostelToMerge = Hostel::findOrFail($request->allocate);

            $amountToReassign = (int) $request->amount;

            // Fetch only the number of payments we intend to reassign
            $payments = Payment::where('hostel_id', $hostel->id)
                ->where('conference_edition_id', $request->edition)
                ->limit($amountToReassign)
                ->get();

            foreach ($payments as $payment) {
                // Increment allocation on the target hostel
                $hostelToMerge->allocation += 1;
                $hostelToMerge->save();

                // Generate a fresh hostel number after saving
                $hostel_number = Self::generateHostelNumber($hostelToMerge);
                \Log::info(['hostel number' => $hostel_number]);
                // Reassign the payment
                $payment->hostel_id = $hostelToMerge->id;
                $payment->hostel_allocation_number = $hostel_number;
                $payment->hostel_allocation_type = 'reassignment';
                $payment->save();
            }

            $hostel->allocation -= $payments->count();
            $hostel->save();

            return true;
        });
    }
}
