<?php

use App\Models\Field;
use App\Models\Chapter;
use Illuminate\Support\Str;
use App\Models\GeneralSetting;
use Illuminate\Support\Carbon;
use App\Models\StakeholderRole;
use App\Models\ConferenceEdition;
use App\Models\StakeholderReport;

if (!function_exists('rootPermissions')) {
    function rootPermissions()
    {
        $menus = [
            [
                'id' => 1,
                'slug' => 'stakeholder*',
                'name' => 'Digital Portal Mgt',
                'icon' => 'fa fa-calendar',
                'children' => [
                    [
                        'id' => 2,
                        'slug' => 'stakeholderreports.index',
                        'name' => 'Reports',
                        'icon' => 'bx bx-file',
                        'children' => [
                            [
                                'id' => 3,
                                'slug' => 'stakeholderreports.index',
                                'name' => 'Monthly Reports'
                            ]
                        ]
                    ],
                    [
                        'id' => 4,
                        'slug' => 'report-structure',
                        'name' => 'Report Structure',
                        'icon' => 'bx bx-layer',
                        'children' => [
                            [
                                'id' => 5,
                                'slug' => 'stakeholderreportsection.index',
                                'name' => 'Sections'
                            ],
                            [
                                'id' => 6,
                                'slug' => 'stakeholderreportsubsection.index',
                                'name' => 'Sub Sections'
                            ],
                            [
                                'id' => 7,
                                'slug' => 'stakeholder.questions.index',
                                'name' => 'Items'
                            ]
                        ]
                    ],
                    [
                        'id' => 8,
                        'slug' => 'access-control',
                        'name' => 'Access Control',
                        'icon' => 'bx bx-lock',
                        'children' => [
                            [
                                'id' => 9,
                                'slug' => 'stakeholderroles.index',
                                'name' => 'Roles'
                            ],
                            [
                                'id' => 10,
                                'slug' => 'stakeholderpermissions.index',
                                'name' => 'Permissions'
                            ],
                            [
                                'id' => 11,
                                'slug' => 'designation.index',
                                'name' => 'Designations'
                            ],
                            [
                                'id' => 12,
                                'slug' => 'stakeholderpersonnel.index',
                                'name' => 'Stakeholders'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 13,
                'slug' => 'nec.index',
                'name' => 'NEC Management',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 1 224 0a128 128 0 1 1 0 256zM209.1 359.2l-18.6-31c-6.4-10.7 1.3-24.2 13.7-24.2H224h19.7c12.4 0 20.1 13.6 13.7 24.2l-18.6 31 33.4 123.9 36-146.9c2-8.1 9.8-13.4 17.9-11.3c70.1 17.6 121.9 81 121.9 156.4c0 17-13.8 30.7-30.7 30.7H285.5c-2.1 0-4-.4-5.8-1.1l.3 1.1H168l.3-1.1c-1.8 .7-3.8 1.1-5.8 1.1H30.7C13.8 512 0 498.2 0 481.3c0-75.5 51.9-138.9 121.9-156.4c8.1-2 15.9 3.3 17.9 11.3l36 146.9 33.4-123.9z"/></svg>'
            ],
            [
                'id' => 14,
                'slug' => 'archive.nec.index',
                'name' => 'Archive NEC Members',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 1 224 0a128 128 0 1 1 0 256zM209.1 359.2l-18.6-31c-6.4-10.7 1.3-24.2 13.7-24.2H224h19.7c12.4 0 20.1 13.6 13.7 24.2l-18.6 31 33.4 123.9 36-146.9c2-8.1 9.8-13.4 17.9-11.3c70.1 17.6 121.9 81 121.9 156.4c0 17-13.8 30.7-30.7 30.7H285.5c-2.1 0-4-.4-5.8-1.1l.3 1.1H168l.3-1.1c-1.8 .7-3.8 1.1-5.8 1.1H30.7C13.8 512 0 498.2 0 481.3c0-75.5 51.9-138.9 121.9-156.4c8.1-2 15.9 3.3 17.9 11.3l36 146.9 33.4-123.9z"/></svg>'
            ],
            [
                'id' => 15,
                'slug' => 'users.index',
                'name' => 'Users',
                'icon' => 'fa fa-user'
            ],
            [
                'id' => 16,
                'slug' => 'officials.index',
                'name' => 'Officials',
                'icon' => 'fa fa-users'
            ],
            [
                'id' => 17,
                'slug' => 'listing-pending',
                'name' => 'Pending Listing',
                'icon' => 'fa fa-user'
            ],
            [
                'id' => 18,
                'slug' => 'users.trashed',
                'name' => 'Trashed Users',
                'icon' => 'fa fa-trash-o'
            ],
            [
                'id' => 19,
                'slug' => 'events.index',
                'name' => 'Events',
                'icon' => 'fa fa-calendar'
            ],
            [
                'id' => 20,
                'slug' => 'donations.all',
                'name' => 'Donations',
                'icon' => 'fa fa-money'
            ],
            [
                'id' => 21,
                'slug' => 'fields.index',
                'name' => 'Fields',
                'icon' => 'fa fa-globe'
            ],
            [
                'id' => 22,
                'slug' => 'zones.index',
                'name' => 'Zones',
                'icon' => 'fa fa-flag'
            ],
            [
                'id' => 23,
                'slug' => 'chapters.index',
                'name' => 'Chapters',
                'icon' => 'fa fa-thumb-tack'
            ],
            [
                'id' => 24,
                'slug' => 'useremails.index',
                'name' => 'Emails',
                'icon' => 'fa fa-envelope'
            ],
            [
                'id' => 25,
                'slug' => 'criticalEmail.index',
                'name' => 'Logged Emails',
                'icon' => 'fa fa-envelope'
            ],
        ];

        return collect($menus);
    }
}

// work on this later
// if (!function_exists('renderMenu')) {
//     function renderMenu($menus = null, $userPermissions = null)
//     {
//         // Load menu structure if not provided
//         if ($menus === null) {
//             $menus = rootPermissions()->toArray();
//         }

//         // Load user permissions if not provided
//         if ($userPermissions === null) {
//             $userPermissions = auth()->user()->permissions ?? [];
//         }

//         foreach ($menus as $menu) {

//             $hasChildren = isset($menu['children']) && count($menu['children']) > 0;

//             // Determine if parent should be shown
//             $showParent = $hasChildren
//                 ? collect($menu['children'])->pluck('slug')->intersect($userPermissions)->isNotEmpty()
//                 : in_array($menu['slug'], $userPermissions);

//             if (!$showParent) continue;

//             // Assign proper classes and icons per menu
//             $icon = $menu['icon'] ?? 'fa fa-circle';
//             $liClass = $hasChildren ? 'nav-item has-sub is_shown' : 'nav-item';
//            $activeClass = $hasChildren
//     ? (collect($menu['children'])->pluck('slug')->intersect($userPermissions)->isNotEmpty() ? 'open' : '')
//     : (Request::is(str_replace('.', '*', $menu['slug']).'*') ? 'active' : '');

//             // Parent menu with children
//             if ($hasChildren) {
//                 echo '<li class="'.$liClass.' '.$activeClass.'">';
//                 echo '<a href="#"><i class="'.$icon.'"></i> <span class="menu-title">'.$menu['name'].'</span></a>';
//                 echo '<ul class="menu-content">';
//                 renderMenu($menu['children'], $userPermissions); // recursion
//                 echo '</ul>';
//                 echo '</li>';
//             } else {
//                 // Single item
//                 echo '<li class="'.$liClass.' '.$activeClass.'">';
//                 echo '<a href="'.route($menu['slug']).'"><i class="bx bx-right-arrow-alt"></i> <span class="menu-item">'.$menu['name'].'</span></a>';
//                 echo '</li>';
//             }
//         }
//     }
// }

function coursesOfStudy()
{
    return [
            // Business, Management & Law
            'Accounting',
            'Banking & Finance',
            'Business Administration',
            'Business Education',
            'Chartered Institute of Bankers of Nigeria (CIBN) Pathway',
            'Legal Studies / Law',
            'Marketing',
            'Insurance',
            'Taxation',
            'Office Technology & Management',
            'Public Administration',
            'International Relations',

            // Humanities & Social Sciences
            'Adult Education',
            'Anthropology',
            'Archaeology',
            'Arts & Social Sciences',
            'Christian Religious Studies',
            'Communication Arts / Mass Communication',
            'Criminology',
            'Economics',
            'Education (various specializations)',
            'English & Literary Studies',
            'French',
            'Geography',
            'History & International Studies',
            'Human Kinetics',
            'Languages & Linguistics',
            'Linguistics',
            'Political Science',
            'Psychology',
            'Religious Studies',
            'Sociology',
            'Social Work',

            // Sciences
            'Biochemistry',
            'Biology',
            'Botany',
            'Chemistry',
            'Computer Science',
            'Environmental Science',
            'Fisheries & Aquaculture',
            'Geography',
            'Geology / Earth Sciences',
            'Industrial Chemistry',
            'Mathematics',
            'Microbiology',
            'Physics',
            'Plant Science',
            'Zoology',

            // Engineering & Technology
            'Agricultural Engineering',
            'Chemical Engineering',
            'Civil Engineering',
            'Computer Engineering',
            'Electrical / Electronic Engineering',
            'Mechanical Engineering',
            'Mechatronics Engineering',
            'Metallurgical / Materials Engineering',
            'Petroleum Engineering',
            'Telecommunications Engineering',
            'Industrial and Production Engineering',
            'Systems Engineering',
            'Biomedical Engineering',

            // Health Sciences & Allied Fields
            'Anatomy',
            'Anatomy & Physiology',
            'Anatomy & Cell Biology',
            'Biochemistry',
            'Environmental Health',
            'Human Nutrition & Dietetics',
            'Medical Laboratory Science',
            'Medicine and Surgery (MBBS)',
            'Medical Radiography',
            'Nursing',
            'Optometry',
            'Pharmacy',
            'Physiotherapy / Physical Therapy',
            'Public Health',
            'Radiography',
            'Toxicology',
            'Dental Surgery / Dentistry',

            // Education (All Specializations)
            'Education & Mathematics',
            'Education & Physics',
            'Education & Biology',
            'Education & Chemistry',
            'Education & Geography',
            'Education & English Language',
            'Education & Social Studies',
            'Education & Computer Science',
            'Education & Economics',
            'Education & Political Science',
            'Education & French',

            // Agricultural & Environmental Sciences
            'Agriculture',
            'Agricultural Economics & Extension',
            'Animal Science',
            'Crop Science',
            'Forestry & Wildlife',
            'Soil Science',
            'Environmental Management & Toxicology',
            'Urban & Regional Planning',

            // Computer, IT & Cyber Fields
            'Software Engineering',
            'Cyber Security',
            'Information Technology (IT)',
            'Artificial Intelligence (AI)',
            'Data Science',
            'Information Systems',
            'Network & System Security',
            'Computer Applications',

            // Arts, Design & Creative Fields
            'Architecture',
            'Fine & Applied Arts',
            'Industrial Design',
            'Theatre Arts',
            'Performing Arts',
            'Music',
            'Fashion Design',
            'Graphic Design',
            'Interior Design',
        ];
}



if (!function_exists('canAddNextMonthReport')) {
    function getMonths()
    {
        $months = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12,
        ];

        return $months;
    }
}

if (!function_exists('getCommunityPortfolios')) {
    function getCommunityPortfolios()
    {
        $portfolios = [
            1 => 'Admin',
            2 => 'Member',
            3 => 'Alumni',
            // 3 => 'President',
            // 4 => 'Publicity Secretary',
            // 5 => 'Media Coordinator',
            // 6 => 'Assistant Publicity Secretary',
            // 7 => 'General Secretary',
            // 8 => 'Assistant General Secretary',
            // 9 => 'Assistant Music Director 1',
            // 10 => 'Assistant Music Director 2',
            // 11 => 'Evangelism Secretary 1',
            // 12 => 'Vice President',
            // 13 => 'Assistant Bible Studies Secretary',
            // 14 => 'Special Duty',
            // 15 => 'Head of Musicians',
            // 16 => 'Sister Cordinator',
            // 17 => 'Assistant Sister Cordinator 1',
            // 18 => 'Assistant Sister Cordinator 2',
            // 19 => 'Organizing Secretary 1',
            // 20 => 'Organizing Secretary 2',
            // 21 => 'Editor In Chief',
            // 22 => 'Technical Director 1',
            // 23 => 'Technical Director 2',
            // 24 => 'Music Director',
            // 25 => 'Financial Secretary',
            // 27 => 'Technical Director 2',
            // 28 => 'Bible Study Secretary',
            // 29 => 'Treasurer',
            // 31 => 'Assistant Sis Cord 1',
            // 32 => 'Assistant Sis Cord 2',
            // 33 => 'Prayer Secretary',
            // 34 => 'Assistant Prayer Secretary',
            // 35 => 'Health Officer',
            // 36 => 'Drama Secretary',
            // 37 => 'Alumni Liaison Officer',
            // 38 => 'Transport Secretary',
            // 39 => 'Special Duty',
            // 40 => 'Worker'
        ];

        return $portfolios;
    }
}

if (!function_exists('finIds')) {
    function finIds()
    {
        $finIds = [13, 14]  ;
        return $finIds;
    }
}

if (!function_exists('canAddNextMonthReport')) {
    function canAddReport($stakeholder, $daysBeforeEnd = 5, $daysAfterStart = 5): ?string
    {
        if (!isset($stakeholder->chapter_id)) {
            return null;
        }

        if (!in_array($stakeholder->role_id, chapterStakeholders())) {
            return null;
        }

        $today = Carbon::today();

        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        // Window: previous month end → days before current month starts
        $windowOpen = $monthStart->copy()->subDays($daysBeforeEnd); // e.g., Dec 26
        // Window: X days into current month
        $windowClose = $monthStart->copy()->addDays($daysAfterStart - 1); // e.g., Jan 5

        // Check if today is within the report window
        if (! $today->between($windowOpen, $windowClose)) {
            return null;
        }

        // Check if report already exists for this chapter & current month
        $reportExists = StakeholderReport::where('chapter_id', $stakeholder->chapter_id)
            ->whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->exists();

        if ($reportExists) {
            return null;
        }

        // Eligible: return the month name
        return $today->format('F');
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
        $roles = StakeholderRole::whereIn('slug', ['chapter-representative'])->pluck('id')->toArray();
        return $roles;
    }
}

if (!function_exists('portfolioStakeholders')) {
    function portfolioStakeholders()
    {
        $roles = StakeholderRole::whereIn('slug', ['portfolio'])->pluck('id')->toArray();
        return $roles;
    }
}

if (!function_exists('finStakeholders')) {
    function finStakeholders()
    {
        $roles = [6];
        return $roles;
    }
}

if (!function_exists('zoneStakeholders')) {
    function zoneStakeholders()
    {
        $roles = StakeholderRole::whereIn('slug', ['zonal-pastor'])->pluck('id')->toArray();
        return $roles;
    }
}

if (!function_exists('fieldStakeholders')) {
    function fieldStakeholders()
    {
        $roles = StakeholderRole::whereIn('slug', ['field-pastor'])->pluck('id')->toArray();
        return $roles;
    }
}

if (!function_exists('secretariatStakeholders')) {
    function secretariatStakeholders()
    {
        $roles = StakeholderRole::whereIn('slug', ['secretariat'])->pluck('id')->toArray();
        return $roles;
    }
}

if (!function_exists('ncpStakeholders')) {
    function ncpStakeholders()
    {
        $roles = StakeholderRole::whereIn('slug', ['ncp'])->pluck('id')->toArray();
        return $roles;
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

