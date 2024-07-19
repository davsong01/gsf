<?php

namespace App\Services;

use App\Models\Hostel;
use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\FastExcel;

class HostelAllocationService
{
    static function assignHostel($data)
    {
        $setting = activeConferenceEdition();
        
        $level = $data['level'];
        $sex = $data['sex'];
        $data = [
            'hostel_id' => null,
            'hostel_allocation_number' => null,
            'hostel_allocation_type' => null,
        ];

        if (in_array($level, ['Official', 'Medical', 'Official'])) {
            $hostel = Hostel::where(['level' => $level, 'conference_edition_id' => $setting->id])->first();
        } else {
            // 'full-random' => 'Fully Randomized (Gender Exclusive)' - in random order, diferently for male and female, irrespective of category/level,
            // 'random' => 'Random (Category Exclusive)' - in random order, differently for male and female, and for levels,
            // 'based_on_chapter' => 'Based On Chapter (Category Exclusive) - based on the chapter and differently for levels and gender',
            // 'based_on_field' => 'Based On Field (Category Exclusive) - based on the field and differently for levels and gender',
            $level = $level == 'Moderator' ? 'Participant' : $level;

            if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "full-random") {
                $allocation_type = $setting->hostel_assignment_type;
                $hostel = Hostel::where(['type' => $sex, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
            }

            if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "random") {
                $allocation_type = $setting->hostel_assignment_type;
                $hostel = Hostel::where(['type' => $sex, 'level' => $level, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
            }

            if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "based_on_field") {
                $allocation_type = $setting->hostel_assignment_type;
                $hostel = Hostel::where(['type' => $sex, 'conference_edition_id' => $setting->id, 'chapter_id'])
                    ->whereJsonContains('chapter_ids', $data['chapter_id'])
                    ->whereRaw('allocation < capacity')->first();
            }

            if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "based_on_chapter") {
                $allocation_type = $setting->hostel_assignment_type;
                $hostel = Hostel::where(['type' => $sex, 'conference_edition_id' => $setting->id, 'chapter_id'])
                    ->whereJsonContains('field_ids', $data['field_id'])
                    ->whereRaw('allocation < capacity')->first();
            }

            if(empty($hostel)){
                $allocation_type = 'SYSTEM-PICKED';
                $hostel = Hostel::where(['level' => $level, 'type' => $sex, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
            }

            if (isset($hostel) && !empty($hostel)) {
                $allocation_number = $hostel->allocation + 1;
                $hostel->update(['allocation' => $allocation_number]);

                $hostel_number = Self::generateHostelNumber($hostel);

                $data = [
                    'hostel_id' => $hostel->id,
                    'hostel_allocation_number' => $hostel_number,
                    'hostel_allocation_type' => $allocation_type,
                ];
            }
        }

        dd($data, $setting->hostel_assignment_type, $data);
        return $data;
    }


    static function generateHostelNumber($hostel){
        // first 2 letters of hostel name and last letter plus current allocation
        $first_two_letters = substr($hostel->name, 0, 2);
        $last_letter = substr($hostel->name, -1);

        $number = str_replace(' ', '', strtoupper($first_two_letters . $last_letter) . '-' . $hostel->allocation);
        return $number;
    }

}
