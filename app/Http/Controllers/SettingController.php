<?php

namespace App\Http\Controllers;

use App\User;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        if(auth()->user()->level== 'Admin'){

            $setting = Setting::first();

            return view('admin.settings.edit', compact('setting'));
        }
    }

    public function update(Request $request, Setting $setting)
    {
        $this->validate($request, [
            'conference_theme' => 'required',
            'registration_fee' => 'required|numeric',
            'official_email' => 'required|email',
            'alumni_fee' => 'required',
            'alumni_registration_fee' => 'required|numeric',
            'new_alumni_registration_fee' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'close_registration' => 'required|date',
            'conference_overview' => 'required'
        ]);

        $setting->update($request->all());

        return back()->with('message', 'Applicaion settings succesfully updated');
    }

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
