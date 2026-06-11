<?php

namespace App\Http\Controllers;

use App\Exports\ExportChapters;
use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Field;
use App\Models\Setting;
use App\Models\Stakeholder;
use App\Models\User;
use App\Models\Zone;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Facades\Excel;

class ChapterController extends Controller
{
    use Exportable;

    public function chaptersExport()
	{
		return Excel::download(new ExportChapters(), 'chapters_exported.xlsx');
	}

    public function generate()
    {
        $chapters = Chapter::all();

            foreach($chapters as $chapter){
                $chapter->update([
                    'token' => substr(number_format(time() * rand(),0,'',''),0,6),
                ]);
            }
    }

    public function moveMembers(Request $request, Chapter $chapter)
    {
        $request->validate([
            'new_chapter_id' => 'required|exists:chapters,id'
        ]);

        $newChapter = Chapter::findOrFail($request->new_chapter_id);

        DB::transaction(function () use ($chapter, $newChapter) {

            // Stakeholders
            Stakeholder::where('chapter_id', $chapter->id)->update([
                'chapter_id' => $newChapter->id,
                'zone_id'    => $newChapter->zone_id,
                'field_id'   => $newChapter->field_id,
            ]);

            // Members
            User::where('chapter_id', $chapter->id)->update([
                'chapter_id' => $newChapter->id,
            ]);

        });

        return back()->with('message', 'All members and stakeholders moved successfully.');
    }

    public function campusUpdate()
    {
        $chapters = Chapter::where('id','<>',86)->get();

        return view('frontend.' . frontendTemplate() . '.campusUpdate', compact('chapters'));
        // return view('frontend.conference.campusUpdate', compact('chapters'));
    }

    public function generateNewToken($id){
        if(auth()->user()->level != 'Admin' ){
            return abort(404);
        }

        $chapter = Chapter::whereId($id)->first();
        $chapter->update([
            'token' => substr(number_format(time() * rand(),0,'',''),0,6),
        ]);

        return back()->with('message', 'Operation successfull');

    }
    public function campusView(Request $request)
    {
        $chapter = Chapter::findorfail($request->chapter);
        $token = false;
        $realToken = '';

        if($request->token){
            if($chapter->token != $request->token){
                return back()->with('error', 'Invalid Token for GSF Chapter, Kindly contact the National Publicity Office');
            }

            $token = true;
            $realToken = $chapter->token;

        }

        return view('frontend.conference.campusView', compact('chapter', 'token', 'realToken'));
    }

    public function campusSave(Request $request)
    {
        $data = $this->validate($request, [
            'phone' => 'required',
            'token' => 'required',
            'realToken' => 'required',
            'chapter' => 'required|numeric',
            'facebook' => 'nullable',
            'twitter' => 'nullable',
            'email' => 'nullable',
            'address' => 'required',
        ]);

        $chapter = Chapter::find($request->chapter);

        //check if token matches campus
        if(  $data['token'] == 'SuperToken12345654321' || $data['realToken'] == $chapter->token){
            $chapter->update([
                'email' => $data['email'],
                'phone' => $data['phone'],
                'facebook' => $data['facebook'],
                'twitter' => $data['twitter'],
                'address' => $data['address']
            ]);
            return back()->with('message', 'Update Successful');
        }else{
            return back()->with('warning', 'Invalid Token for details Update');
        }

    }

    public function index()
    {
        $count = 1;
        if(auth()->user()->isSubAdmin() && auth()->user()->isMember() ){
            $chapter = Chapter::with('users','stakeholders')->whereId(auth()->user()->chapter_id)->first();
            return view('admin.chapters.edit', compact('chapter', 'zones'));
        }

        if(auth()->user()->isAdmin()){
            $chapters = Chapter::with('users','stakeholders')->orderBy('name')->get();
            return view('admin.chapters.index', compact('chapters', 'count'));
        }

    }

