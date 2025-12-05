<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FAQSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'display_order' => 1,
                'status' => 1,
                'question' => 'Who can register for the 2026 National Youth Conference?',
                'answer' => 'The conference is open to all youths, young adults, ministers, and church leaders across all GOFAMINT assemblies nationwide and in the diaspora.',
            ],
            [
                'display_order' => 2,
                'status' => 1,
                'question' => 'How do I register for the conference?',
                'answer' => 'Register at registration.gyfnational.org, select your category, and complete the online form.',
            ],
            [
                'display_order' => 3,
                'status' => 1,
                'question' => 'What are the registration categories available?',
                'answer' => 'General Registration, Senior Friends/Working Class Youths/Married Youths, NEC (GSF & GYF), RYPs/Corpers Presidents, DYP/DYC/Corpers Executives.',
            ],
            [
                'display_order' => 4,
                'status' => 1,
                'question' => 'Is there a registration fee?',
                'answer' => 'Yes, each category has its specific fee. Details and payment instructions are provided on the registration platform.',
            ],
            [
                'display_order' => 5,
                'status' => 1,
                'question' => 'What payment methods are accepted?',
                'answer' => 'Bank transfer, debit/credit cards, and online payment gateways.',
            ],
            [
                'display_order' => 6,
                'status' => 1,
                'question' => 'Will I receive a confirmation after registration?',
                'answer' => 'Yes, a confirmation email or SMS will be sent automatically after successful registration and payment.',
            ],
            [
                'display_order' => 7,
                'status' => 1,
                'question' => 'What happens if I do not receive my confirmation email?',
                'answer' => 'Check your spam folder, verify your email address, and if unresolved, contact support using the helpline on the portal.',
            ],
            [
                'display_order' => 8,
                'status' => 1,
                'question' => 'Can I edit my registration details later?',
                'answer' => 'Some details may be editable before the deadline. Contact support for assistance.',
            ],
            [
                'display_order' => 9,
                'status' => 1,
                'question' => 'What is the conference date and venue?',
                'answer' => 'The portal displays the official date, venue, and schedule for NAYOCO 2026.',
            ],
            [
                'display_order' => 10,
                'status' => 1,
                'question' => 'Is accommodation provided during the conference?',
                'answer' => 'Yes.',
            ],
            [
                'display_order' => 11,
                'status' => 1,
                'question' => 'Do I need to bring my registration slip to the venue?',
                'answer' => 'Yes, bring your registration slip or digital confirmation for verification.',
            ],
            [
                'display_order' => 12,
                'status' => 1,
                'question' => 'Can groups (church zones or districts) register together?',
                'answer' => 'Contact support for group registrations.',
            ],
            [
                'display_order' => 13,
                'status' => 1,
                'question' => 'Is there a deadline for registration?',
                'answer' => 'Yes, the portal will specify the closing date for registration.',
            ],
            [
                'display_order' => 14,
                'status' => 1,
                'question' => 'Who do I contact for help or technical issues?',
                'answer' => 'A support email or customer care number is provided on the registration website.',
            ],
        ];

        DB::table('conference_faqs')->insert($faqs);
    }
}
