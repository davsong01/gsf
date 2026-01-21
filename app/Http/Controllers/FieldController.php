<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FieldController extends Controller
{
    public function index()
    {
        $fields = Field::with(['zones', 'chapters', 'stakeholder'])->get();

        return view('admin.fields.index', compact('fields'));
    }

    public function create()
    {
        $pastors = Stakeholder::whereNotIn('role_id', [1, 2, 5])->get();
        return view('admin.fields.edit', compact('pastors'));
    }

    public function edit(Field $field)
    {
        $pastors = Stakeholder::whereNotIn('role_id', [1, 2, 5])->get();
        return view('admin.fields.edit', compact('field', 'pastors'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|unique:fields,name',
            'stakeholder_id' => 'nullable|exists:stakeholders,id',
        ]);

        DB::beginTransaction();

        try {

            $field = Field::create([
                'name' => $data['name'],
            ]);

            if (!empty($data['stakeholder_id'])) {
                Stakeholder::where('id', $data['stakeholder_id'])
                    ->update([
                        'field_id' => $field->id,
                        'zone_id'  => null,
                        'chapter_id'  => null,
                        'status'    => 'active',
                        'role_id'    => 5,
                    ]);
            }

            DB::commit();

            return redirect()
                ->route('fields.index')
                ->with('message', 'Field successfully created');

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->withErrors('Unable to create field at the moment.');
        }
    }

    public function update(Request $request, Field $field)
    {
        $request->validate([
            'name'           => 'required|unique:fields,name,' . $field->id,
            'stakeholder_id' => 'nullable|exists:stakeholders,id',
        ]);

        // Update field basic info
        $field->update([
            'name' => $request->name,
        ]);

        if ($request->filled('stakeholder_id')) {

            // Deactivate former field pastor
            Stakeholder::where('field_id', $field->id)
                ->where('role_id', 3) // <-- FIELD PASTOR ROLE ID
                ->update([
                    'status'     => 'inactive',
                    'field_id'   => null,
                    'zone_id'    => null,
                    'chapter_id' => null,
                    'portfolio'  => null,
                ]);

            // Assign & activate new field pastor
            Stakeholder::where('id', $request->stakeholder_id)
                ->update([
                    'status'     => 'active',
                    'field_id'   => $field->id,
                    'zone_id'    => null,
                    'chapter_id' => null,
                    'portfolio'  => null,
                    'role_id'    => 3, // FIELD PASTOR
                ]);
        }

        return redirect()
            ->route('fields.index')
            ->with('message', 'Update successful!');
    }

    public function destroy($id)
    {
        $field = Field::findOrFail($id);

        Stakeholder::where('field_id', $field->id)
            ->where('role_id', 3)
            ->update([
                'status'     => 'inactive',
                'field_id'   => null,
                'zone_id'    => null,
                'chapter_id' => null,
                'portfolio'  => null,
            ]);

        // Delete the field
        $field->delete();

        return redirect()->back()->with('message', 'Field deleted successfully!');
    }
}
