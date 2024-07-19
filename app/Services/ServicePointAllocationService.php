<?php

namespace App\Services;

use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Collection;

class ServicePointAllocationService
{
    public function assignFoodStand($data)
    {
        $setting = activeConferenceEdition();
        $level = $data['level'];
        $sex = $data['sex'];

        $level = $level == 'Moderator' ? 'Participant' : $level;
        

        // $chapter = isset($chapter) && !empty($chapter) ? Chapter::where('id',$chapter)->first
        if (in_array($level, ['Official', 'Medical', 'Official'])) {
            $foodstand = Food::where(['level' => $level, 'conference_edition_id' => $setting->id])->first();
        } else {
            if (isset($setting->random_foodstand) && $setting->random_foodstand == "yes") {
                $foodstand = Food::where(['level' => $level])->whereRaw('allocation < capacity')->inRandomOrder()->first();
            } else {
                if (!empty($chapter)) {
                    $campus = Chapter::where('id', $chapter)->first();
                    $field_id = $campus->field->id ?? null;

                    if ($setting->foodstand_field_assignment == 'yes') {
                        // Others foodstand
                        if (in_array($chapter, [86])) {
                            $foodstand = Food::where(['level' => $level, 'conference_edition_id' => $setting->id])->whereNull('field_id')->whereRaw('allocation < capacity')->orderBy('allocation', 'desc')->first();
                        } else {

                            // According to fields
                            $foodstand = Food::where(['level' => $level, 'conference_edition_id' => $setting->id])->where('field_id', $field_id)->whereRaw('allocation < capacity')->orderBy('allocation', 'desc')->first();
                        }
                    } else {
                        $foodstand = Food::where(['level' => $level, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->orderBy('allocation', 'desc')->first();
                    }
                } else {
                    $foodstand = Food::where(['level' => $level, 'conference_edition_id' => $setting->id])->whereRaw('allocation < capacity')->orderBy('allocation', 'desc')->first();
                }
            }
        }

        if (isset($foodstand) && !empty($foodstand)) {
            $foodstand->update(['allocation' => $foodstand->allocation + 1]);
        }

        return $foodstand ?? null;
    }
}
