@extends('layouts.dashboard')
@section('title', 'Blog Settings')
@section('active')<li class="breadcrumb-item">Blog Settings</li>@endsection
@section('content')
<div class="content-body">
    <section class="card">
        <div class="card-header"><h4 class="card-title mb-1">Blog Settings</h4><small class="text-muted">Control campus publishing and reader comments for GSF News.</small></div>
        <div class="card-body">
            @include('includes.alerts')
            <form method="POST" action="{{ route('admin.blog.settings.update') }}" class="blog-settings-form">
                @csrf @method('PUT')
                <section class="border rounded p-3 mb-3" aria-labelledby="blog-comment-moderation-title">
                    <h5 id="blog-comment-moderation-title">Comment moderation</h5>
                    <p class="text-muted">Choose whether new comments need approval before appearing beneath a post.</p>
                    <input type="hidden" name="comments_require_approval" value="0">
                    <label class="d-flex align-items-start mb-0">
                        <input class="mt-1 mr-2" type="checkbox" name="comments_require_approval" value="1" @checked($commentsRequireApproval)>
                        <span><strong>Require approval before publishing comments</strong><br><small class="text-muted">When enabled, new comments appear in the Comments dashboard as pending. When disabled, they appear on the post immediately.</small></span>
                    </label>
                </section>
                <section class="border rounded p-3 mb-3" aria-labelledby="blog-campus-publishing-title">
                    <h5 id="blog-campus-publishing-title">Campus publishing</h5>
                    <p class="text-muted">Set what chapter accounts can do when they create or edit posts.</p>
                    <input type="hidden" name="campus_posts_require_review" value="0">
                    <label class="d-flex align-items-start mb-3">
                        <input class="mt-1 mr-2" type="checkbox" name="campus_posts_require_review" value="1" @checked($campusPostsRequireReview)>
                        <span><strong>Require review for campus news</strong><br><small class="text-muted">When enabled, campus posts are sent for review. When disabled, campus accounts can publish immediately.</small></span>
                    </label>
                    <input type="hidden" name="campus_posts_can_feature" value="0">
                    <label class="d-flex align-items-start mb-3">
                        <input class="mt-1 mr-2" type="checkbox" name="campus_posts_can_feature" value="1" @checked($campusPostsCanFeature)>
                        <span><strong>Allow campuses to feature posts</strong><br><small class="text-muted">Lets chapter accounts feature their stories on GSF News. Disabled by default.</small></span>
                    </label>
                    <input type="hidden" name="campus_posts_can_allow_comments" value="0">
                    <label class="d-flex align-items-start mb-0">
                        <input class="mt-1 mr-2" type="checkbox" name="campus_posts_can_allow_comments" value="1" @checked($campusPostsCanAllowComments)>
                        <span><strong>Allow campuses to enable comments</strong><br><small class="text-muted">Lets chapter accounts choose whether readers can comment on each post.</small></span>
                    </label>
                </section>
                <section class="border rounded p-3 mb-3" aria-labelledby="blog-facebook-publishing-title">
                    <h5 id="blog-facebook-publishing-title">Facebook publishing</h5>
                    <p class="text-muted">Campus requests are moderated separately from website publication. National admin posts can publish to Facebook as soon as they are published on the website.</p>

                    <div class="form-row">
                        <div class="form-group col-12 col-md-6">
                            <label for="facebook_page_id">GSF National Facebook Page ID</label>
                            <input class="form-control" id="facebook_page_id" name="facebook_page_id" value="{{ old('facebook_page_id', $facebookPageId) }}" autocomplete="off">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="facebook_page_access_token">Page access token</label>
                            <input class="form-control" type="password" id="facebook_page_access_token" name="facebook_page_access_token" autocomplete="new-password" placeholder="{{ $facebookCredentialsConfigured ? 'Saved securely; enter a new token to replace it' : 'Paste the Page access token' }}">
                            <small class="form-text text-muted">{{ $facebookCredentialsConfigured ? 'A Page token is saved securely. Leave blank to keep it.' : 'Use a Page access token with pages_manage_posts. Page discovery typically also needs pages_show_list and pages_read_engagement.' }}</small>
                        </div>
                    </div>
                    <p><span class="badge badge-{{ $facebookCredentialsConfigured ? 'success' : 'secondary' }}">{{ $facebookCredentialsConfigured ? 'Facebook credentials configured' : 'Facebook credentials not configured' }}</span></p>

                    <input type="hidden" name="campus_events_facebook_enabled" value="0">
                    <label class="d-flex align-items-start mb-3">
                        <input class="mt-1 mr-2" type="checkbox" name="campus_events_facebook_enabled" value="1" @checked($campusEventsCanPublishToFacebook)>
                        <span><strong>Allow Campus Event Posting to Facebook</strong><br><small class="text-muted">Lets campus stakeholders request that their event be posted to the GSF National Facebook Page. National admin events are shared when published.</small></span>
                    </label>
                    <input type="hidden" name="campus_events_facebook_review_required" value="0">
                    <label class="d-flex align-items-start mb-4">
                        <input class="mt-1 mr-2" type="checkbox" name="campus_events_facebook_review_required" value="1" @checked($campusEventsFacebookRequireReview)>
                        <span><strong>Require Review for Facebook Events</strong><br><small class="text-muted">Campus event requests wait for Facebook approval. This review is separate from website event review.</small></span>
                    </label>

                    <input type="hidden" name="campus_posts_facebook_enabled" value="0">
                    <label class="d-flex align-items-start mb-3">
                        <input class="mt-1 mr-2" type="checkbox" name="campus_posts_facebook_enabled" value="1" @checked($campusPostsCanPublishToFacebook)>
                        <span><strong>Allow Campus Post Posting to Facebook</strong><br><small class="text-muted">Lets campus stakeholders request that their news post be shared on the GSF National Facebook Page. National admin posts are shared when published.</small></span>
                    </label>
                    <input type="hidden" name="campus_posts_facebook_review_required" value="0">
                    <label class="d-flex align-items-start mb-0">
                        <input class="mt-1 mr-2" type="checkbox" name="campus_posts_facebook_review_required" value="1" @checked($campusPostsFacebookRequireReview)>
                        <span><strong>Require Review for Facebook Posts</strong><br><small class="text-muted">Campus post requests wait for Facebook approval, independently of website publication.</small></span>
                    </label>
                </section>
                <button class="btn btn-primary" type="submit">Save settings</button>
            </form>
        </div>
    </section>
</div>
@endsection
