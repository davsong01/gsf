<?php

namespace App\Http\Controllers;

use App\Models\EmailContact;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Zone;
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
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'chapter_id' => ['nullable', 'integer'],
            'field_id' => ['nullable', 'integer'],
            'zone_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:website,campus'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);
        $search = trim((string) $request->query('search', ''));
        $query = EmailContact::query()
            ->with('chapter')
            ->whereIn('type', ['website', 'campus'])
            ->when($request->filled('status'), fn ($query) => $query->where('type', $request->status))
            ->when($request->filled('chapter_id'), fn ($query) => $query->where('chapter_id', $request->chapter_id))
            ->when($request->filled('field_id'), fn ($query) => $query->whereHas('chapter', fn ($chapter) => $chapter->where('field_id', $request->field_id)))
            ->when($request->filled('zone_id'), fn ($query) => $query->whereHas('chapter', fn ($chapter) => $chapter->where('zone_id', $request->zone_id)))
            ->when($request->filled('from_date'), fn ($query) => $query->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn ($query) => $query->whereDate('created_at', '<=', $request->to_date))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($matches) use ($search) {
                    $matches->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('message', 'like', '%'.$search.'%')
                        ->orWhereHas('chapter', fn ($chapter) => $chapter->where('name', 'like', '%'.$search.'%'));
                });
            });

        $totalEnquiries = (clone $query)->count();
        $websiteCount = (clone $query)->where('type', 'website')->count();
        $campusCount = (clone $query)->where('type', 'campus')->count();
        $enquiries = $query->latest()->paginate(20)->withQueryString();
        $chapters = Chapter::actualCampus()->orderBy('name')->get(['id', 'name']);
        $fields = Field::orderBy('name')->get(['id', 'name']);
        $zones = Zone::orderBy('name')->get(['id', 'name']);

        return view('admin.enquiries.index', compact(
            'enquiries', 'search', 'chapters', 'fields', 'zones', 'filters',
            'totalEnquiries', 'websiteCount', 'campusCount'
        ));
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
