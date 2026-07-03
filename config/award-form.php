<?php

return [

    'email_address' => [
        'label' => 'Email Address',
        'type' => 'email',
        'award_type' => 'both',
    ],

    'first_name' => [
        'label' => 'First Name',
        'type' => 'text',
        'award_type' => 'both',
    ],

    'last_name' => [
        'label' => 'Last Name',
        'type' => 'text',
        'award_type' => 'both',
    ],

    'middle_name' => [
        'label' => 'Middle Name',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'date_of_birth' => [
        'label' => 'Date of Birth',
        'type' => 'date',
        'award_type' => 'etf',
    ],

    'gender' => [
        'label' => 'Gender',
        'type' => 'select',
        'award_type' => 'both',
        'options' => [
            [
                'value' => 'Male',
                'label' => 'Male',
            ],
            [
                'value' => 'Female',
                'label' => 'Female',
            ],
        ],
    ],

    'phone_number' => [
        'label' => 'Phone Number',
        'type' => 'text',
        'award_type' => 'both',
    ],

    'home_town' => [
        'label' => 'Home Town',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'permanent_home_address' => [
        'label' => 'Permanent Home Address',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'chapter_id' => [
        'label' => 'Institution',
        'type' => 'select',
        'award_type' => 'both',
    ],

    'faculty_name' => [
        'label' => 'Faculty / School',
        'type' => 'text',
        'award_type' => 'both',
    ],

    'department' => [
        'label' => 'Department',
        'type' => 'text',
        'award_type' => 'both',
    ],

    'course_of_study' => [
        'label' => 'Course of Study',
        'type' => 'text',
        'award_type' => 'both',
    ],

    'proposed_degree_diploma' => [
        'label' => 'Proposed Degree / Diploma',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'matric_no' => [
        'label' => 'Matric Number',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'current_level' => [
        'label' => 'Current Level',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'proposed_year_of_completion' => [
        'label' => 'Proposed Year of Completion',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'name_of_zonal_pastor' => [
        'label' => 'Name of Zonal Pastor',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'are_you_born_again' => [
        'label' => 'Are You Born Again?',
        'type' => 'select',
        'award_type' => 'etf',
        'options' => [
            [
                'value' => 'Yes',
                'label' => 'Yes',
            ],
            [
                'value' => 'No',
                'label' => 'No',
            ],
        ],
    ],

    'born_again_since' => [
        'label' => 'Born Again Since',
        'type' => 'date',
        'award_type' => 'etf',
    ],

    'salvation_testimony' => [
        'label' => 'Salvation Testimony',
        'type' => 'textarea',
        'award_type' => 'etf',
    ],

    'local_church_name' => [
        'label' => 'Local Church Name',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'local_church_address' => [
        'label' => 'Local Church Address',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'name_of_your_local_assembly_pastor' => [
        'label' => 'Local Assembly Pastor',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'local_pastor_phone_number' => [
        'label' => 'Local Pastor Phone Number',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'name_of_your_gsf_campus_president' => [
        'label' => 'GSF Campus President',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'name_of_your_gsf_campus_secretary' => [
        'label' => 'GSF Campus Secretary',
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'cgpa' => [
        'label' => 'CGPA',
        'type' => 'number',
        'step' => '0.01',
        'award_type' => 'both',
    ],

    'result_file' => [
        'label' => 'Most Recent Result',
        'type' => 'file',
        'accept' => '.jpg,.jpeg,.png,.pdf',
        'award_type' => 'both',
    ],

    'first_semester_gpa' => [
        'label' => 'First Semester GPA',
        'type' => 'number',
        'step' => '0.01',
        'award_type' => 'etf',
    ],

    'second_semester_gpa' => [
        'label' => 'Second Semester GPA',
        'type' => 'number',
        'step' => '0.01',
        'award_type' => 'etf',
    ],

    'hods_phone_number' => [
        'label' => "HOD's Phone Number",
        'type' => 'text',
        'award_type' => 'etf',
    ],

    'picture' => [
        'label' => 'Upload a clear Photograph of you',
        'type' => 'image',
        'accept' => '.jpg,.jpeg,.png',
        'award_type' => 'both',
    ],

    // 'select_institution' => [
    //     'label' => 'Institution',
    //     'type' => 'text',
    //     'award_type' => 'both',
    // ],

    'class_of_degree' => [
        'label' => 'Class of Degree',
        'type' => 'text',
        'award_type' => 'go',
    ],

    'account_name' => [
        'label' => 'Account Name',
        'type' => 'text',
        'award_type' => 'both',
    ],

    'account_number' => [
        'label' => 'Account Number',
        'type' => 'text',
        'award_type' => 'both',
    ],

    'bank_name' => [
        'label' => 'Bank Name',
        'type' => 'text',
        'award_type' => 'both',
    ],

    // 'other_institution_name' => [
    //     'label' => 'Other Institution Name',
    //     'type' => 'text',
    //     'award_type' => 'both',
    // ],

];
