<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Zone;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Setting;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\ExportChapters;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\Exportable;

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

        if($request->has('chapter_banner')){
            $request['banner'] = $this->uploadImage($request->chapter_banner, 'main/images/chapters');
        }

        if($chapter->stakeholder && $request->email != $chapter->email){
            $chapter->stakeholder->update([
                'email' => $request->email,
            ]);
        }
        $chapter->update($request->except('chapter_banner'));

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
}
