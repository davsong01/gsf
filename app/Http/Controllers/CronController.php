<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nec;
use App\Models\Chapter;
use App\Models\TempUser;
use App\Models\Stakeholder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CriticalEmail;
use App\Services\EmailService;
use App\Mail\NotificationEmail;
use App\Models\StakeholderReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PaymentController;

class CronController extends Controller
{
    public function cron(){
        //All Notifiable emails
        $notifiables = ['princedamab19057@gmail.com', 'oyedepokds@gmail.com', 'gsfnationalpublicity@gmail.com'];
        //Get all stakeholders
        $stakeholders = Stakeholder::limit(10)->get();

        foreach($stakeholders as $stakeholder){
            if($stakeholder->day == date('d') && ($stakeholder->month == date('m'))){
                $data['name'] = $stakeholder->name;
                $data['type'] = 'birthdaynotification';
                $data['portfolio'] = $stakeholder->portfolio;

                foreach($notifiables as $notifiable){
                    Mail::to($notifiable)->send(new NotificationEmail($data));
                }
            }
        }
    }

    public function birthdayReminderForNec($days)
    {
        //All Notifiable emails
        $notifiables = ['princedamab19057@gmail.com', 'oyedepokds@gmail.com', 'gsfnationalpublicity@gmail.com'];
        //Get all stakeholders
        $stakeholders = Nec::all();
        $twoDaysBefore = Carbon::now()->addDays($days);
        $twoDaysBefore = $twoDaysBefore->format('d');
        if($days == 1){
            $type = 'onedaynecbirthdaynotification';
        }
        if ($days == 2) {
            $type = 'twodaysnecbirthdaynotification';
        }
        if ($days == 3) {
            $type = 'threedaysnecbirthdaynotification';
        }

        foreach ($stakeholders as $stakeholder) {
            if(!empty($stakeholder->bday)){
                $date = explode('/',$stakeholder->bday);
                $day = $date[0];
                $month = $date[1];

                if ($day == $twoDaysBefore && $month == date('m')) {
                    $data['name'] = $stakeholder->name;
                    $data['bday'] = date('Y').'/'.$stakeholder->bday;
                    $data['type'] = $type;
                    $data['portfolio'] = $stakeholder->office;

                    foreach ($notifiables as $notifiable) {
                        Mail::to($notifiable)->send(new NotificationEmail($data));
                    }
                }
            }
        }
    }

    public function emailCron($pick = 10)
    {
        $hourlyRate = 150;

        // Normalize requested pick
        $pick = (int) $pick;
        if ($pick < 1) {
            $pick = 10; // default fallback
        }

        $oneHourAgo = Carbon::now()->subHours(1);
        ini_set('max_execution_time', 600); //5 minutes

        // Check how many have been sent within the hour
        $sentWithinTheHour = CriticalEmail::where('status', 1)
            ->whereBetween('sent_at', [$oneHourAgo, now()])
            ->count();

        // If we've already hit hourly limit, stop
        if ($sentWithinTheHour >= $hourlyRate) {
            \Log::info(now() . ' : Hourly email rate exceeded on server. ' . $sentWithinTheHour . ' emails already sent this hour');
            echo ('Hourly email rate exceeded on server. ' . $sentWithinTheHour . ' emails already sent this hour');
            return;
        }

        // Respect requested pick but cap to remaining allowance
        $allowed = $hourlyRate - $sentWithinTheHour;
        $toFetch = min($pick, $allowed);

        if ($toFetch <= 0) {
            \Log::info(now() . ' : No emails allowed to be sent at this time. Allowed: ' . $allowed . ' Requested: ' . $pick);
            echo 'No emails can be sent at this time';
            return;
        }

        $emails = CriticalEmail::with('settings')->where('status', 0)->whereNull('sent_at')->take($toFetch)->get();
        $count = 0;

        foreach ($emails as $email) {
            $data['settings'] = $email->settings ?? null;
            $data['type'] = $email->type;
            $data['recipient'] = $email->recipient;
            $data['content'] = $email->content;
            $data['subject'] = $email->subject;
            $data['attachments'] = $email->attachments;

            $res = EmailService::sendEmail($data);

            if (isset($res['status'])) {
                $count++;
                $email->status = 1;
                $email->sent_at = now();
                $email->errors = NULL;
                $email->save();
            } else {
                $email->errors = $res['error'] ?? null;
                $email->save();
                continue;
            }
        }

        echo $count . ' emails sent successfully';
    }

