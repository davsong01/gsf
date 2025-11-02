<?php

namespace App\Http\Controllers;

use App\Models\Ministry;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MinistryController extends Controller
{

    public function index()
    {
        $ministries = Ministry::latest()->get();
        
        return view('admin.ministries.index', compact('ministries'));
    }

    public function create()
    {
        return view('admin.ministries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:ministries,code',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Ministry::create($request->all());

        return redirect()->route('ministry.index')->with('success', 'Ministry created successfully.');
    }

    public function edit(Ministry $ministry)
    {
        return view('admin.ministries.create', compact('ministry'));
    }

    public function update(Request $request, Ministry $ministry)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:ministries,code,' . $ministry->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $ministry->update($request->all());

        return redirect()->route('ministry.index')->with('success', 'Ministry updated successfully.');
    }

    public function destroy(Ministry $ministry)
    {
        $ministry->delete();
        return redirect()->route('ministry.index')->with('success', 'Ministry deleted successfully.');
    }

    public function assignmentTypes(Ministry $ministry)
    {
        $fields = $ministry->fields()
            ->where('field_usage', 'allocation')
            ->where('status', 1)
            ->get(['id', 'name', 'label', 'type', 'options','status']);

        $fields->each(function ($field) {
            $field->options = $field->options ?? [];
        });
        
        return response()->json($fields);
    }
}
