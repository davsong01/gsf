<?php

namespace App\Http\Controllers;

use App\Material;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MaterialController extends Controller
{
    public function index()
    {
        $count = 1;
        $materials = Material::all();

        if(auth()->user()->level == 'Admin'){
           
            return view('admin.materials.index', compact('materials', 'count'));

        }
        if(auth()->user()->level == 'Participant' || auth()->user()->level == 'Moderator' || auth()->user()->level == 'Alumni' || auth()->user()->level == 'Nec'){
           return view('participant.materials', compact('materials', 'count'));
        }
       
    }

    public function create()
    {
        return view('admin.materials.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'file*' => 'required|max:5000',
        ]);

        if(auth()->user()->level == 'Admin'){
            foreach($request->file('file') as $file){

                $file->move('conferencematerials', $file->getClientOriginalName());  

                Material::create([
                    'name' =>$file->getClientOriginalName(),
                    'location' => 'conferencematerials/'.$file->getClientOriginalName(),
                ]);
            }

            return redirect(route('materials.index'))->with('message', 'Upload Successful');
        } return abort(404);

    }

   
    public function show(Material $material)
    {
        $realpath = $material->location;
      
        return response()->download($realpath);
    }

    
    public function destroy($id)
    {
        $material = Material::find($id);

        unlink( $material->location);

        $material->delete();
        
        return back()->with('message', 'Material deleted successfully');
    }
}