    public function sendReportReminders()
    {
        $chapterRoleId = 5;
        $allEmailData = [];
        $sentCount = 0;

        // 1. Get window status (no chapter needed)
        $windowCheck = reportWindowStatus();

        if (!$windowCheck['eligible']) {
            return [
                'status' => 'window_closed',
                'message' => 'Reports window is currently closed.',
            ];
        }

        // Eligible report month/year
        $reportMonth = $windowCheck['month_number'];
        $reportYear  = $windowCheck['year'];
        $reportMonthName = $windowCheck['month'];
        $windowClose = $windowCheck['window_close'];

        // 2. Fetch chapters with active stakeholders
        $chapters = Chapter::with('stakeholder')
            ->whereHas('stakeholder', function ($q) use ($chapterRoleId) {
                $q->where('role_id', $chapterRoleId)
                ->where('status', 'active');
            })
            ->whereNotNull('email')
            ->get()
            ->filter(function ($chapter) use ($reportMonth, $reportYear) {
                return !StakeholderReport::where('chapter_id', $chapter->id)
                    ->whereYear('created_at', $reportYear)
                    ->whereMonth('created_at', $reportMonth)
                    ->exists();
            });

        if ($chapters->isEmpty()) {
            return [
                'status' => 'no_reminders',
                'message' => 'No chapters need reminders today.',
            ];
        }

        foreach ($chapters as $chapter) {
            $loginLink = "<a href='" . url('/stakeholders/login') . "'>Login</a>";

            $allEmailData[] = [
                'recipient'  => $chapter->stakeholder->email,
                'type'       => 'report_email',
                'subject'    => "Reminder: Submit {$reportMonthName} Monthly Report",
                'content'    => "
                    <h5>Dear Representative of {$chapter->stakeholder->name},</h5>
                    <p>This is a friendly reminder to submit your chapter report for <strong>{$reportMonthName}</strong>.</p>
                    <p>The reporting window closes on <strong>{$windowClose}</strong>.</p>
                    <p>Please log in here: {$loginLink}. <br>
                    After login, click the Monthly Reports Menu to access the monthly report section.</p>
                    <p>In His Service,<br>GSF National ICT</p>
                " . chapterEmailFooter(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $sentCount++;
        }

        if ($sentCount > 0) {
            EmailService::logEmail([
                'type'       => 'report_email',
                'recipients' => $allEmailData,
            ]);
        }

        return [
            'status' => 'success',
            'message' => "{$sentCount} chapter reminder email(s) queued successfully.",
        ];
    }

    public function sendStakeholderCredentials()
    {
        $chapterRoleId = 5; // Chapter Representative role
        $allEmailData = [];
        $sentCount = 0;

        // Stakeholder::where(function ($q) {
        //     $q->whereNull('chapter_id')
        //     ->whereNull('field_id')
        //     ->whereNull('zone_id');
        // })
        // ->orWhere(function ($q) {
        //     $q->where('status', 'inactive')
        //     ->orWhere('role_id', 0);
        // })
        // ->delete();

        // 1. Get chapters that have email
        $chapters = Chapter::whereNotNull('email')->get();

        foreach ($chapters as $chapter) {
            $stakeholder = Stakeholder::where('chapter_id', $chapter->id)
                ->where('role_id', $chapterRoleId)
                ->where('status', 'active')
                ->first();

            if ($stakeholder) {
                if ($stakeholder->credentials_sent) {
                    continue;
                }

                $passwordPlain = Str::random(8);

                $stakeholder->update([
                    'password'          => bcrypt($passwordPlain),
                    'credentials_sent'  => 1,
                ]);
            } else {
                $passwordPlain = Str::random(8);

                $stakeholder = Stakeholder::create([
                    'role_id'          => $chapterRoleId,
                    'chapter_id'       => $chapter->id,
                    'zone_id'           => $chapter->zone_id,
                    'field_id'           => $chapter->field_id,
                    'name'             => $chapter->name . ' Representative',
                    'email'            => $chapter->email,
                    'phone'            => $chapter->phone,
                    'status'           => 'active',
                    'password'         => bcrypt($passwordPlain),
                    'credentials_sent' => 1,
                ]);
            }

            // Email content
            $loginLink = "<a href='" . url('/stakeholders/login') . "'>Login</a>";

            $allEmailData[] = [
                'recipient'  => $chapter->email,
                'type'       => 'report_email',
                'subject'    => 'Welcome to GSF Digital Portal',
                'content'    => "
                    <h5>Dear Representative of {$chapter->name},</h5>
                    <p>Your fellowship representative account has been created or updated.</p>
                    <p><strong>Email:</strong> {$stakeholder->email}<br>
                    <strong>Password:</strong> {$passwordPlain}</p>
                    <p>{$loginLink}</p>
                    <p>Please change your password after first login.</p>
                    <p>In His Service,<br>GSF National ICT</p>
                ",
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $sentCount++;
        }

        if ($sentCount > 0) {
            EmailService::logEmail([
                'type'       => 'report_email',
                'recipients' => $allEmailData,
            ]);
        }

        return back()->with(
            'message',
            "{$sentCount} stakeholder credential email(s) queued successfully."
        );
    }

    public function createNecDummyCredentials(){
        // return;

        $tenure = '2025/2027';
        $necRoleId = 7;
        $fieldRoleId = 6;
        $zoneRoleId = 4;
        $ncpRoleId = 2;

        // Stakeholder::where(function ($q) {
        //     // Case 1: no chapter, field, and zone
        //     $q->whereNull('chapter_id')
        //     ->whereNull('field_id')
        //     ->whereNull('zone_id');
        // })
        // ->orWhere(function ($q) {
        //     // Case 2: inactive OR invalid role
        //     $q->where('status', 'inactive')
        //     ->orWhere('role_id', 0);
        // })
        // ->delete();

        $newStakeholders = [
            [
                'name'     => 'Pastor Taiwo Adebayo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $ncpRoleId,
                'email'    => 'taiwo_adebayo@example.com',
            ],
            [
                'name'     => 'Pastor Dr Olayemi Olanegan',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'olayemi_olanegan@example.com',
            ],
            [
                'name'     => 'Pastor Dr Olayemi Olanegan',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'olayemi_olanegan@ondofield.com',
            ],
            [
                'name'     => 'Pastor David Ajayi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'david_ajayi@lagosfield.com',
            ],
            [
                'name'     => 'Pastor Olabode Oyela',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'olabode_oyela@abeokutafield.com',
            ],
            [
                'name'     => 'Pastor Oyewole Saseun',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'oyewole_saseun@ijeburemo.com',
            ],
            [
                'name'     => 'Pastor Dapo Sowumi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'dapo_sowumi@ibadanfield.com',
            ],
            [
                'name'     => 'Pastor Oluwasina Luyi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'oluwasina_luyi@oyofield.com',
            ],
            [
                'name'     => 'Pastor Dr. Olayemi Olanegan',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'olayemi_olanegan@ondofield.com',
            ],
            [
                'name'     => 'Pastor Tolulope Oluwasanmi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'tolulope_oluwasanmi@ekitifield.com',
            ],
            [
                'name'     => 'To Be Decided',
                'tenure'   => $tenure,
                'gender'   => null,
                'role_id'  => $fieldRoleId,
                'email'    => null,
            ],
            [
                'name'     => 'Pastor Olanrewaju Olawale',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'olanrewaju_olawale@kwarafield.com',
            ],
            [
                'name'     => 'Pastor (Bar.) Monday Omorogiuwa',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'monday_omorogiuwa@southsouthfield.com',
            ],
            [
                'name'     => 'Pastor Moses Akinjobi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'moses_akinjobi@kogifield.com',
            ],
            [
                'name'     => 'Pastor Joseph Gbesoevi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'joseph_gbesoevi@abujafield.com',
            ],
            [
                'name'     => 'Pastor Samuel Adebayo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $fieldRoleId,
                'email'    => 'samuel_adebayo@field.com',
            ],

            [
                'name'     => 'Pastor Dare Fayemi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'dare_fayemi@example.com',
            ],
            [
                'name'     => 'Pastor Sanmi Oso',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'sanmi_oso@example.com',
            ],
            [
                'name'     => 'Pastor Akintoye Akinniyi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'akintoye_akinniyi@example.com',
            ],
            [
                'name'     => 'Pastor Gabriel Oyedele',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'gabriel_oyedele@example.com',
            ],
            [
                'name'     => 'Pastor Oluwatope Ojo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'oluwatope_ojo@example.com',
            ],
            [
                'name'     => 'Sister Shola Abayomi',
                'tenure'   => $tenure,
                'gender'   => 'Female',
                'role_id'  => $necRoleId,
                'email'    => 'shola_abayomi@example.com',
            ],
            [
                'name'     => 'Sister Ruth Ogunmola',
                'tenure'   => $tenure,
                'gender'   => 'Female',
                'role_id'  => $necRoleId,
                'email'    => 'ruth_ogunmola@example.com',
            ],
            [
                'name'     => 'Sister Joy Adenugba',
                'tenure'   => $tenure,
                'gender'   => 'Female',
                'role_id'  => $necRoleId,
                'email'    => 'joy_adenugba@example.com',
            ],
            [
                'name'     => 'Sis Ubuane Lois Omonigho',
                'tenure'   => $tenure,
                'gender'   => 'Female',
                'role_id'  => $necRoleId,
                'email'    => 'lois_omonigho@example.com',
            ],
            [
                'name'     => 'Pastor Segun Owolabi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'segun_owolabi@example.com',
            ],
            [
                'name'     => 'Pastor John Boye',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'john_boye@example.com',
            ],
            [
                'name'     => 'Pastor John Victor',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'john_victor@example.com',
            ],
            [
                'name'     => 'Pastor Akintomowo Olabisi Bolude',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'akintomowo_bolude@example.com',
            ],
            [
                'name'     => 'Pastor Akanji Tobi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'akanji_tobi@example.com',
            ],
            [
                'name'     => 'Pastor Ayodeji Balogun',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'ayodeji_balogun@example.com',
            ],
            [
                'name'     => 'Bro. Oluwayomi Gbenga',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'oluwayomi_gbenga@example.com',
            ],
            [
                'name'     => 'Bro. Nicholas Olaleye',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'nicholas_olaleye@example.com',
            ],
            [
                'name'     => 'Pastor Fola Popoola',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'fola_popoola@example.com',
            ],
            [
                'name'     => 'Pastor Ajulo Seun',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'ajulo_seun@example.com',
            ],
            [
                'name'     => 'Pastor Tolu Oyelaran',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'tolu_oyelaran@example.com',
            ],
            [
                'name'     => 'Bro. Solomon A. Benjamin',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'solomon_benjamin@example.com',
            ],
            [
                'name'     => 'Pastor Adebesin Adeolu',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'adebesin_adeolu@example.com',
            ],
            [
                'name'     => 'Pastor Dr. Tosin Adesina',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'tosin_adesina@example.com',
            ],
            [
                'name'     => 'Pastor Philip Taiwo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'philip_taiwo@example.com',
            ],
            [
                'name'     => 'Pastor Kehinde Oyedepo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'kehinde_oyedepo@example.com',
            ],
            [
                'name'     => 'Pastor Akinyelu Blessing',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'akinyelu_blessing@example.com',
            ],
            [
                'name'     => 'Bro. Nathaniel Adebisi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'nathaniel_adebisi@example.com',
            ],
            [
                'name'     => 'Pastor Peter Oladipupo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'peter_oladipupo@example.com',
            ],
            [
                'name'     => 'Pastor David Oghi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'david_oghi@example.com',
            ],
            [
                'name'     => 'Bro. Samuel Ojuade',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $necRoleId,
                'email'    => 'samuel_ojuade@example.com',
            ],
            [
                'name'     => 'Pastor Sewedo Anago',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'sewedo_anago@example.com',
            ],
            [
                'name'     => 'Pastor Sewedo Anago',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'sewedo_anago@example.com',
            ],
            [
                'name'     => 'Pastor Adeyeye Esther',
                'tenure'   => $tenure,
                'gender'   => 'Female',
                'role_id'  => $zoneRoleId,
                'email'    => 'adeyeye_esther@example.com',
            ],
            [
                'name'     => 'Pastor Peter Oladipupo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'peter_oladipupo@example.com',
            ],
            [
                'name'     => 'Sister Seun Shofela',
                'tenure'   => $tenure,
                'gender'   => 'Female',
                'role_id'  => $zoneRoleId,
                'email'    => 'seun_shofela@example.com',
            ],
            [
                'name'     => 'Pastor Paul Whetto',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'paul_whetto@example.com',
            ],
            [
                'name'     => 'Pastor Oyebiyi Erioluwa',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'oyebiyi_erioluwa@example.com',
            ],
            [
                'name'     => 'Pastor Segun Owolabi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'segun_owolabi@example.com',
            ],
            [
                'name'     => 'Pastor John Boye',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'john_boye@example.com',
            ],
            [
                'name'     => 'Pastor Seun Ajulo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'seun_ajulo@example.com',
            ],
            [
                'name'     => 'Pastor Oyedele Gabriel',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'oyedele_gabriel@example.com',
            ],
            [
                'name'     => 'Pastor Akinboro Folorunso',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'akinboro_folorunso@example.com',
            ],
            [
                'name'     => 'Pastor Adebesin Adeolu',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'adebesin_adeolu@example.com',
            ],
            [
                'name'     => 'Pastor Ademola Tunde Oba',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'ademola_tundeoba@example.com',
            ],
            [
                'name'     => 'Pastor Adesuyan Felix',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'adesuyan_felix@example.com',
            ],
            [
                'name'     => 'Pastor Ogunlana Samson',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'ogunlana_samson@example.com',
            ],
            [
                'name'     => 'Pastor Dr Olaniyan Kunle',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'olaniyan_kunle@example.com',
            ],
            [
                'name'     => 'Pastor Blessing Akinyelu',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'blessing_akinyelu@example.com',
            ],
            [
                'name'     => 'Pastor Olatunji Segun',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'olatunji_segun@example.com',
            ],
            [
                'name'     => 'Pastor Emmanuel Oludare',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'emmanuel_oludare@example.com',
            ],
            [
                'name'     => 'Pastor Matthew Poviesi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'matthew_poviesi@example.com',
            ],
            [
                'name'     => 'Pastor Mayowa Samuel',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'mayowa_samuel@example.com',
            ],
            [
                'name'     => 'Pastor Fisayo Osho',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'fisayo_osho@example.com',
            ],
            [
                'name'     => 'Pastor Adesina Tosin',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'adesina_tosin@example.com',
            ],
            [
                'name'     => 'Pastor Josiah Joshua',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'josiah_joshua@example.com',
            ],
            [
                'name'     => 'Pastor Dr Adeyemo Olusola',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'adeyemo_olusola@example.com',
            ],
            [
                'name'     => 'Pastor Olorunfemi Philips',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'olorunfemi_philips@example.com',
            ],
            [
                'name'     => 'Pastor Daniel Daisi O.',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'daniel_daisi@example.com',
            ],
            [
                'name'     => 'Pastor Aluko Oluwaseun',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'aluko_oluwaseun@example.com',
            ],
            [
                'name'     => 'Pastor Omonijo Israel',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'omonijo_israel@example.com',
            ],
            [
                'name'     => 'Pastor Akinbusoye Ifeoluwa',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'akinbusoye_ifeoluwa@example.com',
            ],
            [
                'name'     => 'Emmanuel Omokegbhele',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'emmanuel_omokegbhele@example.com',
            ],
            [
                'name'     => 'Pastor Olanegan Paul',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'olanegan_paul@example.com',
            ],
            [
                'name'     => 'Pastor Sulaiman Peter',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'sulaiman_peter@example.com',
            ],
            [
                'name'     => 'Sis Oloruntuyi Gladys',
                'tenure'   => $tenure,
                'gender'   => 'Female',
                'role_id'  => $zoneRoleId,
                'email'    => 'oloruntuyi_gladys@example.com',
            ],
            [
                'name'     => 'Pastor Ayo Oloniniran',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'ayo_oloniniran@example.com',
            ],
            [
                'name'     => 'Sis Ishola Rhoda',
                'tenure'   => $tenure,
                'gender'   => 'Female',
                'role_id'  => $zoneRoleId,
                'email'    => 'ishola_rhoda@example.com',
            ],
            [
                'name'     => 'Pastor Oluwafemi Temitope',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'oluwafemi_temitope@example.com',
            ],
            [
                'name'     => 'Pastor Lajuwomi Iseoluwa Favour',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'lajuwomi_iseoluwa@example.com',
            ],
            [
                'name'     => 'Pastor Ojo Oluwatopé Olufémi',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'ojo_oluwatope@example.com',
            ],
            [
                'name'     => 'Bro Kolawole Adesoji',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'kolawole_adesoji@example.com',
            ],
            [
                'name'     => 'Pastor Dr Kayode Kolapo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'kayode_kolapo@example.com',
            ],
            [
                'name'     => 'Pastor Temidayo Adediran',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'temidayo_adediran@example.com',
            ],
            [
                'name'     => 'Pastor Tosin Olubunmo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'tosin_olubunmo@example.com',
            ],
            [
                'name'     => 'Pastor Isaac Olusola',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'isaac_olusola@example.com',
            ],
            [
                'name'     => 'Pastor Barr. Nifemi Isaac Oluwamurewa',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'nifemi_oluwamurewa@example.com',
            ],
            [
                'name'     => 'Evang. Joel Daramola',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'joel_daramola@example.com',
            ],
            [
                'name'     => 'Bar. Olajide Adegbola',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'olajide_adegbola@example.com',
            ],
            [
                'name'     => 'Pastor Tolulope Oyelaran',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'tolulope_oyelaran@example.com',
            ],
            [
                'name'     => 'Pastor Promise Okunola',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'promise_okunola@example.com',
            ],
            [
                'name'     => 'Pastor Nejo Odunayo Julius',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'nejo_odunayo@example.com',
            ],
            [
                'name'     => 'Pastor Oladunmoye Theophilus',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'oladunmoye_theophilus@example.com',
            ],
            [
                'name'     => 'Pastor Ajibade Kayode',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'ajibade_kayode@example.com',
            ],
            [
                'name'     => 'Pastor Courage Osamuyi Ehigiator',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'courage_osamuyi@example.com',
            ],
            [
                'name'     => 'Pastor Olatokunbo Oluwadayomi Ezaekiel',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'olatokunbo_oluwayomi@example.com',
            ],
            [
                'name'     => 'Pastor Akintomowo Olabisi Bolude',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'akintomowo_olabisi@example.com',
            ],
            [
                'name'     => 'Pastor Okunade Adesoji Samuel',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'okunade_adesoji@example.com',
            ],
            [
                'name'     => 'Pastor Lucky Ukolo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'lucky_ukolo@example.com',
            ],
            [
                'name'     => 'Pastor Sanmi Oso',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'sanmi_oso@example.com',
            ],
            [
                'name'     => 'Pastor Ajayi Samuel Bukola',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'ajayi_samuel@example.com',
            ],
            [
                'name'     => 'Pastor Stephen Daniel Anozie',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'stephen_anozie@example.com',
            ],
            [
                'name'     => 'Pastor Ogunsakin Ademola',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'ogunsakin_ademola@example.com',
            ],
            [
                'name'     => 'Pastor Emmanuel Aiyejuto',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'emmanuel_aiyejuto@example.com',
            ],
            [
                'name'     => 'Pastor Timothy Atim',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'timothy_atim@example.com',
            ],
            [
                'name'     => 'Pastor Oyeboade Yinka',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'oyeboade_yinka@example.com',
            ],
            [
                'name'     => 'Pastor Matthew David',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'matthew_david@example.com',
            ],
            [
                'name'     => 'Pastor Peter Agara',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'peter_agara@example.com',
            ],
            [
                'name'     => 'Pastor Ola Martins',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'ola_martins@example.com',
            ],
            [
                'name'     => 'Pastor David Adebayo',
                'tenure'   => $tenure,
                'gender'   => 'Male',
                'role_id'  => $zoneRoleId,
                'email'    => 'david_adebayo@example.com',
            ],
        ];

        foreach ($newStakeholders as $stakeholder) {
            if(!in_array($stakeholder['role_id'], [$necRoleId, $ncpRoleId])){
                continue;
            }
            // if($stakeholder['role_id'] == $necRoleId){
            //     dd($stakeholder);
            // }
            $check = Stakeholder::where('email', $stakeholder['email'])->where('role_id', $stakeholder['role_id'])->exists();
            if($check) continue;

            $passwordPlain = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

            Stakeholder::create([
                'role_id'       => $stakeholder['role_id'],
                'name'          => $stakeholder['name'],
                'tenure'        => $tenure,
                'gender'        => $stakeholder['gender'],
                'email'         => $stakeholder['email'],
                'status'        => 'active',
                'password'      => bcrypt($passwordPlain)
            ]);
            # code...
        }

        return;
    }

    public function sendZonalPastorCredentials()
    {
        $zonalPastorRoleId = 4;
        $emailsToQueue = [];

       $stakeholders = Stakeholder::query()
        ->where('role_id', $zonalPastorRoleId)
        ->where('credentials_sent', 0)
        ->where('status', 'active')
        ->whereNotNull('email')
        ->where('email', 'not like', '%@example.com')
        ->whereHas('designation', function ($query) {
            $query->whereNotNull('zone_id')->where('status','active');
        })
        ->get();

        foreach ($stakeholders as $stakeholder) {
            // Generate secure random password
            $passwordPlain = Str::random(10);

            $stakeholder->update([
                'password'          => bcrypt($passwordPlain),
                'credentials_sent'  => 1,
            ]);

            $loginLink = url('/stakeholders/login');

            $subject = 'Your GSF Digital Portal Access';

            $content = "
                <p>Dear {$stakeholder->name},</p>

                <p>Calvary greetings.</p>

                <p>Your access to the <strong>GSF Digital Portal</strong> has been activated.
                Below are your login credentials:</p>

                <p>
                    <strong>Email:</strong> {$stakeholder->email}<br>
                    <strong>Password:</strong> {$passwordPlain}
                </p>

                <p>
                    Please <a href='{$loginLink}'>click here to login</a> and change your password immediately after first login.
                </p>

                <p>
                    If you have any issues accessing the portal, kindly contact the GSF ICT team.
                </p>

                <p>
                    In His Service,<br>
                    <strong>GSF National ICT</strong>
                </p>
            ";

            $emailsToQueue[] = [
                'recipient'  => $stakeholder->email,
                'type'       => 'report_email',
                'subject'    => $subject,
                'content'    => $content,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($emailsToQueue)) {
            EmailService::logEmail([
                'type'       => 'report_email',
                'recipients' => $emailsToQueue,
            ]);
        }

        return back()->with('message', "{$stakeholders->count()} zonal pastor credential emails queued successfully.");
    }
     public function sendFieldPastorCredentials()
    {
        $zonalPastorRoleId = 3;
        $emailsToQueue = [];

        $stakeholders = Stakeholder::query()
        ->where('role_id', $zonalPastorRoleId)
        ->where('credentials_sent', 0)
        ->where('status', 'active')
        ->whereNotNull('email')
        ->where('email', 'not like', '%@example.com')
        ->whereHas('designation', function ($query) {
            $query->whereNotNull('field_id')->where('status','active');
        })
        ->get();

        foreach ($stakeholders as $stakeholder) {
            // Generate secure random password
            $passwordPlain = Str::random(10);

            $stakeholder->update([
                'password'          => bcrypt($passwordPlain),
                'credentials_sent'  => 1,
            ]);

            $loginLink = url('/stakeholders/login');

            $subject = 'Your GSF Digital Portal Access';

            $content = "
                <p>Dear {$stakeholder->name},</p>

                <p>Calvary greetings.</p>

                <p>Your access to the <strong>GSF Digital Portal</strong> has been activated.
                Below are your login credentials:</p>

                <p>
                    <strong>Email:</strong> {$stakeholder->email}<br>
                    <strong>Password:</strong> {$passwordPlain}
                </p>

                <p>
                    Please <a href='{$loginLink}'>click here to login</a> and change your password immediately after first login.
                </p>

                <p>
                    If you have any issues accessing the portal, kindly contact the GSF ICT team.
                </p>

                <p>
                    In His Service,<br>
                    <strong>GSF National ICT</strong>
                </p>
            ";

            $emailsToQueue[] = [
                'recipient'  => $stakeholder->email,
                'type'       => 'report_email',
                'subject'    => $subject,
                'content'    => $content,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($emailsToQueue)) {
            EmailService::logEmail([
                'type'       => 'report_email',
                'recipients' => $emailsToQueue,
            ]);
        }

        return back()->with('message', "{$stakeholders->count()} zonal pastor credential emails queued successfully.");
    }

}
