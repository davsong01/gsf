<?php

use App\Models\GeneralSetting;

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

