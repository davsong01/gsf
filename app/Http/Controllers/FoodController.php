<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Field;
use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Models\ConferenceEdition;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FoodParticipantExport;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $count = 1;
        $edition = ConferenceEdition::find($request->edition);
        if(auth()->user()->role == 1){
            $foods = Food::where('conference_edition_id', $edition->id)->orderBy('created_at', 'desc')->get();
            
            $foods->each(function ($food) {
                $food->fields = Field::whereIn('id', $food->field_ids)->get();
                $food->chapters = Chapter::whereIn('id', $food->chapter_ids)->get();
            });
            
            return view('conference_management.admin.food.index', compact('foods', 'count','edition'));
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
            'field_ids' => json_encode($data['field_ids']),
            'chapter_ids' => json_encode($data['chapter_ids']),
        ]);

        return redirect(route('foods.index',['edition'=>$request->edition]))->with('message', 'Food Stand succesfully created');
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
}
