<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConferenceEdition;
use App\Models\ConferenceSpeaker;
use App\Models\ConferenceSchedule;

class ConferenceScheduleController extends Controller
{
    public $edition;

    public function __construct()
    {
        $this->edition = ConferenceEdition::find(request()->edition);
    }

    /**
     * Display a listing of the schedules.
     */
    public function index()
    {
        $edition = $this->edition;
        $schedules = ConferenceSchedule::where('conference_edition_id', $edition->id)->latest()->get();

        return view('admin.conference_schedules.index', compact('schedules', 'edition'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        $edition = $this->edition;
        $speakers = ConferenceSpeaker::where('status', 1)->get();
        return view('admin.conference_schedules.create', compact('edition', 'speakers'));
    }


    /**
     * Show the form for editing the specified schedule.
     */
    public function edit($conference_schedule)
    {
        $edition = $this->edition;
        $speakers = ConferenceSpeaker::where('status', 1)->get();
        $conferenceSchedule = ConferenceSchedule::find(request()->conferenceSchedule);

        return view('admin.conference_schedules.create', compact('conferenceSchedule', 'edition','speakers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'day'      => 'nullable|string|max:50',
            'date'     => 'nullable|date',
            'status'   => 'required|in:0,1',
            'sessions' => 'nullable|array',
            'sessions.*.speaker_id' => 'required|exists:conference_speakers,id',
            'sessions.*.description' => 'required|string|max:255',
            'sessions.*.time' => 'required|date_format:H:i',
        ]);

        $edition = $this->edition;

        $sessionsArray = $request->sessions ?? null;

        ConferenceSchedule::create([
            'day' => $request->day,
            'date' => $request->date,
            'status' => $request->status,
            'sessions' => $sessionsArray ?? null,
            'conference_edition_id' => $edition->id,
        ]);

        return redirect()->route('conference_schedule.index', ['edition' => $edition->id])
            ->with('message', 'Conference schedule created successfully.');
    }

    public function update(Request $request)
    {
        $conferenceSchedule = ConferenceSchedule::find($request->conferenceSchedule);

        $request->validate([
            'day'      => 'nullable|string|max:50',
            'date'     => 'nullable|date',
            'status'   => 'required|in:0,1',
            'sessions' => 'nullable|array',
            'sessions.*.speaker_id' => 'required|exists:conference_speakers,id',
            'sessions.*.description' => 'required|string|max:255',
            'sessions.*.time' => 'required|date_format:H:i',
        ]);

        $sessionsArray = $request->sessions ?? null;

        $conferenceSchedule->update([
            'day' => $request->day,
            'date' => $request->date,
            'status' => $request->status,
            'sessions' => $sessionsArray ?? null,
        ]);

        return redirect()->route('conference_schedule.index', ['edition' => $conferenceSchedule->conference_edition_id])
            ->with('message', 'Conference schedule updated successfully.');
    }


    /**
     * Remove the specified schedule from storage.
     */
    public function destroy(Request $request)
    {
        $schedule = ConferenceSchedule::find($request->conferenceSchedule);

        if (!$schedule) {
            return back()->with('error', 'Conference schedule not found.');
        }

        $schedule->delete();

        return back()->with('message', 'Conference schedule deleted successfully.');
    }
}
