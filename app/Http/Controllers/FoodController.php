<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Field;
use App\Models\Chapter;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ConferenceEdition;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FoodParticipantExport;
use App\Services\HostelAllocationService;
use App\Services\ServicePointAllocationService;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $count = 1;
        $edition = ConferenceEdition::find($request->edition);
        if(auth()->user()->role == 1){
            $foods = Food::where('conference_edition_id', $edition->id)->orderBy('created_at', 'desc')->get();

            $servicePointsToMerge = Food::where('conference_edition_id', $edition->id)->where('allocation', '>', 0)->orderBy('created_at', 'desc')->get();
            
            $foods->each(function ($food) {
                $food->fields = Field::whereIn('id', $food->field_ids)->get();
                $food->chapters = Chapter::whereIn('id', $food->chapter_ids)->get();
            });
            
            $unallocatedSp = Transaction::whereNull('food_id')->where('conference_edition_id', $edition->id)->count();

            return view('conference_management.admin.food.index', compact('foods', 'count','edition', 'servicePointsToMerge', 'unallocatedSp'));
        }return abort(404);
    }

    public function create(Request $request)
    {
        $fields = Field::all();
        $chapters = Chapter::all();
        $edition = ConferenceEdition::find($request->edition);
        return view('conference_management.admin.food.create',compact('edition', 'fields','chapters'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|min:5',
            'level' => 'required',
            'capacity' => 'required',
            'edition' => 'required',
            'field_ids' => 'nullable',
            'chapter_ids' => 'nullable'
        ]);

        Food::create([
            'name' => $data['name'],
            'level' => $data['level'],
            'capacity' => $data['capacity'],
            'conference_edition_id' => $data['edition'],
            'field_ids' => $data['field_ids'] ?? NULL,
            'chapter_ids' => $data['chapter_ids'] ?? NULL,
        ]);

        return redirect(route('foods.index',['edition'=>$request->edition]))->with('message', 'Service Point succesfully created');
    }

    public function show(Food $food)
    {
        //
    }

    public function edit(Food $food,Request $request)
    {
        $edition = ConferenceEdition::find($request->edition);
        $fields = Field::all();
        $chapters = Chapter::all();
        
        return view('conference_management.admin.food.edit', compact('food','edition','fields','chapters'));
    }

    public function update(Request $request, Food $food)
    {
        $food->update($request->except('edition'));
        return back()->with('message', 'Update successful!');        
    }

    public function participantExport(Request $request, $id)
    {
        $food = Food::find($id);
        $count = 1;

        if (auth()->user()->role != 1) {
            return abort(404);
        }

        $data = [
            'food_id' => $food->id,
        ];

        return Excel::download(new FoodParticipantExport($data), $food->name . "'s participants.xlsx");
    }

    public function destroy($id,Request $request)
    {
        $edition = ConferenceEdition::find($request->edition);
        $food = Food::findOrFail($id);

        if(auth()->user()->role == 1){
            $food->delete();          
            return back()->with('message',' Delete succesful!');
        }return abort(404);
    }

    public function repairServicePointAllocation(Request $request)
    {
        ServicePointAllocationService::repairServicePointAllocation($request->edition);
        return back()->with('message', ' Repair succesful!');
    }

    public function autoAllocateServicePoint($edition)
    {
        $res = ServicePointAllocationService::autoAllocateServicePoint($edition);

        if (isset($res['count'])) {
            return back()->with('message', $res['count'] . ' Participants allocated successfully!');
        } else {
            return back()->with('error', 'We couldn\'t assign any hostel!');
        }
    }

    public function getAvailableServicePoints(Request $request)
    {
        $toDeallocate = Food::where('conference_edition_id', $request->edition_id)->where('id', $request->deallocated_service_point_id)->first();

        $foods = Food::withCount('payments')->where('conference_edition_id', $toDeallocate->conference_edition_id)->where('level', $toDeallocate->level)->where('id', '!=', $toDeallocate->id)->where('allocation', '>', 0)->where('capacity', '>', 0)
            // ->where('field_ids', $toDeallocate->field_ids)->where('chapter_ids', $toDeallocate->chapter_ids)
            ->get(['id', 'name', 'allocation']);

        return response()->json([
            'status' => true,
            'foods' => $foods
        ]);
    }

    
    public function servicePointMerger(Request $request)
    {
        ServicePointAllocationService::servicePointMerger($request);

        return back()->with('message', 'Merger Succesful!');
    }

}
