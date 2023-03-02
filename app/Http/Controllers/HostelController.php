<?php

namespace App\Http\Controllers;

use App\Hostel;
use App\ConferenceEdition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HostelParticipantExport;

class HostelController extends Controller
{

    public function index(Request $request)
    {
        $edition = ConferenceEdition::find($request->edition);
        $count = 1;
        
        if(auth()->user()->role == 1){
            $hostels = Hostel::where('conference_edition_id', $edition->id)->orderBy('created_at', 'desc')->get();
            return view('conference_management.admin.hostel.index', compact('hostels', 'count','edition'));
        }return abort(404);
    }

    public function create(Request $request)
    {
        $edition = ConferenceEdition::find($request->edition);
        return view('conference_management.admin.hostel.create',compact('edition'));
    }

    public function participantExport(Request $request, $id){
        $hostel = Hostel::find($id);
        $count = 1;
        
        if (auth()->user()->role != 1) {
            return abort(404);
        }

        $data = [
            'hostel_id' => $hostel->id,
        ];
        
        return Excel::download(new HostelParticipantExport($data), $hostel->name."'s participants.xlsx");
    
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|min:2',
            'type' => 'required',
            'level' => 'required',
            'capacity' => 'required',
            'edition' => 'required',
        ]);

        Hostel::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'level' => $data['level'],
            'capacity' => $data['capacity'],
            'conference_edition_id' => $data['edition'],
        ]);

        return redirect(route('hostels.index',['edition'=>$request->edition]))->with('message', 'Hostel succesfully created');
    }

    public function show(Hostel $hostel)
    {
        //
    }


    public function edit(Hostel $hostel, Request $request)
    {
        $edition = ConferenceEdition::find($request->edition);
        return view('conference_management.admin.hostel.edit', compact('hostel','edition'));
    }

    public function update(Request $request, Hostel $hostel)
    {
        $hostel->update($request->except('edition_id'));
        return back()->with('message', 'Update successful!');
    }

    public function destroy($id, Request $request)
    {
        $edition = ConferenceEdition::find($request->edition);
        $hostel = Hostel::findOrFail($id);

        if(auth()->user()->role == 1){
           $hostel->delete();          
            
            return back()->with('message',' Delete succesful!');

        }return abort(404);
    }
}
