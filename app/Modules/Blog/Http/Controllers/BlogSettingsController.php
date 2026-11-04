<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Modules\Blog\BlogPermissions;
use App\Modules\Blog\Models\BlogSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BlogSettingsController extends Controller
{
    public function index()
    {
        $this->authorizeSettings();
        return view('modules.blog.admin.settings', [
            'commentsRequireApproval' => BlogSetting::commentsRequireApproval(),
        ]);
    }

    public function update(Request $request)
    {
        $this->authorizeSettings();
        $request->validate(['comments_require_approval' => ['nullable', 'boolean']]);
        BlogSetting::setValue('comments_require_approval', $request->boolean('comments_require_approval') ? '1' : '0');

        return back()->with('message', 'Blog settings saved.');
    }

    private function authorizeSettings(): void
    {
        abort_unless(BlogPermissions::allows(auth()->user(), BlogPermissions::SETTINGS), 403);
    }
}
