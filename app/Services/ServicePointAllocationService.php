<?php

namespace App\Services;

use App\Models\ConferenceEdition;
use App\Models\Food;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

class ServicePointAllocationService
{
    // public static function assignFoodStand($transaction, $newData = [])
    // {
    //     $defaultResponse = [
    //         'status' => false,
    //         'message' => 'Service point could not be allocated.',
    //         'service_point_allocation_id' => null,
    //         'service_point_allocation_number' => null,
    //         'service_point_allocation_type' => null,
    //         'service_point_allocation_name' => null,
    //     ];

    //     try {

    //         $setting = $transaction->edition;
    //         $level = $transaction->level === 'Moderator' ? 'Participant' : $transaction->level;
    //         $conference_edition_id = $transaction->conference_edition_id;

    //         DB::beginTransaction();

    //         // --- CASE 1: Admin manually sets food stand ---
    //         if (!empty($newData['new_food_id']) && $newData['new_food_id'] != $transaction->food_id) {
    //             $food = Food::where('id', $newData['new_food_id'])
    //                 ->where('conference_edition_id', $conference_edition_id)
    //                 ->whereRaw('allocation < capacity')
    //                 ->first();

    //             if (!$food) {
    //                 DB::rollBack();
    //                 return [
    //                     ...$defaultResponse,
    //                     'message' => 'Selected service point not available or it has reached full capacity.',
    //                 ];
    //             }

    //             $allocationNumber = $food->allocation + 1;
    //             $food->update(['allocation' => $allocationNumber]);

    //             DB::commit();
    //             return [
    //                 ...$defaultResponse,
    //                 'status' => true,
    //                 'message' => 'Service point allocated successfully (admin).',
    //                 'service_point_allocation_id' => $food->id,
    //                 'service_point_allocation_number' => self::generateServicePointNumber($food),
    //                 'service_point_allocation_type' => 'admin',
    //                 'service_point_allocation_name' => $food->name,
    //             ];
    //         }

    //         // --- CASE 2: Automatic allocation ---
    //         $allocationType = $setting->service_point_assignment_type ?? null;
    //         $chapterId = $data['chapter'] ?? null;
    //         $fieldId = $data['field_id'] ?? null;
    //         $food = null;

    //         // Handle Official / Medical special levels
    //         if (in_array($level, ['Official', 'Medical'])) {
    //             $food = Food::where([
    //                 'level' => $level,
    //                 'conference_edition_id' => $conference_edition_id,
    //             ])->first();

    //             if (!$food) {
    //                 DB::rollBack();
    //                 return [
    //                     ...$defaultResponse,
    //                     'message' => "No service point found for {$level}.",
    //                 ];
    //             }
    //         } else {
    //             switch ($allocationType) {
    //                 case 'full-random':
    //                     $food = Food::where('conference_edition_id', $conference_edition_id)
    //                         ->whereRaw('allocation < capacity')
    //                         ->inRandomOrder()
    //                         ->first();
    //                     break;

    //                 case 'random':
    //                     $food = Food::where([
    //                         'level' => $level,
    //                         'conference_edition_id' => $conference_edition_id,
    //                     ])
    //                         ->whereRaw('allocation < capacity')
    //                         ->inRandomOrder()
    //                         ->first();
    //                     break;

    //                 case 'based_on_chapter':
    //                     $food = Food::where('conference_edition_id', $conference_edition_id)
    //                         ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [json_encode((string) $chapterId)])
    //                         ->whereRaw('allocation < capacity')
    //                         ->first();
    //                     break;

    //                 case 'based_on_field':
    //                     $food = Food::where('conference_edition_id', $conference_edition_id)
    //                         ->whereRaw('JSON_CONTAINS(field_ids, ?)', [json_encode((string) $fieldId)])
    //                         ->whereRaw('allocation < capacity')
    //                         ->first();
    //                     break;

    //                 case 'based_on_chapter_with_category':
    //                     $food = Food::where([
    //                         'level' => $level,
    //                         'conference_edition_id' => $conference_edition_id,
    //                     ])
    //                         ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [json_encode((string) $chapterId)])
    //                         ->whereRaw('allocation < capacity')
    //                         ->first();
    //                     break;

    //                 case 'based_on_field_with_category':
    //                     $food = Food::where([
    //                         'level' => $level,
    //                         'conference_edition_id' => $conference_edition_id,
    //                     ])
    //                         ->whereRaw('JSON_CONTAINS(field_ids, ?)', [json_encode((string) $fieldId)])
    //                         ->whereRaw('allocation < capacity')
    //                         ->first();
    //                     break;
    //             }

