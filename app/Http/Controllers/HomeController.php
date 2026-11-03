<?php

namespace App\Http\Controllers;

use App\Imports\GeneralUsersImport;
use App\Models\Chapter;
use App\Models\ConferenceEdition;
use App\Models\ConferenceFaq;
use App\Models\ConferencePlan;
use App\Models\ConferenceSchedule;
use App\Models\ConferenceSpeaker;
use App\Models\Event;
use App\Models\Field;
use App\Models\GeneralSetting;
use App\Models\Nec;
use App\Models\NewListing;
use App\Models\Stakeholder;
use App\Models\StakeholderDesignation;
use App\Models\TempMember;
use App\Models\Transaction;
use App\Models\User;
use App\Modules\Blog\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    private $conference;
    private $frontend;

    public function __construct()
    {
        $this->conference = activeConferenceEdition();
        $this->frontend = frontendTemplate();

        if (!isset($this->conference) && empty($this->conference)) {
            return true;
        }else{
            return false;
        }
    }

    public function index() {
        $upcomingEvents = Event::with('chapter')
            ->where('date', '>=', date('Y-m-d'))
            ->orderBy('date', 'ASC')
            ->limit(6)
            ->get();

        $latestPosts = Schema::hasTable('blog_posts')
            ? BlogPost::published()
                ->with(['category', 'author'])
                ->latest()
                ->limit(3)
                ->get()
            : collect();

        $leadershipRoleIds = array_values(array_unique(array_merge(
            zoneStakeholders(),
            fieldStakeholders(),
            portfolioStakeholders()
        )));

        $leaders = Stakeholder::with(['designation', 'role'])
            ->whereIn('role_id', $leadershipRoleIds)
            ->where('status', 'active')
            ->orderByRaw('designation_id IS NULL, designation_id ASC')
            ->limit(4)
            ->get();

        $impact = [
            'campuses' => Chapter::count(),
            'fields' => Field::count(),
            'leaders' => Stakeholder::where('status', 'active')->count(),
        ];

        return view('frontend.site.home', compact(
            'upcomingEvents',
            'latestPosts',
            'leaders',
            'impact'
        ));
    }

    public function directory()
    {
        $directoryStats = [
            'campuses' => Chapter::where('id', '<>', 86)->count(),
            'members' => User::where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0)->count(),
            'alumni' => User::where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 1)->count(),
        ];

        $featuredCampuses = Chapter::with(['field', 'zone'])
            ->withCount(['users as members_count' => function ($query) {
                $query->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0);
            }])
            ->where('id', '<>', 86)
            ->orderByDesc('members_count')
            ->limit(6)
            ->get();

        return view('frontend.site.directory.index', compact('directoryStats', 'featuredCampuses'));
    }

    public function foundationalConcept()
    {
        return view('frontend.site.about.foundational-concept');
    }

    public function missionStrategies()
    {
        $siteSettings = GeneralSetting::first();

        return view('frontend.site.about.mission-strategies', compact('siteSettings'));
    }

    public function ourFathers()
    {
        return view('frontend.site.leaders.our-fathers');
    }

    public function siteDocument(string $document)
    {
        $documents = [
            'constitution' => [
                'column' => 'gsf_constitution_path',
                'filename' => 'GSF-Constitution.pdf',
            ],
            'code-of-conduct' => [
                'column' => 'gsf_code_of_conduct_path',
                'filename' => 'GSF-Code-of-Conduct.pdf',
            ],
        ];

        abort_unless(isset($documents[$document]), 404);

        $siteSettings = GeneralSetting::first();
        $configuration = $documents[$document];
        $path = $siteSettings?->{$configuration['column']};

        abort_if(blank($path) || !Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response(
            $path,
            $configuration['filename'],
            ['Content-Type' => 'application/pdf']
        );
    }

    // public function regPage($type)
    // {
    //     if (isset($type) && $this->conference) {
    //         $plan = ConferencePlan::where('id', $type)->where('conference_edition_id', $this->conference->id)->first();

    //         $chapters = Chapter::orderBy('name')->get();
    //         $fields = Field::orderBy('name')->get();
    //         $setting = $this->conference;

    //         $conference_year = Carbon::parse($setting->start_date)->year;
    //         $alumnis_amount = [
    //             'alumni_registration_fee' => $setting->alumni_registration_fee,
    //             'new_alumni_registration_fee' => $setting->new_alumni_registration_fee
    //         ];

    //         $title = '';

    //         if($type == 1){
    //             $title = 'Single Registration';
    //         }

    //         if ($type == 2) {
    //             $title = 'Mass Registration';
    //         }

    //         if ($type == 3) {
    //             $title = 'Alumni Registration';
    //         }

    //         $registrationFields = reformatRegistrationFields(
    //             $setting->ministry
    //                 ->fields()
    //                 ->where('field_usage', 'registration')
    //                 ->orderBy('display_order', 'ASC')
    //                 ->get()
    //         );
    //         return view('frontend.conference.template'. $this->conference->template_id.'.registration',compact('title','chapters','setting','conference_year','alumnis_amount','type', 'fields', 'registrationFields'));
    //     } else {
    //         return abort(404);
    //     }
    // }
    public function regPage($type)
    {
        if (isset($type) && $this->conference) {
            $plan = ConferencePlan::where('id', $type)->where('conference_edition_id', $this->conference->id)->first();

            if(!$plan){
                return back()->with('error', 'Invalid Registration Type Selected');
            }

            $chapters = Chapter::orderBy('name')->get();
            $setting = $this->conference;
            $conference_year = Carbon::parse($setting->start_date)->year;
            $title = $plan->title;
            $fields = $plan->fields()->sortBy('display_order');
            // dd( $fields, $plan);
            $registrationFields = reformatRegistrationFields($fields);
            return view('frontend.site.conferences.registration', compact('title', 'chapters', 'setting', 'conference_year', 'type', 'fields', 'registrationFields','plan'));
        } else {
            return abort(404);
        }
    }

    public function showVerifyConferenceRegistration()
    {
        return view('frontend.conference.template' . $this->conference->template_id . '.verify-registration');
    }

    public function verifyConferenceRegistration(Request $request)
    {
        $query = trim($request->input('q'));

        if (!$query) {
            return response()->json([
                'error' => 'Please provide a Transaction ID, Email, or Registration ID.'
            ], 422);
        }


        $transactions = Transaction::query()
        ->with(['conferenceEdition', 'conferencePlan'])
        ->where(function ($q) use ($query) {
            $q->where('transid', $query)
            ->orWhere('email', $query);
        })
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($tx) {
            $mask = fn($str) => strlen($str) > 4
                ? substr($str, 0, 2) . str_repeat('*', strlen($str) - 4) . substr($str, -2)
                : $str;

            return [
                'id' => $tx->id,
                'transid' => $mask($tx->transid),
                'email' => $mask($tx->email),
                'name' => $tx->name,
                'status' => $tx->status,
                'registration_status' => $tx->registration_status,
                'conference_name' => $tx->conferenceEdition?->conference_theme ?? 'N/A',
                'plan_name' => $tx->conferencePlan?->level ?? 'N/A',
                'created_at' => optional($tx->created_at)->format('d M, Y H:i'),
            ];
        });


        return response()->json($transactions);
    }
    public function alumni(Request $request) {
        $alumnis = User::with(['campus', 'stakeholder.role', 'stakeholder.designation'])
            ->whereHas('campus')
            ->where('status', 'active')
            ->where('role', '<>', 1)
            ->where('is_graduated', 1)
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'LIKE', '%'.$request->string('name').'%');
            })
            ->when($request->filled('school'), function ($query) use ($request) {
                $query->whereHas('campus', function ($campusQuery) use ($request) {
                    $campusQuery->where('name', 'LIKE', '%'.$request->string('school').'%');
                });
            })
            ->latest()
            ->paginate(24)
            ->withQueryString();

        return view('frontend.site.directory.people', [
            'people' => $alumnis,
            'directoryType' => 'alumni',
            'searchName' => $request->input('name', ''),
            'searchSchool' => $request->input('school', ''),
        ]);
    }

    public function nec()
    {
        $nec = Stakeholder::with(['designation', 'role'])
            ->where('status', 'active')
            ->whereIn('role_id', [1, 2, 6, 7, 8])
            ->get()
            ->sortBy(function (Stakeholder $stakeholder) {
                return sprintf(
                    '%05d-%02d-%s',
                    $stakeholder->designation?->order ?? 99999,
                    $stakeholder->role_id,
                    mb_strtolower($stakeholder->name)
                );
            })
            ->values();

        return view('frontend.site.leaders.nec', compact('nec'));
    }

    public function students(Request $request) {
        $members = User::with(['campus', 'stakeholder.role', 'stakeholder.designation'])
            ->whereHas('campus')
            ->where('status', 'active')
            ->where('role', '<>', 1)
            ->where('is_graduated', 0)
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'LIKE', '%'.$request->string('name').'%');
            })
            ->when($request->filled('school'), function ($query) use ($request) {
                $query->whereHas('campus', function ($campusQuery) use ($request) {
                    $campusQuery->where('name', 'LIKE', '%'.$request->string('school').'%');
                });
            })
            ->latest()
            ->paginate(24)
            ->withQueryString();

        return view('frontend.site.directory.people', [
            'people' => $members,
            'directoryType' => 'members',
            'searchName' => $request->input('name', ''),
            'searchSchool' => $request->input('school', ''),
        ]);
    }

    public function programs() {
        return redirect()->route('conferences.index');
    }

    public function chapters(Request $request) {
        $chapters = Chapter::with(['field', 'zone'])
            ->withCount(['users as members_count' => function ($query) {
                $query->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0);
            }])
            ->where('id', '<>', 86)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($search) use ($term) {
                    $search->where('name', 'LIKE', $term)
                        ->orWhere('address', 'LIKE', $term)
                        ->orWhereHas('field', fn ($field) => $field->where('name', 'LIKE', $term))
                        ->orWhereHas('zone', fn ($zone) => $zone->where('name', 'LIKE', $term));
                });
            })
            ->orderBy('name')
            ->paginate(18)
            ->withQueryString();

        return view('frontend.site.directory.campuses', [
            'chapters' => $chapters,
            'searchTerm' => $request->input('q', ''),
        ]);
    }

    public function studentsByChapter(Request $request, $id){
        return $this->campusPeopleListing($request, $id, 'members');
    }

    public function alumniByChapter(Request $request, $id)
    {
        return $this->campusPeopleListing($request, $id, 'alumni');
    }

    private function campusPeopleListing(Request $request, $id, string $directoryType)
    {
        $campus = Chapter::findOrFail($id);
        $isAlumni = $directoryType === 'alumni';

        $people = User::with(['campus', 'stakeholder.role', 'stakeholder.designation'])
            ->where('chapter_id', $campus->id)
            ->where('status', 'active')
            ->where('role', '<>', 1)
            ->where('is_graduated', $isAlumni ? 1 : 0)
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'LIKE', '%'.$request->string('name').'%');
            })
            ->latest()
            ->paginate(24)
            ->withQueryString();

        return view('frontend.site.directory.people', [
            'people' => $people,
            'directoryType' => $directoryType,
            'searchName' => $request->input('name', ''),
            'searchSchool' => '',
            'campus' => $campus,
        ]);
    }

    private function getSidebar() {
        $chapters = Chapter::all();
        $portfolios = getCommunityPortfolios();

        return ['chapters' => $chapters, 'portfolios' => $portfolios];
    }

    public function generalSearch(Request $request)
    {

        $this->validate($request, ['name' => 'required']);;

        $searchMember = User::with('campus')->select("users.*", "chapter_id")
        ->where("name", "LIKE", "%{$request->name}%")
        ->where("role", "<>", 1);

        $count = $searchMember->count();
        $searchMember = $searchMember->paginate(30)->withQueryString();

        return view('frontend.' . frontendTemplate() . '.general_search_results', compact('searchMember', 'count'));
    }

    public function alumniSearch(Request $request)
    {
        return redirect()->route('people.alumni', $request->only('name', 'school'));
    }

    public function memberSearch(Request $request)
    {
        return redirect()->route('people.students', $request->only('name', 'school'));
    }

    public function autocomplete(Request $request)
    {
        if ($request->get('data')) {
            $data = $request->get('data');
            $data = Chapter::select("name", "id")
                ->where("name","LIKE","%{$data}%")
                ->take(3)->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative;width: 100%;">';
            foreach ($data as $row) {
                $output .= "
                <a href='singlecampus/" . $row->id. "' class='ml-2'  style='color:black;font-weight: bold;display: block;padding: 0 10px 0 10px;'><li>" . $row->name . "</li></a>";
            }
            $output .= '</ul>';
            echo $output;
        }

    }

    public function autoSearch(Request $request)
    {
        if ($request->get('data')) {
            $data = $request->get('data');
            $data = Chapter::select("name", "id")
                ->where("name","LIKE","%{$data}%")
                ->take(10)->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
            foreach ($data as $row) {
                $output .= "
            <li><a href='singlecampus/" . $row->id. "' class='ml-2'  style='color:black;font-weight: bold;'>" . $row->name . "</a></li>";
            }
            $output .= '</ul>';
            echo $output;
        }

    }

    public function memberAutoComplete(Request $request)
    {
        if ($request->get('data')) {
            $data = $request->get('data');
            $data = User::select("name", "id")
            ->where("name", "LIKE", "%{$data}%")
            ->take(10)->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
            foreach ($data as $row) {
                $output .= "
            <li><a href='singlecampus/" . $row->id . "' class='ml-2'  style='color:black;font-weight: bold;'>" . $row->name . "</a></li>";
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function alumniAutoComplete(Request $request)
    {
        if ($request->ajax()) {
            $query = $request->get('query');
            $data = User::wherehas('campus')->where("name", "LIKE", "%{$query}%")->where('role', '<>', 1)->take(5)->get();

            return response()->json($data);
        }
    }

    public function campusAutocomplete(Request $request)
    {
        // if ($request->get('data')) {
        //     $data = $request->get('data');
        //     $data = Chapter::select("name", "id")
        //         ->where("name", "LIKE", "%{$data}%")
        //         ->take(10)->get();

        //     $output = '<ul class="dropdown-menu form-control side" style="display:block; position:relative">';

        //     foreach ($data as $row) {
        //         $output .= "<option class='ml-2' name='chapter_id' value='" . $row->id . "' style='color:black;font-weight: bold;'>" . $row->name . "</option>";
        //     }
        //     $output .= '</ul>';
        //     echo $output;
        // }

        if ($request->ajax()) {
            $query = $request->get('query');
            $data = Chapter::select("name", "id")
                ->where("name", "LIKE", "%{$query}%")
                ->take(10)->get();

            return response()->json($data);
        }
    }


    // public function singleCampus(){
    //     $chapter = Chapter::where('id', request()->chapter)->first();

    //     $token = false;
    //     $realToken = '';

    //     if (request()->token) {
    //         if ($chapter->token != request()->token) {
    //             return back()->with('error', 'Invalid Token for GSF Chapter, Kindly contact the National Publicity Office');
    //         }

    //         $token = true;
    //         $realToken = $chapter->token;

    //         return view('frontend.conference.campusView', compact('chapter', 'token', 'realToken'));
    //     }

    //     $nationalevents = Event::where('chapter_id', 0)->get();
    //     $related = null;

    //     if($chapter->exists()){
    //         $related = Chapter::where('zone_id', $chapter->zone_id)->orWhere('field_id', $chapter->field->id)->get();
    //     }

    //     return view('frontend.' . frontendTemplate() . '.single_chapter', compact('nationalevents','chapter','related'));
    //     // return view('frontend.main.single-chapter', compact('chapter', 'nationalevents'));
    // }
    public function singleCampus($chapterId = null)
    {
        $id = $chapterId ?? request()->chapter ?? request()->chapter_id;

        $chapter = Chapter::with(['field', 'zone'])
            ->withCount([
                'users as members_count' => function ($query) {
                    $query->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0);
                },
                'users as alumni_count' => function ($query) {
                    $query->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 1);
                },
            ])
            ->findOrFail($id);

        $token = false;
        $realToken = '';

        if (request()->token) {
            if ($chapter->token != request()->token) {
                return back()->with('error', 'Invalid Token for GSF Chapter, Kindly contact the National Publicity Office');
            }

            $token = true;
            $realToken = $chapter->token;

            return view('frontend.conference.campusView', compact('chapter', 'token', 'realToken'));
        }

        $events = $chapter->events()
            ->where('date', '>=', date('Y-m-d'))
            ->orderBy('date')
            ->limit(4)
            ->get();

        $members = $chapter->users()
            ->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0)
            ->latest()->limit(6)->get();

        $alumni = $chapter->users()
            ->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 1)
            ->latest()->limit(6)->get();

        $related = collect();
        if ($chapter->zone_id || $chapter->field_id) {
            $related = Chapter::with(['field', 'zone'])
                ->withCount(['users as members_count' => function ($query) {
                    $query->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0);
                }])
                ->where('id', '<>', $chapter->id)
                ->where(function ($query) use ($chapter) {
                    if ($chapter->zone_id) $query->where('zone_id', $chapter->zone_id);
                    if ($chapter->field_id) $query->orWhere('field_id', $chapter->field_id);
                })
                ->orderBy('name')
                ->limit(3)
                ->get();
        }

        return view('frontend.site.directory.campus', compact('chapter', 'events', 'members', 'alumni', 'related'));
    }


    public function contactCampus(Request $request){
        $this->validate($request, [
            'email' => 'required',
            'phone' => 'required',
            'name' => 'required',
            'message' => 'required'
        ]);

        $chapter = Chapter::findorfail($request->chapter_id);

        if($chapter && !is_null($chapter->email)){
            $type = 'contactCampus';
            $name = 'Publicity Secretary';
            $subject = 'New fellowship contact form filled';
            $content = "Someone has filled the contact form on your fellowship's webpage, view details below '<br>'
            <p>
            <strong>Name: </strong>" . $name . "<br>
            <strong>Email: </strong>" . $request->email . "<br>
            <strong>Phone: </strong>" . $request->phone . "<br>
            <strong>Message: </strong>" . $request->message . "<br>
            </p>";

            try {
                //code...
                $mailresponse = $this->sendEmail($chapter->email, $type, $subject, $name, $content, 1);
            } catch (\Throwable $th) {
                //throw $th;
            }

        }
        return back()->with('message', 'Message sent successfully!');
    }
    public function studentSearch(Request $request) {
        $this->validate($request, ['name' => 'required|min:4']);
        $name = $request->name;
        $results =  User::wherehas('campus')->where("name","LIKE","%{$request->name}%")->where('status', 0)->where('role', '<>', 1)->get();

        if(!is_null($request->chapter) && !is_null($request->chapter_id)){

            $results = $results->where('chapter_id', $request->chapter_id);

        }

        return view('frontend.main.student-search', compact('results', 'name'));

    }

    public function singleAlumni($slug){
        $alumni = User::whereSlug($slug)->first();
        return view('frontend.' . frontendTemplate() . '.single-alumni', compact('alumni'));
    }

    public function singleUser($slug){
        $alumni = User::with([
            'campus.field',
            'campus.zone',
            'designation',
            'stakeholder.designation',
            'stakeholder.role',
        ])->whereSlug($slug)->firstOrFail();

        $related = collect();
        if ($alumni->chapter_id && $alumni->chapter_id != 86) {
            $related = User::with(['designation', 'campus'])
                ->where('chapter_id', $alumni->chapter_id)
                ->whereIn('status', ['active', 0, '0'])
                ->where('role', '<>', 1)
                ->where('id', '<>', $alumni->id)
                ->orderBy('name')
                ->limit(6)
                ->get();
        }

        return view('frontend.site.directory.profile', compact('alumni', 'related'));
    }

    public function singleStudent($slug){
        $alumni = User::whereSlug($slug)->first();

        return view('frontend.' . frontendTemplate() . '.single-alumni', compact('alumni'));

        // return view('frontend.main.single-alumni', compact('alumni'));

    }

    public function userContact(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'message' => 'required',
        ]);

        $alumni = User::findorfail($request->alumni_id);
        if(!is_null($alumni->email)){
            $type = 'user-contact';
            $name = $alumni->name;
            $subject = 'Someone Contacted you on GSF community';
            $content = "Someone has filled the contact form on your profile on GSF Directory webpage, view details below '<br>'
            <p>
            <strong>Name: </strong>" . $request->name . "<br>
            <strong>Email: </strong>" . $request->email . "<br>
            <strong>Phone: </strong>" . $request->phone . "<br>
            <strong>Message: </strong>" . $request->message . "<br>
            </p>";

            $this->saveContactForm($alumni->id, $type, $request->name, $request->email, $request->phone, $request->message );

            $email = [
                'subject' => $subject,
                'recipient_name' => $name,
                'type' => $type,
                'recipient' => $alumni->email,
                'content' => $content,
            ];

            $this->logEmail($email);
            // $mailresponse = $this->sendEmail($alumni->email, $type, $subject, $name, $content, 1);

            //Send toastr
            return back()->with('message', 'Message sent!');

        }else {
            return back()->with('error', 'Email not found, please try again later');
        }
    }

    public function newAlumni(){
        $chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
		$portfolios = StakeholderDesignation::where('type', 'chapter_executive')
			->orderBy('order')
			->orderBy('name')
			->get();
		$sessions = range(date('Y'), date('1982'));

		return view('frontend.site.newalumni', compact('chapters', 'portfolios', 'sessions'));
	}

    public function newDonation()
    {
        $chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
        $portfolios = getCommunityPortfolios();
        $sessions = range(date('Y'), date('1982'));

        return view('frontend.' . frontendTemplate() . '.newdonation', compact('chapters', 'portfolios', 'sessions'));
    }

	public function saveNewAlumni(Request $request){
        $new = NewListing::create($request->all());
        $setting = GeneralSetting::first();

        $data['type'] = 'new_listing';
        $data['chapter'] = Chapter::find($request->campus)->name;
        $data['request'] = $request->all();

        $email = [
            'subject' => 'New Listing',
            'recipient_name' => 'Admin',
            'type' => 'conference_bulk_email',
            'recipient' => $setting->official_email,
            'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
        ];

        $this->logEmail($email);

        return back()->with('message', 'Submission Added successfully');

	}

    public function uploadAlumni(Request $request, $type){
        //Handle Passport Upload
        $setting = GeneralSetting::first();
        if($type == 'single'){
            if ($request->has('image')) {
                $request['passport'] = $this->uploadImage($request->image, 'frontend/passports', 500, 500);
            }

            TempMember::updateOrCreate($request->except(['_token','image']));
            $request['type'] = 'alumni-upload';
            $request['chapter'] = Chapter::find($request->chapter)->name;

            $email = [
                'subject' => 'Thank you for submitting your details',
                'recipient_name' => $request['name'],
                'recipient' => $request['email'],
                'type' => 'conference_bulk_email',
                'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($request),
            ];

            $this->logEmail($email);

            // Send notification to admin
            $request['type'] = 'new_mewmber_listing';
            $email2 = [
                'subject' => $request['name'] . ' has just submitted details on GSF alumni page',
                'recipient_name' => $request['Admin'],
                'recipient' => $setting->official_email,
                'type' => 'conference_bulk_email',
                'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($request),
            ];

            $this->logEmail($email2);

        }else{
            $data = $request->all();
            $data['chapter'] = Chapter::where('id', $request->chapter)->first();

            try {
                Excel::import(new GeneralUsersImport($data), request()->file('file'));
            } catch (\Illuminate\Database\QueryException $ex) {
                $error = $ex->getMessage();
                return back()->with('error', $error);
            }
            // send email to uploader
            $request['type'] = 'alumni-upload';
            $request['chapter'] = Chapter::find($request->chapter)->name;

            $email = [
                'subject' => 'Thank you for submitting your details',
                'recipient_name' => $request['name'],
                'recipient' => $request['email'],
                'type' => 'conference_bulk_email',
                'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($request),
            ];

            $this->logEmail($email);
            // Send notification to admin
            $request['type'] = 'new_mewmber_listing_multiple';
            $email2 = [
                'subject' => $request['name'] . ' has just submitted details on GSF alumni page',
                'recipient_name' => $request['Admin'],
                'recipient' => $setting->official_email,
                'type' => 'conference_bulk_email',
                'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($request),
            ];

            $this->logEmail($email2);

        }

        return back()->with('message', 'Submission Added successfully, we have sent you an email with the next steps');

    }

    public function uploadMultiple(){
        $chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
        $portfolios = getCommunityPortfolios();
        $sessions = range(date('Y'), date('1982'));

        return view('frontend.' . frontendTemplate() . '.newmultiple', compact('chapters', 'portfolios', 'sessions'));
    }

    public function processUploadMultiple(Request $request)
    {
        dd($request->all());
        $chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
        $portfolios = getCommunityPortfolios();
        $sessions = range(date('Y'), date('1982'));

        return view('frontend.' . frontendTemplate() . '.newmultiple', compact('chapters', 'portfolios', 'sessions'));
    }

    public function getListingSample(Request $request)
    {
        $path = public_path() . '/frontend/exportsamples/generalimport.xlsx';

        return response()->download($path);

    }

}
