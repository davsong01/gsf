@extends($isStakeholder ? 'layouts.stakeholderdashboard' : 'layouts.dashboard')
@section('title', !empty($eventsWorkspace) ? 'Programs & Events' : 'Posts')
@section('active')<li class="breadcrumb-item">{{ !empty($eventsWorkspace) ? 'Programs & Events' : 'Posts' }}</li>@endsection
@section('content')
@php($prefix = $isStakeholder ? 'stakeholders' : 'admin')
@php($postRoute = $prefix.(!empty($eventsWorkspace) ? '.events.posts' : '.blog.posts'))
<div class="content-body blog-posts-admin">
    <header class="bpa-hero">
        <div><span class="bpa-kicker">{{ !empty($eventsWorkspace) ? 'GSF Programs & Events' : 'GSF News · Editorial' }}</span><h1>{{ !empty($eventsWorkspace) ? 'Programs & Events' : 'Posts' }}</h1><p>{{ !empty($eventsWorkspace) ? 'Create and manage fellowship gatherings, programs and guest ministers.' : ($isStakeholder ? 'Write and manage your stories for the fellowship.' : 'Manage stories from campuses, zones, fields and the national team.') }}</p></div>
        <div class="bpa-hero-actions">
            <a href="{{ !empty($eventsWorkspace) ? route('events.public.index') : route('blog.index') }}" target="_blank" rel="noopener noreferrer"><i class="fa fa-external-link" aria-hidden="true"></i> {{ !empty($eventsWorkspace) ? 'View events' : 'View news' }}</a>
            @if($canCreate)<a class="bpa-create" href="{{ route($postRoute.'.create') }}"><i class="fa fa-plus" aria-hidden="true"></i> {{ !empty($eventsWorkspace) ? 'Create event' : 'Create post' }}</a>@endif
        </div>
    </header>

    @include('includes.alerts')

    <div class="bpa-stats" aria-label="Post totals">
        @foreach([
            ['key' => 'all', 'label' => 'All posts', 'icon' => 'fa-files-o', 'hint' => 'In the workspace', 'tone' => 'blue'],
            ['key' => 'review', 'label' => 'In review', 'icon' => 'fa-clock-o', 'hint' => 'Awaiting approval', 'tone' => 'amber'],
            ['key' => 'scheduled', 'label' => 'Scheduled', 'icon' => 'fa-calendar', 'hint' => 'Ready to publish', 'tone' => 'violet'],
            ['key' => 'published', 'label' => 'Published', 'icon' => 'fa-check-circle-o', 'hint' => 'Live on GSF News', 'tone' => 'green'],
        ] as $card)
            <a class="bpa-stat bpa-stat--{{ $card['tone'] }} {{ request('status', '') === ($card['key'] === 'all' ? '' : $card['key']) ? 'is-active' : '' }}" href="{{ route($postRoute.'.index', array_merge(request()->except('status', 'page'), $card['key'] === 'all' ? [] : ['status' => $card['key']])) }}">
                <span class="bpa-stat__top"><span>{{ $card['label'] }}</span><i class="fa {{ $card['icon'] }}" aria-hidden="true"></i></span>
                <strong>{{ number_format($statusSummary[$card['key']]) }}</strong><small>{{ $card['hint'] }}</small>
            </a>
        @endforeach
    </div>

    <section class="bpa-panel" aria-labelledby="bpa-list-title">
        <div class="bpa-panel__head">
            <div><span class="bpa-kicker">Story workspace</span><h2 id="bpa-list-title">{{ request('mine') ? 'My posts' : (request('status') ? ucfirst(request('status')).' posts' : 'All posts') }}</h2><p>{{ number_format($posts->total()) }} {{ \Illuminate\Support\Str::plural('story', $posts->total()) }} match the current filters.</p></div>
            <nav class="bpa-tabs" aria-label="Filter posts">
                <a class="{{ !request('status') && !request('mine') ? 'is-active' : '' }}" href="{{ route($postRoute.'.index', request()->except('status', 'mine', 'page')) }}">All</a>
                @unless($isStakeholder)<a class="{{ request('mine') ? 'is-active' : '' }}" href="{{ route($postRoute.'.index', array_merge(request()->except('mine', 'page'), ['mine' => 1])) }}">Mine</a>@endunless
                @foreach(['draft' => 'Drafts', 'review' => 'In review', 'scheduled' => 'Scheduled', 'published' => 'Published', 'archived' => 'Archived'] as $status => $label)
                    <a class="{{ request('status') === $status ? 'is-active' : '' }}" href="{{ route($postRoute.'.index', array_merge(request()->except('status', 'page'), ['status' => $status])) }}">{{ $label }}</a>
                @endforeach
            </nav>
        </div>

        <form class="bpa-filters" method="GET" action="{{ route($postRoute.'.index') }}">
            @if(request('mine'))<input type="hidden" name="mine" value="1">@endif
            <div class="bpa-filters__field bpa-filters__search"><label for="post-search">Search title or author</label><input class="form-control" id="post-search" name="search" value="{{ request('search') }}" placeholder="Search stories…"></div>
            <div class="bpa-filters__field"><label for="post-status">Status</label><select class="form-control" id="post-status" name="status"><option value="">All statuses</option>@foreach(['draft','review','scheduled','published','archived'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            @if(empty($eventsWorkspace))<div class="bpa-filters__field"><label for="post-type">Type</label><select class="form-control" id="post-type" name="type"><option value="">All types</option><option value="article" @selected(request('type') === 'article')>Article</option><option value="video" @selected(request('type') === 'video')>Video</option></select></div>@endif
            @if($canFilterFields)<div class="bpa-filters__field"><label for="post-field">Field</label><select class="form-control" id="post-field" name="field_id"><option value="">All fields</option>@foreach($fields as $field)<option value="{{ $field->id }}" @selected((string) request('field_id') === (string) $field->id)>{{ $field->name }}</option>@endforeach</select></div>@endif
            @if($canFilterZones)<div class="bpa-filters__field"><label for="post-zone">Zone</label><select class="form-control" id="post-zone" name="zone_id"><option value="">All zones</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" @selected((string) request('zone_id') === (string) $zone->id)>{{ $zone->name }}{{ $zone->field ? ' — '.$zone->field->name : '' }}</option>@endforeach</select></div>@endif
            @if($canFilterChapters)<div class="bpa-filters__field"><label for="post-chapter">Chapter</label><select class="form-control" id="post-chapter" name="chapter_id"><option value="">All chapters</option>@foreach($chapters as $chapter)<option value="{{ $chapter->id }}" @selected((string) request('chapter_id') === (string) $chapter->id)>{{ $chapter->name }}{{ $chapter->zone ? ' — '.$chapter->zone->name : '' }}</option>@endforeach</select></div>@endif
            <div class="bpa-filters__field"><label for="post-category">Category</label><select class="form-control" id="post-category" name="category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="bpa-filters__field"><label for="post-tag">Tag</label><select class="form-control" id="post-tag" name="tag"><option value="">All tags</option>@foreach($tags as $tag)<option value="{{ $tag->id }}" @selected((string) request('tag') === (string) $tag->id)>{{ $tag->name }}</option>@endforeach</select></div>
            <div class="bpa-filters__field"><label for="post-date-from">From date</label><input class="form-control" id="post-date-from" type="date" name="date_from" value="{{ request('date_from') }}"></div>
            <div class="bpa-filters__field"><label for="post-date-to">To date</label><input class="form-control" id="post-date-to" type="date" name="date_to" value="{{ request('date_to') }}"></div>
            <div class="bpa-filters__actions"><button class="btn btn-primary" type="submit"><i class="fa fa-filter" aria-hidden="true"></i> Apply filters</button><a class="btn btn-outline-secondary" href="{{ route($postRoute.'.index') }}">Reset</a></div>
        </form>

        <div class="bpa-toolbar">
            @if(!$isStakeholder && !empty($bulkActions))
                <form id="blog-bulk-action-form" method="POST" action="{{ route('admin.blog.posts.bulk-action') }}" class="blog-bulk-actions">
                    @csrf
                    <label class="sr-only" for="blog-bulk-action">Bulk actions</label>
                    <div class="blog-bulk-actions__controls"><select class="custom-select" id="blog-bulk-action" name="action" required><option value="">Bulk actions</option>@foreach($bulkActions as $action => $label)<option value="{{ $action }}">{{ $label }}</option>@endforeach</select><button type="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Apply</button><span data-selected-post-count>Choose posts below</span></div>
                </form>
            @else
                <span>{{ $isStakeholder ? 'Your posts' : 'Stories' }}</span>
            @endif
            <span>{{ number_format($posts->total()) }} {{ \Illuminate\Support\Str::plural('post', $posts->total()) }}</span>
        </div>

        <div class="table-responsive">
            <table class="bpa-table">
                <thead><tr>
                    @if(!$isStakeholder && !empty($bulkActions))<th class="blog-post-select"><input type="checkbox" data-select-all-posts aria-label="Select all posts on this page"></th>@endif
                    <th>S/N</th><th>Story</th><th>Type</th><th>Status</th><th>Author</th><th>Views</th><th>Comments</th><th>Timeline</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @forelse($posts as $post)
                    @php($imageUrl = \App\Modules\Blog\BlogMedia::url($post->featured_image))
                    @php($canEditRow = \App\Modules\Blog\BlogPermissions::canEditPost($viewer, $post))
                    @php($primaryPostDate = $post->published_at ?: $post->created_at)
                    <tr>
                        @if(!$isStakeholder && !empty($bulkActions))<td class="blog-post-select"><input type="checkbox" name="post_ids[]" value="{{ $post->id }}" form="blog-bulk-action-form" data-post-checkbox aria-label="Select {{ $post->title }}"></td>@endif
                        <td class="bpa-number">{{ $posts->firstItem() + $loop->index }}</td>
                        <td class="bpa-story"><div class="bpa-story__wrap">@if($imageUrl)<a class="bpa-story__image" href="{{ $post->status === 'published' ? route('blog.show', $post->slug) : route($postRoute.'.preview', $post) }}" target="_blank" rel="noopener noreferrer"><img src="{{ $imageUrl }}" alt="" loading="lazy"></a>@endif<div><a class="bpa-story__title" href="{{ $post->status === 'published' ? route('blog.show', $post->slug) : route($postRoute.'.preview', $post) }}" target="_blank" rel="noopener noreferrer">{{ $post->title }}</a><span class="bpa-story__meta">{{ $post->categories->pluck('name')->join(', ') ?: 'Uncategorised' }}@if($post->is_featured)<span class="bpa-featured">★ Featured</span>@endif</span></div></div></td>
                        <td class="bpa-type"><i class="fa {{ $post->type === 'event' ? 'fa-calendar-o' : ($post->type === 'video' ? 'fa-play-circle-o' : 'fa-file-text-o') }}" aria-hidden="true"></i> {{ ucfirst($post->type) }}</td>
                        <td><span class="bpa-status bpa-status--{{ $post->status }}">{{ $post->status === 'review' ? 'In review' : ucfirst($post->status) }}</span></td>
                        <td class="bpa-author">{{ $post->author?->name ?: $post->author?->email ?: '—' }}</td>
                        <td class="bpa-number">{{ number_format($post->view_count) }}</td>
                        <td class="bpa-number">@if(!$isStakeholder && $canManageComments)<a href="{{ route('admin.blog.comments.index', ['post_id' => $post->id]) }}" aria-label="View comments on {{ $post->title }}">{{ number_format($post->comments_count) }}</a>@else{{ number_format($post->comments_count) }}@endif</td>
                        <td class="bpa-date"><span>{{ $post->published_at ? 'Published' : 'Created' }}: {{ $primaryPostDate?->format('d M Y') }}</span><small>{{ $primaryPostDate?->format('g:i a') }}</small><span class="d-block mt-1">Updated: {{ $post->updated_at?->format('d M Y') }}</span><small>{{ $post->updated_at?->format('g:i a') }}</small></td>
                        <td class="bpa-actions"><div class="bpa-actions__group">
                            <a class="bpa-actions__view" href="{{ $post->status === 'published' ? route('blog.show', $post->slug) : route($postRoute.'.preview', $post) }}" target="_blank" rel="noopener noreferrer" aria-label="View {{ $post->title }}"><i class="fa fa-external-link" aria-hidden="true"></i> View</a>
                            @if($canEditRow)<a class="bpa-actions__edit" href="{{ route($postRoute.'.edit', $post) }}" aria-label="Edit {{ $post->title }}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>@endif
                            @if(!$isStakeholder && $canArchive && $post->status !== 'archived')
                                <form method="POST" action="{{ route('admin.blog.posts.archive', $post) }}" onsubmit="return confirm('Archive this post?')">@csrf<button class="bpa-actions__archive" type="submit" aria-label="Archive {{ $post->title }}"><i class="fa fa-archive" aria-hidden="true"></i> Archive</button></form>
                            @endif
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="{{ !$isStakeholder && !empty($bulkActions) ? 10 : 9 }}" class="bpa-empty"><i class="fa fa-newspaper-o" aria-hidden="true"></i><strong>No stories match these filters</strong><span>Try another status or reset the filters.</span></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages())<div class="bpa-pagination">{{ $posts->links() }}</div>@endif
    </section>
</div>
@if(!$isStakeholder && !empty($bulkActions['change-category']))
    <div class="blog-category-modal" data-category-modal hidden aria-hidden="true">
        <div class="blog-category-modal__backdrop" data-category-modal-close></div>
        <section class="blog-category-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="blog-category-modal-title" tabindex="-1">
            <button class="blog-category-modal__close" type="button" data-category-modal-close aria-label="Close category dialog">×</button>
            <span class="blog-category-modal__eyebrow">Bulk action</span>
            <h2 id="blog-category-modal-title">Change categories</h2>
            <p>Select the categories to assign to <strong data-category-post-count>0</strong> selected posts. The selection replaces their current categories.</p>
            <form id="blog-category-modal-form" method="POST" action="{{ route('admin.blog.posts.bulk-action') }}">
                @csrf
                <input type="hidden" name="action" value="change-category">
                <div data-category-post-ids></div>
                <label for="blog-category-select">Categories</label>
                <select class="form-control" id="blog-category-select" name="category_ids[]" multiple size="6" required>
                    <option value="uncategorized">Uncategorised — clear existing categories</option>
                    @foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                </select>
                <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple categories.</small>
                <div class="blog-category-modal__actions">
                    <button class="btn btn-outline-secondary" type="button" data-category-modal-close>Cancel</button>
                    <button class="btn btn-primary" type="submit">Update categories</button>
                </div>
            </form>
        </section>
    </div>
@endif
<style>
        .blog-posts-admin { --bpa-ink:#17223b; --bpa-muted:#718096; --bpa-line:#e3e9f1; color:var(--bpa-ink); }
        .bpa-hero { display:flex; align-items:center; justify-content:space-between; gap:22px; padding:29px 34px; border-radius:19px; color:#fff; background:linear-gradient(120deg,#172a51,#234b7d 70%,#306495); box-shadow:0 15px 35px rgba(24,49,87,.14); }
        .bpa-kicker { color:#2d75ac; font-size:10px; font-weight:850; letter-spacing:.14em; text-transform:uppercase; }
        .bpa-hero .bpa-kicker { color:#bcd8f4; }
        .bpa-hero h1 { margin:7px 0 4px; color:#fff; font-size:30px; font-weight:800; }
        .bpa-hero p { max-width:540px; margin:0; color:#d2e0ef; font-size:13px; }
        .bpa-hero-actions { display:flex; flex-wrap:wrap; gap:9px; }
        .bpa-hero-actions a { display:inline-flex; align-items:center; gap:9px; min-height:40px; padding:9px 14px; border:1px solid rgba(255,255,255,.35); border-radius:9px; color:#fff; background:rgba(255,255,255,.09); font-size:12px; font-weight:750; text-decoration:none; }
        .bpa-hero-actions a:hover { color:#fff; background:rgba(255,255,255,.19); }
        .bpa-hero-actions .bpa-create { border-color:#e6c76c; color:#172a51; background:#e6c76c; }
        .bpa-hero-actions .bpa-create:hover { color:#172a51; background:#f0d784; }
        .bpa-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:15px; margin:22px 0; }
        .bpa-stat { display:flex; min-width:0; flex-direction:column; padding:20px 21px; border:1px solid var(--bpa-line); border-radius:15px; background:#fff; box-shadow:0 6px 18px rgba(26,43,73,.035); text-decoration:none; transition:transform .2s ease,box-shadow .2s ease; }
        .bpa-stat:hover { transform:translateY(-2px); box-shadow:0 12px 24px rgba(26,43,73,.09); }
        .bpa-stat.is-active { outline:2px solid currentColor; outline-offset:-2px; }
        .bpa-stat__top { display:flex; align-items:center; justify-content:space-between; gap:10px; font-size:12px; font-weight:800; }
        .bpa-stat__top i { display:grid; place-items:center; width:33px; height:33px; border-radius:10px; font-size:16px; }
        .bpa-stat strong { margin:12px 0 4px; color:var(--bpa-ink); font-size:35px; font-weight:800; line-height:1; }
        .bpa-stat small { color:var(--bpa-muted); font-size:11px; }
        .bpa-stat--blue { color:#23599a; }.bpa-stat--blue i { background:#eaf2ff; }
        .bpa-stat--amber { color:#aa6915; }.bpa-stat--amber i { background:#fff2d9; }
        .bpa-stat--violet { color:#7059aa; }.bpa-stat--violet i { background:#f0eaff; }
        .bpa-stat--green { color:#16845e; }.bpa-stat--green i { background:#e6f7ee; }
        .bpa-panel { overflow:hidden; border:1px solid var(--bpa-line); border-radius:17px; background:#fff; box-shadow:0 8px 24px rgba(26,43,73,.04); }
        .bpa-panel__head { display:flex; align-items:end; justify-content:space-between; gap:20px; padding:24px 27px 20px; }
        .bpa-panel h2 { margin:5px 0; font-size:22px; font-weight:800; }
        .bpa-panel__head p { margin:0; color:var(--bpa-muted); font-size:12px; }
        .bpa-tabs { display:flex; flex-wrap:wrap; gap:4px; padding:4px; border:1px solid var(--bpa-line); border-radius:10px; background:#f7f9fc; }
        .bpa-tabs a { padding:8px 12px; border-radius:7px; color:#65728a; font-size:12px; font-weight:750; text-decoration:none; }
        .bpa-tabs a.is-active { color:#fff; background:#224d80; box-shadow:0 2px 8px rgba(28,68,117,.15); }
        .bpa-filters { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:13px; align-items:end; padding:18px 27px; border-top:1px solid var(--bpa-line); background:#fbfcfe; }
        .bpa-filters__search { grid-column:span 2; }
        .bpa-filters__field { min-width:0; }
        .bpa-filters__field label { display:block; margin-bottom:6px; color:#58677c; font-size:11px; font-weight:800; }
        .bpa-filters__field .form-control { width:100%; min-width:0; height:40px; border-color:#d9e1ec; border-radius:8px; background:#fff; font-size:12px; }
        .bpa-filters__actions { display:flex; flex-wrap:wrap; gap:8px; }
        .bpa-filters__actions .btn { min-height:40px; font-size:12px; font-weight:750; white-space:nowrap; }
        .bpa-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; min-height:68px; padding:14px 27px; border-top:1px solid var(--bpa-line); color:var(--bpa-muted); font-size:12px; }
        .blog-bulk-actions { display:inline-block; margin:0; max-width:100%; }
        .blog-bulk-actions__controls { display:grid; grid-template-columns:minmax(150px,190px) max-content; align-items:center; gap:7px 9px; }
        .blog-bulk-actions__controls .custom-select { grid-column:1; width:100%; min-width:0; height:39px; font-size:12px; }
        .blog-bulk-actions__controls .btn { grid-column:2; min-height:39px; font-size:12px; font-weight:750; white-space:nowrap; }
        .blog-bulk-actions__controls [data-selected-post-count] { grid-column:1/-1; color:var(--bpa-muted); font-size:11px; }
        .bpa-table { width:100%; min-width:1240px; border-collapse:collapse; }
        .bpa-table th { padding:13px; border-bottom:1px solid var(--bpa-line); color:#69778f; background:#f8fafc; font-size:10px; font-weight:850; letter-spacing:.1em; text-align:left; text-transform:uppercase; }
        .bpa-table td { padding:16px 13px; border-bottom:1px solid #edf0f4; color:#34435b; font-size:12px; line-height:1.5; vertical-align:middle; }
        .bpa-table tbody tr:hover { background:#fafcff; }.bpa-table tbody tr:last-child td { border-bottom:0; }
        .blog-post-select { width:42px; padding-left:18px!important; text-align:center; }
        .blog-post-select input { width:16px; height:16px; vertical-align:middle; cursor:pointer; accent-color:#24558e; }
        .bpa-story { min-width:300px; max-width:460px; }.bpa-story__wrap { display:flex; align-items:center; gap:13px; }.bpa-story__image { display:block; width:64px; height:58px; flex:0 0 64px; overflow:hidden; border-radius:9px; background:#edf2f7; }.bpa-story__image img { display:block; width:100%; height:100%; object-fit:cover; }.bpa-story__title { display:-webkit-box; overflow:hidden; color:#1d3151; font-size:13px; font-weight:800; line-height:1.4; -webkit-box-orient:vertical; -webkit-line-clamp:2; text-decoration:none; }.bpa-story__title:hover { color:#2368a1; }.bpa-story__meta { display:flex; flex-wrap:wrap; align-items:center; gap:8px; margin-top:6px; color:var(--bpa-muted); font-size:11px; }
        .bpa-featured { padding:3px 7px; border-radius:999px; color:#9b7016; background:#fff3d5; font-size:9px; font-weight:800; }
        .bpa-type,.bpa-author { color:#52627b; }.bpa-type { white-space:nowrap; }.bpa-type i { margin-right:4px; color:#8294ad; }.bpa-author { min-width:110px; }
        .bpa-status { display:inline-flex; padding:5px 9px; border-radius:999px; font-size:10px; font-weight:800; white-space:nowrap; text-transform:uppercase; }.bpa-status--draft { color:#66758a; background:#edf1f5; }.bpa-status--review { color:#926214; background:#fff0d2; }.bpa-status--scheduled { color:#6e55a5; background:#f0eaff; }.bpa-status--published { color:#117548; background:#e0f5e8; }.bpa-status--archived { color:#7a4552; background:#f6e8eb; }
        .bpa-number { font-variant-numeric:tabular-nums; text-align:center; }.bpa-number a { color:#24578d; font-weight:800; text-decoration:none; }.bpa-date { white-space:nowrap; }.bpa-date small { display:block; margin-top:3px; color:var(--bpa-muted); font-size:10px; }
        .bpa-actions { min-width:224px; }.bpa-actions__group { display:flex; flex-wrap:wrap; align-items:center; gap:6px; }.bpa-actions form { display:inline-flex; margin:0; }
        .bpa-actions a,.bpa-actions button { display:inline-flex; align-items:center; justify-content:center; gap:5px; min-height:32px; margin:0; padding:6px 9px; border:1px solid transparent; border-radius:7px; font-size:11px; font-weight:800; line-height:1; text-decoration:none; white-space:nowrap; cursor:pointer; transition:background .2s ease,border-color .2s ease,transform .2s ease; }
        .bpa-actions a:hover,.bpa-actions button:hover { transform:translateY(-1px); text-decoration:none; }.bpa-actions a:focus-visible,.bpa-actions button:focus-visible { outline:3px solid rgba(42,111,180,.25); outline-offset:2px; }
        .bpa-actions .bpa-actions__view { border-color:#cddced; color:#28588a; background:#f4f8fc; }.bpa-actions .bpa-actions__view:hover { border-color:#93b7d8; background:#e8f2fb; }
        .bpa-actions .bpa-actions__edit { border-color:#224d80; color:#fff; background:#224d80; }.bpa-actions .bpa-actions__edit:hover { color:#fff; background:#163a66; }
        .bpa-actions .bpa-actions__archive { border-color:#ebdfbc; color:#8b6821; background:#fff8e7; }.bpa-actions .bpa-actions__archive:hover { border-color:#d7bc74; background:#fff1cb; }
        .bpa-empty { padding:55px 20px!important; text-align:center; }.bpa-empty i,.bpa-empty strong,.bpa-empty span { display:block; }.bpa-empty i { margin-bottom:10px; color:#aab8c9; font-size:30px; }.bpa-empty strong { color:#253653; font-size:16px; }.bpa-empty span { margin-top:5px; color:var(--bpa-muted); }.bpa-pagination { display:flex; justify-content:center; padding:20px; }
        .blog-category-modal[hidden] { display:none; }
        .blog-category-modal { position:fixed; z-index:2000; inset:0; display:grid; place-items:center; padding:20px; }
        .blog-category-modal__backdrop { position:absolute; z-index:0; inset:0; background:rgba(15,25,43,.58); backdrop-filter:blur(4px); }
        .blog-category-modal__dialog { position:relative; z-index:1; width:min(100%,480px); padding:30px; border:1px solid rgba(255,255,255,.75); border-radius:18px; background:#fff; box-shadow:0 24px 80px rgba(12,27,53,.28); }
        .blog-category-modal__close { position:absolute; top:14px; right:14px; width:34px; height:34px; border:0; border-radius:50%; color:#667085; background:#f2f4f7; font-size:22px; line-height:1; }
        .blog-category-modal__eyebrow { color:#c2185b; font-size:11px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
        .blog-category-modal__dialog h2 { margin:7px 40px 8px 0; color:#19253a; font-size:23px; font-weight:750; }
        .blog-category-modal__dialog p { margin-bottom:22px; color:#68758a; line-height:1.55; }
        .blog-category-modal__dialog label { display:block; margin-bottom:7px; color:#39465c; font-size:13px; font-weight:700; }
        .blog-category-modal__actions { display:flex; justify-content:flex-end; gap:9px; margin-top:22px; }
        .blog-category-picker { position:relative; z-index:2; }
        .blog-category-picker__toggle { display:flex; width:100%; min-height:48px; align-items:center; justify-content:space-between; gap:12px; padding:0 14px; border:1px solid #cbd3df; border-radius:7px; color:#344054; background:#fff; text-align:left; }
        .blog-category-picker__toggle:focus-visible { outline:3px solid rgba(47,130,230,.25); border-color:#4589e8; }
        .blog-category-picker__chevron { color:#758198; font-size:18px; }
        .blog-category-picker__menu { position:absolute; z-index:20; top:calc(100% + 5px); right:0; left:0; max-height:240px; overflow-y:auto; padding:5px; border:1px solid #e0e5ed; border-radius:9px; background:#fff; box-shadow:0 14px 35px rgba(20,35,60,.2); }
        .blog-category-picker__menu[hidden] { display:none; }
        .blog-category-picker__option { display:block; width:100%; padding:10px 12px; border:0; border-radius:6px; color:#344054; background:#fff; text-align:left; }
        .blog-category-picker__option:hover, .blog-category-picker__option:focus-visible { color:#153d78; background:#eef5ff; outline:0; }
        .blog-category-picker__error { display:block; margin-top:6px; color:#bd2940; font-size:12px; }
        body.blog-category-modal-open { overflow:hidden; }
        @media(max-width:1120px) { .bpa-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }.bpa-hero { align-items:flex-start; flex-direction:column; }.bpa-panel__head { align-items:flex-start; flex-direction:column; }.bpa-filters { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media(max-width:640px) { .bpa-hero { padding:23px; }.bpa-stats { gap:9px; }.bpa-stat { padding:15px; }.bpa-stat strong { font-size:28px; }.bpa-panel__head { padding:21px; }.bpa-tabs { flex-wrap:nowrap; width:100%; overflow-x:auto; }.bpa-tabs a { flex:0 0 auto; }.bpa-filters { grid-template-columns:1fr; padding:18px 21px; }.bpa-filters__search { grid-column:auto; }.bpa-toolbar { align-items:flex-start; flex-direction:column; padding:14px 21px; }.blog-bulk-actions { width:100%; }.blog-bulk-actions__controls { grid-template-columns:minmax(150px,1fr) max-content; width:100%; } }
    </style>
@if(!$isStakeholder && !empty($bulkActions))
    <script>
        (() => {
            const master = document.querySelector('[data-select-all-posts]');
            const checkboxes = [...document.querySelectorAll('[data-post-checkbox]')];
            const countLabel = document.querySelector('[data-selected-post-count]');
            const bulkForm = document.querySelector('#blog-bulk-action-form');
            const bulkAction = document.querySelector('#blog-bulk-action');
            const categoryModal = document.querySelector('[data-category-modal]');
            const categoryPostIds = categoryModal?.querySelector('[data-category-post-ids]');
            const categoryPostCount = categoryModal?.querySelector('[data-category-post-count]');
            const categoryForm = categoryModal?.querySelector('#blog-category-modal-form');
            const categorySelect = categoryModal?.querySelector('#blog-category-select');
            if (!master || !countLabel) return;

            const updateSelection = () => {
                const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
                master.checked = checkboxes.length > 0 && selected === checkboxes.length;
                master.indeterminate = selected > 0 && selected < checkboxes.length;
                countLabel.textContent = selected ? `${selected} post(s) selected.` : 'Select posts to apply an action.';
            };

            master.addEventListener('change', () => {
                checkboxes.forEach((checkbox) => { checkbox.checked = master.checked; });
                updateSelection();
            });
            checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelection));

            const closeCategoryModal = () => {
                if (!categoryModal) return;
                categoryModal.hidden = true;
                categoryModal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('blog-category-modal-open');
                bulkAction?.focus();
            };

            bulkForm?.addEventListener('submit', (event) => {
                const selected = checkboxes.filter((checkbox) => checkbox.checked);
                if (selected.length === 0) {
                    event.preventDefault();
                    window.alert('Select at least one post first.');
                    return;
                }
                if (bulkAction?.value === 'change-category') {
                    event.preventDefault();
                    if (!categoryModal || !categoryPostIds || !categoryPostCount || !categoryForm) return;

                    categoryPostIds.replaceChildren(...selected.map((checkbox) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'post_ids[]';
                        input.value = checkbox.value;
                        return input;
                    }));
                    categoryPostCount.textContent = String(selected.length);
                    if (categorySelect) [...categorySelect.options].forEach((option) => { option.selected = false; });
                    categoryModal.hidden = false;
                    categoryModal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('blog-category-modal-open');
                    categorySelect?.focus();
                    return;
                }

                const message = bulkAction?.value === 'delete'
                    ? `Delete ${selected.length} selected post(s)?`
                    : `Apply ${bulkAction?.selectedOptions[0]?.textContent?.trim() || 'this action'} to ${selected.length} selected post(s)?`;
                if (!window.confirm(message)) event.preventDefault();
            });

            categoryModal?.querySelectorAll('[data-category-modal-close]').forEach((button) => {
                button.addEventListener('click', closeCategoryModal);
            });
            categorySelect?.addEventListener('change', () => {
                const uncategorized = [...categorySelect.options].find((option) => option.value === 'uncategorized');
                if (uncategorized?.selected && categorySelect.selectedOptions.length > 1) {
                    [...categorySelect.options].forEach((option) => { option.selected = option.value === 'uncategorized'; });
                } else if ([...categorySelect.selectedOptions].some((option) => option.value === 'uncategorized')) {
                    [...categorySelect.options].forEach((option) => { if (option.value === 'uncategorized') option.selected = false; });
                }
            });
            categoryModal?.addEventListener('click', (event) => {
                if (event.target === categoryModal) closeCategoryModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape' || !categoryModal || categoryModal.hidden) return;
                closeCategoryModal();
            });
            categoryForm?.addEventListener('submit', (event) => {
                if (!categorySelect?.selectedOptions.length) {
                    event.preventDefault();
                    categorySelect?.focus();
                }
            });
            updateSelection();
        })();
    </script>
@endif
@endsection
