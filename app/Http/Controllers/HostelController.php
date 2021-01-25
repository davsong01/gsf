<?php

namespace App\Http\Controllers;

use App\Hostel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HostelController extends Controller
{

    public function index()
    {
        $count = 1;
        if(auth()->user()->level == 'Admin'){
            $hostels = Hostel::orderBy('created_at', 'desc')->get();
            
            
            return view('admin.hostel.index', compact('hostels', 'count'));
        }return abort(404);
    }

    public function create()
    {
        return view('admin.hostel.create');
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|min:5',
            'type' => 'required',
            'level' => 'required',
            'capacity' => 'required',
        ]);

        Hostel::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'level' => $data['level'],
            'capacity' => $data['capacity'],
        ]);

        return redirect(route('hostels.index'))->with('message', 'Hostel succesfully created');
    }

    public function show(Hostel $hostel)
    {
        //
    }


    public function edit(Hostel $hostel)
    {
        return view('admin.hostel.edit', compact('hostel'));
    }

    public function update(Request $request, Hostel $hostel)
    {
     
            $hostel->update($request->all());
       

        return redirect()->back()->with('message', 'Update successful!');
    }

    public function destroy($id)
    {
        $hostel = Hostel::findOrFail($id);

        if(auth()->user()->level == 'Admin'){
           $hostel->delete();          
            
            return back()->with('message',' Delete succesful!');

        }return abort(404);
    }
}
