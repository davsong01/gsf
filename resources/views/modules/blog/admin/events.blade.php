@extends($isStakeholder ? 'layouts.stakeholderdashboard' : 'layouts.dashboard')
@section('title', 'Programs & Events')
@section('active')<li class="breadcrumb-item">Programs &amp; Events</li>@endsection
@section('content')
@php($eventRoute = ($isStakeholder ? 'stakeholders' : 'admin').'.events.posts')
<div class="content-body event-workspace">
    <header class="event-workspace__hero">
        <div><span>GSF fellowship calendar</span><h1>Programs &amp; Events</h1><p>Plan gatherings, share event details and introduce speakers or guest ministers.</p></div>
        <div class="event-workspace__hero-actions"><a class="event-workspace__hero-button event-workspace__hero-button--outline" href="{{ route('events.public.index') }}" target="_blank" rel="noopener"><i class="fa fa-external-link" aria-hidden="true"></i> Public events</a>@if($canCreate)<a class="event-workspace__hero-button event-workspace__hero-button--primary" href="{{ route($eventRoute.'.create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Create event</a>@endif</div>
    </header>
    @include('includes.alerts')
    <div class="event-workspace__stats">
        @foreach(['all' => 'All events', 'published' => 'Published', 'review' => 'In review', 'draft' => 'Drafts'] as $status => $label)
            <a href="{{ route($eventRoute.'.index', $status === 'all' ? [] : ['status' => $status]) }}"><span>{{ $label }}</span><strong>{{ number_format($statusSummary[$status]) }}</strong></a>
        @endforeach
    </div>
    <section class="event-workspace__panel">
        <div class="event-workspace__panel-head"><div><h2>Event calendar</h2><p>{{ number_format($posts->total()) }} {{ \Illuminate\Support\Str::plural('event', $posts->total()) }} match the current filters.</p></div></div>
        <form class="event-workspace__filters" method="GET" action="{{ route($eventRoute.'.index') }}">
            <div class="event-workspace__filter-fields">
                <label><span>Search</span><input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Event or author"></label>
                <label><span>Status</span><select class="form-control" name="status"><option value="">All statuses</option>@foreach(['draft','review','scheduled','published','archived'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></label>
                @unless($isStakeholder)<label><span>Facebook status</span><select class="form-control" name="facebook_status"><option value="">All Facebook statuses</option>@foreach(['pending_review' => 'Pending review', 'waiting_for_publication' => 'Waiting for website publication', 'pending' => 'Ready to publish', 'published' => 'Published', 'failed' => 'Failed', 'rejected' => 'Rejected'] as $facebookStatus => $label)<option value="{{ $facebookStatus }}" @selected(request('facebook_status') === $facebookStatus)>{{ $label }}</option>@endforeach</select></label>@endunless
                @if($canFilterFields)<label><span>Field</span><select class="form-control" name="field_id"><option value="">All fields</option>@foreach($fields as $field)<option value="{{ $field->id }}" @selected((string) request('field_id') === (string) $field->id)>{{ $field->name }}</option>@endforeach</select></label>@endif
                @if($canFilterZones)<label><span>Zone</span><select class="form-control" name="zone_id"><option value="">All zones</option>@foreach($zones as $zone)<option value="{{ $zone->id }}" @selected((string) request('zone_id') === (string) $zone->id)>{{ $zone->name }}</option>@endforeach</select></label>@endif
                @if($canFilterChapters)<label><span>Campus</span><select class="form-control" name="chapter_id"><option value="">All campuses</option>@foreach($chapters as $chapter)<option value="{{ $chapter->id }}" @selected((string) request('chapter_id') === (string) $chapter->id)>{{ $chapter->name }}</option>@endforeach</select></label>@endif
            </div>
            <div class="event-workspace__filter-actions"><button type="submit"><i class="fa fa-filter" aria-hidden="true"></i> Apply filters</button><a href="{{ route($eventRoute.'.index') }}">Reset</a></div>
        </form>
        @if(!$isStakeholder && !empty($bulkActions))
            <div class="row mx-0">
                <div class="col-md-6">
                    <form id="event-bulk-action-form" class="event-workspace__bulk" method="POST" action="{{ route($eventRoute.'.bulk-action') }}">
                        @csrf
                        <label class="sr-only" for="event-bulk-action">Bulk actions</label>
                        <div class="event-workspace__bulk-controls"><select class="form-control" id="event-bulk-action" name="action" required><option value="">Bulk actions</option>@foreach($bulkActions as $action => $label)<option value="{{ $action }}">{{ $label }}</option>@endforeach</select><button type="submit"><i class="fa fa-check" aria-hidden="true"></i> Apply</button></div>
                        <span data-selected-event-count>Select events to apply an action.</span>
                    </form>
                </div>
            </div>
        @endif
        <div class="table-responsive"><table class="table event-workspace__table"><thead><tr>@if(!$isStakeholder && !empty($bulkActions))<th scope="col" class="event-workspace__select"><input type="checkbox" data-select-all-events aria-label="Select all events on this page"></th>@endif<th scope="col">S/N</th><th scope="col">Event</th><th scope="col">Date &amp; time</th><th scope="col">Venue</th><th scope="col">People</th><th scope="col">Status</th><th scope="col">Actions</th></tr></thead><tbody>
            @forelse($posts as $event)
                @php($eventImage = \App\Modules\Blog\BlogMedia::url($event->featured_image))
                <tr>@if(!$isStakeholder && !empty($bulkActions))<td class="event-workspace__select"><input type="checkbox" name="post_ids[]" value="{{ $event->id }}" form="event-bulk-action-form" data-event-checkbox aria-label="Select {{ $event->title }}"></td>@endif<td class="event-workspace__serial">{{ $posts->firstItem() + $loop->index }}</td><td><div class="event-workspace__event">@if($eventImage)<img src="{{ $eventImage }}" alt="" loading="lazy">@else<span class="event-workspace__placeholder"><i class="fa fa-calendar-o" aria-hidden="true"></i></span>@endif<div><strong>{{ $event->title }}</strong><small>{{ $event->author?->name ?: 'GSF' }}</small>@if($event->is_featured)<span class="event-workspace__featured"><i class="fa fa-star" aria-hidden="true"></i> Featured</span>@endif</div></div></td><td><strong>{{ $event->event_starts_at?->format('d M Y') ?: 'Date pending' }}</strong><small>{{ $event->event_starts_at?->format('g:i A') }}</small></td><td>{{ $event->event_venue ?: '—' }}</td><td>{{ count($event->event_speakers ?? []) }} listed</td><td><span class="event-workspace__status">{{ $event->status === 'review' ? 'In review' : ucfirst($event->status) }}</span>@if($event->facebook_publish_requested)<small class="d-block mt-1">Facebook: {{ str($event->facebook_status)->replace('_', ' ')->title() }}</small>@endif</td><td><div class="event-workspace__actions">@if(\App\Modules\Blog\BlogPermissions::canEditPost($viewer, $event))<a href="{{ route($eventRoute.'.edit', $event) }}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>@endif<a href="{{ $event->status === 'published' ? route('events.public.show', $event->slug) : route($eventRoute.'.preview', $event) }}" target="_blank" rel="noopener"><i class="fa fa-external-link" aria-hidden="true"></i> {{ $event->status === 'published' ? 'View' : 'Preview' }}</a>
                    @if(!$isStakeholder && \App\Modules\Blog\BlogPermissions::allows($viewer, \App\Modules\Blog\BlogPermissions::PUBLISH) && $event->facebook_status === 'pending_review')
                        <form method="POST" action="{{ route('admin.blog.posts.facebook-review', $event) }}">@csrf<input type="hidden" name="decision" value="approve"><button class="btn btn-sm btn-success" type="submit">Approve Facebook</button></form>
                        <form method="POST" action="{{ route('admin.blog.posts.facebook-review', $event) }}">@csrf<input type="hidden" name="decision" value="reject"><button class="btn btn-sm btn-outline-danger" type="submit">Reject Facebook</button></form>
                    @elseif(!$isStakeholder && \App\Modules\Blog\BlogPermissions::allows($viewer, \App\Modules\Blog\BlogPermissions::PUBLISH) && $event->facebook_status === 'failed')
                        <form method="POST" action="{{ route('admin.blog.posts.facebook-review', $event) }}">@csrf<input type="hidden" name="decision" value="retry"><button class="btn btn-sm btn-outline-warning" type="submit">Retry Facebook</button></form>
                    @endif
                    </div></td></tr>
            @empty
                <tr><td colspan="{{ !$isStakeholder && !empty($bulkActions) ? 8 : 7 }}" class="text-center py-5">No events match these filters. @if($canCreate)<a href="{{ route($eventRoute.'.create') }}">Create the first event</a>.@endif</td></tr>
            @endforelse
        </tbody></table></div>
        <div class="event-workspace__pagination">{{ $posts->links() }}</div>
    </section>
</div>
<style>
.event-workspace { color:#1a2d48; }
.event-workspace__hero { display:flex; align-items:end; justify-content:space-between; flex-wrap:wrap; gap:20px; margin-bottom:23px; padding:30px; border-radius:18px; color:#fff; background:linear-gradient(120deg,#122b50,#176f8d); }
.event-workspace__hero span { color:#c3e5ed; font-size:11px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
.event-workspace__hero h1 { margin:5px 0 8px; color:#fff; font-size:31px; font-weight:800; }
.event-workspace__hero p { max-width:560px; margin:0; color:#d6e5ed; font-size:14px; }
.event-workspace__hero-actions { display:flex; flex-wrap:wrap; gap:9px; }
.event-workspace__hero-actions .event-workspace__hero-button { display:inline-flex; align-items:center; justify-content:center; gap:9px; min-height:44px; padding:10px 17px; border:1px solid; border-radius:9px; font-size:13px; font-weight:800; line-height:1.2; text-decoration:none; transition:background .2s,color .2s,transform .2s; }
.event-workspace__hero-actions .event-workspace__hero-button:hover { transform:translateY(-2px); }
.event-workspace__hero-actions .event-workspace__hero-button--outline { border-color:rgba(255,255,255,.62); background:transparent; color:#fff; }
.event-workspace__hero-actions .event-workspace__hero-button--outline:hover { background:rgba(255,255,255,.13); color:#fff; }
.event-workspace__hero-actions .event-workspace__hero-button--primary { border-color:#e1bc66; background:#e1bc66; color:#142c4d; }
.event-workspace__hero-actions .event-workspace__hero-button--primary:hover { border-color:#f4d68a; background:#f4d68a; color:#142c4d; }
.event-workspace__stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:22px; }
.event-workspace__stats a { display:grid; gap:6px; padding:18px; border:1px solid #dbe4ec; border-radius:12px; background:#fff; text-decoration:none; }
.event-workspace__stats span { color:#667d94; font-size:12px; font-weight:700; }
.event-workspace__stats strong { color:#173d67; font-size:25px; }
.event-workspace__panel { overflow:hidden; border:1px solid #dbe4ec; border-radius:15px; background:#fff; }
.event-workspace__panel-head { padding:22px 23px 0; }
.event-workspace__panel-head h2 { margin:0 0 4px; font-size:21px; font-weight:800; }
.event-workspace__panel-head p { color:#708198; font-size:12px; }
.event-workspace__filters { display:grid; gap:14px; padding:14px 23px 23px; }
.event-workspace__filter-fields { display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,190px),1fr)); gap:12px; }
.event-workspace__filter-fields label { display:block; min-width:0; margin:0; color:#596d85; font-size:11px; font-weight:800; letter-spacing:.04em; }
.event-workspace__filter-fields label > span { display:block; margin-bottom:7px; }
.event-workspace__filter-fields .form-control { display:block; width:100%; max-width:none; min-width:0; height:42px; box-sizing:border-box; border:1px solid #cfdae5; border-radius:9px; background:#fff; color:#253b54; font-size:13px; }
.event-workspace__filter-actions { display:flex; flex-wrap:wrap; align-items:center; gap:9px; }
.event-workspace__filter-actions button,.event-workspace__filter-actions a { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:38px; padding:8px 15px; border:1px solid #1b5e8f; border-radius:8px; font:inherit; font-size:12px; font-weight:800; text-decoration:none; cursor:pointer; }
.event-workspace__filter-actions button { background:#1b5e8f; color:#fff; }
.event-workspace__filter-actions button:hover { background:#143f67; }
.event-workspace__filter-actions a { background:#fff; color:#1b5e8f; }
.event-workspace__filter-actions a:hover { background:#edf5fa; }
.event-workspace__bulk { display:grid; gap:8px; margin:0; padding:0 0 17px; }
.event-workspace__bulk-controls { display:flex; align-items:center; gap:9px; }
.event-workspace__bulk .form-control { flex:1 1 auto; width:1%; min-width:0; max-width:none; height:38px; border:1px solid #cfdae5; border-radius:8px; font-size:12px; }
.event-workspace__bulk button { display:inline-flex; align-items:center; gap:7px; min-height:38px; padding:8px 15px; border:1px solid #1b5e8f; border-radius:8px; color:#fff; background:#1b5e8f; font-size:12px; font-weight:800; cursor:pointer; }
.event-workspace__bulk > span { color:#708198; font-size:11px; }
.event-workspace__select { width:38px; text-align:center; }
.event-workspace__select input { width:16px; height:16px; cursor:pointer; }
.event-workspace__table { margin:0; }
.event-workspace__table th { white-space:nowrap; color:#65788d; font-size:11px; text-transform:uppercase; }
.event-workspace__table td { vertical-align:middle; font-size:12px; }
.event-workspace__table td > strong,.event-workspace__table td > small { display:block; }
.event-workspace__table td > small { color:#71859b; }
.event-workspace__serial { color:#71859b; font-weight:800; white-space:nowrap; }
.event-workspace__event { display:flex; align-items:center; gap:11px; min-width:220px; }
.event-workspace__event img,.event-workspace__placeholder { width:62px; height:48px; flex:0 0 62px; border-radius:8px; object-fit:cover; }
.event-workspace__placeholder { display:grid; place-items:center; color:#256f99; background:#edf5fa; }
.event-workspace__event strong,.event-workspace__event small { display:block; }
.event-workspace__event small { margin-top:4px; color:#75879b; }
.event-workspace__featured { display:inline-flex; align-items:center; gap:4px; margin-top:6px; padding:3px 7px; border-radius:100px; background:#fff3d6; color:#78561d; font-size:10px; font-weight:850; }
.event-workspace__status { padding:5px 8px; border-radius:7px; background:#edf5fa; color:#20547b; font-weight:700; }
.event-workspace__actions { display:flex; flex-wrap:wrap; gap:7px; white-space:nowrap; }
.event-workspace__actions a { display:inline-flex; align-items:center; justify-content:center; gap:6px; min-height:31px; padding:6px 10px; border:1px solid #c8d8e5; border-radius:7px; background:#f5f9fc; color:#20547b; font-size:11px; font-weight:800; line-height:1; text-decoration:none; }
.event-workspace__actions a:first-child { border-color:#1c648f; background:#1c648f; color:#fff; }
.event-workspace__actions a:hover { border-color:#103f65; background:#103f65; color:#fff; }
.event-workspace__pagination { padding:15px 20px; }
@media(max-width:767px) { .event-workspace__stats { grid-template-columns:repeat(2,minmax(0,1fr)); }.event-workspace__filter-fields { grid-template-columns:1fr; }.event-workspace__hero { padding:22px; } }
</style>
@if(!$isStakeholder && !empty($bulkActions))
<script>
    (() => {
        const master = document.querySelector('[data-select-all-events]');
        const checkboxes = [...document.querySelectorAll('[data-event-checkbox]')];
        const form = document.getElementById('event-bulk-action-form');
        const action = document.getElementById('event-bulk-action');
        const count = document.querySelector('[data-selected-event-count]');
        if (!master || !form || !count) return;
        const updateSelection = () => {
            const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
            master.checked = checkboxes.length > 0 && selected === checkboxes.length;
            master.indeterminate = selected > 0 && selected < checkboxes.length;
            count.textContent = selected ? `${selected} event(s) selected.` : 'Select events to apply an action.';
        };
        master.addEventListener('change', () => {
            checkboxes.forEach((checkbox) => { checkbox.checked = master.checked; });
            updateSelection();
        });
        checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelection));
        form.addEventListener('submit', (event) => {
            const selected = checkboxes.filter((checkbox) => checkbox.checked);
            if (!selected.length) {
                event.preventDefault();
                window.alert('Select at least one event first.');
                return;
            }
            const label = action.selectedOptions[0]?.textContent?.trim() || 'this action';
            const message = action.value === 'delete'
                ? `Permanently delete ${selected.length} selected event(s)?`
                : `Apply “${label}” to ${selected.length} selected event(s)?`;
            if (!window.confirm(message)) event.preventDefault();
        });
    })();
</script>
@endif
@endsection
