<?php

use App\Models\GeneralSetting;
use Illuminate\Support\Carbon;
use App\Models\ConferenceEdition;

if (!function_exists("hostelAssignmentTypes")) {
    function hostelAssignmentTypes()
    {
        return [
            'full-random' => 'Fully Randomized (Gender Exclusive)',
            'random' => 'Random (Category Exclusive)',
            'based_on_chapter' => 'Based On Chapter (Category Exclusive)',
            'based_on_field' => 'Based On Field (Category Exclusive)',
            'based_on_chapter_with_category' => 'Based On Chapter (Category Inclusive)',
            'based_on_field_with_category' => 'Based On Field (Category Inclusive)' 
        ];
    }
}

if (!function_exists("servicePointAssignmentTypes")) {
    function servicePointAssignmentTypes()
    {
        return [
            'full-random' => 'Fully Randomized',
            'random' => 'Random (Category Exclusive)',
            'based_on_chapter' => 'Based On Chapter (Category Exclusive)',
            'based_on_field' => 'Based On Field (Category Exclusive)',
            'based_on_gender' => 'Based On Gender (Category Exclusive)',
            'based_on_chapter_with_category' => 'Based On Chapter (Category Inclusive)',
            'based_on_field_with_category' => 'Based On Field (Category Inclusive)'
        ];
    }
}

if (!function_exists("activeConferenceEdition")) {
    function activeConferenceEdition()
    {
        $conference = ConferenceEdition::where('status', 'active')->where('close_registration', '>', date('Y-m-d'))->first();
        return $conference;
    }
}

if (!function_exists("menu")) {
    function menu(){
        $menu = [
            [
                'route' => 'home.index',
                'name' => 'Directory'
            ],
            [
                'route' => 'people.campuses',
                'name' => 'Campuses'
            ],
            [
                'route' => 'people.alumni',
                'name' => 'Alumni'
            ],
            [
                'route' => 'people.students',
                'name' => 'Members'
            ],
            [
                'route' => 'people.nec',
                'name' => 'NEC'
            ],
            [
                'route' => 'campus.tracker',
                'name' => 'Campus Tracker'
            ],
            // [
            //     'route' => 'people.programs',
            //     'name' => 'Events'
            // ]
        ];
                
        return $menu;
    }
}

if (!function_exists("frontendTemplate")) {
    function frontendTemplate(){
        return GeneralSetting::first()->frontend_template;
    }
}

if (!function_exists("getDayWithSuffix")) {
    function getDayWithSuffix($day)
    {
        if (!in_array(($day % 100), array(11, 12, 13))) {
            switch ($day % 10) {
                case 1:
                    return $day . 'st';
                case 2:
                    return $day . 'nd';
                case 3:
                    return $day . 'rd';
            }
        }
        return $day . 'th';
    }
}

if (!function_exists("formatDates")) {
    function formatDates($start, $end)
    {
        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        $startDay = getDayWithSuffix($startDate->day);
        $endDay = getDayWithSuffix($endDate->day);

        $monthYear = $startDate->format('F, Y');

        return "{$startDay} - {$endDay} {$monthYear}";
    }
}