    //             // --- Fallback allocation ---
    //             if (!$food) {
    //                 $food = Food::where([
    //                     'level' => $level,
    //                     'conference_edition_id' => $conference_edition_id,
    //                 ])
    //                     ->whereRaw('allocation < capacity')
    //                     ->inRandomOrder()
    //                     ->first();

    //                 if (!$food) {
    //                     DB::rollBack();
    //                     return [
    //                         ...$defaultResponse,
    //                         'message' => 'No available service point found for your category or fallback type.',
    //                     ];
    //                 }

    //                 $allocationType = 'fallback-random';
    //             }
    //         }

    //         // --- Assign service point ---
    //         $allocationNumber = $food->allocation + 1;
    //         $food->update(['allocation' => $allocationNumber]);

    //         DB::commit();
    //         return [
    //             ...$defaultResponse,
    //             'status' => true,
    //             'message' => 'Service point allocated successfully.',
    //             'service_point_allocation_id' => $food->id,
    //             'service_point_allocation_number' => self::generateServicePointNumber($food),
    //             'service_point_allocation_type' => $allocationType,
    //             'service_point_allocation_name' => $food->name,
    //         ];
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error('Service point allocation failed', [
    //             'error' => $e->getMessage(),
    //         ]);

    //         return [
    //             'status' => false,
    //             'message' => 'Error: ' . $e->getMessage().'File: '.$e->getFile() . ' Line: '.$e->getLine(),
    //             'service_point_allocation_id' => null,
    //             'service_point_allocation_number' => null,
    //             'service_point_allocation_type' => null,
    //             'service_point_allocation_name' => null,
    //         ];
    //     }
    // }
    public static function assignFoodStand($transaction, $newData = [])
    {
        $defaultResponse = [
            'status' => false,
            'message' => 'Service point could not be allocated.',
            'service_point_allocation_id' => null,
            'service_point_allocation_number' => null,
            'service_point_allocation_type' => null,
            'service_point_allocation_name' => null,
        ];

        try {
            $setting = $transaction->edition;
            $conference_edition_id = $transaction->conference_edition_id;
            $conference_plan_id = $transaction->conference_plan_id;

            $allocationType = $setting->service_point_assignment_type ?? 'random';

            $chapterId = $transaction->allocationFields
                ->first(fn($f) => in_array($f->key, ['chapter', 'chapter_id'], true))->value ?? null;
            $fieldId = $transaction->allocationFields
                ->first(fn($f) => $f->key === 'field_id')->value ?? null;

            DB::beginTransaction();

            // --- CASE 1: Admin manually sets food ---
            if (!empty($newData['new_food_id']) && $newData['new_food_id'] != $transaction->food_id) {
                $food = Food::where([
                    'id' => $newData['new_food_id'],
                    'conference_edition_id' => $conference_edition_id,
                    'conference_plan_id' => $conference_plan_id,
                ])
                ->whereColumn('allocation', '<', 'capacity')
                ->lockForUpdate()
                ->first();

                if (!$food) {
                    DB::rollBack();
                    return [
                        ...$defaultResponse,
                        'message' => 'Selected service point not available or full.',
                    ];
                }

                $food->increment('allocation');

                DB::commit();
                return [
                    ...$defaultResponse,
                    'status' => true,
                    'message' => 'Service point allocated successfully (admin).',
                    'service_point_allocation_id' => $food->id,
                    'service_point_allocation_number' => self::generateServicePointNumber($food),
                    'service_point_allocation_type' => 'admin',
                    'service_point_allocation_name' => $food->name,
                ];
            }

            // --- BASE QUERY ---
            $baseQuery = Food::where([
                'conference_edition_id' => $conference_edition_id,
                'conference_plan_id' => $conference_plan_id,
            ])
            ->whereColumn('allocation', '<', 'capacity');

            $food = null;

            // --- ALLOCATION TYPES ---
            switch ($allocationType) {
                case 'full-random':
                    $food = (clone $baseQuery)
                        ->inRandomOrder()
                        ->lockForUpdate()
                        ->first();
                    break;

                case 'random':
                    $food = (clone $baseQuery)
                        ->orderByRaw('(allocation / NULLIF(capacity,0)) ASC')
                        ->lockForUpdate()
                        ->first();
                    break;

                case 'based_on_chapter':
                    if ($chapterId) {
                        $food = (clone $baseQuery)
                            ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [json_encode((string)$chapterId)])
                            ->orderByRaw('(allocation / NULLIF(capacity,0)) ASC')
                            ->lockForUpdate()
                            ->first();
                    }
                    break;

                case 'based_on_field':
                    if ($fieldId) {
                        $food = (clone $baseQuery)
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [json_encode((string)$fieldId)])
                            ->orderByRaw('(allocation / NULLIF(capacity,0)) ASC')
                            ->lockForUpdate()
                            ->first();
                    }
                    break;

                case 'based_on_chapter_with_category':
                    if ($chapterId) {
                        $food = (clone $baseQuery)
                            ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [json_encode((string)$chapterId)])
                            ->orderByRaw('(allocation / NULLIF(capacity,0)) ASC')
                            ->lockForUpdate()
                            ->first();
                    }
                    break;

                case 'based_on_field_with_category':
                    if ($fieldId) {
                        $food = (clone $baseQuery)
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [json_encode((string)$fieldId)])
                            ->orderByRaw('(allocation / NULLIF(capacity,0)) ASC')
                            ->lockForUpdate()
                            ->first();
                    }
                    break;
            }

