<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use Illuminate\Http\File;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FileUploadService;
use App\Rules\UniqueStakeholderRole;
use App\Http\Controllers\Controller;
use App\Models\StakeholderRole;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Mail\NotificationEmail;

class StakeholderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->role == 1) {
			$query = Stakeholder::query()
                ->with(['role', 'designation', 'chapter', 'field', 'zone'])
                ->orderBy('created_at', 'desc');

            if (request()->filled('search')) {
                $search = trim(request('search'));
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if (request()->filled('role_id')) {
                $query->where('role_id', request('role_id'));
            }

            if (request()->filled('status')) {
                $query->where('status', request('status'));
            }

            if (request()->filled('appraisal_access')) {
                if (request('appraisal_access') === 'system') {
                    $query->where('access_appraisal_system', true);
                } elseif (request('appraisal_access') === 'evaluation') {
                    $query->where('access_appraisal_evaluation', true);
                } elseif (request('appraisal_access') === 'none') {
                    $query->where(function ($builder) {
                        $builder->where('access_appraisal_system', false)
                            ->where('access_appraisal_evaluation', false);
                    });
                }
            }

            $roles = StakeholderRole::where('status', 'active')->orderBy('name')->get();
            $stakeholders = $query->paginate(100)->withQueryString();
            $count = ($stakeholders->currentPage() - 1) * $stakeholders->perPage();
            $stats = [
                'total' => Stakeholder::count(),
                'active' => Stakeholder::where('status', 'active')->count(),
                'appraisal_system' => Stakeholder::where('access_appraisal_system', true)->count(),
                'appraisal_evaluation' => Stakeholder::where('access_appraisal_evaluation', true)->count(),
            ];

			return view('admin.stakeholders.index', compact('stakeholders', 'count', 'roles', 'stats'));

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
        $stakeholder = null;
        $fields = Field::all();
        $zones = Zone::all();
        $chapters = Chapter::all();
        $months = getMonths();
        $roles = StakeholderRole::where('status', 'active')->get();
        $portfolios = getCommunityPortfolios();

        return view('admin.stakeholders.edit', compact('stakeholder', 'zones', 'fields', 'chapters', 'months', 'roles','portfolios'));
    }

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
            'phone'      => ['nullable', 'string', 'max:50'],
            'password'   => ['nullable', 'string', 'min:8'],
            'day'        => ['nullable', 'integer', 'between:1,31'],
            'month'      => ['nullable', 'integer', 'between:1,12'],
            'year'       => ['nullable', 'digits:4'],
            'role_id'    => ['required', 'integer', 'exists:stakeholder_roles,id'],
            'chapter_id' => ['nullable', 'integer', 'exists:chapters,id'],
            'zone_id'    => ['nullable', 'integer', 'exists:zones,id'],
            'field_id'   => ['nullable', 'integer', 'exists:fields,id'],
            'designation_id' => ['nullable', 'integer', 'exists:stakeholder_designations,id'],
            'portfolio'  => ['nullable', 'string', 'max:255'],
            'signature'  => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'status'     => ['required', 'in:active,inactive'],
            'gender'     => ['nullable'],
            'access_appraisal_system' => ['nullable', 'boolean'],
            'access_appraisal_evaluation' => ['nullable', 'boolean'],
        ]);

        $stakeholder = new Stakeholder();
        $role = StakeholderRole::findOrFail($request->role_id);

        /*
        |--------------------------------------------------------------------------
        | Role-based assignments (SAME AS UPDATE)
        |--------------------------------------------------------------------------
        */
        switch (true) {
            case in_array($role->slug, ['chapter-representative']):
                $chapter = Chapter::find($request->chapter_id);

                $stakeholder->fill([
                    'chapter_id' => $request->chapter_id,
                    'zone_id'    => $chapter?->zone?->id,
                    'field_id'   => $chapter?->field?->id,
                    'portfolio'  => null,
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

            case $role->slug === 'portfolio':
                $stakeholder->fill([
                    'portfolio'  => $request->portfolio,
                    'chapter_id' => null,
                    'zone_id'    => null,
                    'field_id'   => null,
                ]);
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Password handling (same logic as update)
        |--------------------------------------------------------------------------
        */
        $stakeholder->password = Hash::make(
            $request->filled('password') ? $request->password : '12345@GSF2021'
        );

        /*
        |--------------------------------------------------------------------------
        | Signature upload
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('signature')) {
            $stakeholder->signature = app(FileUploadService::class)->secureUpload(
                $request->file('signature'),
                'signatures'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | General info
        |--------------------------------------------------------------------------
        */
        $stakeholder->fill([
            'name'           => $request->name,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'gender'         => $request->gender,
            'day'            => $request->day,
            'month'          => $request->month,
            'year'           => $request->year,
            'status'         => $request->status,
            'role_id'        => $request->role_id,
            'designation_id' => $request->designation_id,
            'access_appraisal_system' => $request->boolean('access_appraisal_system'),
            'access_appraisal_evaluation' => $request->boolean('access_appraisal_evaluation'),
        ])->save();

        return redirect()
            ->route('stakeholderpersonnel.index')
            ->with('message', 'Operation Successful');
    }

    public function update(Request $request, Stakeholder $stakeholderpersonnel)
    {
        $stakeholder = $stakeholderpersonnel;

        // Validate input
        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:stakeholders,email,' . $stakeholder->id],
            'phone'      => ['nullable', 'string', 'max:50'],
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
            'access_appraisal_system' => ['nullable', 'boolean'],
            'access_appraisal_evaluation' => ['nullable', 'boolean'],
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

        if($request->email != $stakeholder->email && !empty($stakeholder->chapter_id)){
            $stakeholder->chapter->update([
                'email' => $request->email
            ]);
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
            'access_appraisal_system' => $request->boolean('access_appraisal_system'),
            'access_appraisal_evaluation' => $request->boolean('access_appraisal_evaluation'),
        ])->save();

        return redirect()
            ->route('stakeholderpersonnel.index')
            ->with('message', 'Update Successful');
    }

    public function show(Stakeholder $stakeholder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Stakeholder  $stakeholder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stakeholder $id)
    {
        // $this->deleteImage($id->signature);
        // $this->deleteImage($id->avatar);
        // $id->delete();

        return back()->with('message', 'Operation succesful!');
    }

    public function bulkAction(Request $request)
    {
        if (auth()->user()->role != 1) {
            abort(404);
        }

        $data = $request->validate([
            'bulk_action' => ['required', 'in:allow_appraisal_access,remove_appraisal_access'],
            'selected_ids' => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'exists:stakeholders,id'],
        ]);

        $stakeholders = Stakeholder::whereIn('id', $data['selected_ids'])->get();

        if ($data['bulk_action'] === 'allow_appraisal_access') {
            foreach ($stakeholders as $stakeholder) {
                $stakeholder->update([
                    'access_appraisal_system' => true,
                    'access_appraisal_evaluation' => true,
                ]);
            }
        } elseif ($data['bulk_action'] === 'remove_appraisal_access') {
            foreach ($stakeholders as $stakeholder) {
                $stakeholder->update([
                    'access_appraisal_system' => false,
                    'access_appraisal_evaluation' => false,
                ]);
            }
        }

        return back()->with('message', 'Bulk action completed successfully.');
    }

    public function resendCredentials(Stakeholder $stakeholderpersonnel)
    {
        $stakeholder = $stakeholderpersonnel;

        if (! filled($stakeholder->email)) {
            return back()->with('error', 'This stakeholder does not have an email address.');
        }

        $passwordPlain = Str::random(10);

        $stakeholder->update([
            'password' => bcrypt($passwordPlain),
            'credentials_sent' => 1,
        ]);

        $loginLink = url('/stakeholders/login');

        Mail::to($stakeholder->email)->send(new NotificationEmail([
            'type' => 'generic',
            'subject' => 'Your GSF Digital Portal Access',
            'content' => "
                <p>Dear {$stakeholder->name},</p>

                <p>Calvary greetings.</p>

                <p>Your access to the <strong>GSF Digital Portal</strong> has been reset.
                Below are your new login credentials:</p>

                <p>
                    <strong>Email:</strong> {$stakeholder->email}<br>
                    <strong>Password:</strong> {$passwordPlain}
                </p>

                <p>
                    Please <a href='{$loginLink}'>click here to login</a> and change your password immediately after first login.
                </p>

                <p>
                    If you have any issues accessing the portal, kindly contact the GSF ICT team.
                </p>

                <p>
                    In His Service,<br>
                    <strong>GSF National ICT</strong>
                </p>
            ",
        ]));

        return back()->with('message', 'Credentials resent successfully.');
    }
}
