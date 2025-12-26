<?php

namespace App\Http\Controllers;


use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StakeholderRole;
use App\Models\StakeholderPermission;

class StakeholderRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role == 1) {
            $roles = StakeholderRole::withCount('permissions')->orderBy('created_at', 'desc')->get();
            return view('admin.stakeholders.roles.index', compact('roles'));
        } else {
            return abort(404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = StakeholderPermission::latest()->get();
        return view('admin.stakeholders.roles.edit', compact('permissions'));
    }

    /**
     * Display the specified resource.
     */
    public function show(StakeholderRole $stakeholderRole)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($stakeholderroles)
    {
        $permissions = StakeholderPermission::latest()->get();
        $stakeholderRole = StakeholderRole::find($stakeholderroles);

        return view('admin.stakeholders.roles.edit', compact('permissions', 'stakeholderRole'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stakeholder_roles,name',
            'slug' => 'nullable|string|max:255|unique:stakeholder_roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:stakeholder_permissions,id',
        ]);

        $role = StakeholderRole::create([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('stakeholderroles.index')
            ->with('message', 'Role created successfully.');
    }

    public function update(Request $request, $id)
    {
        $stakeholderRole = StakeholderRole::find($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:stakeholder_roles,name,' . $stakeholderRole->id,
            'slug' => 'nullable|string|max:255|unique:stakeholder_roles,slug,' . $stakeholderRole->id,
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:stakeholder_permissions,id',
        ]);

        $stakeholderRole->update([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
        ]);

        $stakeholderRole->permissions()->sync($request->permissions ?? []);

        return redirect()->route('stakeholderroles.index')
            ->with('message', 'Role updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StakeholderRole $stakeholderRole)
    {
        $stakeholderRole->permissions()->detach();

        $stakeholderRole->delete();

        return redirect()->route('stakeholderroles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
