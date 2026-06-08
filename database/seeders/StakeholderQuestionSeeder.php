<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\StakeholderReportQuestion;
use App\Models\StakeholderQuestionSection;
use App\Models\StakeholderQuestionSubSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StakeholderQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StakeholderReportQuestion::truncate();
        StakeholderQuestionSection::truncate();
        StakeholderQuestionSubSection::truncate();

        $ratingOptions = [
            '1' => 'Very Poor',
            '2' => 'Poor',
            '3' => 'Average',
            '4' => 'Good',
            '5' => 'Very Good'
        ];


        $sections = [
            [
                'name' => 'SECTION A: Introduction',
                'status' => 1,
                'subsections' => [
                    [
                        'name' => 'Chapter Information',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Name of the Chapter',
                                'slug' => 'chapter_name',
                                'type' => 'text',
                                'is_required' => true,
                                'order' => 1,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Year Established',
                                'slug' => 'year_established',
                                'type' => 'year',
                                'is_required' => true,
                                'order' => 2,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Name of President',
                                'slug' => 'president_name',
                                'type' => 'text',
                                'is_required' => true,
                                'order' => 3,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Month',
                                'slug' => 'month',
                                'type' => 'number',
                                'is_required' => true,
                                'order' => 4,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Year',
                                'slug' => 'year',
                                'type' => 'number',
                                'is_required' => true,
                                'order' => 5,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Session',
                                'slug' => 'session',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 6,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => [
                                    '2020/2021' => '2020/2021',
                                    '2021/2022' => '2021/2022',
                                    '2022/2023' => '2022/2023',
                                    '2023/2024' => '2023/2024',
                                    '2024/2025' => '2024/2025',
                                ],
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Attendance & Workforce',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Highest attendance this month',
                                'slug' => 'highest_attendance',
                                'type' => 'number',
                                'is_required' => false,
                                'order' => 7,
                                'width_class' => 'col-md-7',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Highest attendance program',
                                'slug' => 'highest_attendance_program',
                                'type' => 'select',
                                'is_required' => false,
                                'order' => 8,
                                'width_class' => 'col-md-5',
                                'status' => 1,
                                'options' => [
                                    'Bible Study' => 'Bible Study',
                                    'Prayer Meeting' => 'Prayer Meeting',
                                    'Sunday Bible School' => 'Sunday Bible School',
                                    'Sunday Service' => 'Sunday Service',
                                    // 'Others – Specify' => 'Others – Specify',
                                ],
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Lowest attendance this month',
                                'slug' => 'lowest_attendance',
                                'type' => 'number',
                                'is_required' => false,
                                'order' => 9,
                                'width_class' => 'col-md-7',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Lowest attendance program',
                                'slug' => 'lowest_attendance_program',
                                'type' => 'select',
                                'is_required' => false,
                                'order' => 10,
                                'width_class' => 'col-md-5',
                                'status' => 1,
                                'options' => [
                                    'Bible Study' => 'Bible Study',
                                    'Prayer Meeting' => 'Prayer Meeting',
                                    'Sunday Bible School' => 'Sunday Bible School',
                                    'Sunday Service' => 'Sunday Service',
                                    // 'Others – Specify' => 'Others – Specify',
                                ],
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Number of Active workers',
                                'slug' => 'active_workers',
                                'type' => 'number',
                                'is_required' => false,
                                'order' => 11,
                                'width_class' => 'col-md-4',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Number of newly baptized',
                                'slug' => 'newly_baptized',
                                'type' => 'number',
                                'is_required' => false,
                                'order' => 12,
                                'width_class' => 'col-md-4',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Number of first timers/Visitors',
                                'slug' => 'first_timers',
                                'type' => 'number',
                                'is_required' => false,
                                'order' => 13,
                                'width_class' => 'col-md-4',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Programs',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Special Programs Held This Month',
                                'slug' => 'special_programs',
                                'type' => 'dynamic_table',
                                'is_required' => false,
                                'order' => 14,
                                'width_class' => 'col-md-12',
                                'status' => 1,
                                'options' => [
                                    ['label' => 'Program Name', 'type' => 'text', 'required' => true, 'is_quantifiable' => false],
                                    ['label' => 'Attendance', 'type' => 'number', 'required' => true, 'is_quantifiable' => true],
                                    ['label' => 'Theme', 'type' => 'text', 'required' => false, 'is_quantifiable' => false],
                                    ['label' => 'Main Speaker', 'type' => 'text', 'required' => false, 'is_quantifiable' => false],
                                    ['label' => 'Date', 'type' => 'date', 'required' => true, 'is_quantifiable' => false],
                                ],
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Proposed Special Programs (Next Month)',
                                'slug' => 'proposed_programs',
                                'type' => 'dynamic_table',
                                'is_required' => false,
                                'order' => 15,
                                'width_class' => 'col-md-12',
                                'status' => 1,
                                'options' => [
                                    ['label' => 'Program Name', 'type' => 'text', 'required' => false, 'is_quantifiable' => false],
                                    ['label' => 'Theme', 'type' => 'text', 'required' => false, 'is_quantifiable' => false],
                                    ['label' => 'Main Speaker', 'type' => 'text', 'required' => false, 'is_quantifiable' => false],
                                    ['label' => 'Proposed Date', 'type' => 'date', 'required' => false, 'is_quantifiable' => false],
                                ],
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Leadership & Team Dynamics',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Relationship among EXCOS',
                                'slug' => 'excos_relationship',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 16,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => $ratingOptions,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Communication between leaders and members',
                                'slug' => 'leaders_communication',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 17,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => $ratingOptions,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Noticeable problems in activity groups',
                                'slug' => 'activity_problems',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 18,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => $ratingOptions,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Efforts taken to address problems',
                                'slug' => 'activity_efforts',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 19,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => $ratingOptions,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Zonal Pastor’s Section',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Overall average attendance this month met the chapter’s goal',
                                'slug' => 'average_attendance_goal',
                                'type' => 'select',
                                'is_required' => false,
                                'order' => 21,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => $ratingOptions,
                                'is_quantifiable' => true,
                                'role' => 'Zonal Pastor'
                            ],
                            [
                                'label' => 'Clarity of monthly focus & objectives for programs',
                                'slug' => 'pastor_clarity',
                                'type' => 'select',
                                'is_required' => false,
                                'order' => 22,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => $ratingOptions,
                                'is_quantifiable' => true,
                                'role' => 'Zonal Pastor',
                                'access_roles' => ['Zonal Pastor']
                            ],
                            [
                                'label' => 'Quality of message/teaching/prayer topics',
                                'slug' => 'pastor_quality',
                                'type' => 'select',
                                'is_required' => false,
                                'order' => 23,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => $ratingOptions,
                                'is_quantifiable' => true,
                                'role' => 'Zonal Pastor'
                            ],
                            [
                                'label' => 'Noticeable spiritual result or impact',
                                'slug' => 'pastor_spiritual_impact',
                                'type' => 'select',
                                'is_required' => false,
                                'order' => 24,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => $ratingOptions,
                                'is_quantifiable' => true,
                                'role' => 'Zonal Pastor'
                            ],
                            [
                                'label' => 'Speaker effectiveness',
                                'slug' => 'speaker_effectiveness',
                                'type' => 'select',
                                'is_required' => false,
                                'order' => 25,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => $ratingOptions,
                                'is_quantifiable' => true,
                                'role' => 'Zonal Pastor'
                            ],
                        ]
                    ],
                    
                    [
                        'name' => 'Final Remarks',
                        'status' => 1,
                        'questions' => [
                            
                            [
                                'label' => 'Zonal Pastor’s Comment',
                                'slug' => 'section_a_zonal_pastor_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 1,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'Zonal Pastor'
                            ],
                            [
                                'label' => 'Field Pastor’s Comment',
                                'slug' => 'section_a_field_pastor_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 2,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'Field Pastor'
                            ],
                            [
                                'label' => 'National secretariat Comment',
                                'slug' => 'section_a_secretariat_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 3,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'Secretariat'
                            ],
                            [
                                'label' => 'NCP Comment',
                                'slug' => 'section_a_ncp_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 4,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'NCP'
                            ],
                        ]
                    ],

                ]
            ],
            // SECTION B & SECTION C can follow the same structure...
            [
                'name' => 'SECTION B: Evangelism & Follow-Up',
                'status' => 1,
                'subsections' => [
                    [
                        'name' => 'Corporate Personal Evangelism',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Did the fellowship go out for corporate personal evangelism during the month?',
                                'slug' => 'corporate_evangelism',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 1,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => ['1' => 'Yes', '0' => 'No'],
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'If yes, how many times?',
                                'slug' => 'evangelism_times',
                                'type' => 'number',
                                'is_required' => false,
                                'order' => 2,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'If No, main reason (1–5 scale: 1 = Very Poor … 5 = Very Good)',
                                'slug' => 'evangelism_no_reason',
                                'type' => 'select',
                                'is_required' => false,
                                'order' => 3,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => [
                                    '1' => '1 - Very Poor',
                                    '2' => '2 - Poor',
                                    '3' => '3 - Average',
                                    '4' => '4 - Good',
                                    '5' => '5 - Very Good',
                                ],
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Soul-Winning Results',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Number of souls won',
                                'slug' => 'souls_won',
                                'type' => 'number',
                                'is_required' => false,
                                'order' => 4,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Number of souls reached who attended at least one service',
                                'slug' => 'souls_reached',
                                'type' => 'number',
                                'is_required' => false,
                                'order' => 5,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Names of souls reached last month who now attend regularly',
                                'slug' => 'new_attendees_names',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 6,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Follow-Up & Training',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Members actively participated in personal evangelism',
                                'slug' => 'members_participation',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 7,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => [
                                    '1' => '1 - Very Poor',
                                    '2' => '2 - Poor',
                                    '3' => '3 - Average',
                                    '4' => '4 - Good',
                                    '5' => '5 - Very Good',
                                ],
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Follow-up of new contacts was timely and consistent',
                                'slug' => 'followup_quality',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 8,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => [
                                    '1' => '1 - Very Poor',
                                    '2' => '2 - Poor',
                                    '3' => '3 - Average',
                                    '4' => '4 - Good',
                                    '5' => '5 - Very Good',
                                ],
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Our follow-up sub-group is functioning effectively (1-5)',
                                'slug' => 'followup_group',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 9,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => [
                                    '1' => '1 - Very Poor',
                                    '2' => '2 - Poor',
                                    '3' => '3 - Average',
                                    '4' => '4 - Good',
                                    '5' => '5 - Very Good',
                                ],
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Discipleship methods (calls, visits, classes) are sufficient',
                                'slug' => 'discipleship_methods',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 10,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => [
                                    '1' => '1 - Very Poor',
                                    '2' => '2 - Poor',
                                    '3' => '3 - Average',
                                    '4' => '4 - Good',
                                    '5' => '5 - Very Good',
                                ],
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Number of converts baptized during the month',
                                'slug' => 'converts_baptized',
                                'type' => 'number',
                                'is_required' => false,
                                'order' => 11,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Number being prepared for baptism',
                                'slug' => 'converts_in_training',
                                'type' => 'number',
                                'is_required' => false,
                                'order' => 12,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'New believers class or similar programme for the coming month?',
                                'slug' => 'new_believers_class',
                                'type' => 'select',
                                'is_required' => false,
                                'order' => 13,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => ['1' => 'Yes', '0' => 'No'],
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'If yes, name of programme',
                                'slug' => 'new_believers_program_name',
                                'type' => 'text',
                                'is_required' => false,
                                'order' => 14,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Comments by Evangelism Secretary',
                                'slug' => 'evangelism_comments',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 15,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'Comments by Visitation/Follow-Up Secretary',
                                'slug' => 'visitation_comments',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 16,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ],
                        ]
                    ],
                    [
                        'name' => 'Zonal Pastor’s Feedback',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Chapter’s overall soul-winning effort this month was effective',
                                'slug' => 'pastor_soul_winning',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 17,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => [
                                    '1' => '1 - Very Low',
                                    '2' => '2 - Low',
                                    '3' => '3 - Average',
                                    '4' => '4 - High',
                                    '5' => '5 - Very High',
                                ],
                                'is_quantifiable' => true,
                                'role' => 'Zonal Pastor'
                            ],
                        ]
                    ],
                    
                    [
                        'name' => 'Final Remarks',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Zonal Pastor’s Comment',
                                'slug' => 'section_b_zonal_pastor_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 1,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'Zonal Pastor'
                            ],
                            [
                                'label' => 'Field Pastor’s Comment',
                                'slug' => 'section_b_field_pastor_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 2,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'Field Pastor'
                            ],
                            [
                                'label' => 'National secretariat Comment',
                                'slug' => 'section_b_secretariat_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 3,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'Secretariat'
                            ],
                            [
                                'label' => 'NCP Comment',
                                'slug' => 'section_b_ncp_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 4,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'NCP'
                            ],
                        ]
                    ],
                ]
            ],
            //end section b
            [
                'name' => 'SECTION C: Finance & Capital Projects',
                'status' => 1,
                'subsections' => [
                    [
                        'name' => 'Capital Projects',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'A capital project or special expense was undertaken this month',
                                'slug' => 'capital_project',
                                'type' => 'select',
                                'is_required' => true,
                                'order' => 1,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => ['1' => 'Yes', '0' => 'No'],
                                'is_quantifiable' => true,
                                'role' => 'default'
                            ],
                            [
                                'label' => 'If yes, list them (Name of project)',
                                'slug' => 'project_name',
                                'type' => 'text',
                                'is_required' => false,
                                'order' => 2,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Income Records',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Income Records',
                                'slug' => 'income_records',
                                'type' => 'income_table', // custom type for fixed income table
                                'is_required' => false,
                                'order' => 3,
                                'width_class' => 'col-md-12',
                                'status' => 1,
                                'options' => [
                                    'columns' => [
                                        'Sunday Worship Offering',
                                        'Thanksgiving Offering',
                                        'Bible Study Offering',
                                        'Prayer Meeting Offering',
                                        'Other Offering',
                                        'Remark'
                                    ],
                                    'rows' => [
                                        'Week 1',
                                        'Week 2',
                                        'Week 3',
                                        'Week 4',
                                        'Week 5',
                                    ]
                                ],
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ]
                        ]
                    ],

                    [
                        'name' => 'Expenditure Records',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Expenditure Records',
                                'slug' => 'expenditure_records',
                                'type' => 'dynamic_table',
                                'is_required' => false,
                                'order' => 4,
                                'width_class' => 'col-md-12',
                                'status' => 1,
                                'options' => [
                                    ['label' => 'Date', 'type' => 'date', 'required' => false, 'is_quantifiable' => false],
                                    ['label' => 'Expenditure No', 'type' => 'text', 'required' => false, 'is_quantifiable' => false],
                                    ['label' => 'Expenditure Details', 'type' => 'text', 'required' => false, 'is_quantifiable' => false],
                                    ['label' => 'Amount', 'type' => 'number', 'required' => false, 'is_quantifiable' => false],
                                    ['label' => 'Remarks', 'type' => 'textarea', 'required' => false, 'is_quantifiable' => false],
                                ],
                                'is_quantifiable' => false,
                                'role' => 'default'
                            ]
                        ]
                    ],
                    
                    [
                        'name' => 'Final Remarks',
                        'status' => 1,
                        'questions' => [
                            [
                                'label' => 'Zonal Pastor’s Comment',
                                'slug' => 'section_c_zonal_pastor_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 1,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'Zonal Pastor'
                            ],
                            [
                                'label' => 'Field Pastor Comment',
                                'slug' => 'section_c_field_pastor_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 2,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'Field Pastor'
                            ],
                            [
                                'label' => 'National secretariat Comment',
                                'slug' => 'section_c_secretariat_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 3,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'Secretariat'
                            ],
                            [
                                'label' => 'NCP Comment',
                                'slug' => 'section_c_ncp_comment',
                                'type' => 'textarea',
                                'is_required' => false,
                                'order' => 4,
                                'width_class' => 'col-md-6',
                                'status' => 1,
                                'options' => null,
                                'is_quantifiable' => false,
                                'role' => 'NCP'
                            ],
                        ]
                    ],
                ],
            ],
            // end section c
        ];

        foreach ($sections as $sectionData) {
            // 1. Create Section
            $section = StakeholderQuestionSection::create([
                'name' => $sectionData['name'],
            'status' => 1,]);


            // 2. Loop through subsections
            if (!empty($sectionData['subsections'])) {
                foreach ($sectionData['subsections'] as $subsectionData) {
                    $subsection = StakeholderQuestionSubSection::create([
                        'section_id' => $section->id,
                        'name' => $subsectionData['name'],
                    'status' => 1,]);


                    // 3. Loop through questions inside subsection
                    if (!empty($subsectionData['questions'])) {
                        foreach ($subsectionData['questions'] as $question) {
                            StakeholderReportQuestion::create([
                                'label' => $question['label'],
                                'slug' => $question['slug'],
                                'type' => $question['type'],
                                'section_id' => $section->id,
                                'sub_section_id' => $subsection->id, // link to subsection
                                'is_required' => $question['is_required'],
                                'order' => $question['order'],
                                'width_class' => isset($question['width_class']) ? $question['width_class'].' '. $question['slug'] : 'col-md-6',
                                'status' => 1,
                                'options' => $question['options'] ?? null,
                                'is_quantifiable' => $question['is_quantifiable'],
                                'role' => $question['role'],
                            ]);
                        }
                    }
                }
            }
        }
    }
}
