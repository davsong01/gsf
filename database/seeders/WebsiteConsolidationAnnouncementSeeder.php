<?php

namespace Database\Seeders;

use App\Models\CriticalEmail;
use App\Models\Stakeholder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebsiteConsolidationAnnouncementSeeder extends Seeder
{
    private const SUBJECT = 'GSF website update: services move to gsfnational.org';
    private const TYPE = 'generic';

    public function run(): void
    {
        $recipients = User::query()
            ->where(function ($query) {
                $query->where('role', 1)
                    ->orWhere(function ($subAdmins) {
                        $subAdmins->whereIn('role', [3, 4, 5, 6])->where('status', 0);
                    });
            })
            ->whereNotNull('email')
            ->pluck('email')
            ->merge(Stakeholder::query()->whereNotNull('email')->pluck('email'))
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique(fn ($email) => mb_strtolower($email))
            ->values();

        $alreadyLogged = CriticalEmail::query()
            ->where('type', self::TYPE)
            ->where('subject', self::SUBJECT)
            ->pluck('recipient')
            ->map(fn ($email) => mb_strtolower(trim((string) $email)))
            ->flip();

        $newRecipients = $recipients
            ->reject(fn ($email) => $alreadyLogged->has(mb_strtolower($email)))
            ->values();

        if ($newRecipients->isEmpty()) {
            $this->command?->info('No new admin or stakeholder recipients to log.');
            return;
        }

        $content = <<<'HTML'
<h1>GSF services have moved to our main website</h1>
<p>Dear GSF Admins and Stakeholders,</p>
<p>We are pleased to announce that GOFAMINT Students’ Fellowship services have been brought together on our refreshed main website:</p>
<p><a href="https://gsfnational.org"><strong>https://gsfnational.org</strong></a></p>
<p>The separate directory subdomain will cease to function from <strong>15 November 2026</strong>. Please update your bookmarks and use the main website for GSF services and information.</p>
<h2>Stakeholder sign-in</h2>
<p>The stakeholder login has moved from:<br><a href="https://directory.gsfnational.org/stakeholders/login">https://directory.gsfnational.org/stakeholders/login</a></p>
<p>to:<br><a href="https://gsfnational.org/stakeholders/login"><strong>https://gsfnational.org/stakeholders/login</strong></a></p>
<h2>Useful pages</h2>
<ul>
    <li>GSF Directory: <a href="https://gsfnational.org/directory">https://gsfnational.org/directory</a></li>
    <li>Campus directory: <a href="https://gsfnational.org/people/campuses">https://gsfnational.org/people/campuses</a></li>
    <li>Alumni directory: <a href="https://gsfnational.org/all-alumni">https://gsfnational.org/all-alumni</a></li>
    <li>Member directory: <a href="https://gsfnational.org/all-members">https://gsfnational.org/all-members</a></li>
    <!-- <li>Programs and initiatives: <a href="https://gsfnational.org/people/programs">https://gsfnational.org/people/programs</a></li> -->
    <li>GSF News: <a href="https://gsfnational.org/news">https://gsfnational.org/news</a></li>
    <li>Member login: <a href="https://gsfnational.org/login">https://gsfnational.org/login</a></li>
</ul>
<h2>What’s new</h2>
<ul>
    <li>A refreshed layout and navigation to make services easier to find on desktop and mobile.</li>
    <li>Campus, alumni and member directories gathered under the main website.</li>
    <li>A GSF News area for national and network stories, with featured and most-read coverage.</li>
    <li>News topics and comments with editorial review and moderation.</li>
    <li>A central stakeholder sign-in and access to stakeholder services on the main domain.</li>
</ul>
<p>Please update your bookmarks, begin using <a href="https://gsfnational.org">https://gsfnational.org</a>.</p>
<p>For assistance, use the contact page: <a href="https://gsfnational.org/about/contact">https://gsfnational.org/about/contact</a></p>

<p><strong>GOFAMINT Students’ Fellowship</strong><br><strong>GSF National ICT</strong></p>
HTML;

        $rows = $newRecipients->map(fn ($recipient) => [
            'recipient' => $recipient,
            'type' => self::TYPE,
            'conference_edition_id' => null,
            'subject' => self::SUBJECT,
            'attachments' => null,
            'content' => $content,
            'status' => 0,
            'sent_at' => null,
            'errors' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::transaction(function () use ($rows) {
            $rows->chunk(500)->each(fn ($chunk) => CriticalEmail::insert($chunk->all()));
        });

        $this->command?->info('Logged '.$newRecipients->count().' pending announcement emails.');
        $this->command?->warn('Emails are queued in CriticalEmail and have not been sent by this seeder.');
    }
}
