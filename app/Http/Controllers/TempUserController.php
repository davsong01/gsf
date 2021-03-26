<?php

namespace App\Http\Controllers;

use App\TempUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TempUserController extends Controller
{
    public function index()
	{
		$count = 1;
		if (auth()->user()->level == 'Admin') {

			$participants = TempUser::all();

			return view('admin.temp_users.index', compact('participants', 'count'));
        }
        return abort(404);
	}

    public function destroy($id){
        $user = TempUser::find($id);
        $user->force();

        return back()->with('message', 'Delete succesful');
    }
}
