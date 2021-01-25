<?php

namespace App\Http\Controllers;

use App\Food;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FoodController extends Controller
{
    public function index()
    {
         $count = 1;
        if(auth()->user()->level == 'Admin'){
            $foods = Food::orderBy('created_at', 'desc')->get();
            
            
            return view('admin.food.index', compact('foods', 'count'));
        }return abort(404);
    }

    public function create()
    {
        return view('admin.food.create');
    }


    public function store(Request $request)
    {
         $data = $this->validate($request, [
            'name' => 'required|min:5',
            'level' => 'required',
            'capacity' => 'required',
        ]);

        Food::create([
            'name' => $data['name'],
            'level' => $data['level'],
            'capacity' => $data['capacity'],
        ]);

        return redirect(route('foods.index'))->with('message', 'Food Stand succesfully created');
    }

    public function show(Food $food)
    {
        //
    }

    public function edit(Food $food)
    {
        return view('admin.food.edit', compact('food'));
    }

    public function update(Request $request, Food $food)
    {
        $food->update($request->all());
    
        return redirect()->back()->with('message', 'Update successful!');
    }

    public function destroy($id)
    {
        $food = Food::findOrFail($id);

        if(auth()->user()->level == 'Admin'){
           $food->delete();          
            
            return back()->with('message',' Delete succesful!');

        }return abort(404);
    }
}
