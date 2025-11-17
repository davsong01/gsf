<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class OfficialController extends Controller
{
    public function index()
	{
		$count = 1;
   
		if (auth()->user()->level == 'Admin' && auth()->user()->official == NULL ) {

			$participants = User::with('moderator')->wherelevel('Admin')->orderBy('created_at', 'desc')->where('id', '<>', auth()->user()->id)->get();
            
			return view('admin.official.index', compact('participants', 'count'));
		}

		return abort(404);
	}

    public function create()
	{
        if (auth()->user()->level == 'Admin' && auth()->user()->official == NULL ) {
			return view('admin.official.create');
		} 
		return back(404);
	}

    public function store(Request $request){
        // dd($request->all());
       
		//Store block for Admin
		if (auth()->user()->level == 'Admin') {
			$data = $this->validate($request, [
				'name' => 'required|min:3',
				'email' => 'required|unique:users,email',
				'phone' => 'required',
				'gender' => 'required',
                'passport' => 'nullable|max:200',
                'password' => 'nullable',
			]);

             //Handle password
            if ($request['password']) {
                $password = Hash::make($request['password']);
            } else {
                $password = Hash::make($request['phone']);
            }

                //Handle Passport Upload
                //get filename with extensionz 
            if ($request['passport']) {

                $imgName = date('Y-m-d-His') . $request->passport->getClientOriginalName();
                $passport = Image::make($request->passport)->resize(500, 500);
                $passport->save('frontend/passports' . '/' . $imgName);
                $passport = 'frontend/passports/' . $imgName;
            
            } else {
                $passport = NULL;
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'gender' => $data['gender'],
                'password' => $password,
                'slot' => 1,
                'slot_filled' => 1,
                'level' => 'Admin',
                'type' => 4,
                'Official' => 'YES',
                'uploaded_by' => auth()->user()->id
                
            ]);

            $user->update([
                'conference_number' => 'GSF-official-' . $user->id,

            ]);

            return redirect(route('officials.index'))->with('message', 'Official successfully created');

        }
    }

    public function edit(User $official)
	{
        if (auth()->user()->level == 'Admin' && auth()->user()->official == NULL ) {
        
            return view('admin.official.edit')->with('official', $official);
            
        }
    }

    public function update(User $official, Request $request){
// dd($request->all(), $official);
        if ($request['password']) {
            $request['password'] = Hash::make($request['password']);
        } else {
            $request['password'] = Hash::make($request['phone']);
        }
        
        if($request->level == 'Admin'){
            $request['official'] = NULL;

        }
        if($request->level == 'Official'){
            $request['official'] = 'YES';
            $request['level'] = 'Admin';
        }
        $official->update($request->all());

        return back()->with('message', 'Update successful');
    }

    public function delete(User $official){

        if (auth()->user()->id == $official->id) {
            return back()->with('warning', 'I\'m sorry but You cannot delete your self');
        }
     
        if (isset($official->passport)) {
            unlink($official->passport);
        }
        $official->forceDelete();
    
        
        return redirect(route('officials.index'))->with('message', 'Delete Successful');
    }
    
}
