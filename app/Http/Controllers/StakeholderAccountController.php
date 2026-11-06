<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\GeneralSetting;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Services\UserService;
use App\Services\ExcelService;
use App\Models\StakeholderReport;
use App\Models\CampusRecordBookUpload;
use App\Services\AppraisalService;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\StakeholderDesignation;
use App\Modules\Blog\Models\BlogPost;
use App\Traits\UserDatatableFeaturesTrait;
use App\Http\Controllers\Concerns\LogsStakeholderAccessDenials;
use Illuminate\Validation\Rule;

class StakeholderAccountController extends Controller
{
	use UserDatatableFeaturesTrait;
    use LogsStakeholderAccessDenials;
    protected $userService;
    protected $user;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->user = auth::guard('stakeholder')->user();
        // $this->user = auth()->guard('stakeholder')->user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function dashboard()
    {
        $user = auth::guard('stakeholder')->user();
        $chapter = $user->chapter ?? null;
        $role = Auth::guard('stakeholder')->user()->role_id;
        // return redirect(route('stakeholder.login'));
        if (!auth::guard('stakeholder')->check()) return redirect(route('stakeholders.login'));

        if (in_array($role, chapterStakeholders())) {
            $reports = StakeholderReport::whereChapterId(Auth::guard('stakeholder')->user()->chapter_id)->orderBy('created_at', 'desc')->get();
        } elseif (in_array($role, fieldStakeholders())) {
            $reports = StakeholderReport::whereFieldId(Auth::guard('stakeholder')->user()->field_id)->orderBy('created_at', 'desc')->get();
        } elseif (in_array($role, zoneStakeholders())) {
            $reports = StakeholderReport::whereZoneId(Auth::guard('stakeholder')->user()->zone_id)->orderBy('created_at', 'desc')->get();
        } elseif (in_array($role, array_merge(secretariatStakeholders(), ncpStakeholders()))) {
            $reports = StakeholderReport::orderBy('created_at', 'desc')->get();
        } elseif (in_array($role, [6])) {
            $reports = StakeholderReport::orderBy('created_at', 'desc')->get();
        }

        $reports = collect([]);
        $appraisalAccess = app(AppraisalService::class)->dashboardAccess($user);
        $appraisalSummary = app(AppraisalService::class)->summary($user);
        $dashboardPosts = collect();
        $dashboardEvents = collect();
        $canManagePosts = \App\Modules\Blog\BlogPermissions::allows($user, \App\Modules\Blog\BlogPermissions::VIEW)
            || \App\Modules\Blog\BlogPermissions::allows($user, \App\Modules\Blog\BlogPermissions::CREATE)
            || \App\Modules\Blog\BlogPermissions::allows($user, \App\Modules\Blog\BlogPermissions::MANAGE_OWN);

        if ($canManagePosts) {
            $authoredPosts = BlogPost::query()
                ->where('author_type', $user::class)
                ->where('author_id', $user->id);

            $dashboardPosts = (clone $authoredPosts)
                ->where('type', '!=', 'event')
                ->latest('updated_at')
                ->limit(3)
                ->get();

            $dashboardEvents = (clone $authoredPosts)
                ->where('type', 'event')
                ->orderByRaw('CASE WHEN event_starts_at >= ? THEN 0 ELSE 1 END', [now()])
                ->orderByRaw('CASE WHEN event_starts_at >= ? THEN event_starts_at END ASC', [now()])
                ->orderByRaw('CASE WHEN event_starts_at < ? THEN event_starts_at END DESC', [now()])
                ->limit(3)
                ->get();
        }
        
        return view('stakeholder.dashboard', compact('reports', 'chapter', 'user', 'appraisalAccess', 'appraisalSummary', 'dashboardPosts', 'dashboardEvents', 'canManagePosts'));
    }

    public function profile()
    {
        return view('stakeholder.profile');
    }

