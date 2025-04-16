<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Hostel;
use App\Models\Chapter;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\ConferenceEdition;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HostelParticipantExport;
use App\Services\HostelAllocationService;

class HostelController extends Controller
{

    public function index(Request $request)
    {
        $edition = ConferenceEdition::find($request->edition);
        $count = 1;
        
        if(auth()->user()->role == 1){
            $hostels = Hostel::where('conference_edition_id', $edition->id)->orderBy('created_at', 'desc')->get();
            $hostelsToMerge = Hostel::where('conference_edition_id', $edition->id)->where('allocation', '>',0)->orderBy('created_at', 'desc')->get();
            
            $hostels->each(function ($hostel) {
                $hostel->fields = Field::whereIn('id', $hostel->field_ids)->get();
                $hostel->chapters = Chapter::whereIn('id', $hostel->chapter_ids)->get();
            });
            return view('conference_management.admin.hostel.index', compact('hostels', 'count','edition','hostelsToMerge'));
        }return abort(404);
    }

    public function create(Request $request)
    {
        $fields = Field::all();
        $chapters = Chapter::all();
        $edition = ConferenceEdition::find($request->edition);
        return view('conference_management.admin.hostel.create',compact('edition','fields','chapters'));
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
            'field_ids' => 'nullable',
            'chapter_ids' => 'nullable'
        ]);

        Hostel::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'level' => $data['level'],
            'capacity' => $data['capacity'],
            'conference_edition_id' => $data['edition'],
            'field_ids' => $data['field_ids'] ?? NULL,
            'chapter_ids' => $data['chapter_ids'] ?? NULL,
        ]);

        return redirect(route('hostels.index',['edition'=>$request->edition]))->with('message', 'Hostel succesfully created');
    }

    public function show(Hostel $hostel)
    {
        //
    }


    public function edit(Hostel $hostel, Request $request)
    {
        $fields = Field::all();
        $chapters = Chapter::all();
        $edition = ConferenceEdition::find($request->edition);
        return view('conference_management.admin.hostel.edit', compact('hostel','edition','chapters','fields'));
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

        // check if hostel has any participant
        $hasPayment = Payment::where('hostel_id', $id)->where('conference_edition_id', $request->edition)->count();
        
        if($hasPayment > 0){
            return back()->with('error','Hostel has participants, cannot delete!');
        }
        if(auth()->user()->role == 1){
            $hostel->delete();          
            
            return back()->with('message',' Delete succesful!');
            
        }return abort(404);
    }

    public function repairHostelAllocation(Request $request){
        HostelAllocationService::repairHostelAllocation($request->edition);
        return back()->with('message', ' Repair succesful!');
    }

    public function autoAllocateHostel($edition){
        $res = HostelAllocationService::autoAllocateHostel($edition);

        if(isset($res['count'])){
            return back()->with('message', $res['count'] . ' Participants allocated successfully!');
        }else{
            return back()->with('error', 'We couldn\'t assign any hostel!');
        }
    }

    public function getAvailableHostels(Request $request){
        $toDeallocate = Hostel::where('conference_edition_id', $request->edition_id)->where('id', $request->deallocated_hostel_id)->first();
        
        $hostels = Hostel::withCount('payments')->where('conference_edition_id', $toDeallocate->conference_edition_id)->where('type', $toDeallocate->type)->where('level', $toDeallocate->level)->where('id', '!=', $toDeallocate->id)->where('allocation', '>', 0)->where('capacity', '>', 0)
        // ->where('field_ids', $toDeallocate->field_ids)->where('chapter_ids', $toDeallocate->chapter_ids)
        ->get(['id','name','allocation']);

        return response()->json([
            'status' => true,
            'hostels' => $hostels
        ]);
    }

    public function hostelMerger(Request $request) {
        HostelAllocationService::hostelMerger($request);

        return back()->with('message', 'Merger Succesful!');
    }
}
