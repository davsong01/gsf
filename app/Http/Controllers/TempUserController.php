<?php

namespace App\Http\Controllers;

use App\TempUser;
use App\ConferenceEdition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TempUserController extends Controller
{
    public function index(Request $request)
	{
		$count = 1;
        $edition = ConferenceEdition::with(['payments', 'donations'])->find($request->edition);

		if (auth()->user()->role == 1) {
			$participants = TempUser::where('conference_edition_id', $edition->id)->orderBy('created_at', 'desc')->get();
			return view('admin.temp_users.index', compact('participants', 'count','edition'));
        }
        return abort(404);
	}

    public function show(Request $request, $id){
      
        $tempusers = TempUser::find($id);

        $tempusers->delete();

        return back()->with('message', 'Delete succesful');
    }

    public function destroy($id){
       
    }
}
