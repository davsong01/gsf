<?php

namespace Database\Seeders;

use App\Models\ConferenceEdition;
use App\Models\Ministry;
use Illuminate\Database\Seeder;

class Conference2017Seeder extends Seeder
{
    private const SLUG = '17th-biennial-national-conference-2017';

    public function run(): void
    {
        $referenceEdition = ConferenceEdition::query()->latest('id')->first();

        $edition = ConferenceEdition::query()->updateOrCreate(
            ['slug' => self::SLUG],
            [
                'payment_provider_id' => null,
                'ministry_id' => Ministry::query()->orderBy('id')->value('id'),
                'speaker_ids' => null,
                'faq_ids' => null,
                'speaker_section_status' => 0,
                'faq_section_status' => 0,
                'status' => 'inactive',
                'registration_fee' => 0,
                'official_email' => $referenceEdition?->official_email
                    ?: 'gofamintyouthfellowship@gmail.com',
                'official_phone' => null,
                'mass_registration' => 'no',
                'new_alumni_registration_fee' => 0,
                'alumni_registration_fee' => 0,
                'start_registration' => null,
                'close_registration' => '2017-04-12',
                'template_id' => 3,
                'banner' => 'frontend/img/site/conference-2017-triumphant-christian-living.jpeg',
                'conference_logo' => null,
                'conference_favicon' => null,
                'template_settings' => null,
                'facebook_page' => null,
                'telegram' => null,
                'instagram' => null,
                'facebook_event_page' => null,
                'start_date' => '2017-04-13 00:00:00',
                'end_date' => '2017-04-16 00:00:00',
                'conference_theme' => '17th Biennial National Conference — The Triumphant Christian Living',
                'conference_overview' => <<<'HTML'
<p>The Gospel Students’ Fellowship National Secretariat held its <strong>17th Biennial National Conference</strong> and <strong>35th Anniversary Celebration</strong> under the theme <em>“The Triumphant Christian Living.”</em></p>
<p>The gathering took place from Thursday, 13 April to Sunday, 16 April 2017 at the GOFAMINT International Conference Centre, Ojoo, Ibadan, Oyo State, Nigeria.</p>
<p>Students, alumni and ministers gathered for worship, biblical teaching, fellowship and a renewed call to live victoriously in Christ.</p>
HTML,
                'PAYSTACK_PUBLIC_KEY' => null,
                'PAYSTACK_SECRET_KEY' => null,
                'MERCHANT_EMAIL' => null,
                'random_hostel' => 'yes',
                'random_foodstand' => 'no',
                'foodstand_field_assignment' => 'yes',
                'lock_online_payment' => 'no',
                'reg_prefix' => 'BNC17',
                'hostel_assignment_type' => null,
                'service_point_assignment_type' => null,
                'enable_sub_account' => 'no',
                'paystack_subaccount_id' => null,
                'registration_tickers_enabled' => false,
            ]
        );

        $this->command?->info(
            "Archived conference ready: {$edition->conference_theme} (ID {$edition->id})."
        );
    }
}
