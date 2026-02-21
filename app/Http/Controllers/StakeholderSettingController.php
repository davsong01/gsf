<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StakeholderSetting;
use Illuminate\Http\Request;

class StakeholderSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = StakeholderSetting::orderBy('key')->get();

        return view('admin.stakeholder_settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.stakeholder_settings.edit'); // reuse edit blade
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'key'   => 'required|string|unique:stakeholder_settings,key',
            'value' => 'nullable|string',
        ]);

        StakeholderSetting::create($data);

        return redirect()
            ->route('stakeholdersetting.index')
            ->with('message', 'Setting created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show(StakeholderSetting $stakeholdersetting)
    {
        return view('admin.settings.show', compact('setting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StakeholderSetting $stakeholdersetting)
    {
        return view('admin.stakeholder_settings.edit', compact('stakeholdersetting'));
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, StakeholderSetting $stakeholdersetting)
    {
        $data = $request->validate([
            'key'   => 'required|string|unique:stakeholder_settings,key,' . $stakeholdersetting->id,
            'value' => 'nullable|string',
        ]);

        $stakeholdersetting->update($data);

        return redirect()
            ->route('stakeholdersetting.index')
            ->with('message', 'Setting updated successfully!');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(StakeholderSetting $stakeholdersetting)
    {
        $stakeholdersetting->delete();

        return redirect()
            ->route('stakeholdersetting.index')
            ->with('message', 'Setting deleted successfully!');
    }

    /**
     * Bulk update settings (useful for config pages).
     */
    public function bulkUpdate(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            StakeholderSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('message', 'Settings saved successfully!');
    }

    /**
     * Get setting value by key (API/helper).
     */
    public function getByKey(string $key)
    {
        $setting = StakeholderSetting::where('key', $key)->first();

        return response()->json($setting?->value);
    }
}
