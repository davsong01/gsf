<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function editProfile($id)
    {
        $user = User::findorFail($id);

        return view('user.profile')->with('user', $user);
    }

    public function saveProfile(Request $request)
    {
         $data = $this->validate($request, [
                'name' => 'nullable|min:3',
                'username' => 'required|min:3|max:200',
                'email' => 'required',
                'phone' => 'nullable',
                'password' => 'nullable|min:8',
            ]);

        $user = User::findOrFail(Auth()->user()->id);

          //handle password
        if($request['password']){
            $request['password'] = Hash::make($request['password']);
        }else $request['password'] = $user->password;

        $user->update($request->all());

        return back()->with('message', 'Profile update successful');
    }
}
