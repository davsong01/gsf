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
use App\Models\EmailContact;
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
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
                ->with(['categories', 'author'])
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(3)
                ->get()
            : collect();

        $leadershipRoleIds = array_values(array_unique(array_merge(
            zoneStakeholders(),
            fieldStakeholders(),
            portfolioStakeholders()
        )));

        $excludeAssemblyRepresentatives = static function ($query): void {
            $query->whereDoesntHave('role', function ($roleQuery) {
                $roleQuery->where(function ($criteria) {
                    $criteria->whereIn('slug', ['chapter-representative', 'assembly-representative'])
                        ->orWhere('name', 'like', '%assembly%rep%');
                });
            })->whereDoesntHave('designation', function ($designationQuery) {
                $designationQuery->where('name', 'like', '%assembly%rep%');
            });
        };

        $leaderCardsQuery = Stakeholder::query();
        $excludeAssemblyRepresentatives($leaderCardsQuery);
        $leaders = $leaderCardsQuery->with(['designation', 'role'])
            ->whereIn('role_id', $leadershipRoleIds)
            ->where('status', 'active')
            ->orderByRaw('designation_id IS NULL, designation_id ASC')
            ->limit(4)
            ->get();

        $leaderCountQuery = Stakeholder::query()->where('status', 'active');
        $excludeAssemblyRepresentatives($leaderCountQuery);

        $impact = [
            'campuses' => Chapter::actualCampus()->count(),
            'fields' => Field::count(),
            'leaders' => $leaderCountQuery->count(),
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
            'campuses' => Chapter::actualCampus()->count(),
            'members' => User::whereHas('campus', fn ($query) => $query->actualCampus())->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0)->count(),
            'alumni' => User::whereHas('campus', fn ($query) => $query->actualCampus())->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 1)->count(),
        ];

        $featuredCampuses = Chapter::actualCampus()->with(['field', 'zone'])
            ->withCount(['users as members_count' => function ($query) {
                $query->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0);
            }])
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

    public function contact()
    {
        $siteSettings = GeneralSetting::first();

        return view('frontend.site.about.contact', compact('siteSettings'));
    }

    public function submitContact(Request $request)
    {
        // Ignore likely spam submissions without attaching a validation error to
        // a honeypot field that real visitors cannot see or correct.
        if ($request->filled('website') || $request->filled('company_website')) {
            return back()
                ->withInput($request->except(['website', 'company_website']))
                ->with('contact_error', 'We could not process this message. Please try again.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $siteSettings = GeneralSetting::first();
        $recipient = $siteSettings?->site_contact_email ?: $siteSettings?->official_email;

        EmailContact::create([
            'user_id' => null,
            'type' => 'website',
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => null,
            'message' => 'Subject: '.$data['subject']."\n\n".$data['message'],
        ]);

        if (blank($recipient)) {
            return back()->withInput()->with('contact_error', 'Your enquiry was saved, but the contact address is unavailable right now.');
        }

        $emailResult = EmailService::logEmail([
            'type' => 'generic',
            'priority' => 1,
            'recipient' => $recipient,
            'reply_to' => [$data['email'], $data['name']],
            'subject' => 'GSF website: '.$data['subject'],
            'content' => '<p><strong>Name:</strong> '.e($data['name']).'<br>'
                .'<strong>Email:</strong> '.e($data['email']).'<br>'
                .'<strong>Subject:</strong> '.e($data['subject']).'</p>'
                .'<p>'.nl2br(e($data['message'])).'</p>',
        ]);

        if (!($emailResult['status'] ?? false)) {
            return back()->withInput()->with('contact_error', 'Your message could not be sent. Please try the email address shown on this page.');
        }

        return back()->with('contact_success', 'Your message has been sent. Thank you for reaching out.');
    }

    public function privacy()
    {
        $siteSettings = GeneralSetting::first();

        return view('frontend.site.legal.privacy', compact('siteSettings'));
    }

    public function terms()
    {
        $siteSettings = GeneralSetting::first();

        return view('frontend.site.legal.terms', compact('siteSettings'));
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
            ->whereHas('campus', fn ($query) => $query->actualCampus())
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
            ->whereDoesntHave('role', function ($query) {
                $query->where('slug', 'chapter-representative');
            })
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
            ->whereHas('campus', fn ($query) => $query->actualCampus())
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
        $hasStateColumn = Schema::hasColumn('chapters', 'state');
        $chapters = Chapter::actualCampus()->with(['field', 'zone'])
            ->withCount(['users as members_count' => function ($query) {
                $query->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0);
            }])
            ->when($hasStateColumn && $request->filled('state'), fn ($query) => $query->where('state', $request->input('state')))
            ->when($request->filled('q'), function ($query) use ($request, $hasStateColumn) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($search) use ($term, $hasStateColumn) {
                    $search->where('name', 'LIKE', $term)
                        ->orWhere('address', 'LIKE', $term)
                        ->when($hasStateColumn, fn ($query) => $query->orWhere('state', 'LIKE', $term))
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
            'selectedState' => $hasStateColumn ? $request->input('state', '') : '',
        ]);
    }

    public function studentsByChapter(Request $request, $slug){
        return $this->campusPeopleListing($request, $slug, 'members');
    }

    public function alumniByChapter(Request $request, $slug)
    {
        return $this->campusPeopleListing($request, $slug, 'alumni');
    }

    private function campusPeopleListing(Request $request, string $slug, string $directoryType)
    {
        if (is_numeric($slug)) {
            $campus = Chapter::actualCampus()->findOrFail($slug);
            $routeName = $directoryType === 'alumni' ? 'alumni.campus' : 'members.campus';

            return redirect()->route($routeName, array_merge([$campus->slug], $request->query()), 301);
        }

        $campus = Chapter::actualCampus()->where('slug', $slug)->firstOrFail();
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
        $chapters = Chapter::actualCampus()->get();
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
            $data = Chapter::actualCampus()->select("name", "id")
                ->where("name","LIKE","%{$data}%")
                ->take(3)->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative;width: 100%;">';
            foreach ($data as $row) {
                $output .= "
                <a href='singlecampus/" . $row->slug. "' class='ml-2'  style='color:black;font-weight: bold;display: block;padding: 0 10px 0 10px;'><li>" . $row->name . "</li></a>";
            }
            $output .= '</ul>';
            echo $output;
        }

    }

    public function autoSearch(Request $request)
    {
        if ($request->get('data')) {
            $data = $request->get('data');
            $data = Chapter::actualCampus()->select("name", "id")
                ->where("name","LIKE","%{$data}%")
                ->take(10)->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
            foreach ($data as $row) {
                $output .= "
            <li><a href='singlecampus/" . $row->slug. "' class='ml-2'  style='color:black;font-weight: bold;'>" . $row->name . "</a></li>";
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
            <li><a href='singlecampus/" . $row->slug . "' class='ml-2'  style='color:black;font-weight: bold;'>" . $row->name . "</a></li>";
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
            $data = Chapter::actualCampus()->select("name", "id")
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
        $identifier = $chapterId ?? request()->chapter ?? request()->chapter_id;
        $chapterQuery = Chapter::with(['field.fieldCord', 'zone.zonalCord', 'zone.field.fieldCord', 'meetings', 'galleryPhotos', 'needs' => fn ($query) => $query->where('is_active', true)])
            ->withCount([
                'users as members_count' => function ($query) {
                    $query->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0);
                },
                'users as alumni_count' => function ($query) {
                    $query->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 1);
                },
            ]);

        if (is_numeric($identifier)) {
            $chapter = $chapterQuery->findOrFail($identifier);
            if ($chapterId !== null && request()->isMethod('get') && !request()->has('token')) {
                return redirect()->route('campus.single', $chapter->slug, 301);
            }
        } else {
            $chapter = $chapterQuery->where('slug', $identifier)->firstOrFail();
        }

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

        abort_if(strtolower(trim((string) $chapter->name)) === 'other', 404);

        $campusEvents = BlogPost::published()
            ->where('type', 'event')
            ->whereNotNull('event_starts_at')
            ->where(function ($query) use ($chapter) {
                $query->where(fn ($scope) => $scope->where('scope_type', 'chapter')->where('scope_id', $chapter->id))
                    ->orWhere(fn ($author) => $author->whereHasMorph('author', [Stakeholder::class], fn ($stakeholder) => $stakeholder->where('chapter_id', $chapter->id)));
            });

        $events = (clone $campusEvents)->where('event_starts_at', '>=', now())
            ->orderByDesc('is_featured')->orderBy('event_starts_at')->limit(5)->get();
        if ($events->count() < 5) {
            $events = $events->concat(
                (clone $campusEvents)->where('event_starts_at', '<', now())
                    ->orderByDesc('is_featured')->orderByDesc('event_starts_at')->limit(5 - $events->count())->get()
            );
        }

        $members = $chapter->users()
            ->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 0)
            ->latest()->limit(6)->get();

        $alumni = $chapter->users()
            ->where('status', 'active')->where('role', '<>', 1)->where('is_graduated', 1)
            ->latest()->limit(6)->get();

        $campusPosts = BlogPost::published()
            ->where('type', '!=', 'event')
            ->where(function ($query) use ($chapter) {
                $query->where(fn ($scope) => $scope->where('scope_type', 'chapter')->where('scope_id', $chapter->id))
                    ->orWhere(fn ($author) => $author->whereHasMorph('author', [Stakeholder::class], fn ($stakeholder) => $stakeholder->where('chapter_id', $chapter->id)));
            })
            ->latest('published_at')
            ->latest('id')
            ->limit(4)
            ->get();

        $related = collect();
        if ($chapter->zone_id || $chapter->field_id) {
            $related = Chapter::actualCampus()->with(['field', 'zone'])
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

        return view('frontend.site.directory.campus', compact('chapter', 'events', 'members', 'alumni', 'campusPosts', 'related'));
    }


    public function contactCampus(Request $request)
    {
        $data = $request->validate([
            'chapter_id' => ['required', 'integer', 'exists:chapters,id'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $chapter = Chapter::actualCampus()->findOrFail($data['chapter_id']);

        EmailContact::create([
            'user_id' => null,
            'chapter_id' => $chapter->id,
            'type' => 'campus',
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'message' => $data['message'],
        ]);

        if (filled($chapter->email)) {
            $subject = 'New enquiry for '.$chapter->name;
            $content = "A visitor submitted the campus contact form.<br><br>"
                .'<strong>Name:</strong> '.e($data['name']).'<br>'
                .'<strong>Email:</strong> '.e($data['email']).'<br>'
                .'<strong>Phone:</strong> '.e($data['phone']).'<br>'
                .'<strong>Message:</strong><br>'.nl2br(e($data['message']));

            $this->logEmail([
                'type' => 'campus_contact_enquiry',
                'recipient' => $chapter->email,
                'subject' => $subject,
                'content' => $content,
                'priority' => 1,
            ]);
        }

        return back()->with('message', filled($chapter->email)
            ? 'Your message has been sent to the campus fellowship.'
            : 'Your message has been received by the campus fellowship.');
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
        $chapters = Chapter::actualCampus()->orderBy('name')->get(); //sort in alphabetical order
		$portfolios = StakeholderDesignation::where('type', 'chapter_executive')
			->orderBy('order')
			->orderBy('name')
			->get();
		$sessions = range(date('Y'), date('1982'));

		return view('frontend.site.newalumni', compact('chapters', 'portfolios', 'sessions'));
	}

    public function newDonation()
    {
        $chapters = Chapter::actualCampus()->orderBy('name')->get(); //sort in alphabetical order
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
        $chapters = Chapter::actualCampus()->orderBy('name')->get(); //sort in alphabetical order
        $portfolios = getCommunityPortfolios();
        $sessions = range(date('Y'), date('1982'));

        return view('frontend.' . frontendTemplate() . '.newmultiple', compact('chapters', 'portfolios', 'sessions'));
    }

    public function processUploadMultiple(Request $request)
    {
        dd($request->all());
        $chapters = Chapter::actualCampus()->orderBy('name')->get(); //sort in alphabetical order
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
