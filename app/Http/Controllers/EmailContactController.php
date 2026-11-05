<?php

namespace App\Http\Controllers;

use App\Models\EmailContact;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $enquiries = EmailContact::query()
            ->with('chapter')
            ->whereIn('type', ['website', 'campus'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($matches) use ($search) {
                    $matches->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('message', 'like', '%'.$search.'%')
                        ->orWhereHas('chapter', fn ($chapter) => $chapter->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.enquiries.index', compact('enquiries', 'search'));
    }

    public function campusIndex(): View
    {
        $stakeholder = auth('stakeholder')->user();
        abort_unless($stakeholder && $stakeholder->chapter_id && $stakeholder->role?->slug === 'chapter-representative', 403);

        $chapter = \App\Models\Chapter::actualCampus()->findOrFail($stakeholder->chapter_id);
        $enquiries = EmailContact::query()
            ->where('type', 'campus')
            ->where('chapter_id', $chapter->id)
            ->latest()
            ->paginate(20);

        return view('stakeholder.enquiries.index', compact('enquiries', 'chapter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmailContact  $emailContact
     * @return \Illuminate\Http\Response
     */
    public function show(EmailContact $emailContact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmailContact  $emailContact
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailContact $emailContact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmailContact  $emailContact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmailContact $emailContact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmailContact  $emailContact
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailContact $emailContact)
    {
        //
    }
}
