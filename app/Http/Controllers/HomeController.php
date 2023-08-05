<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Models\Event;
use App\Models\Payout;
use App\Models\Chapter;
use App\Models\Setting;
use App\Models\TempUser;
use App\Models\Stakeholder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Mail\NotificationEmail;
use App\Models\ConferenceEdition;
use App\Models\NewListing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    private $conference;
    private $frontend;
    public function __construct()
    {
        $this->conference = ConferenceEdition::where('status','active')->where('close_registration','>', date('Y-m-d'))->first();
        $this->frontend = GeneralSetting::first()->frontend_template;
        
        if (!isset($this->conference) && empty($this->conference)) {
            return true;
        }else{
            return false;
        }
    }

    public function index() {
        $events = Event::where('date', '>=', date('Y-m-d'))->orderBy('date', 'ASC')->where('chapter_id', '<>', 0)->limit(6)->get();
        $national = Event::where('date', '>=', date('Y-m-d'))->orderBy('date', 'ASC')->where('chapter_id', 0)->limit(3)->get();
       
        if($this->conference){
            $chapters = Chapter::orderBy('name')->get();
            $setting = $this->conference;
            $conference_year = Carbon::parse($setting->start_date)->year;
            $alumnis_amount = [
                'alumni_registration_fee' => $setting->alumni_registration_fee,
                'new_alumni_registration_fee' => $setting->new_alumni_registration_fee
            ];
        
            return view('frontend.conference.template'. $this->conference->template_id.'.welcome')
                ->with('events',$events)
                ->with('national',$national)
                ->with('conference', $this->conference)
                ->with('conference_year', $conference_year)
                ->with('alumnis_amount', $alumnis_amount)
                ->with('chapters', $chapters);
        }else{
            $used = ['Zonal Pastor', 'Field Pastor', 'Portfolio'];
            $officials = Stakeholder::whereIn('role', $used)->get();

            foreach($officials as $user){
                if($user->role == 'Zonal Pastor' && !is_null($user->zone_id)){
                    $user->office = 'Zonal Pastor, ' .$user->zone->name ?? 'N/A' ;
                }

                if($user->role == 'Field Pastor' && !is_null($user->field_id)){
                    $user->office = "Field Pastor, ". $user->field->name ?? 'N/A';
                } 
               
                if($user->role == 'Portfolio'){
                    $user->office = "GSF National ".$user->portfolio;
                } 
            }

            return view('frontend.'. frontendTemplate().'.index', compact('events', 'national', 'officials'));
        }
    }

    public function regPage($type)
    {
        if (isset($type) and $this->conference) {
            $chapters = Chapter::orderBy('name')->get();
            $setting = $this->conferenceEdition();
            $conference_year = Carbon::parse($setting->start_date)->year;
            $alumnis_amount = [
                'alumni_registration_fee' => $setting->alumni_registration_fee,
                'new_alumni_registration_fee' => $setting->new_alumni_registration_fee
            ];
        
            return view('frontend.conference.template'. $this->conference->template_id.'.registration',compact('chapters','setting','conference_year','alumnis_amount','type'));
            // }
            // if ($type == 2) {
            //     return view('frontend.conference.template' . $this->conference->template_id . '.registration', compact('chapters', 'setting', 'conference_year', 'alumnis_amount'));
            // }
            // if ($type == 3) {
            //     return view('frontend.conference.template' . $this->conference->template_id . '.registration', compact('chapters', 'setting', 'conference_year', 'alumnis_amount'));
            // }
        } else {
            return abort(404);
        }
    }

    public function alumni() {
        $alumnis = User::wherehas('campus')->whereStatus(1)->where('role', '<>', 1)->paginate(12)->withQueryString();
        return view('frontend.' . frontendTemplate() . '.alumni', compact('alumnis'));
        // return view('frontend.main.alumni', compact('alumnis'));
    }

    public function students() {
        $alumnis = User::wherehas('campus')->whereStatus(0)->where('role', '<>', 1)->paginate(15)->withQueryString();
        return view('frontend.' . frontendTemplate() . '.student', compact('alumnis'));
    }

    public function programs() {
        $programs = Event::where('date', '>=', date('Y-m-d'))->orderBy('date', 'ASC')->get(); 
        return view('frontend.main.program', compact('programs'));
    }

    public function chapters() {
        $chapters = Chapter::withCount('users')->where('id','<>',86)->get();
       
        return view('frontend.' . frontendTemplate() . '.campuses', compact('chapters'));
        // return view('frontend.main.chapters', compact('chapters'));
    }

    private function getSidebar() {
        $chapters = Chapter::all();
        $portfolios = $this->getCommunityPortfolios();

        return ['chapters' => $chapters, 'portfolios' => $portfolios];
    }

    public function generalSearch(Request $request)
    {

        $this->validate($request, ['name' => 'required']);;

        $searchMember = User::with('campus')->select("users.*", "chapter_id")
        ->where("name", "LIKE", "%{$request->name}%")
        ->where("role", "<>", 1);

        $count = $searchMember->count();
        $searchMember = $searchMember->paginate(15)->withQueryString();

        return view('frontend.' . frontendTemplate() . '.general_search_results', compact('searchMember', 'count'));
    }

    public function alumniSearch(Request $request)
    {
        $this->validate($request, ['name' => 'required|min:4']);

        $results = User::wherehas('campus')->where("name", "LIKE", "%{$request->name}%")->where('status', 1)->where('role', '<>', 1)->orderBy('users.created_at', 'desc')->get();
       
        if ($request->school) {
            $searchFromSchool = Chapter::select("id", "name")
            ->where("chapters.name", "LIKE", "%{$request->school}%")
            ->leftJoin('users', 'chapters.id', '=', 'users.chapter_id')
            // ->where('users.status', 1)
            ->where('users.role', '<>', 1)
            ->select('users.*','chapters.*')
            ->orderBy('users.created_at', 'desc')
            ->get();
        }else{
            $searchFromSchool = collect([]);
        }

        $searchAlumni = collect([]);
        $searchMember = $searchFromSchool->merge($results);
        $count = $searchMember->count();
        
        return view('frontend.' . frontendTemplate() . '.general_search_results', compact('count','searchMember'));
    }

    public function memberSearch(Request $request)
    {
        $this->validate($request, ['name' => 'required|min:4']);
        
        $results = User::with(['campus' => function($query){
            return $query->where('id', '<>', 86);
        }])->where("name", "LIKE", "%{$request->name}%")->where('status', 0)->where('role', '<>', 0)->orderBy('users.created_at', 'desc')->get();

        if ($request->school) {
            $searchFromSchool = Chapter::select("id", "name")
                ->where("chapters.name", "LIKE", "%{$request->school}%")
                ->leftJoin('users', 'chapters.id', '=', 'users.chapter_id')
                // ->where('users.status', 1)
                // ->where('users.role', '<>', 0)
                ->select('users.*', 'chapters.*', 'users.name AS name', 'users.id AS id', 'chapters.name AS c_name')
                ->orderBy('users.created_at', 'desc')
                ->get();
        }else{
            $searchFromSchool = collect([]);
        }
       
        $searchMember = $searchFromSchool->merge($results);
        $count = $searchMember->count();

        // run this through a service
        
        return view('frontend.' . frontendTemplate() . '.general_search_results', compact('count','searchMember', ));
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


    public function singleCampus(Chapter $chapter){
        
        $nationalevents = Event::where('chapter_id', 0)->get();
        $related = Chapter::where('zone_id', $chapter->zone_id)->orWhere('field_id', $chapter->field->id)->get();
      
        return view('frontend.' . frontendTemplate() . '.single_chapter', compact('nationalevents','chapter','related'));

        // return view('frontend.main.single-chapter', compact('chapter', 'nationalevents'));
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
        $alumni = User::whereSlug($slug)->first();
        $related = User::where('chapter_id', $alumni->chapter_id)->where('role', '<>', 1)->get();
        
        // Check if this is an alumni
        return view('frontend.' . frontendTemplate() . '.single-alumni', compact('alumni', 'related'));
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
		$portfolios = $this->getCommunityPortfolios();
		$sessions = range(date('Y'), date('1982'));

        return view('frontend.' . frontendTemplate() . '.newalumni', compact('chapters', 'portfolios', 'sessions'));
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
            'type' => $data['type'],
            'recipient' => $setting->official_email,
            'content' => app('App\Http\Controllers\CriticalEmailController')->getContent($data),
        ];
        
        $this->logEmail($email);

        return back()->with('message', 'Submission Added successfully');
        
	}
}
