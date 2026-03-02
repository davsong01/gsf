<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\ConferenceEdition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $count = 1;
        $edition = ConferenceEdition::find($request->edition);
        $materials = Material::where('conference_edition_id', $edition->id)->orderBy('created_at', 'desc')->get();
        $user = auth()->user();

        if($user->role == 1){
            return view('admin.materials.index', compact('materials', 'count', 'edition'));
        }

        $edition = $this->edition;

        if (getRegistrationUserLevel(['Participant','Alumni','Moderator'], $edition)){
            $payment = $request->payment_id;
            return view('conference_management.participant.materials', compact('edition','materials', 'count','payment'));
        }

        if($user->level == 'Participant' || $user->level == 'Moderator' || $user->level == 'Alumni' || auth()->user()->level == 'Nec'){
            return view('participant.materials', compact('materials', 'count'));
        }
    }

    public function create(Request $request)
    {
        $edition = ConferenceEdition::find($request->edition);
        $edition = $edition->id;
        return view('admin.materials.create',compact('edition'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'file*' => 'required|max:5000',
        ]);

        if(auth()->user()->role == 1){
            foreach($request->file('file') as $file){
                $location = $this->uploadFile($file, 'conferencematerials');
                // $file->move('conferencematerials', $file->getClientOriginalName());
                Material::create([
                    'name' =>$file->getClientOriginalName(),
                    // 'location' => 'conferencematerials/' . $file->getClientOriginalName(),
                    'location' => $location,
                    'conference_edition_id' =>  $request->edition,
                ]);
            }
            return redirect(route('materials.index', ['edition' => $request->edition]))->with('message', 'Material(s) succesfully uploaded');

        } return abort(404);

    }


    public function show(Material $material)
    {
        $realpath = $material->location;

        if(file_exists($realpath)){
            return response()->download($realpath);
        }else{
            return back()->with('error','File doesnt exist');
        }
    }


    public function destroy($id)
    {
        $material = Material::find($id);

        if (file_exists($material->location)) unlink( $material->location);

        $material->delete();

        return back()->with('message', 'Material deleted successfully');
    }
}