    public function saveProfile(Request $request)
    {
        //Handle Password
        if ($request['password']) {
			$password = Hash::make($request['password']);
		} else {
			$password = Hash::make($request['12345@GSF2021']);
		}
        $user = Auth::guard('stakeholder')->user();

        if ($request->hasFile('avatar')) {
            $user->avatar = app(FileUploadService::class)->uploadImage(
                $request->file('avatar'),
                'avatars',
                $user->avatar
            );
        }

        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->password = $password;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->day = $request->day;
        $user->month = $request->month;
        $user->year = $request->year;

        $user->save();

        return back()->with('message', 'Update Successful');
    }

    public function memberIndex(Request $request)
    {
        $chapter = auth()->guard('stakeholder')->user()?->chapter;
        $memberCount = $chapter?->users()->where('role', '<>', 1)->where('is_graduated', 0)->count() ?? 0;
        $activeCount = $chapter?->users()->where('role', '<>', 1)->where('is_graduated', 0)->where('status', 'active')->count() ?? 0;

        return view('stakeholder.users.index', [
            'routes' => [
                'create' => route('stakeholders.users.create'),
                'import' => route('stakeholders.import.index'),
                'recordBook' => route('stakeholders.users.record-book'),
                'export' => route('stakeholders.export'),
                'all'    => route('stakeholders.all'),
            ],
            'isAdmin' => false,
            'chapter'   => $chapter,
            'isAlumni' => false,
            'memberCount' => $memberCount,
            'activeCount' => $activeCount,
            'canUploadRecordBook' => GeneralSetting::query()->value('enable_campus_record_book_upload') ?? true,
        ]);
    }

    public function recordBookUpload(Request $request)
    {
        $stakeholder = auth()->guard('stakeholder')->user();
        abort_unless($stakeholder && $stakeholder->chapter_id, 403);
        abort_unless((bool) (GeneralSetting::query()->value('enable_campus_record_book_upload') ?? true), 404);

        $recordType = $request->query('type') === 'alumni' ? 'alumni' : 'member';
        $uploads = CampusRecordBookUpload::where('chapter_id', $stakeholder->chapter_id)
            ->where('record_type', $recordType)
            ->latest()->limit(100)->get();

        $memberIndexUrl = $recordType === 'alumni' ? route('stakeholders.alumni.index') : route('stakeholders.users.index');

        return view('stakeholder.users.record-book', compact('uploads', 'recordType', 'memberIndexUrl'));
    }

