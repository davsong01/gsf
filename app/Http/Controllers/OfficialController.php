<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class OfficialController extends Controller
{
    public function index()
	{
        $participants = User::where('role', 1)->latest()->get();

        return view('admin.official.index', compact('participants'));

		return abort(404);
	}

    public function create()
	{
        if (auth()->user()->role != 1 ) {
            return back(404);
        }

        return view('admin.official.create');
	}

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|min:3',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'required|string',
            'gender'      => 'required|in:Male,Female',
            'passport'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB
            'password'    => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        // Handle password
       if ($request['password']) {
            $password = Hash::make($request['password']);
        } else {
            $password = Hash::make($request['phone']);
        }

        // Handle passport upload
        $passportPath = null;
        if ($request->hasFile('passport')) {
            $passportPath = $this->uploadImage(
                $request->file('passport'),
                'frontend/passports'
            );
        }

        $user = User::create([
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']),
            'email'       => $data['email'],
            'phone'       => $data['phone'],
            'gender'      => $data['gender'],
            'passport'    => $passportPath,
            'password'    => $password,
            'role'        => 1,
            'permissions' => $data['permissions'] ?? [],
        ]);

        $user->update([
            'family_id' => 'GSF-OFF-' . $user->id,
        ]);

        return redirect()
            ->route('officials.index')
            ->with('message', 'Official successfully created');
    }

    public function edit(User $official)
	{
        return view('admin.official.create')->with('official', $official);
    }

    public function update(User $official, Request $request){
        if ($request['password']) {
            $request['password'] = Hash::make($request['password']);
        } else {
            $request['password'] = Hash::make($request['phone']);
        }

        if($request->filled('passport')){
            $request['passport'] = $this->uploadImage($request->passport, 'frontend/passports');
        }
        $official->update($request->all());

        return redirect(route('officials.index'))->with('message', 'Update successful');
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
