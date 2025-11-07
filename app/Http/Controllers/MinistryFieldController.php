<?php

namespace App\Http\Controllers;

use App\Models\MinistryField;
use App\Models\Ministry;
use Illuminate\Http\Request;

class MinistryFieldController extends Controller
{
    public function index(Ministry $ministry)
    {
        $fields =  $ministry->fields()->latest()->get();
        
        return view('admin.ministryFields.index', compact('ministry', 'fields'));
    }

    public function create(Ministry $ministry)
    {
        return view('admin.ministryFields.edit', compact('ministry'));
    }

    public function store(Request $request, Ministry $ministry)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'display_order' => 'required|numeric',
            'type' => 'required|in:text,number,email,select,textarea,checkbox,radio',
            'field_usage' => 'required|in:registration,allocation,both',
            'registration_types' => 'required|array',
            'options' => 'nullable|array',
            'depends_on' => 'nullable|array',
        ]);

        $data = $request->only([
            'name',
            'label',
            'type',
            'field_usage',
            'required',
            'status',
            'has_other_option',
            'onchange',
            'options',
            'registration_types',
            'depends_on',
            'display_order'
        ]);

        $data['options'] = $data['options'] ?? null;
        $data['registration_types'] = $data['registration_types'] ?? [];
        $data['depends_on'] = $data['depends_on'] ?? null;

        $ministry->fields()->create($data);

        return redirect()->route('ministries.fields.index', $ministry->id)
            ->with('success', 'Field created successfully.');
    }

    public function edit(Ministry $ministry, MinistryField $field)
    {
        return view('admin.ministryFields.edit', compact('ministry', 'field'));
    }


    public function update(Request $request, Ministry $ministry, MinistryField $field)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,email,select,textarea,checkbox,radio',
            'display_order' => 'required|numeric',
            'field_usage' => 'required|string|in:registration,allocation,both',
            'registration_types' => 'nullable|array',
            'registration_types.*' => 'integer',
            'options' => 'nullable|string',
            'required' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'has_other_option' => 'nullable|boolean',
            'onchange' => 'nullable|string|max:255',
            'depends_on' => 'nullable|array',
        ]);

        $options = $request->options;
        $options = !empty($request->options)
            ? (json_validate($request->options) ? json_decode($request->options, true) : $request->options)
            : null;

        
        $validated['options'] = $options ?? null;
        
        $validated['required'] = $request->required;
        $validated['status'] = $request->status;
        $validated['has_other_option'] = $request->has_other_option;
        
        $field->update($validated);

        return redirect()
            ->route('ministries.fields.index', $ministry->id)
            ->with('success', 'Field updated successfully.');
    }

    public function destroy(Ministry $ministry, MinistryField $field)
    {
        $field->delete();
        return redirect()->route('ministries.fields.index', $ministry->id)
            ->with('success', 'Field deleted successfully.');
    }
}