            // --- FALLBACK ---
            if (!$food) {
                $food = (clone $baseQuery)
                    ->orderByRaw('(allocation / NULLIF(capacity,0)) ASC')
                    ->lockForUpdate()
                    ->first();

                if (!$food) {
                    DB::rollBack();
                    return [
                        ...$defaultResponse,
                        'message' => 'No available service point found for your conference plan.',
                    ];
                }

                $allocationType = 'fallback-random';
            }

            // --- ALREADY ASSIGNED ---
            if ($food->id == $transaction->food_id) {
                DB::commit();
                return [
                    'status' => true,
                    'message' => 'Service point already assigned.',
                    'service_point_allocation_id' => $food->id,
                    'service_point_allocation_name' => $food->name,
                    'service_point_allocation_type' => $allocationType,
                ];
            }

            // --- ASSIGN SERVICE POINT ---
            $food->increment('allocation');

            DB::commit();

            return [
                ...$defaultResponse,
                'status' => true,
                'message' => 'Service point allocated successfully.',
                'service_point_allocation_id' => $food->id,
                'service_point_allocation_number' => self::generateServicePointNumber($food),
                'service_point_allocation_type' => $allocationType,
                'service_point_allocation_name' => $food->name,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Service point allocation failed', [
                'transaction_id' => $transaction->id ?? null,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return [
                'status' => false,
                'message' => $e->getMessage().' File: '.$e->getFile().' Line: '.$e->getLine(),
                'service_point_allocation_id' => null,
                'service_point_allocation_number' => null,
                'service_point_allocation_type' => null,
                'service_point_allocation_name' => null,
            ];
        }
    }


    static function generateServicePointNumber($point)
    {
        // first 2 letters of point name and last letter plus current allocation
        $first_two_letters = substr($point->name, 0, 2);
        $last_letter = substr($point->name, -1);

        $number = str_replace(' ', '', strtoupper($first_two_letters . $last_letter) . '-' . $point->allocation);
        return $number;
    }

    static function repairServicePointAllocation($edition_id)
    {
        $foods = Food::where('conference_edition_id', $edition_id)->get();

        foreach ($foods as $food) {
            $payments = $food->payments()->count();
            if ($food->allocation == $payments) {
                continue;
            }
            if ($food->allocation > $payments) {
                $food->update(['allocation' => $payments]);
                continue;
            }
            if ($food->allocation < $payments) {
                $food->update(['allocation' => $payments]);
                continue;
            }
            $food->update(['allocation' => $payments]);
        }

        return true;
    }

    static function servicePointMerger($request)
    {
        return DB::transaction(function () use ($request) {
            $food = Food::findOrFail($request->deallocate);
            $foodToMerge = Food::findOrFail($request->allocate);

            $amountToReassign = (int) $request->amount;

            // Fetch only the number of payments we intend to reassign
            $payments = Transaction::where('food_id', $food->id)
                ->where('conference_edition_id', $request->edition)
                ->limit($amountToReassign)
                ->get();

            foreach ($payments as $payment) {
                // Increment allocation on the target hostel
                $foodToMerge->allocation += 1;
                $foodToMerge->save();

                // Generate a fresh hostel number after saving
                $food_number = Self::generateServicePointNumber($foodToMerge);
                // Reassign the payment
                $payment->food_id = $foodToMerge->id;
                $payment->service_point_allocation_number = $food_number;
                $payment->service_point_allocation_type = 'reassignment';
                $payment->save();
            }

            $food->allocation -= $payments->count();
            $food->save();

            return true;
        });
    }

    public static function reduceFoodStandAllocation($food){
        if (!$food) {
            return null;
        }

        if ($food->allocation <= 0) {
            return $food;
        }

        $food->decrement('allocation');

        return $food;
    }
}
