<?php

namespace App\Http\Controllers;

use App\Material;
use App\ConferenceEdition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $count = 1;
        $edition = ConferenceEdition::find($request->edition);
        $materials = Material::where('conference_edition_id', $edition->id)->orderBy('created_at', 'desc')->get();
    
        if(auth()->user()->role == 1){
            return view('admin.materials.index', compact('materials', 'count', 'edition'));
        }

        $edition = $this->edition;
        
        if (auth()->user()->isParticipant($edition) || auth()->user()->isAlumni($edition) || auth()->user()->isModerator($edition)) {
            $payment = $request->payment_id;
            return view('conference_management.participant.materials', compact('edition','materials', 'count','payment'));
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
       
        if(file_exists(public_path().'/'. $realpath)){
            return response()->download($realpath);
        }else{
            return back()->with('error','File doesnt exist');
        }
    }

    
    public function destroy($id)
    {
        $material = Material::find($id);

        unlink( $material->location);

        $material->delete();
        
        return back()->with('message', 'Material deleted successfully');
    }
}
