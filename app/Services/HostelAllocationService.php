<?php

namespace App\Services;

use App\Models\Hostel;
use App\Models\Payment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class HostelAllocationService
{
    // static function assignHostel($data)
    // {
    //     $setting = activeConferenceEdition();

    //     $level = $data['level'] == 'Moderator' ? 'Participant' : $data['level'];
    //     $sex = $data['sex'];

    //     $res = [
    //         'hostel_id' => null,
    //         'hostel_allocation_number' => null,
    //         'hostel_allocation_type' => null,
    //         'hostel_name' => null
    //     ];

    //     if (in_array($level, ['Official', 'Medical', 'Official'])) {
    //         $hostel = Hostel::where(['level' => $level, 'conference_edition_id' => $setting->id])->first();
    //     } else {
    //         // 'full-random' => 'Fully Randomized (Gender Exclusive)' - in random order, diferently for male and female, irrespective of category/level,
    //         // 'random' => 'Random (Category Exclusive)' - in random order, differently for male and female, and for levels,
    //         // 'based_on_chapter' => 'Based On Chapter (Category Exclusive) - based on the chapter and differently for levels and gender',
    //         // 'based_on_field' => 'Based On Field (Category Exclusive) - based on the field and differently for levels and gender',
    //         //  'based_on_chapter_with_category' => 'Based On Chapter With (Category Inclusive) - based on the chapter, irrespective of category/level',
    //         // 'based_on_field_with_category' => 'Based On Field (Category Inclusive) - based on the field ,irrespective of category/level',

    //         if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "full-random") {
    //             $allocation_type = $setting->hostel_assignment_type;
    //             $hostel = Hostel::where(['type' => $sex, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
    //         }

    //         if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "random") {
    //             $allocation_type = $setting->hostel_assignment_type;
    //             $hostel = Hostel::where(['type' => $sex, 'level' => $level, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
    //         }

    //         if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "based_on_chapter") {
    //             $allocation_type = $setting->hostel_assignment_type;

    //             $chapter_id_json = json_encode((string) $data['chapter']);
    //             $hostel = Hostel::where([
    //                 'type' => $sex,
    //                 'conference_edition_id' => $setting->id,
    //             ])
    //             ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [$chapter_id_json])
    //             ->whereRaw('allocation < capacity')
    //             ->first();
    //         }

    //         if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "based_on_field") {
    //             $allocation_type = $setting->hostel_assignment_type;
    //             $field_id_json = json_encode((string) $data['field_id']);

    //             $hostel = Hostel::where([
    //                 'type' => $sex,
    //                 'conference_edition_id' => $setting->id,
    //             ])
    //             ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
    //             ->whereRaw('allocation < capacity')
    //             ->first();                
    //         }

    //         if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "based_on_chapter_with_category") {
    //             $allocation_type = $setting->hostel_assignment_type;

    //             $field_id_json = json_encode((string) $data['field_id']);
    //             $hostel = Hostel::where([
    //                 'type' => $sex,
    //                 'level' => $level,
    //                 'conference_edition_id' => $setting->id,
    //             ])
    //                 ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
    //                 ->whereRaw('allocation < capacity')
    //                 ->first();
    //         }

    //         if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "based_on_field_with_category") {
    //             $allocation_type = $setting->hostel_assignment_type;

    //             $field_id_json = json_encode((string) $data['field_id']);
    //             $hostel = Hostel::where([
    //                 'type' => $sex,
    //                 'level' => $level,
    //                 'conference_edition_id' => $setting->id,
    //             ])
    //                 ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
    //                 ->whereRaw('allocation < capacity')
    //                 ->first();
    //         }

    //         if(empty($hostel)){
    //             $allocation_type = 'HOS-'.$setting->hostel_assignment_type;
    //             $hostel = Hostel::where(['level' => $level, 'type' => $sex, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
    //         }

    //         if (isset($hostel) && !empty($hostel)) {
    //             $allocation_number = $hostel->allocation + 1;
    //             $hostel->update(['allocation' => $allocation_number]);

    //             $hostel_number = Self::generateHostelNumber($hostel);

    //             $res = [
    //                 'hostel_id' => $hostel->id,
    //                 'hostel_name' => $hostel->name,
    //                 'hostel_allocation_number' => $hostel_number,
    //                 'hostel_allocation_type' => $allocation_type,
    //             ];
    //         }
    //     }

    //     return $res;
    // }
    static function assignHostel($data)
    {
        return DB::transaction(function () use ($data) {
            $setting = activeConferenceEdition();

            $level = $data['level'] == 'Moderator' ? 'Participant' : $data['level'];
            $sex = $data['sex'];

            $res = [
                'hostel_id' => null,
                'hostel_allocation_number' => null,
                'hostel_allocation_type' => null,
                'hostel_name' => null
            ];

            if (in_array($level, ['Official', 'Medical', 'Official'])) {
                $hostel = Hostel::where([
                    'level' => $level,
                    'conference_edition_id' => $setting->id
                ])->first();
            } else {
                $allocation_type = $setting->hostel_assignment_type ?? null;

                switch ($allocation_type) {
                    case 'full-random':
                        $hostel = Hostel::where([
                            'type' => $sex,
                            'conference_edition_id' => $setting->id
                        ])
                            ->whereRaw('allocation < capacity')
                            ->inRandomOrder()
                            ->first();
                        break;

                    case 'random':
                        $hostel = Hostel::where([
                            'type' => $sex,
                            'level' => $level,
                            'conference_edition_id' => $setting->id
                        ])
                            ->whereRaw('allocation < capacity')
                            ->inRandomOrder()
                            ->first();
                        break;

                    case 'based_on_chapter':
                        $chapter_id_json = json_encode((string) $data['chapter']);
                        $hostel = Hostel::where([
                            'type' => $sex,
                            'conference_edition_id' => $setting->id,
                        ])
                            ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [$chapter_id_json])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_field':
                        $field_id_json = json_encode((string) $data['field_id']);
                        $hostel = Hostel::where([
                            'type' => $sex,
                            'conference_edition_id' => $setting->id,
                        ])
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_chapter_with_category':
                        $field_id_json = json_encode((string) $data['field_id']);
                        $hostel = Hostel::where([
                            'type' => $sex,
                            'level' => $level,
                            'conference_edition_id' => $setting->id,
                        ])
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_field_with_category':
                        $field_id_json = json_encode((string) $data['field_id']);
                        $hostel = Hostel::where([
                            'type' => $sex,
                            'level' => $level,
                            'conference_edition_id' => $setting->id,
                        ])
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;
                }

                if (empty($hostel)) {
                    $allocation_type = 'HOS-' . ($setting->hostel_assignment_type ?? 'unknown');
                    $hostel = Hostel::where([
                        'level' => $level,
                        'type' => $sex,
                        'conference_edition_id' => $setting->id
                    ])
                        ->whereRaw('allocation < capacity')
                        ->inRandomOrder()
                        ->first();
                }

                if (!empty($hostel)) {
                    $allocation_number = $hostel->allocation + 1;
                    $hostel->update(['allocation' => $allocation_number]);

                    $hostel_number = Self::generateHostelNumber($hostel);

                    $res = [
                        'hostel_id' => $hostel->id,
                        'hostel_name' => $hostel->name,
                        'hostel_allocation_number' => $hostel_number,
                        'hostel_allocation_type' => $allocation_type,
                    ];
                }
            }

            return $res;
        });
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

        \Log::info(['hostel number' => $number, 'hostel' => $hostel]);
        return $number;
    }


    static function repairHostelAllocation($edition_id){
        $hostels = Hostel::where('conference_edition_id', $edition_id)->get();

        foreach($hostels as $hostel){
            $payments = $hostel->payments()->count();
            if($hostel->allocation == $payments){
                continue;
            }
            if($hostel->allocation > $payments){
                $hostel->update(['allocation' => $payments]);
                continue;
            }
            if($hostel->allocation < $payments){
                $hostel->update(['allocation' => $payments]);
                continue;
            }
            $hostel->update(['allocation' => $payments]);
        }

        return true;
    }

    // static function hostelMerger($request){
    //     $hostel = Hostel::find($request->deallocate);
    //     $hostelToMerge = Hostel::find($request->allocate);

    //     $payments = Payment::where('hostel_id', $hostel->id)->where('conference_edition_id', $request->edition)->get();

    //     foreach ($payments as $payment) {
    //         // depopulte hostel
    //         $hostel->allocation = $hostel->allocation - 1;
    //         $hostel->save();
    //         // populate hosteltomerge
    //         $hostelToMerge->allocation = $hostelToMerge->allocation + 1;
    //         $hostelToMerge->save();
    //         $hostelToMerge->refresh();
    //         // generate hostel number
    //         $hostel_number = Self::generateHostelNumber($hostelToMerge);

    //         // update payment
    //         $payment->hostel_id = $hostelToMerge->id;
    //         $payment->hostel_allocation_number = $hostel_number;
    //         $payment->hostel_allocation_type = 'reassignment';
    //         $payment->save();

    //     }

    //     return true;
    // }

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
