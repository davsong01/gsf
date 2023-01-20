<?php

namespace App\Http\Controllers;

use App\Zone;
use App\Field;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZoneController extends Controller
{
    public function index()
    {
        $count = 1;
        $zones = Zone::with(['chapters', 'stakeholder'])->get();

        return view('admin.zones.index', compact('zones', 'count'));
    }

    public function create()
    {
        $fields = Field::all();
        return view('admin.zones.create', compact('fields'));
    }

    public function edit(Zone $zone)
    {
        $fields = Field::all();
        return view('admin.zones.edit', compact('zone', 'fields'));
    }

    public function store(Request $request)
    {
    
        $data = $this->validate($request, [
            'name' => 'required|unique:zones,name',
            'field_id' => 'required|numeric',
        ]);

        $zone = Zone::create([
            'name' => $data['name'],
            'field_id' => $data['field_id'],
        ]);

        return redirect(route('zones.index'))->with('message', 'Zone succesfully created');
    }

    public function update(Request $request, Zone $zone)
    {
        $zone->update($request->all());
       
        return redirect(route('zones.index'))->with('message', 'Update successful!');
    }

    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        if($zone->chapters->count() > 1){
            return back()->with('error', 'You cannot delete this zone because it has campuses');
        }
        
        $zone->delete(); 
        return redirect()->back()->with('message', 'delete successful!');
    }


}
