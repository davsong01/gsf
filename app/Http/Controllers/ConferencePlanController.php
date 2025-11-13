<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\MinistryField;
use App\Models\ConferencePlan;
use App\Models\ConferenceEdition;

class ConferencePlanController extends Controller
{
    /**
     * Display a listing of the conference plans.
     */
    public $edition;

    public function __construct(){
        $this->edition = ConferenceEdition::find(request()->edition);
    }
    
    public function index()
    {
        $edition = $this->edition;
        $plans = ConferencePlan::where('conference_edition_id', $edition->id)->latest()->get();
        return view('admin.conference_plans.index', compact('plans', 'edition'));
    }

    /**
     * Show the form for creating a new conference plan.
     */
    public function create()
    {
        $edition = $this->edition;
        
        $registration_fields = $edition->ministry->fields->where('status', 1)->where('field_usage', 'registration')->sortBy('display_order');
        
        return view('admin.conference_plans.create', compact('edition', 'registration_fields'));
    }

    /**
     * Store a newly created conference plan in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'  => 'required|string|max:255',
            'items'  => 'nullable|string',
            'price'  => 'nullable|numeric|min:0',
            'type'   => 'required|in:single,multiple',
            'status' => 'required|in:0,1',
            'level'  => 'required|string|max:100',
            'registration_fields' => 'required',
        ]);

        $edition = $this->edition;
        
        $itemsArray = null;
        if (!empty($request->items)) {
            $itemsArray = preg_split('/[\r\n,]+/', trim($request->items));
            $itemsArray = array_filter(array_map('trim', $itemsArray));
        }
        
        ConferencePlan::create([
            'title'  => $request->title,
            'items'  => $itemsArray ?? null,
            'price'  => $request->price,
            'type'   => $request->type,
            'status' => $request->status,
            'level'  => $request->level,
            'registration_fields' => $request->registration_fields,
            'conference_edition_id' => $edition->id
        ]);

        return redirect()->route('conference_plans.index', ['edition' => $edition->id])->with('message', 'Conference plan created successfully.');
    }


    /**
     * Show the form for editing the specified conference plan.
     */
    public function edit($conference_plan)
    {
        $edition = $this->edition;
        $conferencePlan = ConferencePlan::where('id', request()->conferencePlan)->first();
        $registration_fields = $edition->ministry->fields->where('status', 1)->where('field_usage', 'registration')->sortBy('display_order');

        return view('admin.conference_plans.create', compact('conferencePlan','edition', 'registration_fields'));
    }

    /**
     * Update the specified conference plan in storage.
     */
    public function update(Request $request)
    {
        $edition = $this->edition;
        $conferencePlan = ConferencePlan::where('id', request()->conferencePlan)->first();

        $request->validate([
            'title'  => 'required|string|max:255',
            'items'  => 'nullable|string',
            'price'  => 'nullable|numeric|min:0',
            'type'   => 'required|in:single,multiple',
            'status' => 'required|in:0,1',
            'level'  => 'required|string|max:100',
            'registration_fields' => 'required',
        ]);

        $itemsArray = null;
        if (!empty($request->items)) {
            $itemsArray = preg_split('/[\r\n,]+/', trim($request->items));
            $itemsArray = array_filter(array_map('trim', $itemsArray)); // clean up blanks
        }

        $conferencePlan->update([
            'title'  => $request->title,
            'items'  => $itemsArray ?? null,
            'price'  => $request->price,
            'type'   => $request->type,
            'status' => $request->status,
            'level'  => $request->level,
            'registration_fields' => $request->registration_fields,
        ]);

        return redirect()->route('conference_plans.index', ['edition' => $edition->id])->with('message', 'Conference plan updated successfully.');

    }

    /**
     * Remove the specified conference plan from storage.
     */
    public function destroy(Request $request)
    {
        $conferencePlan = ConferencePlan::find($request->conferencePlan);

        if (!$conferencePlan) {
            return back()->with('error', 'Conference plan not found.');
        }

        $hasActiveParticipants = Transaction::where('conference_plan_id', $conferencePlan->id)
            ->where('status', 'Complete')
            ->exists();

        if ($hasActiveParticipants) {
            return back()->with('warning', 'Plan has active participants.');
        }

        $conferencePlan->delete();

        return back()->with('message', 'Conference plan deleted successfully.');
    }
}