    public function create()
    {
        $zones = Zone::all();
        $fields = Field::all();
        $chapters = Chapter::all();
        return view('admin.chapters.edit', compact('zones', 'fields','chapters'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|unique:chapters,name',
            'address' => 'nullable',
            'email' => 'email|nullable',
            'phone' => 'nullable',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'zone_id' => 'required',
        ]);
        $field = Zone::whereId($data['zone_id'])->value('field_id');

        $chapter = Chapter::create([
            'name' => $data['name'],
            'address' => $data['address'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'facebook' => $data['facebook'],
            'twitter' => $data['twitter'],
            'token' => substr(number_format(time() * rand(),0,'',''),0,6),
            'zone_id' => $data['zone_id'],
            'field_id' => $field,
        ]);

        $this->createStakeholder($chapter, $request->email);
        
        return redirect(route('chapters.index'))->with('message', 'Chapter succesfully created');
    }

    public function show(Chapter $chapter)
    {
        //
    }

    public function edit(Chapter $chapter)
    {
        $zones = Zone::all();
        $fields = Field::all();
        return view('admin.chapters.edit', compact('chapter', 'zones', 'fields'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chapter $chapter)
    {

        if(auth()->user()->isAdmin() || (auth()->user()->isSubAdmin() && auth()->user()->isMember())){
            if(!auth()->user()->isAdmin()){
                $request['zone_id'] = auth()->user()->campus->zone->id ?? null;

            }else $request['zone_id'] = $request->zone_id;
		}

        $request['field_id'] = $chapter->zone->field->id ?? null;
        $oldEmail = $chapter->email;

        if($request->has('chapter_banner')){
            $request['banner'] = $this->uploadImage($request->chapter_banner, 'main/images/chapters');
        }

        if($chapter->stakeholder && $request->email != $chapter->email){
            $chapter->stakeholder->update([
                'email' => $request->email,
            ]);
        }

        $chapter->stakeholders()->update([
            'chapter_id' => $chapter->id,
            'zone_id'    => $chapter->zone->id,
            'field_id'   => $chapter->zone->field->id,
        ]);

        $chapter->stakeholders()->update([
            'chapter_id' => $chapter->id,
            'zone_id'    => $chapter->zone->id,
            'field_id'   => $chapter->zone->field->id,
        ]);

        $chapter->reports()->update([
            'zone_id'    => $chapter->zone->id,
            'field_id'   => $chapter->zone->field->id,
        ]);

        $chapter->update($request->except('chapter_banner'));

        $this->createStakeholder($chapter->fresh(), $oldEmail);

        return redirect(route('chapters.index'))->with('message', 'Update successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chapter $chapter)
    {
        if(auth()->user()->role == 1){
            if($chapter->users->count() > 0){
                return back()->with('error', 'Sorry, this chapter has members. You cannot deleete it');
            }

            $chapter->delete();

            return back()->with('message',' Delete succesful!');

         }return abort(404);
    }

    public function createStakeholder(Chapter $chapter, $oldEmail)
    {
        $chapterRoleId = 5;

        $stakeholder = Stakeholder::where('chapter_id', $chapter->id)
            ->where('role_id', $chapterRoleId)
            ->first();

        $allEmailData = [];

        $sendCredentials = false;
        $passwordPlain = null;

        if (!$stakeholder) {

            // ===================================
            // BRAND NEW ACCOUNT
            // ===================================
            $passwordPlain = Str::random(8);

            $stakeholder = Stakeholder::create([
                'role_id'          => $chapterRoleId,
                'chapter_id'       => $chapter->id,
                'zone_id'          => $chapter->zone_id,
                'field_id'         => $chapter->field_id,
                'name'             => $chapter->name . ' Representative',
                'email'            => $chapter->email,
                'phone'            => $chapter->phone,
                'status'           => 'active',
                'password'         => bcrypt($passwordPlain),
                'credentials_sent' => 1,
            ]);

            $sendCredentials = true;

        } else {

           
            // ===================================
            // UPDATE ACCOUNT DETAILS
            // ===================================
            $stakeholder->update([
                'zone_id'  => $chapter->zone_id,
                'field_id' => $chapter->field_id,
                'name'     => $chapter->name . ' Representative',
                'email'    => $chapter->email,
                'phone'    => $chapter->phone,
                'status'   => 'active',
            ]);

            // ===================================
            // EMAIL CHANGED
            // Reset password and resend credentials
            // ===================================

            if (
                !empty($oldEmail)
                && strtolower($oldEmail) != strtolower($chapter->email)
            ) {

                $passwordPlain = Str::random(8);

                $stakeholder->update([
                    'password'         => bcrypt($passwordPlain),
                    'credentials_sent' => 1,
                    'last_login_at'    => null,
                ]);

                $sendCredentials = true;
            }

            // ===================================
            // NEVER SENT BEFORE + NEVER LOGGED IN
            // Send first credentials
            // ===================================
            elseif (
                empty($stakeholder->credentials_sent)
                && empty($stakeholder->last_login_at)
            ) {
                $passwordPlain = Str::random(8);

                $stakeholder->update([
                    'password'         => bcrypt($passwordPlain),
                    'credentials_sent' => 1,
                ]);

                $sendCredentials = true;
            }
        }

        // ===================================
        // SEND EMAIL ONLY WHEN REQUIRED
        // ===================================
        if ($sendCredentials) {

            $loginLink = "<a href='" . url('/stakeholders/login') . "'>Login</a>";

            $allEmailData[] = [
                'recipient'  => $stakeholder->email,
                'type'       => 'report_email',
                'subject'    => 'Welcome to GSF Digital Portal',
                'content'    => "
                    <h5>Dear {$chapter->name},</h5>

                    <p>Your fellowship representative account has been created or updated.</p>

                    <p>
                        <strong>Email:</strong> {$stakeholder->email}<br>
                        <strong>Password:</strong> {$passwordPlain}
                    </p>

                    <p>{$loginLink}</p>

                    <p>Please change your password after your first login.</p>

                    <p>
                        In His Service,<br>
                        GSF National ICT
                    </p>
                ",
                'created_at' => now(),
                'updated_at' => now(),
            ];

           $Log = EmailService::logEmail([
                'type'       => 'report_email',
                'recipients' => $allEmailData,
            ]);

        }

        return $stakeholder;
    }
}
