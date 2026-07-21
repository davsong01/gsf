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
            'passport'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'    => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $data['password'] = $request->filled('password')
            ? Hash::make($request->password)
            : Hash::make($request->phone);

        if ($request->hasFile('passport')) {
            $data['passport'] = $this->uploadImage(
                $request->file('passport'),
                'frontend/passports'
            );
        }

        $data['slug'] = Str::slug($data['name']);
        $data['role'] = 1;
        $data['status'] = 'active';

        $user = User::create($data);

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

   public function update(User $official, Request $request)
    {
        $data = $request->except('passport');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('passport')) {
            $data['passport'] = $this->uploadImage(
                $request->file('passport'),
                'frontend/passports'
            );
        }
       
        $official->update($data);

        return redirect()
            ->route('officials.index')
            ->with('message', 'Update successful');
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
