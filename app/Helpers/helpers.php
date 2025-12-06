<?php

use App\Models\Field;
use App\Models\Chapter;
use Illuminate\Support\Str;
use App\Models\GeneralSetting;
use Illuminate\Support\Carbon;
use App\Models\ConferenceEdition;
use App\Models\StakeholderReport;

if (!function_exists('canAddThisMonthReport')) {
    function canAddThisMonthReport($stakeholder)
    {
        if (!isset($stakeholder->chapter_id)) {
            return false; 
        }

        // Get current year and month
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Check if report exists for this stakeholder's chapter for current month
        $reportExists = StakeholderReport::where('chapter_id', $stakeholder->chapter_id)
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->exists();

        return !$reportExists;
    }
}

if (!function_exists('getSectionAccess')) {
    /**
     * Determine edit/view access for a stakeholder on a model.
     *
     * @param \App\Models\User $stakeholder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array ['edit' => bool, 'view' => bool]
     */
    function getSectionAccess($stakeholder, $model)
    {
        // Static access hierarchy from lowest to highest
        $accessLevel = ['Chapter President', 'Chapter Secretary', 'Chapter Financial Secretary', 'Zonal Pastor', 'Field Pastor', 'Secretariat', 'NCP'];
        
        
        $roles = $model->access_roles ?? ['Chapter President', 'Chapter Secretary', 'Chapter Financial Secretary']; // array cast in the model
        $role = $stakeholder->role;

        // Edit access: role explicitly allowed
        $edit = in_array($role, $roles);

        // View access: if user's role is higher than or equal to the lowest allowed role
        $lowestRoleIndex = null;
        foreach ($roles as $r) {
            $idx = array_search($r, $accessLevel);
            if ($idx !== false) {
                if ($lowestRoleIndex === null || $idx < $lowestRoleIndex) {
                    $lowestRoleIndex = $idx;
                }
            }
        }

        $userIndex = array_search($role, $accessLevel);
        $view = false;
        if ($lowestRoleIndex !== null && $userIndex !== false) {
            $view = $userIndex >= $lowestRoleIndex;
        }

        // If edit is true, view must always be true
        if ($edit) {
            $view = true;
        }

        return ['edit' => $edit, 'view' => $view];
    }
}



if (!function_exists('generateSampleValue')) {
    function generateSampleValue($type, $name)
    {
        $slugName = Str::slug($name, '_');

        $nameSamples = [
            'name'          => 'David Oghi',
            'gender'        => 'Male',
            'phone'         => '08143511076',
            'state'         => 'Lagos',
            'country'       => 'Nigeria',
            'chapter'       => Chapter::where('id', 19)->value('name'),
            'chapter_id'    => Chapter::where('id', 19)->value('name'),
            'assembly_id'   => 'Wonders Cathdral, Magodo',
            'district_id'   => 'Magodo Headquarters',
            'region_id'     => 'Region 12',
        ];

        // Check for manual override
        foreach ($nameSamples as $key => $value) {
            if (str_contains($slugName, $key)) {
                return $value;
            }
        }

        return match (strtolower($type)) {
            'email'     => fake()->safeEmail(),
            'phone'     => fake()->phoneNumber(),
            'number'    => fake()->numberBetween(10, 500),
            'text'      => fake()->sentence(3),
            'name'      => fake()->name(),
            default     => fake()->word(),
        };
    }
}

if (!function_exists('participantAllowedUpdateFields')) {
    function participantAllowedUpdateFields()
    {
        return ['name', 'gender','phone'];
    }
}

if (!function_exists('chapterStakeholders')) {
    function chapterStakeholders()
    {
        return ['Chapter President', 'Chapter Secretary', 'Chapter Financial Secretary'];
    }
}

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

if (!function_exists("reformatRegistrationFields")) {
    function reformatRegistrationFields($fields)
    {
        return collect($fields)->map(function ($field) {
            $name = strtolower($field->name ?? '');

            switch (true) {
                case in_array($name, ['chapter', 'chapter_id']):
                    $field->options = Chapter::select('id', 'name')->get();
                    break;

                case in_array($name, ['field', 'field_id']):
                    $field->options = Field::select('id', 'name')->get();
                    break;
                    // add more mappings easily later:
                    // case in_array($name, ['department', 'department_id']):
                    //     $options = Department::select('id', 'name')->get();
                    //     break;
            }

            return $field;
        })->values()->toArray();
    }
}


