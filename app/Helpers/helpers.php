<?php

use App\Models\GeneralSetting;

if (!function_exists("menu")) {
    function menu(){
        $menu = [
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

