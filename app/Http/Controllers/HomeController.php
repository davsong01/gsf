<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nec;
use App\Models\User;
use App\Models\Event;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\NewListing;
use App\Models\TempMember;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Imports\GeneralUsersImport;
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
                ->with('setting',$setting)
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
        if (isset($type) && $this->conference) {
            $chapters = Chapter::orderBy('name')->get();
            $fields = Field::orderBy('name')->get();
            $setting = $this->conference;
            $conference_year = Carbon::parse($setting->start_date)->year;
            $alumnis_amount = [
                'alumni_registration_fee' => $setting->alumni_registration_fee,
                'new_alumni_registration_fee' => $setting->new_alumni_registration_fee
            ];
            
            $title = '';

            if($type == 1){
                $title = 'Single Registration';
            }

            if ($type == 2) {
                $title = 'Mass Registration';
            }

            if ($type == 3) {
                $title = 'Alumni Registration';
            }
            
            return view('frontend.conference.template'. $this->conference->template_id.'.registration',compact('title','chapters','setting','conference_year','alumnis_amount','type', 'fields'));
        } else {
            return abort(404);
        }
    }

    public function alumni() {
        $alumnis = User::wherehas('campus')->whereStatus(1)->where('role', '<>', 1)->paginate(12)->withQueryString();
        return view('frontend.' . frontendTemplate() . '.alumni', compact('alumnis'));
    }

    public function nec()
    {
        $nec = Nec::orderBy('order', 'ASC')->get();
        return view('frontend.' . frontendTemplate() . '.nec', compact('nec'));
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

    public function studentsByChapter($id){
        $alumnis = User::where('chapter_id', $id)->whereStatus(0)->where('role', '<>', 1)->paginate(15)->withQueryString();
        return view('frontend.' . frontendTemplate() . '.student', compact('alumnis'));
    }

    public function alumniByChapter($id)
    {
        $alumnis = User::where('chapter_id', $id)->whereStatus(1)->where('role', '<>', 1)->paginate(15)->withQueryString();
        return view('frontend.' . frontendTemplate() . '.student', compact('alumnis'));
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
        $alumni = $results = User::with('campus')
        ->where('status', 1)
        ->where('role', '<>', 1)->orderBy('created_at','desc');
        
        if(!empty($request->name)){
            $alumni = $alumni->where("name", "LIKE", "%{$request->name}%");
        }
        
        if ($request->school) {
            $campus = Chapter::where("name", "LIKE", "%{$request->school}%")->pluck('id');
            $alumni = $alumni->whereIn('users.chapter_id', $campus);            
        }

        $searchMember = $alumni->get();
        $count = $searchMember->count();
        return view('frontend.' . frontendTemplate() . '.general_search_results', compact('count','searchMember'));
    }

    public function memberSearch(Request $request)
    {
        $results = User::with('campus')
        ->where('status', 0)
        ->where('role', '<>', 1)->orderBy('created_at', 'desc');
        
        if (!empty($request->name)) {
            $results = $results->where("name", "LIKE", "%{$request->name}%");
        }

        if ($request->school) {
            $campus = Chapter::where("name", "LIKE", "%{$request->school}%")->pluck('id');
            $results = $results->whereIn('users.chapter_id', $campus);
        }

        $searchMember = $results->get();
        $count = $searchMember->count();
        
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


    public function singleCampus(){
        $chapter = Chapter::where('id', request()->chapter)->first();
        
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

    public function newDonation()
    {
        $chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
        $portfolios = $this->getCommunityPortfolios();
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
        $portfolios = $this->getCommunityPortfolios();
        $sessions = range(date('Y'), date('1982'));

        return view('frontend.' . frontendTemplate() . '.newmultiple', compact('chapters', 'portfolios', 'sessions'));
    }

    public function processUploadMultiple(Request $request)
    {
        dd($request->all());
        $chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
        $portfolios = $this->getCommunityPortfolios();
        $sessions = range(date('Y'), date('1982'));

        return view('frontend.' . frontendTemplate() . '.newmultiple', compact('chapters', 'portfolios', 'sessions'));
    }

    public function getListingSample(Request $request)
    {
        $path = public_path() . '/frontend/exportsamples/generalimport.xlsx';
       
        return response()->download($path);

    }

}