if (!function_exists("currency")) {
    function currency()
    {
        return 'NGN';
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


if (!function_exists('generalSetting')) {
    function generalSetting($columns = ['*'])
    {
        $query = GeneralSetting::query();

        if (is_array($columns)) {
            $settings = $query->first($columns);
        } else {
            $settings = $query->value($columns);
        }

        return $settings ?? null;
    }
}


if (!function_exists('conferenceSpeakers')) {
    function conferenceSpeakers()
    {
        return [
            // [
            //     'name' => 'Joshua Henry',
            //     'title' => 'Chief AI Scientist, OpenAI',
            //     'image' => 'conference_templates/template3/images/speakers/1.png',
            // ],
            // [
            //     'name' => 'Leila Zhang',
            //     'title' => 'VP of Machine Learning, Google',
            //     'image' => 'conference_templates/template3/images/speakers/2.png',
            // ],
            // [
            //     'name' => 'Carlos Rivera',
            //     'title' => 'Founder & CEO, NeuralCore',
            //     'image' => 'conference_templates/template3/images/speakers/3.png',
            // ],
        ];
    }
}

if (!function_exists('conferenceSchedule')) {
    function conferenceSchedule()
    {
        return [
            // [
            //     'day' => 'Day 1',
            //     'date' => 'Thursday, April 2, 2026',
            //     'sessions' => [
            //         [
            //             'time' => '08:00 AM – 09:30 AM',
            //             'speaker' => 'Pastor David O.',
            //             'position' => 'Conference Host',
            //             'image' => 'conference_templates/template3/images/speakers/1.png',
            //             'title' => 'Opening Session: The Shining Lights',
            //             'description' => 'A grand opening declaring the theme — “The Shining Lights” (Proverbs 4:18). Step into four days of divine encounter, worship, and illumination.',
            //         ],
            //         [
            //             'time' => '10:00 AM – 12:00 PM',
            //             'speaker' => 'Rev. Grace Eze',
            //             'position' => 'Guest Minister',
            //             'image' => 'conference_templates/template3/images/speakers/2.png',
            //             'title' => 'Session: Walking in the Light of God’s Word',
            //             'description' => 'Discover the transforming power of living daily by the Word and shining God’s truth in every area of your life.',
            //         ],
            //         [
            //             'time' => '02:00 PM – 04:00 PM',
            //             'speaker' => 'Evangelist Samuel Adeyemi',
            //             'position' => 'Youth Missionary',
            //             'image' => 'conference_templates/template3/images/speakers/3.png',
            //             'title' => 'Workshop: Rekindling the Fire of Youth Evangelism',
            //             'description' => 'A practical training on how to be a light in schools, campuses, and communities — carrying the fire of revival wherever you go.',
            //         ],
            //         [
            //             'time' => '06:00 PM – 08:00 PM',
            //             'speaker' => 'Pastor (Mrs.) Joy Akande',
            //             'position' => 'Women of Faith Network',
            //             'image' => 'conference_templates/template3/images/speakers/4.png',
            //             'title' => 'Evening Revival: Let Your Light Shine',
            //             'description' => 'A stirring revival service calling all believers to rise and shine as ambassadors of Christ in a dark world.',
            //         ],
            //     ],
            // ],
            // [
            //     'day' => 'Day 2',
            //     'date' => 'Friday, April 3, 2026',
            //     'sessions' => [
            //         [
            //             'time' => '08:00 AM – 09:30 AM',
            //             'speaker' => 'Minister John Paul',
            //             'position' => 'Worship Leader',
            //             'image' => 'conference_templates/template3/images/speakers/1.png',
            //             'title' => 'Morning Worship & Exhortation',
            //             'description' => 'A powerful morning of worship and short exhortation to set the tone for the day’s divine encounters.',
            //         ],
            //         [
            //             'time' => '10:00 AM – 12:30 PM',
            //             'speaker' => 'Rev. Naomi Okafor',
            //             'position' => 'Bible Teacher',
            //             'image' => 'conference_templates/template3/images/speakers/2.png',
            //             'title' => 'Bible Study: Growing Brighter Daily',
            //             'description' => 'A deep dive into Proverbs 4:18, uncovering how every believer is called to increase in light, grace, and purpose daily.',
            //         ],
            //         [
            //             'time' => '02:00 PM – 04:00 PM',
            //             'speaker' => 'Panel of Young Ministers',
            //             'position' => 'Youth Forum',
            //             'image' => 'conference_templates/template3/images/speakers/1.png',
            //             'title' => 'Youth Panel: Being a Light in a Digital World',
            //             'description' => 'An interactive youth session discussing practical ways to shine for Christ in the media, tech, and creative industries.',
            //         ],
            //         [
            //             'time' => '06:00 PM – 08:30 PM',
            //             'speaker' => 'Pastor Victor A.',
            //             'position' => 'Lead Pastor, Gospel City',
            //             'image' => 'conference_templates/template3/images/speakers/3.png',
            //             'title' => 'Evening Power Night: Manifesting the Glory',
            //             'description' => 'A night of worship, deliverance, and impartation. Come ready for miracles, healing, and encounters with the Holy Spirit.',
            //         ],
            //     ],
            // ],
            // [
            //     'day' => 'Day 3',
            //     'date' => 'Saturday, April 4, 2026',
            //     'sessions' => [
            //         [
            //             'time' => '08:00 AM – 09:30 AM',
            //             'speaker' => 'Minister Peace O.',
            //             'position' => 'Prayer Coordinator',
            //             'image' => 'conference_templates/template3/images/speakers/2.png',
            //             'title' => 'Morning Prayer Fire',
            //             'description' => 'Join hundreds of believers as we intercede for the nations, the youth, and a greater move of God’s light.',
            //         ],
            //         [
            //             'time' => '10:00 AM – 12:00 PM',
            //             'speaker' => 'Dr. Caleb Johnson',
            //             'position' => 'Guest Minister',
            //             'image' => 'conference_templates/template3/images/speakers/3.png',
            //             'title' => 'Session: Disciples of Light — Transforming the World',
            //             'description' => 'Discover your calling as a disciple of light and how to influence your generation for Christ through service and love.',
            //         ],
            //         [
            //             'time' => '02:00 PM – 04:30 PM',
            //             'speaker' => 'Pastor Emmanuel A.',
            //             'position' => 'Youth Director',
            //             'image' => 'conference_templates/template3/images/speakers/2.png',
            //             'title' => 'Workshop: Leadership by the Light',
            //             'description' => 'Training session on spiritual leadership, excellence, and integrity for youth leaders and ministry workers.',
            //         ],
            //         [
            //             'time' => '06:00 PM – 08:30 PM',
            //             'speaker' => 'Guest Music Ministers',
            //             'position' => 'Concert Night',
            //             'image' => 'conference_templates/template3/images/speakers/1.png',
            //             'title' => 'Night of Worship: Shine Jesus Shine',
            //             'description' => 'A spirit-filled worship concert featuring anointed music ministers, setting hearts ablaze for God’s glory.',
            //         ],
            //     ],
            // ],
            // [
            //     'day' => 'Day 4',
            //     'date' => 'Sunday, April 5, 2026',
            //     'sessions' => [
            //         [
            //             'time' => '08:00 AM – 09:00 AM',
            //             'speaker' => 'Minister Sarah I.',
            //             'position' => 'Choir Director',
            //             'image' => 'conference_templates/template3/images/speakers/1.png',
            //             'title' => 'Morning Worship & Thanksgiving',
            //             'description' => 'A joyful thanksgiving session filled with praises as we celebrate the move of God throughout NAYOCO 2026.',
            //         ],
            //         [
            //             'time' => '09:30 AM – 12:00 PM',
            //             'speaker' => 'Rev. (Dr.) Michael Okechukwu',
            //             'position' => 'General Overseer',
            //             'image' => 'conference_templates/template3/images/speakers/2.png',
            //             'title' => 'Final Message: The Light That Never Fades',
            //             'description' => 'A closing message on sustaining the fire and walking continually in the light of Christ.',
            //         ],
            //         [
            //             'time' => '01:00 PM – 03:00 PM',
            //             'speaker' => 'All Ministers',
            //             'position' => 'Closing Ceremony',
            //             'image' => 'conference_templates/template3/images/speakers/4.png',
            //             'title' => 'Commissioning & Prophetic Blessing',
            //             'description' => 'Final prayers, declarations, and sending forth — go and shine your light across the world!',
            //         ],
            //     ],
            // ],
        ];
    }
}

if (!function_exists('conferencePlans')) {
    function conferencePlans($setting, ?array $ids = null)
    {
        $plans = [
            [
                'title' => 'Single Registration',
                'items' => ['Students', 'Working Youths', 'Artisans'],
                'price' => number_format($setting->registration_fee),
                'route_id' => 1,
                'show' => isset($setting->lock_online_payment) && $setting->lock_online_payment == 'no',
                'per_participant' => false,
            ],
            // [
            //     'title' => 'Mass Registration',
            //     'items' => ['2 or more Undergraduates', '2 or more SSS Students', '2 or more Youths'],
            //     'price' => number_format($setting->registration_fee),
            //     'route_id' => 2,
            //     'show' => isset($setting->lock_online_payment) && $setting->lock_online_payment == 'no',
            //     'per_participant' => true,
            // ],
            // [
            //     'title' => 'Alumni Registration',
            //     'items' => ['GSF Alumni', 'Youth Corpers', 'Senior Friends'],
            //     'price' => number_format($setting->new_alumni_registration_fee) . ' - ₦' . number_format($setting->alumni_registration_fee),
            //     'route_id' => 3,
            //     'show' => isset($setting->lock_online_payment) && $setting->lock_online_payment == 'no',
            //     'per_participant' => false,
            // ],
        ];

        return $plans;
    }
}

