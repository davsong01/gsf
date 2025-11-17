<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConferenceFaq;

class ConferenceFaqController extends Controller
{
    /**
     * Display a listing of the FAQs.
     */
    public function index()
    {
        $faqs = ConferenceFaq::orderBy('display_order')->get();
        return view('admin.conference_faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create()
    {
        return view('admin.conference_faqs.create');
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer'   => 'required|string|max:1000',
            'status'   => 'required|in:0,1',
            'display_order' => 'required',
        ]);

        ConferenceFaq::create([
            'question' => $request->question,
            'answer'   => $request->answer,
            'status'   => $request->status,
            'display_order' => $request->display_order
        ]);

        return redirect()->route('conference_faqs.index')
            ->with('message', 'FAQ added successfully.');
    }

    /**
     * Show the form for editing the specified FAQ.
     */
    public function edit($conferenceFaq)
    {
        $faq = ConferenceFaq::findOrFail($conferenceFaq);
        return view('admin.conference_faqs.create', compact('faq'));
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(Request $request, ConferenceFaq $conferenceFaq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer'   => 'required|string|max:1000',
            'status'   => 'required|in:0,1',
            'display_order'   => 'required',
        ]);

        $conferenceFaq->update([
            'question' => $request->question,
            'answer'   => $request->answer,
            'status'   => $request->status,
            'display_order' => $request->display_order
        ]);

        return redirect()->route('conference_faqs.index')
            ->with('message', 'FAQ updated successfully.');
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(ConferenceFaq $conferenceFaq)
    {
        $conferenceFaq->delete();
        return back()->with('message', 'FAQ deleted successfully.');
    }
}
