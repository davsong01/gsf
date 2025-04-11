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

        $level = $data['level'] == 'Moderator' ? 'Participant' : $data['level'];
        $sex = $data['sex'];
        
        $res = [
            'hostel_id' => null,
            'hostel_allocation_number' => null,
            'hostel_allocation_type' => null,
            'hostel_name' => null
        ];
        
        if (in_array($level, ['Official', 'Medical', 'Official'])) {
            $hostel = Hostel::where(['level' => $level, 'conference_edition_id' => $setting->id])->first();
        } else {
            // 'full-random' => 'Fully Randomized (Gender Exclusive)' - in random order, diferently for male and female, irrespective of category/level,
            // 'random' => 'Random (Category Exclusive)' - in random order, differently for male and female, and for levels,
            // 'based_on_chapter' => 'Based On Chapter (Category Exclusive) - based on the chapter and differently for levels and gender',
            // 'based_on_field' => 'Based On Field (Category Exclusive) - based on the field and differently for levels and gender',
            //  'based_on_chapter_with_category' => 'Based On Chapter With (Category Inclusive) - based on the chapter, irrespective of category/level',
            // 'based_on_field_with_category' => 'Based On Field (Category Inclusive) - based on the field ,irrespective of category/level',
            
            if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "full-random") {
                $allocation_type = $setting->hostel_assignment_type;
                $hostel = Hostel::where(['type' => $sex, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
            }

            if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "random") {
                $allocation_type = $setting->hostel_assignment_type;
                $hostel = Hostel::where(['type' => $sex, 'level' => $level, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
            }

            if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "based_on_chapter") {
                $allocation_type = $setting->hostel_assignment_type;

                $chapter_id_json = json_encode((string) $data['chapter']);
                $hostel = Hostel::where([
                    'type' => $sex,
                    'conference_edition_id' => $setting->id,
                ])
                ->whereRaw('JSON_CONTAINS(chapter_ids, ?)', [$chapter_id_json])
                ->whereRaw('allocation < capacity')
                ->first();
            }

            if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "based_on_field") {
                $allocation_type = $setting->hostel_assignment_type;
                $field_id_json = json_encode((string) $data['field_id']);

                $hostel = Hostel::where([
                    'type' => $sex,
                    'conference_edition_id' => $setting->id,
                ])
                ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
                ->whereRaw('allocation < capacity')
                ->first();                
            }

            if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "based_on_chapter_with_category") {
                $allocation_type = $setting->hostel_assignment_type;

                $field_id_json = json_encode((string) $data['field_id']);
                $hostel = Hostel::where([
                    'type' => $sex,
                    'level' => $level,
                    'conference_edition_id' => $setting->id,
                ])
                    ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
                    ->whereRaw('allocation < capacity')
                    ->first();
            }

            if (isset($setting->hostel_assignment_type) && $setting->hostel_assignment_type == "based_on_field_with_category") {
                $allocation_type = $setting->hostel_assignment_type;

                $field_id_json = json_encode((string) $data['field_id']);
                $hostel = Hostel::where([
                    'type' => $sex,
                    'level' => $level,
                    'conference_edition_id' => $setting->id,
                ])
                    ->whereRaw('JSON_CONTAINS(field_ids, ?)', [$field_id_json])
                    ->whereRaw('allocation < capacity')
                    ->first();
            }

            if(empty($hostel)){
                $allocation_type = 'HOS-'.$setting->hostel_assignment_type;
                $hostel = Hostel::where(['level' => $level, 'type' => $sex, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->inRandomOrder()->first();
            }

            if (isset($hostel) && !empty($hostel)) {
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
    }


    static function generateHostelNumber($hostel){
        // first 2 letters of hostel name and last letter plus current allocation
        $first_two_letters = substr($hostel->name, 0, 2);
        $last_letter = substr($hostel->name, -1);

        $number = str_replace(' ', '', strtoupper($first_two_letters . $last_letter) . '-' . $hostel->allocation);
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

}
