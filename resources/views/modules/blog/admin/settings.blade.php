@extends('layouts.dashboard')
@section('title', 'Blog Settings')
@section('active')<li class="breadcrumb-item">Blog Settings</li>@endsection
@section('content')
<div class="content-body">
    <section class="card">
        <div class="card-header"><h4 class="card-title mb-1">Blog Settings</h4><small class="text-muted">Control how the GSF News module handles reader comments.</small></div>
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
                <button class="btn btn-primary" type="submit">Save settings</button>
            </form>
        </div>
    </section>
</div>
@endsection
