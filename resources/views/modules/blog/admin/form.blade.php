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
                        <div class="mb-2"><label class="btn btn-outline-primary mb-0" for="blog-inline-images">Add images to article</label><input id="blog-inline-images" type="file" accept="image/jpeg,image/png,image/webp,image/gif" multiple hidden><span id="blog-image-status" class="ml-2" role="status" aria-live="polite"></span><small class="text-muted d-block mt-1">Place the cursor where the image belongs, then choose images. You can also drag or paste images into the editor. JPG, PNG, WebP or GIF, up to 10 MB each.</small></div>
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
                    @php($featuredImageUrl = $post ? (\App\Modules\Blog\BlogMedia::url($post->featured_image) ?: (filter_var($post->featured_image, FILTER_VALIDATE_URL) && str_starts_with($post->featured_image, 'https://') ? $post->featured_image : null)) : null)
                    <div class="form-group">
                        <label for="blog-featured-image">Featured image</label>
                        <label class="blog-featured-dropzone" for="blog-featured-image" data-featured-dropzone tabindex="0" role="button" aria-describedby="blog-featured-help blog-featured-status">
                            <img class="blog-featured-dropzone__preview" data-featured-preview src="{{ $featuredImageUrl ?: '' }}" alt="Featured image preview" @if(!$featuredImageUrl) hidden @endif>
                            <span class="blog-featured-dropzone__prompt" data-featured-prompt>{{ $featuredImageUrl ? 'Drop a new image here or click to replace it' : 'Drop an image here or click to choose one' }}</span>
                        </label>
                        <input id="blog-featured-image" class="blog-featured-dropzone__input" type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small id="blog-featured-help" class="text-muted d-block">JPG, PNG, WebP or GIF, up to 5 MB. Saving the post replaces the featured image.</small>
                        <small id="blog-featured-status" class="d-block" role="status" aria-live="polite"></small>
                        @error('featured_image')<small class="text-danger d-block">{{ $message }}</small>@enderror
                    </div>
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
<style>
.blog-featured-dropzone { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; min-height:170px; padding:16px; border:2px dashed #a9b9d0; border-radius:12px; background:#f7f9fc; cursor:pointer; text-align:center; transition:border-color .2s, background-color .2s; }
.blog-featured-dropzone:hover, .blog-featured-dropzone:focus, .blog-featured-dropzone.is-dragging { border-color:#3166a7; background:#eef5fc; outline:none; }
.blog-featured-dropzone__preview { display:block; max-width:100%; max-height:240px; border-radius:8px; object-fit:contain; }
.blog-featured-dropzone__preview[hidden] { display:none; }
.blog-featured-dropzone__prompt { color:#31527c; font-weight:600; }
.blog-featured-dropzone__input { position:absolute; width:1px; height:1px; opacity:0; overflow:hidden; }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor@2.47.12/dist/css/suneditor.min.css">
<script src="https://cdn.jsdelivr.net/npm/suneditor@2.47.12/dist/suneditor.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var dropzone = document.querySelector('[data-featured-dropzone]');
    var featuredInput = document.querySelector('#blog-featured-image');
    var featuredPreview = document.querySelector('[data-featured-preview]');
    var featuredPrompt = document.querySelector('[data-featured-prompt]');
    var featuredStatus = document.querySelector('#blog-featured-status');
    var savedImageUrl = featuredPreview.getAttribute('src');
    var previewUrl = null;

    function previewFeaturedImage(file) {
        if (!file) return;
        if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            featuredInput.value = '';
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = null;
            featuredPreview.src = savedImageUrl || '';
            featuredPreview.hidden = !savedImageUrl;
            featuredPrompt.textContent = savedImageUrl ? 'Drop a new image here or click to replace it' : 'Drop an image here or click to choose one';
            featuredStatus.textContent = 'Choose a JPG, PNG, WebP or GIF image under 5 MB.';
            featuredStatus.classList.add('text-danger');
            return;
        }
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = URL.createObjectURL(file);
        featuredPreview.src = previewUrl;
        featuredPreview.hidden = false;
        featuredPrompt.textContent = 'Drop another image here or click to change it';
        featuredStatus.classList.remove('text-danger');
        featuredStatus.textContent = file.name + ' selected. Save the post to upload it.';
    }

    featuredInput.addEventListener('change', function () {
        previewFeaturedImage(featuredInput.files[0]);
    });
    dropzone.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            featuredInput.click();
        }
    });
    ['dragenter', 'dragover'].forEach(function (type) {
        dropzone.addEventListener(type, function (event) {
            event.preventDefault();
            dropzone.classList.add('is-dragging');
        });
    });
    ['dragleave', 'drop'].forEach(function (type) {
        dropzone.addEventListener(type, function (event) {
            event.preventDefault();
            dropzone.classList.remove('is-dragging');
        });
    });
    dropzone.addEventListener('drop', function (event) {
        var file = event.dataTransfer.files[0];
        if (!file) return;
        if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            featuredStatus.textContent = 'Choose a JPG, PNG, WebP or GIF image under 5 MB.';
            featuredStatus.classList.add('text-danger');
            return;
        }
        var transfer = new DataTransfer();
        transfer.items.add(file);
        featuredInput.files = transfer.files;
        previewFeaturedImage(file);
    });

    var textarea = document.querySelector('#blog-body');
    if (!textarea) return;

    var form = document.querySelector('#blog-post-form');
    var imageInput = document.querySelector('#blog-inline-images');
    var imageStatus = document.querySelector('#blog-image-status');
    if (!window.SUNEDITOR) {
        imageStatus.textContent = 'The rich text editor could not load. Check your connection and reload the page.';
        imageStatus.classList.add('text-danger');
        imageInput.disabled = true;
        return;
    }
    var csrf = form.querySelector('input[name="_token"]').value;
    var uploadUrl = @json(route($prefix.'.blog.media.upload'));
    var editor = SUNEDITOR.create(textarea, {
        height: '520px',
        minHeight: '360px',
        resizingBar: true,
        imageResizing: true,
        imageFileInput: true,
        imageMultipleFile: true,
        imageUrlInput: false,
        imageAccept: '.jpg,.jpeg,.png,.webp,.gif',
        imageUploadSizeLimit: 10 * 1024 * 1024,
        mediaAutoSelect: true,
        imageUploadUrl: uploadUrl,
        imageUploadHeader: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        buttonList: [
            ['undo', 'redo', 'formatBlock', 'blockquote', 'fontSize', 'bold', 'underline', 'italic', 'strike', 'removeFormat'],
            ['fontColor', 'hiliteColor', 'align', 'outdent', 'indent', 'list', 'horizontalRule', 'lineHeight'],
            ['table', 'link', 'image', 'video', 'fullScreen', 'showBlocks', 'codeView', 'preview']
        ],
        placeholder: 'Start writing your story…',
        attributesWhitelist: { all: 'style|src|alt|width|height|class|target|rel' }
    });

    var uploading = false;
    editor.onImageUploadBefore = function () {
        uploading = true;
        imageStatus.classList.remove('text-danger');
        imageStatus.textContent = 'Uploading images…';
        return true;
    };
    editor.onImageUpload = function (element, index, state, info, remainingFilesCount) {
        if (state !== 'create' && state !== 'update') return;
        if (remainingFilesCount === 0) {
            uploading = false;
            imageStatus.classList.remove('text-danger');
            imageStatus.textContent = 'Image uploaded and added to the article.';
            editor.save();
        }
    };
    editor.onImageUploadError = function (message) {
        uploading = false;
        imageStatus.textContent = message || 'The image could not be uploaded.';
        imageStatus.classList.add('text-danger');
        return false;
    };

    imageInput.addEventListener('change', function () {
        if (!imageInput.files.length || uploading) return;
        var invalid = Array.from(imageInput.files).some(function (file) {
            return !['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type) || file.size > 10 * 1024 * 1024;
        });
        if (invalid) {
            imageStatus.classList.add('text-danger');
            imageStatus.textContent = 'Choose JPG, PNG, WebP or GIF images under 10 MB each.';
            imageInput.value = '';
            return;
        }
        editor.insertImage(imageInput.files);
        imageInput.value = '';
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
