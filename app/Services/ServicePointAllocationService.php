<?php

namespace App\Services;

use App\Models\Food;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class ServicePointAllocationService
{
    // static function assignFoodStand($data)
    // {
    //     $setting = activeConferenceEdition();

    //     $level = $data['level'] == 'Moderator' ? 'Participant' : $data['level'];
    //     $sex = $data['sex'];

    //     $res = [
    //         'service_point_allocation_id' => null,
    //         'service_point_allocation_number' => null,
    //         'service_point_allocation_type' => null,
    //         'service_point_allocation_name' => null
    //     ];

    //     $level = $level == 'Moderator' ? 'Participant' : $level;

    //     if (in_array($level, ['Official', 'Medical', 'Official'])) {
    //         $foodstand = Food::where(['level' => $level, 'conference_edition_id' => $setting->id])->first();
    //     } else {
    //         // 'full-random' => 'Fully Randomized (Gender Exclusive)' - in random order, irrespective of category/level
    //         // 'random' => 'Random (Category Exclusive)' - in random order, differently for male and female, and for levels,
    //         // 'based_on_chapter' => 'Based On Chapter (Category Exclusive) - based on the chapter and differently for levels',
    //         // 'based_on_field' => 'Based On Field (Category Exclusive) - based on the field and differently for levels',
    //         //  'based_on_chapter_with_category' => 'Based On Chapter With (Category Inclusive) - based on the chapter, irrespective of category/level',
    //         // 'based_on_field_with_category' => 'Based On Field (Category Inclusive) - based on the field ,irrespective of category/level',
    //         if (isset($setting->service_point_assignment_type) && $setting->service_point_assignment_type == "full-random") {
    //             $allocation_type = $setting->service_point_assignment_type;
    //             $food = Food::where(['conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
    //         }

    //         if (isset($setting->service_point_assignment_type) && $setting->service_point_assignment_type == "random") {
    //             $allocation_type = $setting->service_point_assignment_type;
    //             $food = Food::where(['level' => $level, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
    //         }

    //         if (isset($setting->service_point_assignment_type) && $setting->service_point_assignment_type == "based_on_chapter") {
    //             $allocation_type = $setting->service_point_assignment_type;

    //             $chapter_id_json = json_encode((string) $data['chapter']);
    //             $food = Food::where([
    //                 'conference_edition_id' => $setting->id,
    //             ])
    //                 ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [$chapter_id_json])
    //                 ->whereRaw('allocation < capacity')
    //                 ->first();
    //         }

    //         if (isset($setting->service_point_assignment_type) && $setting->service_point_assignment_type == "based_on_field") {
    //             $allocation_type = $setting->service_point_assignment_type;

    //             $field_id_json = json_encode((string) $data['field_id']);
    //             $food = Food::where([
    //                 'conference_edition_id' => $setting->id,
    //             ])
    //                 ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
    //                 ->whereRaw('allocation < capacity')
    //                 ->first();
    //         }


    //         if (isset($setting->service_point_assignment_type) && $setting->service_point_assignment_type == "based_on_chapter_with_category") {
    //             $allocation_type = $setting->service_point_assignment_type;

    //             $field_id_json = json_encode((string) $data['field_id']);
    //             $food = Food::where([
    //                 'level' => $level,
    //                 'conference_edition_id' => $setting->id,
    //             ])
    //                 ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
    //                 ->whereRaw('allocation < capacity')
    //                 ->first();
    //         }

    //         if (isset($setting->service_point_assignment_type) && $setting->service_point_assignment_type == "based_on_field_with_category") {
    //             $allocation_type = $setting->service_point_assignment_type;

    //             $field_id_json = json_encode((string) $data['field_id']);
    //             $food = Food::where([
    //                 'type' => $sex,
    //                 'level' => $level,
    //                 'conference_edition_id' => $setting->id,
    //             ])
    //                 ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
    //                 ->whereRaw('allocation < capacity')
    //                 ->first();
    //         }

    //         if (empty($food)) {
    //             $allocation_type = 'SP-' . $setting->service_point_assignment_type;
    //             $food = Food::where(['level' => $level, 'conference_edition_id' => $setting->id
    //             ])->whereRaw('allocation < capacity')->inRandomOrder()->first();
    //         }


    //         if (isset($food) && !empty($food)) {
    //             $allocation_number = $food->allocation + 1;
    //             $food->update(['allocation' => $allocation_number]);

    //             $food_number = Self::generateServicePointNumber($food);

    //             $res = [
    //                 'service_point_allocation_id' => $food->id,
    //                 'service_point_allocation_number' => $food_number,
    //                 'service_point_allocation_type' => $allocation_type,
    //                 'service_point_allocation_name' => $food->name,
    //             ];
    //         }

    //         return $res;
    //     }

    // }
    static function assignFoodStand($data)
    {
        return DB::transaction(function () use ($data) {
            $setting = activeConferenceEdition();

            $level = $data['level'] == 'Moderator' ? 'Participant' : $data['level'];
            $sex = $data['sex'];

            $res = [
                'service_point_allocation_id' => null,
                'service_point_allocation_number' => null,
                'service_point_allocation_type' => null,
                'service_point_allocation_name' => null
            ];

            if (in_array($level, ['Official', 'Medical'])) {
                $foodstand = Food::where([
                    'level' => $level,
                    'conference_edition_id' => $setting->id
                ])->first();
            } else {
                $allocation_type = $setting->service_point_assignment_type ?? null;

                switch ($allocation_type) {
                    case 'full-random':
                        $food = Food::where('conference_edition_id', $setting->id)
                            ->whereRaw('allocation < capacity')
                            ->inRandomOrder()
                            ->first();
                        break;

                    case 'random':
                        $food = Food::where([
                            'level' => $level,
                            'conference_edition_id' => $setting->id
                        ])
                            ->whereRaw('allocation < capacity')
                            ->inRandomOrder()
                            ->first();
                        break;

                    case 'based_on_chapter':
                        $chapter_id_json = json_encode((string) $data['chapter']);
                        $food = Food::where('conference_edition_id', $setting->id)
                            ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [$chapter_id_json])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_field':
                        $field_id_json = json_encode((string) $data['field_id']);
                        $food = Food::where('conference_edition_id', $setting->id)
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_chapter_with_category':
                        $field_id_json = json_encode((string) $data['field_id']);
                        $food = Food::where([
                            'level' => $level,
                            'conference_edition_id' => $setting->id
                        ])
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    case 'based_on_field_with_category':
                        $field_id_json = json_encode((string) $data['field_id']);
                        $food = Food::where([
                            'type' => $sex,
                            'level' => $level,
                            'conference_edition_id' => $setting->id
                        ])
                            ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
                            ->whereRaw('allocation < capacity')
                            ->first();
                        break;

                    default:
                        $allocation_type = 'SP-' . $allocation_type;
                        $food = Food::where([
                            'level' => $level,
                            'conference_edition_id' => $setting->id
                        ])
                            ->whereRaw('allocation < capacity')
                            ->inRandomOrder()
                            ->first();
                }

                if (!empty($food)) {
                    $allocation_number = $food->allocation + 1;
                    $food->update(['allocation' => $allocation_number]);

                    $food_number = self::generateServicePointNumber($food);

                    $res = [
                        'service_point_allocation_id' => $food->id,
                        'service_point_allocation_number' => $food_number,
                        'service_point_allocation_type' => $allocation_type,
                        'service_point_allocation_name' => $food->name,
                    ];
                }
            }

            return $res;
        });
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
        $food = Food::where('conference_edition_id', $edition_id)->get();

        foreach ($food as $hostel) {
            $payments = $hostel->payments()->count();
            if ($hostel->allocation == $payments) {
                continue;
            }
            if ($hostel->allocation > $payments) {
                $hostel->update(['allocation' => $payments]);
                continue;
            }
            if ($hostel->allocation < $payments) {
                $hostel->update(['allocation' => $payments]);
                continue;
            }
            $hostel->update(['allocation' => $payments]);
        }

        return true;
    }
}
