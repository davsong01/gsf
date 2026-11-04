@extends(auth('stakeholder')->check() ? 'layouts.stakeholderdashboard' : 'layouts.dashboard')
@php($post = $post ?? null)
@php($isStakeholder = auth('stakeholder')->check())
@php($prefix = $isStakeholder ? 'stakeholders' : 'admin')
@section('title', $post ? 'Edit Post' : 'Create Post')
@section('active')<li class="breadcrumb-item">{{ $post ? 'Edit Post' : 'Create Post' }}</li>@endsection
@section('content')
<div class="content-body">
    <section><div class="card"><div class="card-header"><h4 class="card-title">{{ $post ? 'Edit post' : 'Create a post' }}</h4>@include('includes.alerts')</div><div class="card-body">
        <form id="blog-post-form" method="POST" enctype="multipart/form-data" action="{{ $post ? route($prefix.'.blog.posts.update', $post) : route($prefix.'.blog.posts.store') }}">
            @csrf @if($post) @method('PUT') @endif
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group"><label>Title</label><input class="form-control" name="title" required value="{{ old('title', $post?->title) }}"></div>
                    <div class="form-group"><label>Slug</label><input class="form-control" name="slug" value="{{ old('slug', $post?->slug) }}"><small class="text-muted">Leave blank to generate from the title.</small></div>
                    <div class="form-group"><label>Excerpt</label><textarea class="form-control" name="excerpt" rows="3">{{ old('excerpt', $post?->excerpt) }}</textarea></div>
                    <div class="form-group">
                        <label for="blog-body">Rich content</label>
                        <div class="mb-2"><label class="btn btn-outline-primary mb-0" for="blog-inline-images">Add images to article</label><input id="blog-inline-images" type="file" accept="image/jpeg,image/png,image/webp,image/gif" multiple hidden><span id="blog-image-status" class="ml-2" role="status" aria-live="polite"></span></div>
                        <textarea class="form-control" id="blog-body" name="body" rows="16" required>{{ old('body', str_replace('//storage/', '/storage/', $post?->body ?? '')) }}</textarea>
                    </div>
                    <div class="form-group"><label>Video URL <small class="text-muted">(for video posts)</small></label><input class="form-control" type="url" name="video_url" value="{{ old('video_url', $post?->video_url) }}"></div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group"><label>Post type</label><select class="form-control" name="type"><option value="article" @selected(old('type', $post?->type) === 'article')>Article</option><option value="video" @selected(old('type', $post?->type) === 'video')>Video</option></select></div>
                    @php($selectedCategoryIds = collect(old('category_ids', $post?->categories?->pluck('id')->all() ?? []))->map('strval')->all())
                    <div class="form-group"><label>Categories</label><select class="form-control" name="category_ids[]" multiple size="5" aria-describedby="blog-categories-help">@foreach($categories as $category)<option value="{{ $category->id }}" @selected(in_array((string) $category->id, $selectedCategoryIds, true))>{{ $category->name }}</option>@endforeach</select><small id="blog-categories-help" class="text-muted">Select one or more categories. Hold Ctrl (Windows) or Command (Mac) to select multiple.</small></div>
                    @if($post && !empty($canChangeAuthor))
                    @php($currentAuthorKey = $post->author_type && $post->author_id ? $post->author_type.':'.$post->author_id : '')
                    <div class="form-group"><label for="blog-post-author">Post author</label><select class="form-control" id="blog-post-author" name="author_key"><option value="">Keep current author</option>@foreach($authors as $author)<option value="{{ $author['key'] }}" @selected(old('author_key', $currentAuthorKey) === $author['key'])>{{ $author['name'] }}{{ !empty($author['email']) ? ' · '.$author['email'] : '' }}</option>@endforeach</select><small class="text-muted">Choose an account with permission to create blog posts.</small>@error('author_key')<small class="text-danger d-block">{{ $message }}</small>@enderror</div>
                    @endif
                    @php($selectedTagIds = collect(old('tag_ids', $post?->tags?->pluck('id')->all() ?? []))->map('strval')->all())
                    <div class="form-group"><label for="blog-post-tags">Tags</label><select class="form-control" id="blog-post-tags" name="tag_ids[]" multiple size="5" aria-describedby="blog-tags-help">@foreach($tags as $tag)<option value="{{ $tag->id }}" @selected(in_array((string) $tag->id, $selectedTagIds, true))>{{ $tag->name }}</option>@endforeach</select><small id="blog-tags-help" class="text-muted">Select one or more tags. Hold Ctrl (Windows) or Command (Mac) to select multiple.</small></div>
                    @unless($isStakeholder)
                    <div class="form-group"><label>Status</label><select class="form-control" name="status">@foreach(['draft','review','scheduled','published','archived'] as $status)<option value="{{ $status }}" @selected(old('status', $post?->status ?: 'draft') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
                    @endunless
                    <div class="form-group"><label>Publication date</label><input class="form-control" type="datetime-local" name="published_at" value="{{ old('published_at', $post?->published_at?->format('Y-m-d\\TH:i')) }}"></div>
                    @if($canFeaturePosts)<div class="form-group"><label class="d-flex align-items-center"><input class="mr-2" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $post?->is_featured ?? false))> Feature this post in GSF News</label><small class="text-muted">Featured posts appear in the lead story area on the public news page.</small></div>@endif
                    <div class="form-group"><label>Featured image</label><input class="form-control-file" type="file" name="featured_image" accept="image/*"></div>
                    <div class="form-group"><label>SEO title</label><input class="form-control" name="seo_title" value="{{ old('seo_title', $post?->seo_title) }}"></div>
                    <div class="form-group"><label>SEO description</label><textarea class="form-control" name="seo_description" rows="3">{{ old('seo_description', $post?->seo_description) }}</textarea></div>
                    <div class="form-group"><label>Canonical URL</label><input class="form-control" type="url" name="canonical_url" value="{{ old('canonical_url', $post?->canonical_url) }}"></div>
                    <div class="form-group"><label class="d-flex align-items-center"><input class="mr-2" type="checkbox" name="allow_comments" value="1" @checked(old('allow_comments', $post?->allow_comments ?? true))> Allow comments</label></div>
                </div>
            </div>
            @if($post)<a class="btn btn-outline-secondary" href="{{ route($prefix.'.blog.posts.preview', $post) }}" target="_blank" rel="noopener">View post</a>@endif
            <button class="btn btn-primary" name="submit_review" value="1">{{ $isStakeholder ? 'Save and submit for review' : 'Save post' }}</button>
        </form>
    </div></div></section>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor@2.47.12/dist/css/suneditor.min.css">
