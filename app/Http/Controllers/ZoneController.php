<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Field;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::with(['chapters', 'stakeholder'])->get();
        return view('admin.zones.index', compact('zones'));
    }

    public function create()
    {
        $fields = Field::all();
        $pastors = Stakeholder::whereNotIn('role_id', [1, 2, 5])->get();

        return view('admin.zones.edit', compact('fields', 'pastors'));
    }

    public function edit(Zone $zone)
    {
        $fields = Field::all();
        $pastors = Stakeholder::whereNotIn('role_id', [1, 2, 5])
        ->get();

        return view('admin.zones.edit', compact('zone', 'fields','pastors'));
    }

    public function getZonesByField($fieldId)
    {
        $zones = Zone::where('field_id', $fieldId)->get();
        return response()->json($zones);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|unique:zones,name',
            'field_id'       => 'required|integer|exists:fields,id',
            'stakeholder_id' => 'nullable|array',
            'stakeholder_id.*' => 'integer|exists:stakeholders,id',
        ]);

        DB::transaction(function () use ($data) {

            // Create the zone
            $zone = Zone::create([
                'name'     => $data['name'],
                'field_id' => $data['field_id'],
            ]);

            // Assign zonal pastors if any selected
            if (!empty($data['stakeholder_id'])) {
                $stakeholderIds = collect($data['stakeholder_id'])
                    ->map(fn($id) => (int) $id)
                    ->toArray();

                Stakeholder::whereIn('id', $stakeholderIds)
                    ->update([
                        'status'     => 'active',
                        'zone_id'    => $zone->id,
                        'field_id'   => $zone->field_id,
                        'chapter_id' => null,
                        'portfolio'  => null,
                        'role_id'    => 4, // Zonal Pastor
                    ]);
            }
        });

        return redirect()
            ->route('zones.index')
            ->with('message', 'Zone successfully created');
    }

    public function update(Request $request, Zone $zone)
    {
        $zone->update([
            'name'     => $request->name,
            'field_id' => $request->field_id,
        ]);

        $selectedStakeholders = collect($request->input('stakeholder_id', []))
            ->map(fn ($id) => (int) $id)
            ->toArray();

        Stakeholder::where('zone_id', $zone->id)
            ->whereNotIn('id', $selectedStakeholders)
            ->update([
                'zone_id'    => null,
                'field_id'   => null,
                'chapter_id' => null,
            ]);

        if (!empty($selectedStakeholders)) {
            Stakeholder::whereIn('id', $selectedStakeholders)
                ->update([
                    'status'     => 'active',
                    'zone_id'    => $zone->id,
                    'field_id'   => $zone->field_id,
                    'chapter_id' => null,
                    'role_id'    => 4,
                ]);
        }

        return redirect()
            ->route('zones.index')
            ->with('message', 'Update successful!');
    }

    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);

        if ($zone->chapters->count() > 1) {
            return back()->with('error', 'You cannot delete this zone because it has campuses');
        }

        Stakeholder::where('zone_id', $zone->id)
            ->where('role_id', 4)
            ->update([
                'status'     => 'inactive',
                'zone_id'    => null,
                'field_id'   => null,
                'chapter_id' => null,
            ]);

        $zone->delete();

        return redirect()->back()->with('message', 'Zone deleted and pastor unlinked successfully!');
    }
}
