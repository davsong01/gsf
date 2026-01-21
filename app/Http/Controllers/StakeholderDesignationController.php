<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Field;
use Illuminate\Http\Request;
use App\Models\StakeholderDesignation;

class StakeholderDesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $designations = StakeholderDesignation::orderBy('order')->get();
        return view('admin.designations.index', compact('designations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.designations.edit'); // Using the same blade as edit
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|unique:stakeholder_designations,name',
            'order'  => 'nullable|numeric|min:1',
            'type'   => 'required|in:nec,chapter-exco',
            'status' => 'required|in:active,inactive',
            'zone_id' => 'nullable',
            'field_id' => 'nullable',
        ]);

        StakeholderDesignation::create($data);

        return redirect()
            ->route('designation.index')
            ->with('message', 'Designation created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(StakeholderDesignation $designation)
    {
        // Optional: You can use this if you want a details page
        return view('admin.designations.show', compact('designation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StakeholderDesignation $designation)
    {
        return view('admin.designations.edit', compact('designation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StakeholderDesignation $designation)
    {
        $data = $request->validate([
            'name'   => 'required|string|unique:stakeholder_designations,name,' . $designation->id,
            'order'  => 'nullable|numeric|min:1',
            'type'   => 'required|in:nec,chapter-exco',
            'zone_id'   => 'nullable',
            'field_id'   => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        $designation->update($data);

        return redirect()
            ->route('designation.index')
            ->with('message', 'Designation updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StakeholderDesignation $designation)
    {
        $designation->delete();

        return redirect()
            ->route('designation.index')
            ->with('message', 'Designation deleted successfully!');
    }

    public function populate()
    {
        $zones = Zone::all();
        $fields = Field::all();

        $order = 2; // start order for everything except NCP

        // First, create the National Campus Pastor (order = 1)
        StakeholderDesignation::create([
            'name'   => 'National Campus Pastor',
            'order'  => 1,
            'type'   => 'nec',
            'status' => 'active',
        ]);

        // Field Pastors
        foreach ($fields as $field) {
            StakeholderDesignation::create([
                'name'     => 'Field Pastor - ' . $field->name,
                'order'    => $order++,
                'type'     => 'nec',
                'status'   => 'active',
                'field_id' => $field->id,
            ]);
        }

        // Zonal Pastors and Assistants
        foreach ($zones as $zone) {
            StakeholderDesignation::create([
                'name'    => 'Zonal Pastor - ' . $zone->name,
                'order'   => $order++,
                'type'    => 'nec',
                'status'  => 'active',
                'zone_id' => $zone->id,
            ]);

            StakeholderDesignation::create([
                'name'    => 'Assistant Zonal Pastor - ' . $zone->name,
                'order'   => $order++,
                'type'    => 'nec',
                'status'  => 'active',
                'zone_id' => $zone->id,
            ]);
        }

        // Other NEC designations
        $otherNec = [
            'President',
            'Vice President West',
            'Vice President South & East',
            'Vice President North',
            'General Secretary',
            'Assistant General Secretary',
            'National Sister Coordinator',
            'National Assistant Sister Coordinator 1',
            'National Assistant Sister Coordinator 2',
            'National Assistant Sister Coordinator 3',
            'National Financial Secretary',
            'National Bible Study Secretary',
            'National Prayer Secretary',
            'National Assistant Prayer Secretary South-East',
            'National Assistant Prayer Secretary North',
            'National Music Director',
            'National Assistant Music Director 1',
            'National Assistant Music Director 2',
            'National Head of Musicians',
            'National Technical Director',
            'National Evangelism Secretary',
            'National Assistant Evangelism Secretary',
            'National Organizing Secretary',
            'National Academic Secretary',
            'National Assistant Academic Secretary',
            'National Publicity Secretary',
            'National Editor-In-Chief',
            'National Drama Secretary',
            'National Liaison Officer',
            'National ICT Officer',
            'National Transport Officer',
        ];

        foreach ($otherNec as $designationName) {
            StakeholderDesignation::create([
                'name'   => $designationName,
                'order'  => $order++,
                'type'   => 'nec',
                'status' => 'active',
            ]);
        }

        return "Designations populated successfully.";
    }

   public function getDesignationsByRole($roleSlug)
    {
        $designations = collect(); // default empty collection

        if ($roleSlug === 'zonal-pastor') {
            $designations = StakeholderDesignation::where('type', 'nec')
                ->where('status', 'active')
                ->whereNotNull('zone_id')
                ->orderBy('order')
                ->get();
        } elseif ($roleSlug === 'field-pastor') {
            $designations = StakeholderDesignation::where('type', 'nec')
                ->where('status', 'active')
                ->whereNotNull('field_id')
                ->orderBy('order')
                ->get();
        } elseif ($roleSlug === 'chapter-representative') {
            $designations = StakeholderDesignation::where('type', 'chapter_executive')
                ->where('status', 'active')
                ->orderBy('order')
                ->get();
        } elseif ($roleSlug === 'ncp') {
            $designations = StakeholderDesignation::where('type', 'nec')
                ->where('status', 'active')
                ->where('name', 'National Campus Pastor')
                ->orderBy('order')
                ->get();
        } elseif (in_array($roleSlug, ['secretariat', 'portfolio', 'nec'])) {
            $designations = StakeholderDesignation::where('type', 'nec')
                ->where('status', 'active')
                ->orderBy('order')
                ->get();
        }

        return response()->json($designations);
    }

}
