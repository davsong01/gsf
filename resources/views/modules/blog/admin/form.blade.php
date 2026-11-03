@extends(auth('stakeholder')->check() ? 'layouts.stakeholderdashboard' : 'layouts.dashboard')
@php($post = $post ?? null)
@php($isStakeholder = auth('stakeholder')->check())
@php($prefix = $isStakeholder ? 'stakeholders' : 'admin')
@section('title', $post ? 'Edit Post' : 'Create Post')
@section('active')<li class="breadcrumb-item">{{ $post ? 'Edit Post' : 'Create Post' }}</li>@endsection
@section('content')
<div class="content-body">
    <section><div class="card"><div class="card-header"><h4 class="card-title">{{ $post ? 'Edit post' : 'Create a post' }}</h4>@include('includes.alerts')</div><div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="{{ $post ? route($prefix.'.blog.posts.update', $post) : route($prefix.'.blog.posts.store') }}">
            @csrf @if($post) @method('PUT') @endif
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group"><label>Title</label><input class="form-control" name="title" required value="{{ old('title', $post?->title) }}"></div>
                    <div class="form-group"><label>Slug</label><input class="form-control" name="slug" value="{{ old('slug', $post?->slug) }}"><small class="text-muted">Leave blank to generate from the title.</small></div>
                    <div class="form-group"><label>Excerpt</label><textarea class="form-control" name="excerpt" rows="3">{{ old('excerpt', $post?->excerpt) }}</textarea></div>
                    <div class="form-group"><label>Rich content</label><textarea class="form-control" id="blog-body" name="body" rows="16" required>{{ old('body', $post?->body) }}</textarea></div>
                    <div class="form-group"><label>Video URL <small class="text-muted">(for video posts)</small></label><input class="form-control" type="url" name="video_url" value="{{ old('video_url', $post?->video_url) }}"></div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group"><label>Post type</label><select class="form-control" name="type"><option value="article" @selected(old('type', $post?->type) === 'article')>Article</option><option value="video" @selected(old('type', $post?->type) === 'video')>Video</option></select></div>
                    <div class="form-group"><label>Category</label><select class="form-control" name="category_id"><option value="">Uncategorised</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $post?->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label>Tags</label><input class="form-control" name="tag_names" value="{{ old('tag_names', $post?->tags?->pluck('name')->implode(', ')) }}" placeholder="faith, leadership, campus"><small class="text-muted">Separate tags with commas.</small></div>
                    <div class="form-group"><label>Related articles</label><select class="form-control" name="related_ids[]" multiple>@foreach($relatedPosts as $relatedPost)<option value="{{ $relatedPost->id }}" @selected($post?->related?->contains($relatedPost->id))>{{ $relatedPost->title }}</option>@endforeach</select></div>
                    <div class="form-group"><label>Network scope</label><select class="form-control" name="scope_type"><option value="national" @selected(old('scope_type', $post?->scope_type) === 'national')>National</option><option value="field" @selected(old('scope_type', $post?->scope_type) === 'field')>Field</option><option value="zone" @selected(old('scope_type', $post?->scope_type) === 'zone')>Zone</option><option value="chapter" @selected(old('scope_type', $post?->scope_type) === 'chapter')>Chapter</option></select></div>
                    @unless($isStakeholder)
                    <div class="form-group"><label>Scope record</label><input class="form-control" name="scope_id" type="number" value="{{ old('scope_id', $post?->scope_id) }}"><small class="text-muted">Use the matching Field, Zone, or Chapter ID.</small></div>
                    <div class="form-group"><label>Status</label><select class="form-control" name="status">@foreach(['draft','review','scheduled','published','archived'] as $status)<option value="{{ $status }}" @selected(old('status', $post?->status ?: 'draft') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                    @endunless
                    <div class="form-group"><label>Publication date</label><input class="form-control" type="datetime-local" name="published_at" value="{{ old('published_at', $post?->published_at?->format('Y-m-d\\TH:i')) }}"></div>
                    <div class="form-group"><label>Featured image</label><input class="form-control-file" type="file" name="featured_image" accept="image/*"></div>
                    <div class="form-group"><label>Social sharing image</label><input class="form-control-file" type="file" name="social_image" accept="image/*"></div>
                    <div class="form-group"><label>SEO title</label><input class="form-control" name="seo_title" value="{{ old('seo_title', $post?->seo_title) }}"></div>
                    <div class="form-group"><label>SEO description</label><textarea class="form-control" name="seo_description" rows="3">{{ old('seo_description', $post?->seo_description) }}</textarea></div>
                    <div class="form-group"><label>Canonical URL</label><input class="form-control" type="url" name="canonical_url" value="{{ old('canonical_url', $post?->canonical_url) }}"></div>
                    <div class="form-group"><label class="d-flex align-items-center"><input class="mr-2" type="checkbox" name="allow_comments" value="1" @checked(old('allow_comments', $post?->allow_comments ?? true))> Allow comments</label></div>
                </div>
            </div>
            <button class="btn btn-primary" name="submit_review" value="1">{{ $isStakeholder ? 'Save and submit for review' : 'Save post' }}</button>
        </form>
    </div></div></section>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor@2.47.8/dist/css/suneditor.min.css">
<script src="https://cdn.jsdelivr.net/npm/suneditor@2.47.8/dist/suneditor.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var textarea = document.querySelector('#blog-body');
    if (!textarea || !window.SUNEDITOR) return;

    var csrf = document.querySelector('input[name="_token"]').value;
    var editor = SUNEDITOR.create(textarea, {
        height: '520px',
        minHeight: '360px',
        resizingBar: true,
        imageResizing: true,
        imageFileInput: true,
        imageMultipleFile: true,
        imageAccept: '.jpg,.jpeg,.png,.webp,.gif',
        imageUploadSizeLimit: 10 * 1024 * 1024,
        mediaAutoSelect: true,
        imageUploadUrl: @json(route($prefix.'.blog.media.upload')),
        imageUploadHeader: { 'X-CSRF-TOKEN': csrf },
        buttonList: [
            ['undo', 'redo', 'formatBlock', 'fontSize', 'bold', 'underline', 'italic', 'strike', 'removeFormat'],
            ['fontColor', 'hiliteColor', 'align', 'outdent', 'indent', 'list', 'horizontalRule'],
            ['table', 'link', 'image', 'video', 'fullScreen', 'showBlocks', 'codeView']
        ],
        placeholder: 'Start writing your story…',
        attributesWhitelist: { all: 'style|src|alt|width|height|class|target|rel' },
        onImageUploadError: function (message) { window.alert(message || 'The image could not be uploaded.'); }
    });

    document.querySelector('form').addEventListener('submit', function () {
        editor.save();
    });
});
</script>
@endsection
