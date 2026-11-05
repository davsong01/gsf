<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function editGeneralSettings()
    {
        abort_unless((isAdmin()['status'] ?? false) && userHasPermission('general-settings.edit'), 403);

        $settings = GeneralSetting::firstOrFail();

        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneralSettings(Request $request)
    {
        abort_unless((isAdmin()['status'] ?? false) && userHasPermission('general-settings.edit'), 403);

        $request->validate([
            'site_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'site_favicon' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,ico', 'max:2048'],
            'gsf_constitution_document' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'gsf_code_of_conduct_document' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'gsf_family_song_lyrics' => ['nullable', 'string', 'max:30000'],
            'gsf_family_song_audio' => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a,aac', 'max:51200'],
            'site_contact_email' => ['nullable', 'email:rfc', 'max:255'],
        ]);

        $settings = GeneralSetting::firstOrFail();
        $replacedFiles = [];
        $fileUploadService = app(FileUploadService::class);

        foreach (['site_logo' => 'site-branding/logos', 'site_favicon' => 'site-branding/favicons'] as $input => $folder) {
            if (!$request->hasFile($input)) {
                continue;
            }

            $replacedFiles[] = $settings->{$input};
            $extension = strtolower($request->file($input)->getClientOriginalExtension());
            $settings->{$input} = $fileUploadService->publicUpload(
                $request->file($input),
                $folder,
                '',
                Str::uuid().'.'.$extension
            );
        }

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

        $settings->gsf_family_song_lyrics = $request->input('gsf_family_song_lyrics');
        $settings->site_contact_email = $request->input('site_contact_email');

        if ($request->hasFile('gsf_family_song_audio')) {
            $replacedFiles[] = $settings->gsf_family_song_audio_path;
            $audio = $request->file('gsf_family_song_audio');
            $extension = strtolower($audio->getClientOriginalExtension());
            $settings->gsf_family_song_audio_path = $fileUploadService->publicUpload(
                $audio,
                'site-audio',
                '',
                Str::uuid().'.'.$extension
            );
        }

        $settings->save();

        foreach (array_filter($replacedFiles) as $file) {
            $path = filter_var($file, FILTER_VALIDATE_URL)
                ? ltrim((string) parse_url($file, PHP_URL_PATH), '/')
                : ltrim((string) $file, '/');

            if (str_starts_with($path, 'uploads/')) {
                $publicUploadRoot = rtrim(config('filesystems.image_upload_path', public_path()), '/');
                foreach ([$publicUploadRoot.'/'.$path, public_path($path), base_path($path)] as $absolutePath) {
                    if (is_file($absolutePath)) {
                        @unlink($absolutePath);
                        break;
                    }
                }
            } else {
                Storage::disk('public')->delete(str_starts_with($path, 'storage/') ? substr($path, 8) : $path);
            }
        }

        return back()->with('message', 'Website settings updated successfully.');
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
