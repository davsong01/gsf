<?php

namespace App\Http\Controllers;

use App\Moels\Setting;
use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Support\Carbon;
use App\Models\ConferenceEdition;
use App\Models\ConferenceFaq;
use App\Models\ConferenceSchedule;
use App\Models\ConferenceSpeaker;
use App\Http\Controllers\Controller;

class ConferenceController extends Controller
{
    public function index()
    {
        $edition = activeConferenceEdition();

        return $edition
            ? redirect()->route('conferences.show', $edition->slug)
            : redirect()->route('conferences.index');
    }

    public function archive()
    {
        $currentEdition = activeConferenceEdition();
        $activeConference = $currentEdition ? ConferenceEdition::with(['conferenceplans' => function ($query) {
                $query->where('status', 1)->orderBy('id');
            }])->find($currentEdition->id) : null;
        if ($activeConference) {
            $activeConference->registrations_count = $this->registrationCount($activeConference);
        }

        $editions = ConferenceEdition::query()
            ->when($activeConference, fn ($query) => $query->where('id', '<>', $activeConference->id))
            ->orderByDesc('start_date')
            ->paginate(12);
        $editions->getCollection()->each(function ($edition) {
            $edition->registrations_count = $this->registrationCount($edition);
        });

        return view('frontend.site.conferences.index', compact('activeConference', 'editions'));
    }

    public function show($slug)
    {
        $edition = ConferenceEdition::with(['conferenceplans' => function ($query) {
                $query->where('status', 1)->orderBy('id');
            }])->where('slug', $slug)->firstOrFail();
        $edition->registrations_count = $this->registrationCount($edition);

        $activeEdition = activeConferenceEdition();
        $isRegistrationOpen = $activeEdition && (int) $activeEdition->id === (int) $edition->id;
        $isUpcoming = $edition->start_date && Carbon::parse($edition->start_date)->isFuture();

        $speakerIds = $edition->speaker_ids ?: [];
        $speakers = empty($speakerIds)
            ? collect()
            : ConferenceSpeaker::whereIn('id', $speakerIds)->where('status', 1)->get();

        $faqIds = $edition->faq_ids ?: [];
        $faqs = empty($faqIds)
            ? collect()
            : ConferenceFaq::whereIn('id', $faqIds)->where('status', 1)->orderBy('display_order')->get();

        $schedule = ConferenceSchedule::where('conference_edition_id', $edition->id)
            ->where('status', 1)->orderBy('date')->get();

        $showSpeakerSection = (int) $edition->speaker_section_status === 1
            && ($speakers->isNotEmpty() || $isUpcoming);
        $showScheduleSection = $schedule->isNotEmpty();
        $showFaqSection = (int) $edition->faq_section_status === 1;

        return view('frontend.site.conferences.show', compact(
            'edition', 'isRegistrationOpen', 'isUpcoming', 'speakers', 'faqs', 'schedule',
            'showSpeakerSection', 'showScheduleSection', 'showFaqSection'
        ));
    }

    private function registrationCount(ConferenceEdition $edition): int
    {
        return $edition->transactions()
            ->where('registration_status', 'Complete')
            ->get(['id', 'provider_reference'])
            ->unique(fn ($transaction) => filled($transaction->provider_reference)
                ? $transaction->provider_reference
                : 'transaction-'.$transaction->id)
            ->count();
    }
    
    public function thankyou()
    {
        return view('frontend.conference.thankyou');
    }

    public function  necRegistration()
    {
        $chapters = Chapter::orderBy('name')->get();
        $setting =  $this->conferenceEdition();
        $conference_year = Carbon::parse($setting->start_date)->year;

        return view('frontend.conference.necregistration', compact('conference_year'));
    }
}
