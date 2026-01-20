<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConferenceSpeaker;
use App\Services\FileUploadService;

class ConferenceSpeakerController extends Controller
{
    /**
     * Display a listing of the speakers.
     */
    public function index()
    {
        $speakers = ConferenceSpeaker::latest()->get();
        return view('admin.conference_speakers.index', compact('speakers'));
    }

    /**
     * Show the form for creating a new speaker.
     */
    public function create()
    {
        return view('admin.conference_speakers.create');
    }

    /**
     * Store a newly created speaker in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = app(FileUploadService::class)->uploadImage($request->image, 'frontend/images/conferencespeakers');
        }

        ConferenceSpeaker::create([
            'name'   => $request->name,
            'title'  => $request->title,
            'status' => $request->status,
            'image'  => $imagePath,
        ]);

        return redirect()->route('conference_speakers.index')->with('message', 'Speaker added successfully.');
    }

    /**
     * Show the form for editing the specified speaker.
     */
    public function edit($conferenceSpeaker)
    {
        $speaker = ConferenceSpeaker::find($conferenceSpeaker);

        return view('admin.conference_speakers.create', compact('speaker'));
    }

    /**
     * Update the specified speaker in storage.
     */
    public function update(Request $request, ConferenceSpeaker $conferenceSpeaker)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = $conferenceSpeaker->image;

        if ($request->hasFile('image')) {
            $imagePath = app(FileUploadService::class)->uploadImage($request->image, 'frontend/images/conferencespeakers');
        }

        $conferenceSpeaker->update([
            'name'   => $request->name,
            'title'  => $request->title,
            'status' => $request->status,
            'image'  => $imagePath,
        ]);

        return redirect()->route('conference_speakers.index')->with('message', 'Speaker updated successfully.');
    }

    /**
     * Remove the specified speaker from storage.
     */
    public function destroy(ConferenceSpeaker $conferenceSpeaker)
    {
        $conferenceSpeaker->delete();

        return back()->with('message', 'Speaker deleted successfully.');
    }
}
