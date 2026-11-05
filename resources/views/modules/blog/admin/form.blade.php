@extends(auth('stakeholder')->check() ? 'layouts.stakeholderdashboard' : 'layouts.dashboard')
@php($post = $post ?? null)
@php($prefix = $isStakeholder ? 'stakeholders' : 'admin')
@php($postRoute = $prefix.(!empty($eventsWorkspace) ? '.events.posts' : '.blog.posts'))
@section('title', !empty($eventsWorkspace) ? ($post ? 'Edit Event' : 'Create Event') : ($post ? 'Edit Post' : 'Create Post'))
@section('active')<li class="breadcrumb-item">{{ !empty($eventsWorkspace) ? ($post ? 'Edit Event' : 'Create Event') : ($post ? 'Edit Post' : 'Create Post') }}</li>@endsection
@section('content')
<div class="content-body blog-composer">
    <header class="blog-composer__hero">
        <div class="blog-composer__hero-copy">
            <span class="blog-composer__eyebrow"><span></span> {{ !empty($eventsWorkspace) ? 'GSF Programs & Events' : 'GSF News · Editorial workspace' }}</span>
            <h1>{{ !empty($eventsWorkspace) ? ($post ? 'Edit your event' : 'Create an event') : ($post ? 'Edit your story' : 'Create a story') }}</h1>
            <p>{{ !empty($eventsWorkspace) ? 'Add event details, speakers and an image for your campus or ministry.' : ($post ? 'Refine your article, update its media and manage how it appears across GSF News.' : 'Bring your story to life, then choose when and where it appears on GSF News.') }}</p>
        </div>
        <div class="blog-composer__hero-actions">
            <a href="{{ route($postRoute.'.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> {{ !empty($eventsWorkspace) ? 'All events' : 'All posts' }}</a>
            @if($post)<a href="{{ route($postRoute.'.preview', $post) }}" target="_blank" rel="noopener"><i class="fa fa-external-link" aria-hidden="true"></i> View saved post</a>@endif
        </div>
    </header>
    @include('includes.alerts')

    <form id="blog-post-form" class="blog-composer__form" method="POST" enctype="multipart/form-data" action="{{ $post ? route($postRoute.'.update', $post) : route($postRoute.'.store') }}">
        @csrf @if($post) @method('PUT') @endif
        <div class="blog-composer__grid">
            <div class="blog-composer__main">
                <section class="blog-composer__panel" aria-labelledby="blog-story-heading">
                    <div class="blog-composer__panel-head"><span class="blog-composer__step">01</span><div><span class="blog-composer__section-label">The essentials</span><h2 id="blog-story-heading">{{ !empty($eventsWorkspace) ? 'Event details' : 'Story details' }}</h2></div></div>
                    <div class="blog-composer__panel-body">
                        <div class="form-group"><label for="blog-title">Headline <span class="blog-composer__required">Required</span></label><input id="blog-title" class="form-control blog-composer__title-input" name="title" required placeholder="Give your story a clear, memorable headline" value="{{ old('title', $post?->title) }}"></div>
                        <div class="form-group"><label for="blog-slug">{{ !empty($eventsWorkspace) ? 'Event URL' : 'Story URL' }}</label><div class="blog-composer__slug-field"><span>{{ !empty($eventsWorkspace) ? '/programs-events/' : '/news/' }}</span><input id="blog-slug" class="form-control" name="slug" placeholder="Created from headline if left blank" value="{{ old('slug', $post?->slug) }}"></div><small class="blog-composer__help">Use a short, readable URL. Leave blank to generate it automatically.</small></div>
                        <div class="form-group mb-0"><label for="blog-excerpt">Summary</label><textarea id="blog-excerpt" class="form-control" name="excerpt" rows="3" placeholder="A short introduction for news cards and social sharing">{{ old('excerpt', $post?->excerpt) }}</textarea><small class="blog-composer__help">If left empty, a summary is taken from the beginning of the story.</small></div>
                    </div>
                </section>

                <section class="blog-composer__panel blog-composer__writing-panel" aria-labelledby="blog-content-heading">
                    <div class="blog-composer__panel-head"><span class="blog-composer__step">02</span><div><span class="blog-composer__section-label">Write &amp; enrich</span><h2 id="blog-content-heading">{{ !empty($eventsWorkspace) ? 'Event description' : 'Your article' }}</h2></div></div>
                    <div class="blog-composer__panel-body">
                        <div class="blog-composer__media-tools">
                            <div class="blog-composer__media-actions">
                                <label class="blog-composer__tool" for="blog-inline-images"><i class="fa fa-picture-o" aria-hidden="true"></i> Add images</label><input id="blog-inline-images" type="file" accept="image/jpeg,image/png,image/webp,image/gif" multiple hidden>
                                <label class="blog-composer__tool" for="blog-inline-audio"><i class="fa fa-music" aria-hidden="true"></i> Add MP3 audio</label><input id="blog-inline-audio" type="file" accept="audio/mpeg,.mp3" hidden>
                            </div>
                            <small>Place the cursor in the article before adding media. Images up to 10 MB; MP3 audio up to 200 MB.</small>
                            <span id="blog-image-status" role="status" aria-live="polite"></span><span id="blog-audio-status" role="status" aria-live="polite"></span>
                        </div>
                        <label class="sr-only" for="blog-body">{{ !empty($eventsWorkspace) ? 'Event description' : 'Article content' }}</label>
                        <textarea class="form-control" id="blog-body" name="body" rows="16" required>{{ old('body', str_replace('//storage/', '/storage/', $post?->body ?? '')) }}</textarea>
                        <div class="blog-composer__editor-footer"><span><i class="fa fa-lightbulb-o" aria-hidden="true"></i> Use headings, quotes and images to make longer stories easier to read.</span><span data-blog-word-count>0 words</span></div>
                    </div>
                </section>

                @if(empty($eventsWorkspace))<section class="blog-composer__panel" aria-labelledby="blog-video-heading">
                    <div class="blog-composer__panel-head"><span class="blog-composer__step">03</span><div><span class="blog-composer__section-label">Optional media</span><h2 id="blog-video-heading">Video</h2></div></div>
                    <div class="blog-composer__panel-body"><div class="form-group mb-0"><label for="blog-video-url">Video URL</label><input id="blog-video-url" class="form-control" type="url" name="video_url" placeholder="https://…" value="{{ old('video_url', $post?->video_url) }}"><small class="blog-composer__help">Add a link when publishing a video post.</small></div></div>
                </section>@endif
            </div>

            <aside class="blog-composer__sidebar" aria-label="Post settings">
                <section class="blog-composer__panel" aria-labelledby="blog-publish-heading">
                    <div class="blog-composer__panel-head blog-composer__panel-head--compact"><span class="blog-composer__side-icon"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></span><div><span class="blog-composer__section-label">Visibility</span><h2 id="blog-publish-heading">Publishing</h2></div></div>
                    <div class="blog-composer__panel-body">
                        @if(!empty($eventsWorkspace))<input type="hidden" id="blog-type" name="type" value="event">@else<div class="form-group"><label for="blog-type">Post type</label><select id="blog-type" class="form-control" name="type"><option value="article" @selected(old('type', $post?->type ?: 'article') === 'article')>Article</option><option value="video" @selected(old('type', $post?->type) === 'video')>Video</option></select></div>@endif
                        <div id="blog-event-fields" hidden>
                            <div class="form-group"><label for="blog-event-starts-at">Event date and time</label><input id="blog-event-starts-at" class="form-control" type="datetime-local" name="event_starts_at" value="{{ old('event_starts_at', $post?->event_starts_at?->format('Y-m-d\\TH:i')) }}">@error('event_starts_at')<small class="text-danger d-block">{{ $message }}</small>@enderror</div>
                            <div class="form-group"><label for="blog-event-venue">Venue</label><input id="blog-event-venue" class="form-control" name="event_venue" maxlength="255" value="{{ old('event_venue', $post?->event_venue ?: ($isCampusStakeholder && !empty($eventsWorkspace) ? $userCampusAddress : null)) }}" placeholder="Where will this event take place?">@error('event_venue')<small class="text-danger d-block">{{ $message }}</small>@enderror</div>
                            <div class="form-group mb-0"><label>Host and Guest Minister</label><small class="blog-composer__help d-block mb-2">Add the host and any guest ministers for this event.</small>
                                <div id="blog-event-speakers" class="blog-event-speakers">
                                    @foreach(old('event_speakers', $post?->event_speakers ?? []) as $speakerIndex => $speaker)
                                        <div class="blog-event-speaker" data-event-speaker>
                                            <select class="form-control" data-speaker-field="kind" name="event_speakers[{{ $speakerIndex }}][kind]" aria-label="Person type"><option value="speaker" @selected(($speaker['kind'] ?? '') === 'speaker')>Host</option><option value="guest_minister" @selected(($speaker['kind'] ?? '') === 'guest_minister')>Guest Minister</option></select>
                                            <input class="form-control" data-speaker-field="name" name="event_speakers[{{ $speakerIndex }}][name]" value="{{ $speaker['name'] ?? '' }}" maxlength="255" placeholder="Full name" aria-label="Full name">
                                            <input class="form-control" data-speaker-field="ministry" name="event_speakers[{{ $speakerIndex }}][ministry]" value="{{ $speaker['ministry'] ?? '' }}" maxlength="255" placeholder="Ministry or title (optional)" aria-label="Ministry or title">
                                            <input type="hidden" data-speaker-field="existing_image" name="event_speakers[{{ $speakerIndex }}][existing_image]" value="{{ $speaker['image'] ?? '' }}">
                                            @if($speakerPhoto = \App\Modules\Blog\BlogMedia::url($speaker['image'] ?? null))<img class="blog-event-speaker__photo" src="{{ $speakerPhoto }}" alt="Photo of {{ $speaker['name'] ?? 'speaker' }}">@endif
                                            <label class="blog-event-speaker__picture">Picture <span>(optional)</span><input type="file" data-speaker-field="image" name="event_speakers[{{ $speakerIndex }}][image]" accept="image/jpeg,image/png,image/webp,image/gif"></label>
                                            <button type="button" class="blog-event-speaker__remove" data-remove-speaker aria-label="Remove speaker"><i class="fa fa-times" aria-hidden="true"></i></button>
                                        </div>
                                        @error('event_speakers.'.$speakerIndex.'.name')<small class="text-danger d-block">{{ $message }}</small>@enderror
                                        @error('event_speakers.'.$speakerIndex.'.image')<small class="text-danger d-block">{{ $message }}</small>@enderror
                                    @endforeach
                                </div>
                                <button type="button" class="blog-event-speakers__add" id="blog-add-speaker"><i class="fa fa-plus" aria-hidden="true"></i> Add host or guest minister</button>
                                @error('event_speakers')<small class="text-danger d-block">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <template id="blog-event-speaker-template"><div class="blog-event-speaker" data-event-speaker><select class="form-control" data-speaker-field="kind" aria-label="Person type"><option value="speaker">Host</option><option value="guest_minister">Guest Minister</option></select><input class="form-control" data-speaker-field="name" maxlength="255" placeholder="Full name" aria-label="Full name"><input class="form-control" data-speaker-field="ministry" maxlength="255" placeholder="Ministry or title (optional)" aria-label="Ministry or title"><input type="hidden" data-speaker-field="existing_image" value=""><label class="blog-event-speaker__picture">Picture <span>(optional)</span><input type="file" data-speaker-field="image" accept="image/jpeg,image/png,image/webp,image/gif"></label><button type="button" class="blog-event-speaker__remove" data-remove-speaker aria-label="Remove host or guest minister"><i class="fa fa-times" aria-hidden="true"></i></button></div></template>
                        @unless($isStakeholder)<div class="form-group"><label for="blog-status">Status</label><select id="blog-status" class="form-control" name="status">@foreach(['draft','review','scheduled','published','archived'] as $status)<option value="{{ $status }}" @selected(old('status', $post?->status ?: 'draft') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>@endunless
                        <div class="form-group"><label for="blog-published-at">{{ !empty($eventsWorkspace) ? 'Program Date' : 'Publication date' }}</label><input id="blog-published-at" class="form-control" type="datetime-local" name="published_at" value="{{ old('published_at', $post?->published_at?->format('Y-m-d\\TH:i')) }}"><small class="blog-composer__help">{{ !empty($eventsWorkspace) ? 'Choose when this program listing should go live. A future date schedules it.' : 'Choose a future date to schedule a post.' }}</small></div>
                        @if($canFeaturePosts)<label class="blog-composer__switch"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $post?->is_featured ?? false))><span><strong>{{ !empty($eventsWorkspace) ? 'Feature this event' : 'Feature on GSF News' }}</strong><small>{{ !empty($eventsWorkspace) ? 'Highlight this program in the public events list.' : 'Show in the lead story area.' }}</small></span></label>@endif
                        @if(!$isCampusStakeholder || $campusPostsCanAllowComments)<label class="blog-composer__switch"><input type="checkbox" name="allow_comments" value="1" @checked(old('allow_comments', $post?->allow_comments ?? true))><span><strong>Allow comments</strong><small>Let readers respond to this story.</small></span></label>@endif
                        @if($isCampusStakeholder && $campusFacebookPublishingEnabled)
                            <label class="blog-composer__switch"><input type="checkbox" name="publish_on_facebook" value="1" @checked(old('publish_on_facebook', $post?->facebook_publish_requested ?? false))><span><strong>Publish on GSF National Facebook</strong><small>{{ $campusFacebookReviewRequired ? 'This request will go to Facebook review, separately from website review.' : 'This will publish to Facebook when you save.' }}</small></span></label>
                        @endif
                        @if($post && $post->facebook_publish_requested)
                            <div class="alert {{ $post->facebook_status === 'failed' ? 'alert-danger' : ($post->facebook_status === 'published' ? 'alert-success' : 'alert-info') }} mt-1 mb-0" role="status">
                                <strong>Facebook:</strong> {{ str($post->facebook_status)->replace('_', ' ')->title() }}
                                @if($post->facebook_error)<br><small>{{ $post->facebook_error }}</small>@endif
                            </div>
                        @endif
                    </div>
                </section>

                @php($featuredImageUrl = $post ? (\App\Modules\Blog\BlogMedia::url($post->featured_image) ?: (filter_var($post->featured_image, FILTER_VALIDATE_URL) && str_starts_with($post->featured_image, 'https://') ? $post->featured_image : null)) : null)
                <section class="blog-composer__panel" aria-labelledby="blog-featured-heading">
                    <div class="blog-composer__panel-head blog-composer__panel-head--compact"><span class="blog-composer__side-icon"><i class="fa fa-image" aria-hidden="true"></i></span><div><span class="blog-composer__section-label">Visual identity</span><h2 id="blog-featured-heading">{{ !empty($eventsWorkspace) ? 'Program flyer' : 'Featured image' }}</h2></div></div>
                    <div class="blog-composer__panel-body">
                        <label class="blog-featured-dropzone" for="blog-featured-image" data-featured-dropzone data-upload-name="{{ !empty($eventsWorkspace) ? 'program flyer' : 'image' }}" tabindex="0" role="button" aria-describedby="blog-featured-help blog-featured-status">
                            <img class="blog-featured-dropzone__preview" data-featured-preview src="{{ $featuredImageUrl ?: '' }}" alt="{{ !empty($eventsWorkspace) ? 'Program flyer preview' : 'Featured image preview' }}" @if(!$featuredImageUrl) hidden @endif>
                            <span class="blog-featured-dropzone__icon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                            <span class="blog-featured-dropzone__prompt" data-featured-prompt>{{ $featuredImageUrl ? 'Drop a new '.(!empty($eventsWorkspace) ? 'program flyer' : 'image').' here or click to replace it' : (!empty($eventsWorkspace) ? 'Drop your program flyer here or click to choose one' : 'Drop an image here or click to choose one') }}</span>
                        </label>
                        <input id="blog-featured-image" class="blog-featured-dropzone__input" type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small id="blog-featured-help" class="blog-composer__help">JPG, PNG, WebP or GIF · up to 5 MB. {{ !empty($eventsWorkspace) ? 'Upload the flyer for this program.' : 'A wide image works best.' }}</small>
                        <small id="blog-featured-status" class="d-block" role="status" aria-live="polite"></small>
                        @error('featured_image')<small class="text-danger d-block">{{ $message }}</small>@enderror
                    </div>
                </section>

                @if(empty($eventsWorkspace))<section class="blog-composer__panel" aria-labelledby="blog-organize-heading">
                    <div class="blog-composer__panel-head blog-composer__panel-head--compact"><span class="blog-composer__side-icon"><i class="fa fa-tags" aria-hidden="true"></i></span><div><span class="blog-composer__section-label">Discovery</span><h2 id="blog-organize-heading">Organize</h2></div></div>
                    <div class="blog-composer__panel-body">
                        @php($selectedCategoryIds = collect(old('category_ids', $post?->categories?->pluck('id')->all() ?? []))->map('strval')->all())
                        <fieldset class="blog-composer__fieldset" data-choice-group>
                            <legend>Categories</legend>
                            @if($categories->isNotEmpty())<label class="blog-composer__choice-search"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search categories</span><input type="search" placeholder="Search categories…" autocomplete="off" data-choice-search></label>@endif
                            <div class="blog-composer__choices" data-choice-list>@forelse($categories as $category)<label><input type="checkbox" name="category_ids[]" value="{{ $category->id }}" @checked(in_array((string) $category->id, $selectedCategoryIds, true))><span>{{ $category->name }}</span></label>@empty<span class="blog-composer__help">No categories available.</span>@endforelse</div>
                            @if($categories->isNotEmpty())<small class="blog-composer__choice-summary" data-choice-summary aria-live="polite"></small><small class="blog-composer__choice-empty" data-choice-empty hidden>No categories match your search.</small>@endif
                        </fieldset>
                        @php($selectedTagIds = collect(old('tag_ids', $post?->tags?->pluck('id')->all() ?? []))->map('strval')->all())
                        <fieldset class="blog-composer__fieldset" data-choice-group>
                            <legend>Tags</legend>
                            @if($tags->isNotEmpty())<label class="blog-composer__choice-search"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search tags</span><input type="search" placeholder="Search tags…" autocomplete="off" data-choice-search></label>@endif
                            <div class="blog-composer__choices" data-choice-list>@forelse($tags as $tag)<label><input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" @checked(in_array((string) $tag->id, $selectedTagIds, true))><span>{{ $tag->name }}</span></label>@empty<span class="blog-composer__help">No tags available.</span>@endforelse</div>
                            @if($tags->isNotEmpty())<small class="blog-composer__choice-summary" data-choice-summary aria-live="polite"></small><small class="blog-composer__choice-empty" data-choice-empty hidden>No tags match your search.</small>@endif
                        </fieldset>
                    </div>
                </section>@endif

                @if($post && !empty($canChangeAuthor))
                @php($currentAuthorKey = $post->author_type && $post->author_id ? $post->author_type.':'.$post->author_id : '')
                <section class="blog-composer__panel" aria-labelledby="blog-author-heading"><div class="blog-composer__panel-head blog-composer__panel-head--compact"><span class="blog-composer__side-icon"><i class="fa fa-user-o" aria-hidden="true"></i></span><div><span class="blog-composer__section-label">Attribution</span><h2 id="blog-author-heading">Author</h2></div></div><div class="blog-composer__panel-body"><label for="blog-post-author">Post author</label><select class="form-control" id="blog-post-author" name="author_key"><option value="">Keep current author</option>@foreach($authors as $author)<option value="{{ $author['key'] }}" @selected(old('author_key', $currentAuthorKey) === $author['key'])>{{ $author['name'] }}{{ !empty($author['email']) ? ' · '.$author['email'] : '' }}</option>@endforeach</select><small class="blog-composer__help">Choose an account with permission to create blog posts.</small>@error('author_key')<small class="text-danger d-block">{{ $message }}</small>@enderror</div></section>
                @endif

                <section class="blog-composer__panel" aria-labelledby="blog-seo-heading"><div class="blog-composer__panel-head blog-composer__panel-head--compact"><span class="blog-composer__side-icon"><i class="fa fa-search" aria-hidden="true"></i></span><div><span class="blog-composer__section-label">Search appearance</span><h2 id="blog-seo-heading">SEO settings</h2></div></div><div class="blog-composer__panel-body"><div class="form-group"><label for="blog-seo-title">SEO title</label><input id="blog-seo-title" class="form-control" name="seo_title" placeholder="Optional search result title" value="{{ old('seo_title', $post?->seo_title) }}"></div><div class="form-group"><label for="blog-seo-description">SEO description</label><textarea id="blog-seo-description" class="form-control" name="seo_description" rows="3" placeholder="Optional search result summary">{{ old('seo_description', $post?->seo_description) }}</textarea></div><div class="form-group mb-0"><label for="blog-canonical-url">Canonical URL</label><input id="blog-canonical-url" class="form-control" type="url" name="canonical_url" placeholder="https://…" value="{{ old('canonical_url', $post?->canonical_url) }}"></div></div></section>
            </aside>
        </div>
        <div class="blog-composer__savebar"><span><i class="fa fa-info-circle" aria-hidden="true"></i> @if($isCampusStakeholder){{ $campusPostsRequireReview ? 'Your campus '.(!empty($eventsWorkspace) ? 'event' : 'story').' will be sent for review.' : 'Your campus '.(!empty($eventsWorkspace) ? 'event' : 'story').' will publish when saved.' }}@elseif($isStakeholder)Your {{ !empty($eventsWorkspace) ? 'event' : 'story' }} will be submitted for review.@else Choose the status above, then save your changes.@endif</span><div><a href="{{ route($postRoute.'.index') }}" class="blog-composer__cancel">Back to {{ !empty($eventsWorkspace) ? 'events' : 'posts' }}</a><button class="blog-composer__save" type="submit" name="submit_review" value="1"><i class="fa fa-check" aria-hidden="true"></i> @if($isCampusStakeholder){{ $campusPostsRequireReview ? 'Save and submit for review' : ($post ? 'Save and publish' : 'Publish '.(!empty($eventsWorkspace) ? 'event' : 'post')) }}@else{{ $isStakeholder ? 'Save and submit for review' : ($post ? 'Save changes' : 'Save '.(!empty($eventsWorkspace) ? 'event' : 'post')) }}@endif</button></div></div>
    </form>
