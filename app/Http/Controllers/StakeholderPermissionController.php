<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StakeholderPermission;

class StakeholderPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role == 1) { // assuming role 1 is admin
            $permissions = StakeholderPermission::orderBy('created_at', 'desc')->get();
            return view('admin.stakeholders.permissions.index', compact('permissions'));
        } else {
            return abort(404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.stakeholders.permissions.edit');
    }

    /**
     * Display the specified resource.
     */
    public function show(StakeholderPermission $stakeholderPermission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $stakeholderPermission = StakeholderPermission::findOrFail($id);
        return view('admin.stakeholders.permissions.edit', compact('stakeholderPermission'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stakeholder_permissions,name',
            'slug' => 'nullable|string|max:255|unique:stakeholder_permissions,slug',
        ]);

        $permission = StakeholderPermission::create([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
        ]);

        return redirect()->route('stakeholderpermissions.index')
            ->with('message', 'Permission created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $permission = StakeholderPermission::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:stakeholder_permissions,name,' . $permission->id,
            'slug' => 'nullable|string|max:255|unique:stakeholder_permissions,slug,' . $permission->id,
        ]);

        $permission->update([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
        ]);

        return redirect()->route('stakeholderpermissions.index')
            ->with('message', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $permission = StakeholderPermission::findOrFail($id);

        // Detach from roles to avoid foreign key conflicts
        $permission->roles()->detach();

        $permission->delete();

        return redirect()->route('stakeholderpermissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