    public function storeRecordBookImages(Request $request)
    {
        $stakeholder = auth()->guard('stakeholder')->user();
        abort_unless($stakeholder && $stakeholder->chapter_id, 403);
        abort_unless((bool) (GeneralSetting::query()->value('enable_campus_record_book_upload') ?? true), 404);

        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:20'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'types' => ['required', 'array', 'size:'.count($request->file('images', []))],
            'types.*' => ['required', 'in:member,alumni'],
        ]);

        $saved = [];
        foreach ($validated['images'] as $index => $image) {
            $sha256 = hash_file('sha256', $image->getRealPath());
            $originalUpload = CampusRecordBookUpload::where('sha256', $sha256)->oldest()->first();
            $path = app(FileUploadService::class)->secureUpload(
                $image,
                'campus-record-books',
                null,
                Str::uuid().'.'.$image->extension()
            );

            $upload = CampusRecordBookUpload::create([
                'chapter_id' => $stakeholder->chapter_id,
                'stakeholder_id' => $stakeholder->id,
                'image_path' => $path,
                'original_name' => $image->getClientOriginalName(),
                'mime_type' => $image->getMimeType(),
                'file_size' => $image->getSize(),
                'sha256' => $sha256,
                'record_type' => $validated['types'][$index],
                'status' => $originalUpload ? 'duplicate' : 'pending',
                'duplicate_of_upload_id' => $originalUpload
                    ? ($originalUpload->duplicate_of_upload_id ?: $originalUpload->id)
                    : null,
            ]);

            $saved[] = [
                'id' => $upload->id,
                'name' => $upload->original_name,
                'size' => $upload->file_size,
                'record_type' => $upload->record_type,
                'status' => $upload->status,
                'uploaded_at' => $upload->created_at->format('M j, Y g:i A'),
                'preview_url' => route('protected.download', ['file' => $upload->image_path]),
                'delete_url' => route('stakeholders.users.record-book.destroy', $upload),
            ];
        }

        return response()->json(['message' => count($saved).' image(s) uploaded successfully.', 'uploads' => $saved]);
    }

    public function deleteRecordBookImage(CampusRecordBookUpload $upload)
    {
        $stakeholder = auth()->guard('stakeholder')->user();
        abort_unless($stakeholder && $stakeholder->chapter_id, 403);
        abort_unless((bool) (GeneralSetting::query()->value('enable_campus_record_book_upload') ?? true), 404);
        abort_unless($upload->chapter_id === $stakeholder->chapter_id && $upload->stakeholder_id === $stakeholder->id, 404);
        abort_unless(in_array($upload->status, ['pending', 'rejected', 'duplicate'], true), 403, 'Approved or extracted images cannot be deleted.');

        $path = base64_decode($upload->image_path, true);
        if ($path && !str_contains($path, '..')) {
            Storage::disk('protected_uploads')->delete($path);
        }
        $upload->delete();

        return response()->json(['message' => 'Uploaded image deleted.']);
    }

    public function alumniIndex(Request $request)
    {
        $chapter = auth()->guard('stakeholder')->user()?->chapter;
        $memberCount = $chapter?->users()->where('role', '<>', 1)->where('is_graduated', 1)->count() ?? 0;
        $activeCount = $chapter?->users()->where('role', '<>', 1)->where('is_graduated', 1)->where('status', 'active')->count() ?? 0;

        return view('stakeholder.users.index', [
            'routes' => [
                'create' => route('stakeholders.alumni.create'),
                'import' => route('stakeholders.alumni.index'),
                'recordBook' => route('stakeholders.alumni.record-book', ['type' => 'alumni']),
                'export' => route('stakeholders.export'),
                'all'    => route('stakeholders.alumni.all'),
            ],
            'isAdmin' => false,
            'chapter'   => $chapter,
            'isAlumni' => true,
            'memberCount' => $memberCount,
            'activeCount' => $activeCount,
            'canUploadRecordBook' => GeneralSetting::query()->value('enable_campus_record_book_upload') ?? true,
        ]);
    }

    public function allMemberUsers(Request $request){
        $chapterId = auth()->guard('stakeholder')->user()->chapter_id; // Sub-admins restricted by chapter

        $request['chapter_id'] = $chapterId;
        $request['canDelete'] = false;
        $request['canEdit'] = true;
        $request['canSwitch'] = false;
        $request['isStakeholder'] = true;
        $request['filters'] = [
            'is_graduated' => 0
        ];

        $json_data = $this->userService->getAllUsers( $request->all());

        return response()->json($json_data);
    }

    public function allMemberalumni(Request $request){
        $chapterId = auth()->guard('stakeholder')->user()->chapter_id; // Sub-admins restricted by chapter

        $request['chapter_id'] = $chapterId;
        $request['canDelete'] = false;
        $request['canEdit'] = true;
        $request['canSwitch'] = false;
        $request['isStakeholder'] = true;
        $request['filters'] = [
            'is_graduated' => 1
        ];

        $json_data = $this->userService->getAllUsers( $request->all());

        return response()->json($json_data);
    }

    public function memberEdit(User $user)
	{
        if($user->chapter_id != auth()->guard('stakeholder')->user()->chapter_id){
            $this->logStakeholderAccessDenied(request(), 'member edit denied: user belongs to another chapter', [
                'target_user_id' => $user->id ?? null,
                'target_chapter_id' => $user->chapter_id ?? null,
            ]);

            return back()->with('error', 'Invalid route');
        }

		$portfolios = getCommunityPortfolios();
        $campusDesignations = StakeholderDesignation::select('id','name')->where('type', 'chapter_executive')->orderBy('order')->get();
		$sessions = range(date('1982'), date('Y'));
        $isAdmin = true;

        return view('stakeholder.users.edit', compact('user', 'portfolios', 'sessions','campusDesignations', 'isAdmin'));
	}

    public function memberCreate()
	{
		$portfolios = getCommunityPortfolios();
        $campusDesignations = StakeholderDesignation::select('id','name')->where('type', 'chapter_executive')->orderBy('order')->get();
		$sessions = range(date('1982'), date('Y'));
        $isAdmin = false;

        return view('stakeholder.users.edit', compact('portfolios', 'sessions','campusDesignations', 'isAdmin'));
	}

    public function usersImportIndex()
	{
		return view('stakeholder.users.import');
	}

	public function import(Request $request)
	{
        $request['chapter_id'] = auth()->guard('stakeholder')->user()->chapter_id;

		$data = $this->validate($request, [
			'type' => 'required|numeric',
			'chapter_id' => 'required|numeric',
			'file' => 'required|mimes:xlsx,csv',
		]);

		try {
			$this->userService->importUsers($request);
		} catch (Exception $ex) {
			$error = $ex->getMessage();
			return back()->with('error', $error);
		}

        return redirect(route('stakeholders.users.index'));
	}

    public function chapterEdit(Chapter $chapter)
    {
        if($chapter->id != auth()->guard('stakeholder')->user()->chapter_id){
            $this->logStakeholderAccessDenied(request(), 'chapter edit denied: stakeholder attempted to access another chapter', [
                'target_chapter_id' => $chapter->id ?? null,
            ]);

            return back()->with('error', 'Invalid route');
        }

        $chapter->load('meetings');
        return view('stakeholder.chapters.edit', compact('chapter'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function chapterUpdate(Request $request, Chapter $chapter)
    {
        if ($chapter->id != auth()->guard('stakeholder')->user()->chapter_id) {
            $this->logStakeholderAccessDenied($request, 'chapter update denied: stakeholder attempted to update another chapter', [
                'target_chapter_id' => $chapter->id,
            ]);

            return back()->with('error', 'Invalid route');
        }

        if ($request->has('slug')) {
            $request->merge(['slug' => Str::lower(trim((string) $request->input('slug')))]);
        }
        $request->validate([
            'slug' => ['sometimes', 'required', 'alpha_dash', 'max:255', Rule::unique('chapters', 'slug')->ignore($chapter->id)],
            'fellowship_bank_name' => ['nullable', 'string', 'max:120'],
            'fellowship_account_name' => ['nullable', 'string', 'max:160'],
            'fellowship_account_number' => ['nullable', 'string', 'max:40'],
        ]);

        if($request->has('chapter_banner')){
            $request['banner'] = $this->uploadImage($request->chapter_banner, 'main/images/chapters');
        }

        $chapter->update($request->except('chapter_banner','name'));

        return redirect()->back()->with('message', 'Update successful');
    }

    public function memberUpdate(Request $request, User $user){
        if($user->chapter_id != auth()->guard('stakeholder')->user()->chapter_id){
            $this->logStakeholderAccessDenied($request, 'member update denied: user belongs to another chapter', [
                'target_user_id' => $user->id ?? null,
                'target_chapter_id' => $user->chapter_id ?? null,
            ]);

            return back()->with('error', 'Invalid route');
        }

        // Validate email uniqueness
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|unique:users,phone,' . $user->id,
        ]);


        // Prepare data using the service
        $data = $this->userService->prepareUserData($request, $user);

        try {
            $this->userService->updateUser($user, $data);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('stakeholders.users.index')->with('message', 'User updated successfully.');
    }

    public function memberStore(Request $request){
        $request['chapter_id'] = auth()->guard('stakeholder')->user()->chapter_id;

        $request->validate([
            'email' => 'unique:users,email',
            'phone' => 'unique:users,phone',
        ]);

        $data = $this->userService->prepareUserData($request);

        try {
            $this->userService->createUser($data);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route($request->input('is_graduated') == 1 ? 'stakeholders.alumni.index' : 'stakeholders.users.index')
            ->with('message', 'User added successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
