<?php

namespace App\Http\Controllers;

use App\Chapter;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\ExportChapters;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ChapterController extends Controller
{
    
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


    public function campusUpdate()
    {
        $chapters = Chapter::all();
        return view('campusUpdate', compact('chapters'));
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

        return view('campusView', compact('chapter'));
    }
    
    public function campusSave(Request $request)
    {
      
        $data = $this->validate($request, [
            'phone' => 'required',
            'token' => 'required',
            'chapter' => 'required|numeric',
            'facebook' => 'nullable',
            'twitter' => 'nullable',
            'email' => 'nullable',
            'address' => 'required',
        ]);
        
        $chapter = Chapter::find($request->chapter);

        //check if token matches campus
        if(  $data['token'] == 'SuperToken12345654321' || $data['token'] == $chapter->token){
            $chapter->update([
                'email' => $data['email'],
                'phone' => $data['phone'],
                'facebook' => $data['facebook'],
                'twitter' => $data['twitter'],
                'address' => $data['address']
            ]);
            return(redirect(route('campus.update')))->with('message', 'Update Successful');
            
        }else{
            return(redirect(route('campus.update')))->with('warning', 'Invalid Token for details Update');
        }
      
    }


    public function index()
    {
        $count = 1;
        $chapters = Chapter::withCount(['users' => function($query){
            $query->where('level', 'Participant')->orwhere('level', 'Moderator');
        }])->get();


        return view('admin.chapters.index', compact('chapters', 'count'));
    }

    public function create()
    {
        return view('admin.chapters.create');
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

        ]);

        $chapter = Chapter::create([
            'name' => $data['name'],
            'address' => $data['address'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'facebook' => $data['facebook'],
            'twitter' => $data['twitter'],
            'token' => substr(number_format(time() * rand(),0,'',''),0,6),
        ]);

        return redirect(route('chapters.index'))->with('message', 'Chapter succesfully created');
    }

    public function show(Chapter $chapter)
    {
        //
    }

    public function edit(Chapter $chapter)
    {
        return view('admin.chapters.edit', compact('chapter'));
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
        $chapter->update($request->all());
       
        return redirect()->back()->with('message', 'Update successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Chapter  $chapter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chapter $chapter)
    {
        
        if(auth()->user()->level == 'Admin'){
            if($chapter->users->count() > 0){
                return back()->with('error', 'Sorry, this chapter has participants. You cannot deleete it');
            }

            $chapter->delete();          
             
            return back()->with('message',' Delete succesful!');
 
         }return abort(404);
    }
}