</div>
<style>
.blog-composer { max-width:1560px; margin:0 auto; padding-bottom:32px; color:#1d3151; }
.blog-composer__hero { position:relative; display:flex; align-items:center; justify-content:space-between; gap:24px; overflow:hidden; min-height:178px; margin-bottom:24px; padding:32px 38px; border-radius:20px; background:linear-gradient(115deg,#102646,#1e4d7a 70%,#2f7491); box-shadow:0 16px 34px rgba(18,48,84,.13); }
.blog-composer__hero::after { position:absolute; right:-64px; bottom:-210px; width:430px; height:430px; border:1px solid rgba(255,255,255,.16); border-radius:50%; box-shadow:0 0 0 55px rgba(255,255,255,.035),0 0 0 110px rgba(255,255,255,.025); content:""; pointer-events:none; }
.blog-composer__hero-copy,.blog-composer__hero-actions { position:relative; z-index:1; }
.blog-composer__eyebrow { display:inline-flex; align-items:center; gap:8px; color:#cde5fa; font-size:10px; font-weight:800; letter-spacing:.16em; text-transform:uppercase; }
.blog-composer__eyebrow span { width:7px; height:7px; border-radius:50%; background:#e7c278; box-shadow:0 0 0 4px rgba(231,194,120,.17); }
.blog-composer__hero h1 { margin:11px 0 6px; color:#fff; font-size:clamp(27px,3vw,37px); font-weight:800; letter-spacing:-.035em; line-height:1.1; }
.blog-composer__hero p { max-width:590px; margin:0; color:#d1e0f0; font-size:13px; line-height:1.6; }
.blog-composer__hero-actions { display:flex; flex-wrap:wrap; gap:9px; justify-content:flex-end; }
.blog-composer__hero-actions a { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:42px; padding:9px 14px; border:1px solid rgba(255,255,255,.34); border-radius:9px; color:#fff; background:rgba(255,255,255,.1); font-size:12px; font-weight:750; text-decoration:none; white-space:nowrap; }
.blog-composer__hero-actions a:hover { color:#fff; background:rgba(255,255,255,.2); }
.blog-composer__form { padding-bottom:8px; }
.blog-composer__grid { display:grid; grid-template-columns:minmax(0,1.8fr) minmax(300px,1fr); align-items:start; gap:22px; }
.blog-composer__main,.blog-composer__sidebar { display:grid; min-width:0; gap:20px; }
.blog-composer__panel { min-width:0; overflow:hidden; border:1px solid #dfe7f1; border-radius:17px; background:#fff; box-shadow:0 7px 24px rgba(25,49,83,.045); }
.blog-composer__panel-head { display:flex; align-items:center; gap:14px; min-height:90px; padding:20px 25px; border-bottom:1px solid #e9eef4; background:linear-gradient(105deg,#fff,#f9fbfe); }
.blog-composer__panel-head--compact { min-height:76px; padding:16px 20px; }
.blog-composer__step,.blog-composer__side-icon { display:grid; flex:0 0 42px; width:42px; height:42px; place-items:center; border:1px solid #d8e5f5; border-radius:12px; color:#245485; background:#ebf3fc; font-size:14px; font-weight:850; }
.blog-composer__side-icon { flex-basis:38px; width:38px; height:38px; font-size:16px; }
.blog-composer__section-label { display:block; margin-bottom:3px; color:#8796a9; font-size:10px; font-weight:850; letter-spacing:.12em; text-transform:uppercase; }
.blog-composer__panel h2 { margin:0; color:#1d3151; font-size:19px; font-weight:800; line-height:1.2; }
.blog-composer__panel-head--compact h2 { font-size:17px; }
.blog-composer__panel-body { padding:23px 25px 25px; }
.blog-composer__sidebar .blog-composer__panel-body { padding:20px; }
.blog-composer .form-group { margin-bottom:20px; }
.blog-composer .form-group:last-child { margin-bottom:0; }
.blog-composer .form-group > label,.blog-composer__panel-body > label:not(.blog-featured-dropzone):not(.blog-composer__switch) { display:block; margin-bottom:8px; color:#344762; font-size:12px; font-weight:800; }
.blog-composer__required { margin-left:6px; color:#b27327; font-size:10px; font-weight:700; }
.blog-composer .form-control { width:100%; min-width:0; min-height:44px; border:1px solid #d6e0ed; border-radius:9px; color:#243a59; background:#fff; font-size:13px; box-shadow:none; }
.blog-composer .form-control:focus { border-color:#3e83b2; box-shadow:0 0 0 3px rgba(62,131,178,.13); }
.blog-composer textarea.form-control { height:auto; padding:12px 14px; line-height:1.55; }
.blog-composer .form-control::placeholder { color:#a0acbc; opacity:1; }
.blog-composer__title-input { height:55px; padding:12px 15px; font-size:17px!important; font-weight:700; }
.blog-composer__help { display:block; margin-top:7px; color:#8492a5; font-size:11px; line-height:1.5; }
.blog-composer__slug-field { display:flex; align-items:center; min-width:0; overflow:hidden; border:1px solid #d6e0ed; border-radius:9px; }
.blog-composer__slug-field:focus-within { border-color:#3e83b2; box-shadow:0 0 0 3px rgba(62,131,178,.13); }
.blog-composer__slug-field span { flex:0 0 auto; padding:0 12px; color:#5d7089; background:#f3f7fb; font-size:12px; font-weight:750; line-height:44px; }
.blog-composer__slug-field .form-control { min-height:42px; border:0; border-radius:0; box-shadow:none; }
.blog-composer__media-tools { margin-bottom:17px; padding:13px 15px; border:1px solid #e2eaf4; border-radius:11px; background:#f7faff; }
.blog-composer__media-actions { display:flex; flex-wrap:wrap; gap:9px; }
.blog-composer__tool { display:inline-flex; align-items:center; gap:8px; min-height:36px; margin:0; padding:7px 11px; border:1px solid #c9dcf0; border-radius:8px; color:#245687; background:#fff; font-size:11px; font-weight:800; cursor:pointer; }
.blog-composer__tool:hover { border-color:#82add3; color:#194674; background:#eff6fd; }
.blog-composer__media-tools small { display:block; margin-top:10px; color:#788ba2; font-size:11px; line-height:1.5; }
#blog-image-status:not(:empty),#blog-audio-status:not(:empty) { display:block; margin-top:7px; color:#247159; font-size:11px; font-weight:700; }
#blog-image-status.text-danger,#blog-audio-status.text-danger { color:#b44242!important; }
.blog-composer .sun-editor { overflow:hidden; border:1px solid #d6e0ed; border-radius:10px; box-shadow:none; }
.blog-composer .sun-editor .se-toolbar { background:#fbfcfe; }
.blog-composer .sun-editor .sun-editor-editable { padding:22px; color:#283b55; font-size:15px; line-height:1.75; }
.blog-composer__editor-footer { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-top:12px; color:#8290a2; font-size:11px; }
.blog-composer__editor-footer [data-blog-word-count] { flex:0 0 auto; color:#4c6885; font-weight:800; }
.blog-composer__switch { display:flex; align-items:flex-start; gap:11px; margin:0; padding:13px 0; border-top:1px solid #edf1f6; cursor:pointer; }
.blog-composer__switch input { width:16px; height:16px; margin:2px 0 0; accent-color:#245d91; }
.blog-composer__switch span { display:block; }
.blog-composer__switch strong,.blog-composer__switch small { display:block; }
.blog-composer__switch strong { color:#314660; font-size:12px; }
.blog-composer__switch small { margin-top:3px; color:#8a97a8; font-size:11px; line-height:1.4; }
.blog-featured-dropzone { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; min-height:180px; padding:17px; border:2px dashed #abc5df; border-radius:12px; background:#f6faff; cursor:pointer; text-align:center; transition:border-color .2s,background-color .2s; }
.blog-featured-dropzone:hover,.blog-featured-dropzone:focus,.blog-featured-dropzone.is-dragging { border-color:#3477ac; background:#edf6ff; outline:none; }
.blog-featured-dropzone__icon { display:grid; width:44px; height:44px; place-items:center; border-radius:11px; color:#2c6597; background:#e5f1fb; font-size:20px; }
.blog-featured-dropzone__preview { display:block; max-width:100%; max-height:240px; border-radius:8px; object-fit:contain; }
.blog-featured-dropzone__preview[hidden],.blog-featured-dropzone__preview:not([hidden]) + .blog-featured-dropzone__icon { display:none; }
.blog-featured-dropzone__prompt { max-width:230px; color:#31527c; font-size:12px; font-weight:750; line-height:1.5; }
.blog-featured-dropzone__input { position:absolute; width:1px; height:1px; opacity:0; overflow:hidden; }
.blog-composer__fieldset { min-width:0; margin:0 0 19px; padding:0; border:0; }
.blog-event-speakers { display:grid; gap:9px; margin-bottom:10px; }
.blog-event-speaker { display:grid; grid-template-columns:minmax(0,1fr) 32px; gap:7px; align-items:stretch; padding:10px; border:1px solid #dbe4ee; border-radius:9px; background:#f8fafc; }
.blog-event-speaker .form-control { min-width:0; grid-column:1; }
.blog-event-speaker__picture { grid-column:1; display:grid; gap:5px; color:#536a84; font-size:11px; font-weight:800; }
.blog-event-speaker__picture span { color:#8494a8; font-weight:500; }
.blog-event-speaker__picture input { max-width:100%; font-size:11px; font-weight:500; }
.blog-event-speaker__photo { grid-column:1; width:64px; height:64px; border-radius:9px; object-fit:cover; }
.blog-event-speaker__remove,.blog-event-speakers__add { border:1px solid #d7e2ee; border-radius:8px; background:#fff; color:#275f8a; cursor:pointer; }
.blog-event-speaker__remove { grid-column:2; grid-row:1 / 7; height:100%; }
.blog-event-speakers__add { padding:8px 11px; font-size:11px; font-weight:800; }
.blog-event-speakers__add:hover,.blog-event-speaker__remove:hover { background:#eaf4fc; }
.blog-composer__fieldset:last-child { margin-bottom:0; }
.blog-composer__fieldset legend { width:auto; margin-bottom:9px; color:#344762; font-size:12px; font-weight:800; }
.blog-composer__choice-search { display:flex; align-items:center; gap:9px; width:100%; min-height:38px; margin:0 0 10px; padding:0 11px; border:1px solid #d6e0ed; border-radius:8px; color:#8698ab; background:#fff; }
.blog-composer__choice-search:focus-within { border-color:#3e83b2; box-shadow:0 0 0 3px rgba(62,131,178,.13); }
.blog-composer__choice-search input { width:100%; min-width:0; height:36px; padding:0; border:0; outline:0; color:#243a59; background:transparent; font-size:12px; }
.blog-composer__choice-search input::placeholder { color:#9aabc0; }
.blog-composer__choices { display:flex; flex-wrap:wrap; gap:7px; max-height:195px; overflow:auto; }
.blog-composer__choices label { display:inline-flex; align-items:center; gap:6px; max-width:100%; margin:0; padding:7px 9px; border:1px solid #dbe4ef; border-radius:8px; color:#5a6d84; background:#fbfcfe; font-size:11px; font-weight:700; line-height:1.3; cursor:pointer; }
.blog-composer__choices label[hidden] { display:none; }
.blog-composer__choices label:has(input:checked) { border-color:#85afd0; color:#194d7b; background:#eaf4fc; }
.blog-composer__choices input { flex:0 0 auto; width:14px; height:14px; margin:0; accent-color:#28659a; }
.blog-composer__choice-summary,.blog-composer__choice-empty { display:block; margin-top:8px; color:#8796a9; font-size:10px; line-height:1.4; }
.blog-composer__choice-empty[hidden] { display:none; }
.blog-composer__savebar { position:sticky; z-index:20; bottom:14px; display:flex; align-items:center; justify-content:space-between; gap:16px; margin-top:22px; padding:13px 16px 13px 20px; border:1px solid #d7e3f0; border-radius:13px; background:rgba(255,255,255,.97); box-shadow:0 13px 35px rgba(19,47,80,.14); backdrop-filter:blur(10px); }
.blog-composer__savebar > span { color:#697e97; font-size:11px; font-weight:600; }
.blog-composer__savebar > span i { margin-right:6px; color:#3c7faf; }
.blog-composer__savebar > div { display:flex; align-items:center; gap:9px; }
.blog-composer__cancel,.blog-composer__save { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:42px; padding:9px 15px; border-radius:8px; font-size:12px; font-weight:800; text-decoration:none; white-space:nowrap; cursor:pointer; }
.blog-composer__cancel { border:1px solid #d3e0ed; color:#526a84; background:#fff; }
.blog-composer__cancel:hover { color:#245687; background:#f5f9fd; }
.blog-composer__save { border:1px solid #1c5b91; color:#fff; background:#1c5b91; box-shadow:0 4px 10px rgba(28,91,145,.18); }
.blog-composer__save:hover { color:#fff; background:#164c7b; }
@media(max-width:1050px) { .blog-composer__grid { grid-template-columns:minmax(0,1.35fr) minmax(280px,1fr); gap:15px; }.blog-composer__main,.blog-composer__sidebar { gap:15px; } }
@media(max-width:820px) { .blog-composer__hero { align-items:flex-start; flex-direction:column; padding:28px; }.blog-composer__hero-actions { justify-content:flex-start; }.blog-composer__grid { grid-template-columns:1fr; }.blog-composer__sidebar { grid-template-columns:repeat(2,minmax(0,1fr)); align-items:start; } }
@media(max-width:620px) { .blog-composer__hero { min-height:0; padding:25px 21px; border-radius:14px; }.blog-composer__hero-actions a { min-height:38px; }.blog-composer__sidebar { grid-template-columns:1fr; }.blog-composer__panel-head { min-height:76px; padding:16px 18px; }.blog-composer__panel-body { padding:18px; }.blog-composer__sidebar .blog-composer__panel-body { padding:18px; }.blog-composer__editor-footer { align-items:flex-start; flex-direction:column; gap:5px; }.blog-composer__savebar { bottom:0; flex-direction:column; align-items:stretch; margin:16px -10px 0; padding:11px 14px; border-radius:12px 12px 0 0; }.blog-composer__savebar > div { justify-content:flex-end; }.blog-composer__savebar > span { font-size:10px; } }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor@2.47.12/dist/css/suneditor.min.css">
<script src="https://cdn.jsdelivr.net/npm/suneditor@2.47.12/dist/suneditor.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var postType = document.querySelector('#blog-type');
    var eventFields = document.querySelector('#blog-event-fields');
    var eventStartsAt = document.querySelector('#blog-event-starts-at');
    var eventVenue = document.querySelector('#blog-event-venue');
    var speakerList = document.querySelector('#blog-event-speakers');
    function reindexSpeakers() {
        speakerList.querySelectorAll('[data-event-speaker]').forEach(function (row, index) {
            row.querySelectorAll('[data-speaker-field]').forEach(function (field) {
                field.name = 'event_speakers[' + index + '][' + field.dataset.speakerField + ']';
            });
        });
    }
    reindexSpeakers();
    document.querySelector('#blog-add-speaker').addEventListener('click', function () {
        speakerList.appendChild(document.querySelector('#blog-event-speaker-template').content.cloneNode(true));
        reindexSpeakers();
        speakerList.lastElementChild.querySelector('[data-speaker-field="name"]').focus();
    });
    speakerList.addEventListener('click', function (event) {
        if (!event.target.closest('[data-remove-speaker]')) return;
        event.target.closest('[data-event-speaker]').remove();
        reindexSpeakers();
    });
    function toggleEventFields() {
        var isEvent = postType.value === 'event';
        eventFields.hidden = !isEvent;
        eventStartsAt.required = isEvent;
        eventVenue.required = isEvent;
        speakerList.querySelectorAll('input, select').forEach(function (field) { field.disabled = !isEvent; });
    }
    postType.addEventListener('change', toggleEventFields);
    toggleEventFields();
    document.querySelectorAll('[data-choice-group]').forEach(function (group) {
        var search = group.querySelector('[data-choice-search]');
        if (!search) return;
        var choices = Array.from(group.querySelectorAll('[data-choice-list] label'));
        var summary = group.querySelector('[data-choice-summary]');
        var empty = group.querySelector('[data-choice-empty]');

        function filterChoices() {
            var query = search.value.trim().toLocaleLowerCase();
            var shown = 0;
            var selected = 0;
            choices.forEach(function (choice) {
                var checked = choice.querySelector('input').checked;
                if (checked) selected++;
                choice.hidden = !!query && !checked && !choice.textContent.toLocaleLowerCase().includes(query);
                if (!choice.hidden) shown++;
            });
            summary.textContent = query
                ? shown + ' of ' + choices.length + ' shown · ' + selected + ' selected'
                : choices.length + ' available · ' + selected + ' selected';
            empty.hidden = shown > 0;
        }

        search.addEventListener('input', filterChoices);
        group.addEventListener('change', filterChoices);
        filterChoices();
    });

    var dropzone = document.querySelector('[data-featured-dropzone]');
    var featuredInput = document.querySelector('#blog-featured-image');
    var featuredPreview = document.querySelector('[data-featured-preview]');
    var featuredPrompt = document.querySelector('[data-featured-prompt]');
    var featuredStatus = document.querySelector('#blog-featured-status');
    var savedImageUrl = featuredPreview.getAttribute('src');
    var uploadName = dropzone.dataset.uploadName || 'image';
    var previewUrl = null;

    function previewFeaturedImage(file) {
        if (!file) return;
        if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            featuredInput.value = '';
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = null;
            featuredPreview.src = savedImageUrl || '';
            featuredPreview.hidden = !savedImageUrl;
            featuredPrompt.textContent = savedImageUrl ? 'Drop a new ' + uploadName + ' here or click to replace it' : 'Drop your ' + uploadName + ' here or click to choose one';
            featuredStatus.textContent = 'Choose a JPG, PNG, WebP or GIF image under 5 MB.';
            featuredStatus.classList.add('text-danger');
            return;
        }
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = URL.createObjectURL(file);
        featuredPreview.src = previewUrl;
        featuredPreview.hidden = false;
        featuredPrompt.textContent = 'Drop another ' + uploadName + ' here or click to change it';
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
        attributesWhitelist: { all: 'style|src|alt|width|height|class|target|rel|controls|preload|type' }
    });

    var wordCount = document.querySelector('[data-blog-word-count]');
    function updateWordCount() {
        var words = (editor.getText() || '').trim().match(/\S+/g);
        var count = words ? words.length : 0;
        wordCount.textContent = count.toLocaleString() + (count === 1 ? ' word' : ' words');
    }
    editor.onChange = updateWordCount;
    updateWordCount();

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
        // Keep an independent FileList: clearing the input must not empty files
        // while SunEditor is still uploading them.
        var transfer = new DataTransfer();
        Array.from(imageInput.files).forEach(function (file) {
            transfer.items.add(file);
        });
        editor.insertImage(transfer.files);
        imageInput.value = '';
    });

    var audioInput = document.querySelector('#blog-inline-audio');
    var audioStatus = document.querySelector('#blog-audio-status');
    audioInput.addEventListener('change', function () {
        var file = audioInput.files[0];
        if (!file) return;
        if (file.type !== 'audio/mpeg' && !file.name.toLowerCase().endsWith('.mp3')) {
            audioStatus.textContent = 'Choose an MP3 audio file.';
            audioStatus.classList.add('text-danger');
            audioInput.value = '';
            return;
        }
        if (file.size > 200 * 1024 * 1024) {
            audioStatus.textContent = 'Choose an MP3 file under 200 MB.';
            audioStatus.classList.add('text-danger');
            audioInput.value = '';
            return;
        }
        var data = new FormData();
        data.append('file-0', file);
        uploading = true;
        audioStatus.classList.remove('text-danger');
        audioStatus.textContent = 'Uploading audio…';
        fetch(uploadUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: data })
            .then(function (response) { return response.ok ? response.json() : response.json().then(function (body) { return Promise.reject(body.message || 'The audio could not be uploaded.'); }); })
            .then(function (body) {
                var media = body.result && body.result[0];
                if (!media || media.type !== 'audio' || !media.url) throw new Error('The audio could not be uploaded.');
                var safeUrl = media.url.replace(/&/g, '&amp;').replace(/"/g, '&quot;');
                editor.insertHTML('<p><audio controls preload="metadata" src="' + safeUrl + '"></audio></p>');
                editor.save();
                audioStatus.textContent = 'Audio uploaded and added to the article.';
            })
            .catch(function (error) {
                audioStatus.textContent = error.message || String(error) || 'The audio could not be uploaded.';
                audioStatus.classList.add('text-danger');
            })
            .finally(function () { uploading = false; audioInput.value = ''; });
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
