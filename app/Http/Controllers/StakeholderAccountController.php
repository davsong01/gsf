<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Services\UserService;
use App\Services\ExcelService;
use App\Models\StakeholderReport;
use App\Services\AppraisalService;
use App\Services\FileUploadService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\StakeholderDesignation;
use App\Traits\UserDatatableFeaturesTrait;
use App\Http\Controllers\Concerns\LogsStakeholderAccessDenials;

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
        
        return view('stakeholder.dashboard', compact('reports', 'chapter', 'user', 'appraisalAccess', 'appraisalSummary'));
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

        return view('stakeholder.users.index', [
            'routes' => [
                'create' => route('stakeholders.users.create'),
                'import' => route('stakeholders.import.index'),
                'export' => route('stakeholders.export'),
                'all'    => route('stakeholders.all'),
            ],
            'isAdmin' => false,
            'chapter'   => $chapter,
        ]);
    }

    public function alumniIndex(Request $request)
    {
        $chapter = auth()->guard('stakeholder')->user()?->chapter;

        return view('stakeholder.users.index', [
            'routes' => [
                'create' => route('stakeholders.alumni.create'),
                'import' => route('stakeholders.alumni.index'),
                'export' => route('stakeholders.export'),
                'all'    => route('stakeholders.alumni.all'),
            ],
            'isAdmin' => false,
            'chapter'   => $chapter,
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

        return redirect()->route('stakeholders.users.index')->with('message', 'User added successfully.');
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
