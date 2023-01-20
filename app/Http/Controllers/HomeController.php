<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Event;
use App\Payout;
use App\Chapter;
use App\Setting;
use App\TempUser;
use Carbon\Carbon;
use App\Stakeholder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function index() {
        
        $events = Event::where('date', '>=', date('Y-m-d'))->orderBy('date', 'ASC')->where('chapter_id', '<>', 0)->limit(6)->get();
        $national = Event::where('date', '>=', date('Y-m-d'))->orderBy('date', 'ASC')->where('chapter_id', 0)->limit(3)->get();

        return view('frontend.main.index', compact('events', 'national'));
    }
    
    public function alumni() {
        $alumnis = User::wherehas('campus')->whereStatus(1)->where('role', '<>', 1)->paginate(45);        
        return view('frontend.main.alumni', compact('alumnis'));
    }

    public function students() {
        $students = User::wherehas('campus')->whereStatus(0)->where('role', '<>', 1)->paginate(45);        
        return view('frontend.main.student', compact('students'));
    }

    public function programs() {
        $programs = Event::where('date', '>=', date('Y-m-d'))->orderBy('date', 'ASC')->get(); 
        return view('frontend.main.program', compact('programs'));
    }

    public function chapters() {
        $chapters = Chapter::all();  
        return view('frontend.main.chapters', compact('chapters'));
    }
    private function getSidebar() {
        $chapters = Chapter::all();
        $portfolios = $this->getCommunityPortfolios();

        return ['chapters' => $chapters, 'portfolios' => $portfolios];
    }

    public function generalSearch(Request $request) {

        $this->validate($request, ['name' => 'required']);;

        $searchMember = User::with('campus')->select("name","chapter_id", "status","slug","role")
        ->where("name","LIKE","%{$request->name}%")
        ->where("role", "<>", 1)
        ->where("status", 0)
        ->get();

        $searchAlumni = User::with('campus')->select("name","chapter_id", "status","slug","role")
        ->where("name","LIKE","%{$request->name}%")
        ->where("role", "<>", 1)
        ->where("status", 1)
        ->get();

        return view('frontend.main.general_search_results', compact('searchMember', 'searchAlumni'));
    }

    public function autocomplete(Request $request)
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

    public function campusAutocomplete(Request $request){
        
        if ($request->get('data')) {
            $data = $request->get('data');
            $data = Chapter::select("name", "id")
                ->where("name","LIKE","%{$data}%")
                ->take(10)->get();
          
            $output = '<ul class="dropdown-menu form-control side" style="display:block; position:relative">';
            
            foreach ($data as $row) {
                $output .= "<option class='ml-2' name='chapter_id' value='" . $row->id . "' style='color:black;font-weight: bold;'>" . $row->name . "</option>";
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function singleCampus(Chapter $chapter){
        
        $nationalevents = Event::where('chapter_id', 0)->take(6)->get();

        return view('frontend.main.single-chapter', compact('chapter', 'nationalevents'));
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

            $mailresponse = $this->sendEmail($chapter->email, $type, $subject, $name, $content, 1);
           
            if(!is_null($mailresponse)) {
                return back()->with('error', 'Message not sent, please try again!');
            }
            //Send toastr
            return back()->with('message', 'Message sent!');

            }else {
                return back()->with('message', 'Something went wrong, try again later');
            }           
    }

    public function alumniSearch(Request $request) {
        $this->validate($request, ['name' => 'required|min:4']);
      
        $results =  User::wherehas('campus')->where("name","LIKE","%{$request->name}%")->where('status', 1)->where('role', '<>', 1)->get();

        if(!is_null($request->chapter) && !is_null($request->chapter_id)){
            $results = $results->where('chapter_id', $request->chapter_id);
        }

        return view('frontend.main.alumni-search', compact('results'));
        
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
        return view('frontend.main.single-alumni', compact('alumni'));
    }

    public function singleStudent($slug){
        $alumni = User::whereSlug($slug)->first();  
        return view('frontend.main.single-alumni', compact('alumni'));
        
    }

    public function alumniContact(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'message' => 'required',
        ]);

        $alumni = User::findorfail($request->alumni_id);
        if(!is_null($alumni->email)){
            $type = 'contactCampus';
            $name = $alumni->name;
            $subject = 'Someone Contacted you on GSF community';
            $content = "Someone has filled the contact form on your profile on GSF webpage, view details below '<br>'
            <p>
            <strong>Name: </strong>" . $request->name . "<br>
            <strong>Email: </strong>" . $request->email . "<br>
            <strong>Phone: </strong>" . $request->phone . "<br>
            <strong>Message: </strong>" . $request->message . "<br>
            </p>";

            $this->saveContactForm($alumni->id, $type, $request->name, $request->email, $request->phone, $request->message );
            $mailresponse = $this->sendEmail($alumni->email, $type, $subject, $name, $content, 1);
           
            if(!is_null($mailresponse)) {
                return back()->with('danger', 'Message not sent, please try again!');
            }
            //Send toastr
            return back()->with('message', 'Message sent!');

        }else {
            return back()->with('error', 'Email not found, please try again later');
        }           
    }

    public function newAlumni(){
        $chapters = Chapter::orderBy('name')->get(); //sort in alphabetical order
		$portfolios = $this->getCommunityPortfolios();
		$sessions = range(date('1982'), date('Y'));

		return view('frontend.main.newalumni', compact('chapters', 'portfolios', 'sessions'));
	}

	public function saveNewAlumni(){

	}
}
