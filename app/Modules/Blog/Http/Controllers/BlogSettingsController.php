<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Modules\Blog\BlogPermissions;
use App\Modules\Blog\Models\BlogSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;

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
            'campusEventsCanPublishToFacebook' => BlogSetting::campusEventsCanPublishToFacebook(),
            'campusEventsFacebookRequireReview' => BlogSetting::campusEventsFacebookRequireReview(),
            'campusPostsCanPublishToFacebook' => BlogSetting::campusPostsCanPublishToFacebook(),
            'campusPostsFacebookRequireReview' => BlogSetting::campusPostsFacebookRequireReview(),
            'facebookPageId' => BlogSetting::facebookPageId(),
            'facebookCredentialsConfigured' => filled(BlogSetting::facebookPageId()) && filled(BlogSetting::facebookPageAccessToken()),
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
            'campus_events_facebook_enabled' => ['nullable', 'boolean'],
            'campus_events_facebook_review_required' => ['nullable', 'boolean'],
            'campus_posts_facebook_enabled' => ['nullable', 'boolean'],
            'campus_posts_facebook_review_required' => ['nullable', 'boolean'],
            'facebook_page_id' => ['nullable', 'string', 'max:100'],
            'facebook_page_access_token' => ['nullable', 'string', 'max:5000'],
        ]);
        BlogSetting::setValue('comments_require_approval', $request->boolean('comments_require_approval') ? '1' : '0');
        BlogSetting::setValue('campus_posts_require_review', $request->boolean('campus_posts_require_review') ? '1' : '0');
        BlogSetting::setValue('campus_posts_can_feature', $request->boolean('campus_posts_can_feature') ? '1' : '0');
        BlogSetting::setValue('campus_posts_can_allow_comments', $request->boolean('campus_posts_can_allow_comments') ? '1' : '0');
        BlogSetting::setValue('campus_events_facebook_enabled', $request->boolean('campus_events_facebook_enabled') ? '1' : '0');
        BlogSetting::setValue('campus_events_facebook_review_required', $request->boolean('campus_events_facebook_review_required') ? '1' : '0');
        BlogSetting::setValue('campus_posts_facebook_enabled', $request->boolean('campus_posts_facebook_enabled') ? '1' : '0');
        BlogSetting::setValue('campus_posts_facebook_review_required', $request->boolean('campus_posts_facebook_review_required') ? '1' : '0');

        if ($request->filled('facebook_page_id')) {
            BlogSetting::setValue('facebook_page_id', trim($request->input('facebook_page_id')));
        }

        if ($request->filled('facebook_page_access_token')) {
            BlogSetting::setValue('facebook_page_access_token', Crypt::encryptString(trim($request->input('facebook_page_access_token'))));
        }

        return back()->with('message', 'Blog settings saved.');
    }

    private function authorizeSettings(): void
    {
        abort_unless(BlogPermissions::allows(auth()->user(), BlogPermissions::SETTINGS), 403);
    }
}
