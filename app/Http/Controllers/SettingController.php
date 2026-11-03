<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function editSiteDocuments()
    {
        abort_unless(isAdmin()['status'] ?? false, 403);

        $settings = GeneralSetting::firstOrFail();

        return view('admin.settings.site-documents', compact('settings'));
    }

    public function updateSiteDocuments(Request $request)
    {
        abort_unless(isAdmin()['status'] ?? false, 403);

        $request->validate([
            'gsf_constitution_document' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'gsf_code_of_conduct_document' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        $settings = GeneralSetting::firstOrFail();
        $replacedFiles = [];

        $documents = [
            'gsf_constitution_document' => 'gsf_constitution_path',
            'gsf_code_of_conduct_document' => 'gsf_code_of_conduct_path',
        ];

        foreach ($documents as $input => $column) {
            if (!$request->hasFile($input)) {
                continue;
            }

            $replacedFiles[] = $settings->{$column};
            $settings->{$column} = $request->file($input)->store('site-documents', 'public');
        }

        $settings->save();

        foreach (array_filter($replacedFiles) as $file) {
            Storage::disk('public')->delete($file);
        }

        return back()->with('message', 'Website documents updated successfully.');
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
