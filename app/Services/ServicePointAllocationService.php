<?php

namespace App\Services;

use App\Models\Food;
use App\Models\Payment;
use App\Models\ConferenceEdition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class ServicePointAllocationService
{
    // static function assignFoodStand($data)
    // {
    //     return DB::transaction(function () use ($data) {
    //         $setting = $data['setting'] ?? activeConferenceEdition();

    //         $level = $data['level'] == 'Moderator' ? 'Participant' : $data['level'];
    //         $gender = $data['gender'];

    //         $res = [
    //             'service_point_allocation_id' => null,
    //             'service_point_allocation_number' => null,
    //             'service_point_allocation_type' => null,
    //             'service_point_allocation_name' => null
    //         ];

    //         // Check if food_id is provided in the data
    //         if (isset($data['new_food_id']) && !empty($data['new_food_id'])) {
    //             $food = Food::where('id', $data['new_food_id'])->where('conference_edition_id', $data['conference_edition_id'])->first();

    //             if ($food) {
    //                 $allocation_number = $food->allocation + 1;
    //                 $food->update(['allocation' => $allocation_number]);

    //                 $food_number = self::generateServicePointNumber($food);

    //                 $res = [
    //                     'service_point_allocation_id' => $food->id,
    //                     'service_point_allocation_number' => $food_number,
    //                     'service_point_allocation_type' => 'admin',
    //                     'service_point_allocation_name' => $food->name,
    //                 ];
    //             }
    //         } else {
    //             // Continue with the normal food stand allocation logic
    //             if (in_array($level, ['Official', 'Medical'])) {
    //                 $food = Food::where([
    //                     'level' => $level,
    //                     'conference_edition_id' => $setting->id
    //                 ])->first();
    //             } else {
    //                 $allocation_type = $setting->service_point_assignment_type ?? null;

    //                 switch ($allocation_type) {
    //                     case 'full-random':
    //                         $food = Food::where('conference_edition_id', $setting->id)
    //                             ->whereRaw('allocation < capacity')
    //                             ->inRandomOrder()
    //                             ->first();
    //                         break;

    //                     case 'random':
    //                         $food = Food::where([
    //                             'level' => $level,
    //                             'conference_edition_id' => $setting->id
    //                         ])
    //                             ->whereRaw('allocation < capacity')
    //                             ->inRandomOrder()
    //                             ->first();
    //                         break;

    //                     case 'based_on_chapter':
    //                         $chapter_id_json = json_encode((string) $data['chapter']);
    //                         $food = Food::where('conference_edition_id', $setting->id)
    //                             ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [$chapter_id_json])
    //                             ->whereRaw('allocation < capacity')
    //                             ->first();
    //                         break;

    //                     case 'based_on_field':
    //                         $field_id_json = json_encode((string) $data['field_id']);
    //                         $food = Food::where('conference_edition_id', $setting->id)
    //                             ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
    //                             ->whereRaw('allocation < capacity')
    //                             ->first();
    //                         break;

    //                     case 'based_on_chapter_with_category':
    //                         $field_id_json = json_encode((string) $data['field_id']);
    //                         $food = Food::where([
    //                             'level' => $level,
    //                             'conference_edition_id' => $setting->id
    //                         ])
    //                             ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
    //                             ->whereRaw('allocation < capacity')
    //                             ->first();
    //                         break;

    //                     case 'based_on_field_with_category':
    //                         $field_id_json = json_encode((string) $data['field_id']);
    //                         $food = Food::where([
    //                             'type' => $gender,
    //                             'level' => $level,
    //                             'conference_edition_id' => $setting->id
    //                         ])
    //                             ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
    //                             ->whereRaw('allocation < capacity')
    //                             ->first();
    //                         break;

    //                     default:
    //                         $allocation_type = 'SP-' . $allocation_type;
    //                         $food = Food::where([
    //                             'level' => $level,
    //                             'conference_edition_id' => $setting->id
    //                         ])
    //                             ->whereRaw('allocation < capacity')
    //                             ->inRandomOrder()
    //                             ->first();
    //                 }

    //                 if (!empty($food)) {
    //                     $allocation_number = $food->allocation + 1;
    //                     $food->update(['allocation' => $allocation_number]);

    //                     $food_number = self::generateServicePointNumber($food);

    //                     $res = [
    //                         'service_point_allocation_id' => $food->id,
    //                         'service_point_allocation_number' => $food_number,
    //                         'service_point_allocation_type' => $allocation_type,
    //                         'service_point_allocation_name' => $food->name,
    //                     ];
    //                 }
    //             }
    //         }

    //         return $res;
    //     });
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
            $level = $transaction->level === 'Moderator' ? 'Participant' : $transaction->level;
            $gender = $transaction->gender;
            $conference_edition_id = $transaction->conference_edition_id;


            DB::beginTransaction();
;
            // --- CASE 1: Admin manually sets food stand ---
            if (!empty($newData['new_food_id'])) {
                $food = Food::where('id', $newData['new_food_id'])
                    ->where('conference_edition_id', $conference_edition_id)
                    ->whereRaw('allocation < capacity')
                    ->first();

                if (!$food) {
                    DB::rollBack();
                    return [
                        ...$defaultResponse,
                        'message' => 'Selected service point not available or already full.',
                    ];
                }

                $allocationNumber = $food->allocation + 1;
                $food->update(['allocation' => $allocationNumber]);

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

            // --- CASE 2: Automatic allocation ---
            $allocationType = $setting->service_point_assignment_type ?? null;
            $chapterId = $data['chapter'] ?? null;
            $fieldId = $data['field_id'] ?? null;
            $food = null;

            // Handle Official / Medical special levels
            if (in_array($level, ['Official', 'Medical'])) {
                $food = Food::where([
                    'level' => $level,
                    'conference_edition_id' => $conference_edition_id,
                ])->first();

                if (!$food) {
                    DB::rollBack();
                    return [
                        ...$defaultResponse,
                        'message' => "No service point found for {$level}.",
                    ];
                }
            } else {
                switch ($allocationType) {
                    case 'full-random':
                        $food = Food::where('conference_edition_id', $conference_edition_id)
                            ->whereRaw('allocation < capacity')
                            ->inRandomOrder()
                            ->first();
                        break;

                    case 'random':
                        $food = Food::where([
                            'level' => $level,
                            'conference_edition_id' => $conference_edition_id,
                        ])
                            ->whereRaw('allocation < capacity')
                            ->inRandomOrder()
                            ->first();
                        break;

                    case 'based_on_chapter':
                        $food = Food::where('conference_edition_id', $conference_edition_id)
                            ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [json_encode((string) $chapterId)])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_field':
                        $food = Food::where('conference_edition_id', $conference_edition_id)
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [json_encode((string) $fieldId)])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_chapter_with_category':
                        $food = Food::where([
                            'level' => $level,
                            'conference_edition_id' => $conference_edition_id,
                        ])
                            ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [json_encode((string) $chapterId)])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_field_with_category':
                        $food = Food::where([
                            'level' => $level,
                            'conference_edition_id' => $conference_edition_id,
                        ])
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [json_encode((string) $fieldId)])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;
                }

                // --- Fallback allocation ---
                if (!$food) {
                    $food = Food::where([
                        'level' => $level,
                        'conference_edition_id' => $conference_edition_id,
                    ])
                        ->whereRaw('allocation < capacity')
                        ->inRandomOrder()
                        ->first();

                    if (!$food) {
                        DB::rollBack();
                        return [
                            ...$defaultResponse,
                            'message' => 'No available service point found for your category or fallback type.',
                        ];
                    }

                    $allocationType = 'fallback-random';
                }
            }

            // --- Assign service point ---
            $allocationNumber = $food->allocation + 1;
            $food->update(['allocation' => $allocationNumber]);

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
            \Log::error('Service point allocation failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
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

    static function autoAllocateServicePoint($edition_id)
    {
        $payments = Payment::with('user')->whereNull('food_id')->where('conference_edition_id', $edition_id)->get();
        $setting = ConferenceEdition::where('id', $edition_id)->first();
        $data = [];
        $count = 0;
        
        if (!empty($payments)) {
            foreach ($payments as $payment) {
                $count += 1;
                $data['setting'] = $setting;
                $user = $payment->user;
                $data['field_id'] = $user->campus->field->id ?? null;
                $data = array_merge($data, $user->toArray(), $payment->toArray());
                
                $service_point = ServicePointAllocationService::assignFoodStand($data);
                
                if (!empty($service_point)) {
                    $payment->update([
                        'service_point_allocation_number' => $service_point['service_point_allocation_number'],
                        'service_point_allocation_type' => $service_point['service_point_allocation_type'],
                        'food_id' => $service_point['service_point_allocation_id']
                    ]);
                } else {
                    continue;
                }
            }

            return [
                'count' => $count,
            ];
        }
    }

    static function servicePointMerger($request)
    {
        return DB::transaction(function () use ($request) {
            $food = Food::findOrFail($request->deallocate);
            $foodToMerge = Food::findOrFail($request->allocate);

            $amountToReassign = (int) $request->amount;

            // Fetch only the number of payments we intend to reassign
            $payments = Payment::where('food_id', $food->id)
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
}
