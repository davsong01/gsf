<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use Illuminate\Http\File;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Services\FileUploadService;
use App\Rules\UniqueStakeholderRole;
use App\Http\Controllers\Controller;
use App\Models\StakeholderRole;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class StakeholderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $count = 1;
        if (auth()->user()->role == 1) {
			$stakeholders = Stakeholder::with('role')->orderBy('created_at', 'desc')->get();
			return view('admin.stakeholders.index', compact('stakeholders', 'count'));

		}else{
            return abort(404);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fields = Field::all();
        $zones = Zone::all();
        $chapters = Chapter::all();
        $months = getMonths();
        $roles = StakeholderRole::where('status', 'active')->get();
        $portfolios = getCommunityPortfolios();

        return view('admin.stakeholders.edit', compact('zones', 'fields', 'chapters', 'months', 'roles','portfolios'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:stakeholders,email'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'password'   => ['nullable', 'string', 'min:8'],
            'day'        => ['nullable', 'integer', 'between:1,31'],
            'month'      => ['nullable', 'integer', 'between:1,12'],
            'year'       => ['nullable', 'digits:4'],
            'role_id'    => ['nullable', 'integer'],
            'chapter_id' => ['nullable', 'integer', 'exists:chapters,id'],
            'zone_id'    => ['nullable', 'integer', 'exists:zones,id'],
            'field_id'   => ['nullable', 'integer', 'exists:fields,id'],
            'designation_id'   => ['nullable', 'integer', 'exists:fields,id'],
            'portfolio'  => ['nullable', 'string', 'max:255'],
            'signature'  => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
            'gender' => ['nullable'],

        ]);

        $role = $request->input('role');
        $chapterRoles = ['Chapter President', 'Chapter Secretary', 'Chapter Financial Secretary'];

        $stakeholder = new Stakeholder();

        // Role-based assignments
        switch (true) {
            case in_array($role, $chapterRoles):
                $chapter = Chapter::find($request->chapter_id);
                $stakeholder->fill([
                    'chapter_id' => $request->chapter_id,
                    'portfolio'  => null,
                    'zone_id'    => $chapter?->zone?->id,
                    'field_id'   => $chapter?->field?->id,
                ]);
                break;

            case $role === 'Zonal Pastor':
                $zone = Zone::find($request->zone_id);
                $stakeholder->fill([
                    'zone_id'    => $zone?->id,
                    'field_id'   => $zone?->field_id,
                    'chapter_id' => null,
                    'portfolio'  => null,
                ]);
                break;

            case $role === 'Field Pastor':
                $stakeholder->fill([
                    'field_id'   => $request->field_id,
                    'zone_id'    => null,
                    'chapter_id' => null,
                    'portfolio'  => null,
                ]);
                break;

            case $role === 'Portfolio':
                $stakeholder->fill([
                    'portfolio'  => $request->portfolio,
                    'chapter_id' => null,
                    'field_id'   => null,
                    'zone_id'    => null,
                ]);
                break;
        }

        // Password handling
        $stakeholder->password = Hash::make(
            $request->filled('password') ? $request->password : '12345@GSF2021'
        );

        // Signature handling
        if ($request->hasFile('signature')) {
            $stakeholder->signature = app(FileUploadService::class)->secureUpload(
                $request->file('signature'),
                'signatures'
            );
        }

        // General info
        $stakeholder->fill([
            'name'  => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role'  => $role,
            'status'  => $request->status,
            'day'   => $request->day,
            'month' => $request->month,
            'year'  => $request->year,
            'designation_id'  => $request->designation_id,
            'gender' => $request->gender,
        ])->save();

        return redirect()
            ->route('stakeholderpersonnel.index')
            ->with('message', 'Operation Successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Stakeholder  $stakeholder
     * @return \Illuminate\Http\Response
     */
    public function show(Stakeholder $stakeholder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Stakeholder  $stakeholder
     * @return \Illuminate\Http\Response
     */
    public function edit(Stakeholder $stakeholderpersonnel)
    {
        $stakeholder = $stakeholderpersonnel;
        $fields = Field::all();
        $zones = Zone::all();
        $chapters = Chapter::all();
        $months = getMonths();
        $roles = StakeholderRole::where('status', 'active')->get();
        $portfolios = getCommunityPortfolios();

        return view('admin.stakeholders.edit', compact('stakeholder', 'fields', 'zones', 'chapters', 'months', 'roles','portfolios'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Stakeholder  $stakeholder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stakeholder $stakeholderpersonnel)
    {
        $stakeholder = $stakeholderpersonnel;

        // Validate input
        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:stakeholders,email,' . $stakeholder->id],
            'phone'      => ['nullable', 'string', 'max:20'],
            'password'   => ['nullable', 'string', 'min:8'],
            'day'        => ['nullable', 'integer', 'between:1,31'],
            'month'      => ['nullable', 'integer', 'between:1,12'],
            'year'       => ['nullable', 'digits:4'],
            'role_id'    => ['nullable', 'integer'],
            'chapter_id' => ['nullable', 'integer', 'exists:chapters,id'],
            'zone_id'    => ['nullable', 'integer', 'exists:zones,id'],
            'field_id'   => ['nullable', 'integer', 'exists:fields,id'],
            'portfolio'  => ['nullable', 'string', 'max:255'],
            'gender'     => ['nullable'],
            'signature'  => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'status'     => ['required', 'in:active,inactive'],
        ]);

        $role = StakeholderRole::find($request->input('role_id'));

        // Role-based assignments
        switch (true) {
            case in_array($role->slug, ['chapter-representative']):
                $chapter = Chapter::find($request->chapter_id);

                $stakeholder->fill([
                    'chapter_id' => $request->chapter_id,
                    'portfolio'  => null,
                    'zone_id'    => $chapter?->zone?->id,
                    'field_id'   => $chapter?->field?->id,
                ]);
                break;

            case in_array($role->slug, ['zonal-pastor']):
                $zone = Zone::find($request->zone_id);
                $stakeholder->fill([
                    'zone_id'    => $zone?->id,
                    'field_id'   => $zone?->field_id,
                    'chapter_id' => null,
                    'portfolio'  => null,
                ]);
                break;

            case in_array($role->slug, ['field-pastor']):
                $stakeholder->fill([
                    'field_id'   => $request->field_id,
                    'zone_id'    => null,
                    'chapter_id' => null,
                    'portfolio'  => null,
                ]);
                break;

            case $role === 'portfolio':
                $stakeholder->fill([
                    'portfolio'  => $request->portfolio,
                    'chapter_id' => null,
                    'field_id'   => null,
                    'zone_id'    => null,
                ]);
                break;
        }

        // Password handling
        if ($request->filled('password')) {
            $stakeholder->password = Hash::make($request->password);
        } elseif (!$stakeholder->password) {
            $stakeholder->password = Hash::make('12345@GSF0101');
        }

        // Signature handling
        if ($request->hasFile('signature')) {
            $stakeholder->signature = app(FileUploadService::class)->secureUpload(
                $request->file('signature'),
                'signatures',
                $stakeholder->signature
            );
        }

        if ($request->hasFile('avatar')) {
            $stakeholder->avatar = app(FileUploadService::class)->uploadImage(
                $request->file('avatar'),
                'avatars',
                $stakeholder->avatar
            );

        }

        // Update general info
        $stakeholder->fill([
            'name'  => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'gender' => $request->gender,
            // 'role'  => $role,
            'day'   => $request->day,
            'month' => $request->month,
            'year'  => $request->year,
            'status'  => $request->status,
            'role_id'  => $request->role_id,
            'designation_id'  => $request->designation_id,
        ])->save();

        return redirect()
            ->route('stakeholderpersonnel.index')
            ->with('message', 'Update Successful');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stakeholder  $stakeholder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stakeholder $id)
    {
        $this->deleteImage($id->signature);
        $this->deleteImage($id->avatar);
        $id->delete();

        return back()->with('message', 'Operation succesful!');
    }
}
