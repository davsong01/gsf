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
            'campusPostsRequireReview' => BlogSetting::campusPostsRequireReview(),
            'campusPostsCanFeature' => BlogSetting::campusPostsCanFeature(),
            'campusPostsCanAllowComments' => BlogSetting::campusPostsCanAllowComments(),
        ]);
    }

    public function update(Request $request)
    {
        $this->authorizeSettings();
        $request->validate([
            'comments_require_approval' => ['nullable', 'boolean'],
            'campus_posts_require_review' => ['nullable', 'boolean'],
            'campus_posts_can_feature' => ['nullable', 'boolean'],
            'campus_posts_can_allow_comments' => ['nullable', 'boolean'],
        ]);
        BlogSetting::setValue('comments_require_approval', $request->boolean('comments_require_approval') ? '1' : '0');
        BlogSetting::setValue('campus_posts_require_review', $request->boolean('campus_posts_require_review') ? '1' : '0');
        BlogSetting::setValue('campus_posts_can_feature', $request->boolean('campus_posts_can_feature') ? '1' : '0');
        BlogSetting::setValue('campus_posts_can_allow_comments', $request->boolean('campus_posts_can_allow_comments') ? '1' : '0');

        return back()->with('message', 'Blog settings saved.');
    }

    private function authorizeSettings(): void
    {
        abort_unless(BlogPermissions::allows(auth()->user(), BlogPermissions::SETTINGS), 403);
    }
}