<script src="https://cdn.jsdelivr.net/npm/suneditor@2.47.12/dist/suneditor.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var textarea = document.querySelector('#blog-body');
    if (!textarea || !window.SUNEDITOR) return;

    var form = document.querySelector('#blog-post-form');
    var csrf = form.querySelector('input[name="_token"]').value;
    var imageInput = document.querySelector('#blog-inline-images');
    var imageStatus = document.querySelector('#blog-image-status');
    var uploadUrl = @json(route($prefix.'.blog.media.upload'));
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
        imageUploadUrl: uploadUrl,
        imageUploadHeader: { 'X-CSRF-TOKEN': csrf },
        buttonList: [
            ['undo', 'redo', 'formatBlock', 'fontSize', 'bold', 'underline', 'italic', 'strike', 'removeFormat'],
            ['fontColor', 'hiliteColor', 'align', 'outdent', 'indent', 'list', 'horizontalRule'],
            ['table', 'link', 'image', 'video', 'fullScreen', 'showBlocks', 'codeView']
        ],
        placeholder: 'Start writing your story…',
        attributesWhitelist: { all: 'style|src|alt|width|height|class|target|rel' }
    });

    editor.onImageUploadError = function (message) {
        imageStatus.textContent = message || 'The image could not be uploaded.';
        imageStatus.classList.add('text-danger');
        return false;
    };

    var uploading = false;
    imageInput.addEventListener('change', async function () {
        if (!imageInput.files.length || uploading) return;
        uploading = true;
        imageStatus.classList.remove('text-danger');
        imageStatus.textContent = 'Uploading images…';
        var data = new FormData();
        Array.from(imageInput.files).forEach(function (file, index) {
            data.append('file-' + index, file);
        });

        try {
            var response = await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                credentials: 'same-origin',
                body: data
            });
            var result = await response.json();
            if (!response.ok || !Array.isArray(result.result) || !result.result.length) {
                throw new Error(result.message || result.errorMessage || 'Image upload failed.');
            }

            result.result.forEach(function (image) {
                var element = document.createElement('img');
                element.src = image.url;
                element.alt = '';
                element.style.maxWidth = '100%';
                editor.insertHTML('<p>' + element.outerHTML + '</p>');
            });
            editor.save();
            imageStatus.textContent = result.result.length + (result.result.length === 1 ? ' image added.' : ' images added.');
        } catch (error) {
            imageStatus.classList.add('text-danger');
            imageStatus.textContent = error.message || 'Image upload failed.';
        } finally {
            uploading = false;
            imageInput.value = '';
        }
    });

    form.addEventListener('submit', function (event) {
        if (uploading) {
            event.preventDefault();
            imageStatus.textContent = 'Please wait for the images to finish uploading.';
            return;
        }
        editor.save();
    });
});
</script>
@endsection
