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
            'stakeholder_id' => 'nullable|integer|exists:stakeholders,id',
        ]);

        DB::transaction(function () use ($data) {

            $zone = Zone::create([
                'name'     => $data['name'],
                'field_id' => $data['field_id'],
            ]);

            if (!empty($data['stakeholder_id'])) {
                Stakeholder::where('id', $data['stakeholder_id'])
                    ->update([
                        'status'    => 'active',
                        'zone_id'    => $zone->id,
                        'field_id'   => $zone->field_id,
                        'chapter_id' => null,
                        'portfolio'  => null,
                        'role_id'    => 4,
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

        if ($request->filled('stakeholder_id')) {

            // Stakeholder::where('zone_id', $zone->id)
            //     ->where('role_id', 4)
            //     ->update([
            //         'status'    => 'inactive',
            //         'zone_id'    => null,
            //         'field_id'   => null,
            //         'chapter_id' => null,
            //     ]);

            Stakeholder::where('id', $request->stakeholder_id)
                ->update([
                    'status'    => 'active',
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
            ->where('role_id', 4) // Zonal Pastor role ID
            ->update([
                'status'     => 'inactive',
                'zone_id'    => null,
                'field_id'   => null,
                'chapter_id' => null,
            ]);

        // Delete the zone
        $zone->delete();

        return redirect()->back()->with('message', 'Zone deleted and pastor unlinked successfully!');
    }
}
