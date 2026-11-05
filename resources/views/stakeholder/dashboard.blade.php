@extends('layouts.stakeholderdashboard')

@section('title', 'Dashboard · GSF Stakeholders')
@section('extra_styles')
<link rel="stylesheet" href="{{ asset('assets/css/stakeholder-dashboard-home.css') }}?v=2">
@endsection

@section('content')
@php
    $isChapter = in_array($user->role_id, chapterStakeholders());
    $isZoneStakeholder = in_array($user->role_id, zoneStakeholders());
    $isFieldStakeholder = in_array($user->role_id, fieldStakeholders());
    $isNationalStakeholder = in_array($user->role_id, array_merge(secretariatStakeholders(), ncpStakeholders()));
    $isFinance = finStakeholders($user);
    $field = $chapter?->field ?? $user->field;
    $zone = $chapter?->zone ?? $user->zone;
    $fieldPastor = $field?->fieldCord;
    if ($fieldPastor && (int) $fieldPastor->id === (int) $user->id) {
        $fieldPastor = null;
    }
    $zonePastors = ($zone?->zonalCords ?? collect())
        ->reject(fn ($pastor) => (int) $pastor->id === (int) $user->id)
        ->values();
    $workspaceName = $chapter?->name ?? $zone?->name ?? $field?->name ?? ($isNationalStakeholder ? 'National Secretariat' : ($isFinance ? 'Finance workspace' : 'Stakeholder workspace'));
    $workspaceLabel = $isChapter ? 'Chapter workspace' : ($isZoneStakeholder ? 'Zone workspace' : ($isFieldStakeholder ? 'Field workspace' : ($isFinance ? 'Finance workspace' : ($isNationalStakeholder ? 'National workspace' : 'Stakeholder workspace'))));
    $welcomeCopy = $isChapter
        ? 'Support your campus community and keep your reporting moving.'
        : ($isZoneStakeholder
            ? 'Coordinate reports and support campus leaders across your zone.'
            : ($isFieldStakeholder
                ? 'Coordinate reporting and leadership across your field.'
                : ($isFinance
                    ? 'Manage financial reporting and keep records moving.'
                    : ($isNationalStakeholder
                        ? 'Coordinate national work and stay connected across the network.'
                        : 'Manage your assigned GSF responsibilities from this workspace.'))));
    $roleName = $user->role?->name ?: 'GSF Stakeholder';
    $canSubmitReport = $isChapter && $chapter && canAddReport($chapter->id)['eligible'];
    $canCreatePosts = $user->hasPermission('blog.create');
    $canOpenAppraisal = app(\App\Services\AppraisalService::class)->hasMenuAccess($user);
@endphp

<main class="sh-dashboard content-body">
    <section class="sh-hero" aria-labelledby="sh-dashboard-title">
        <div class="sh-hero__copy">
            <span class="sh-eyebrow"><span></span> Stakeholder workspace</span>
            <h1 id="sh-dashboard-title">Good to have you here.</h1>
            <p>Welcome back, <strong>{{ $user->name }}</strong>. {{ $welcomeCopy }}</p>
            <div class="sh-hero__actions">
                @if($canSubmitReport)
                    <a class="sh-button sh-button--primary" href="{{ route('stakeholders.reports.create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Submit a report <span aria-hidden="true">↗</span></a>
                @elseif($isFinance)
                    <a class="sh-button sh-button--primary" href="{{ route('stakeholders.financial.report') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i> Financial reports <span aria-hidden="true">↗</span></a>
                @else
                    <a class="sh-button sh-button--primary" href="{{ route('stakeholders.reports.index') }}"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Open reports <span aria-hidden="true">↗</span></a>
                @endif
                <a class="sh-button sh-button--outline" href="{{ route('stakeholders.profile') }}">My profile <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <div class="sh-hero__identity">
            <span class="sh-hero__identity-label">{{ $workspaceLabel }}</span>
            <strong>{{ $workspaceName }}</strong>
            <span class="sh-hero__role"><i class="fa fa-circle" aria-hidden="true"></i> {{ $roleName }}</span>
            @if($zone || $field)
                <div class="sh-hero__places">
                    @if($zone)<span><small>Zone</small>{{ $zone->name }}</span>@endif
                    @if($field)<span><small>Field</small>{{ $field->name }}</span>@endif
                </div>
            @endif
        </div>
    </section>

    <section class="sh-section" aria-labelledby="sh-actions-title">
        <div class="sh-section__heading"><div><span class="sh-section__eyebrow">Make progress</span><h2 id="sh-actions-title">Your quick actions</h2></div><p>The things you reach for most, all in one place.</p></div>
        <div class="sh-action-grid">
            @if($isFinance)
                <a class="sh-action sh-action--blue" href="{{ route('stakeholders.financial.report') }}"><span class="sh-action__icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span><span class="sh-action__copy"><strong>Financial reports</strong><small>Open your financial reporting workspace</small></span><span class="sh-action__arrow" aria-hidden="true">↗</span></a>
            @else
                <a class="sh-action sh-action--blue" href="{{ route('stakeholders.reports.index') }}"><span class="sh-action__icon"><i class="fa fa-folder-open-o" aria-hidden="true"></i></span><span class="sh-action__copy"><strong>All reports</strong><small>Browse and manage monthly reports</small></span><span class="sh-action__arrow" aria-hidden="true">↗</span></a>
                @if($canSubmitReport)
                    <a class="sh-action sh-action--gold" href="{{ route('stakeholders.reports.create') }}"><span class="sh-action__icon"><i class="fa fa-plus" aria-hidden="true"></i></span><span class="sh-action__copy"><strong>Submit a report</strong><small>Start this month’s report</small></span><span class="sh-action__arrow" aria-hidden="true">↗</span></a>
                @endif
            @endif
            @if($isChapter && $chapter)
                <a class="sh-action sh-action--teal" href="{{ route('stakeholders.users.index') }}"><span class="sh-action__icon"><i class="fa fa-users" aria-hidden="true"></i></span><span class="sh-action__copy"><strong>Chapter members</strong><small>Connect with your student community</small></span><span class="sh-action__arrow" aria-hidden="true">↗</span></a>
                <a class="sh-action sh-action--rose" href="{{ route('stakeholders.alumni.index') }}"><span class="sh-action__icon"><i class="fa fa-graduation-cap" aria-hidden="true"></i></span><span class="sh-action__copy"><strong>Chapter alumni</strong><small>Stay close to your wider GSF family</small></span><span class="sh-action__arrow" aria-hidden="true">↗</span></a>
                <a class="sh-action sh-action--blue" href="{{ route('stakeholders.chapters.edit', $chapter->id) }}"><span class="sh-action__icon"><i class="fa fa-map-marker" aria-hidden="true"></i></span><span class="sh-action__copy"><strong>Chapter details</strong><small>Keep your campus information current</small></span><span class="sh-action__arrow" aria-hidden="true">↗</span></a>
            @endif
            @if($canManagePosts)
                <a class="sh-action sh-action--gold" href="{{ route('stakeholders.blog.posts.index') }}"><span class="sh-action__icon"><i class="fa fa-newspaper-o" aria-hidden="true"></i></span><span class="sh-action__copy"><strong>Posts &amp; stories</strong><small>Share news and updates from your area</small></span><span class="sh-action__arrow" aria-hidden="true">↗</span></a>
            @endif
            @if($canOpenAppraisal && ($appraisalAccess['my_appraisal'] ?? false))
                <a class="sh-action sh-action--teal" href="{{ route('stakeholders.appraisal.my') }}"><span class="sh-action__icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span><span class="sh-action__copy"><strong>Self appraisal</strong><small>Reflect on your work and growth</small></span><span class="sh-action__arrow" aria-hidden="true">↗</span></a>
            @endif
            @if($canOpenAppraisal && ($appraisalAccess['evaluations'] ?? false))
                <a class="sh-action sh-action--rose" href="{{ route('stakeholders.appraisal.evaluations') }}"><span class="sh-action__icon"><i class="fa fa-user-plus" aria-hidden="true"></i></span><span class="sh-action__copy"><strong>Evaluations</strong><small>Review your assigned officers</small></span><span class="sh-action__arrow" aria-hidden="true">↗</span></a>
            @endif
        </div>
    </section>

    <div class="sh-panel-grid">
        @unless($isFinance)
            <section class="sh-panel" aria-labelledby="sh-reviews-title">
                <div class="sh-panel__heading"><span class="sh-panel__symbol sh-panel__symbol--gold"><i class="fa fa-file-text-o" aria-hidden="true"></i></span><div><span class="sh-section__eyebrow">Reporting journey</span><h2 id="sh-reviews-title">Follow every review</h2></div></div>
                <p class="sh-panel__intro">Open reports at each stage and see what needs attention.</p>
                <div class="sh-review-list">
                    <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'zone_pending']) }}"><span class="sh-review-list__step">01</span><span><strong>Pending zonal review</strong><small>Reports awaiting zonal review</small></span><span aria-hidden="true">→</span></a>
                    <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'field_pending']) }}"><span class="sh-review-list__step">02</span><span><strong>Pending field review</strong><small>Reports awaiting field review</small></span><span aria-hidden="true">→</span></a>
                    <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'national_pending']) }}"><span class="sh-review-list__step">03</span><span><strong>Pending national review</strong><small>Waiting for the national team</small></span><span aria-hidden="true">→</span></a>
                    <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'national_approved']) }}"><span class="sh-review-list__step sh-review-list__step--done"><i class="fa fa-check" aria-hidden="true"></i></span><span><strong>Approved reports</strong><small>Completed national approvals</small></span><span aria-hidden="true">→</span></a>
                </div>
            </section>
        @endunless
        <section class="sh-panel" aria-labelledby="sh-leadership-title">
            <div class="sh-panel__heading"><span class="sh-panel__symbol sh-panel__symbol--blue"><i class="fa fa-heart-o" aria-hidden="true"></i></span><div><span class="sh-section__eyebrow">People to know</span><h2 id="sh-leadership-title">Leadership circle</h2></div></div>
            <p class="sh-panel__intro">Contact leaders connected to your assigned area.</p>
            @if($fieldPastor || $zonePastors->isNotEmpty())
                <div class="sh-leaders">
                    @if($fieldPastor)
                        <div class="sh-leader">
                            @if(filled($fieldPastor->avatar))<img src="{{ asset($fieldPastor->avatar) }}" alt="" loading="lazy">@else<span class="sh-leader__initials" aria-hidden="true">{{ avatarInitials($fieldPastor->name) }}</span>@endif
                            <div>
                            <span>Field pastor · {{ $field?->name }}</span><strong>{{ $fieldPastor->name }}</strong>
                            @if($fieldPastor->email)<a href="mailto:{{ $fieldPastor->email }}">{{ $fieldPastor->email }}</a>@endif
                            @if($fieldPastor->phone)<a href="tel:{{ preg_replace('/[^+\d]/', '', $fieldPastor->phone) }}">{{ $fieldPastor->phone }}</a>@endif
                            @if($fieldPastor->day && $fieldPastor->month)<small class="sh-leader__birthday">🎉 {{ \Carbon\Carbon::create(null, $fieldPastor->month, $fieldPastor->day)->format('F j') }}</small>@endif
                            </div>
                        </div>
                    @endif
                    @foreach($zonePastors as $pastor)
                        <div class="sh-leader">
                            @if(filled($pastor->avatar))<img src="{{ asset($pastor->avatar) }}" alt="" loading="lazy">@else<span class="sh-leader__initials" aria-hidden="true">{{ avatarInitials($pastor->name) }}</span>@endif
                            <div>
                            <span>Zonal pastor · {{ $zone?->name }}</span><strong>{{ $pastor->name }}</strong>
                            @if($pastor->email)<a href="mailto:{{ $pastor->email }}">{{ $pastor->email }}</a>@endif
                            @if($pastor->phone)<a href="tel:{{ preg_replace('/[^+\d]/', '', $pastor->phone) }}">{{ $pastor->phone }}</a>@endif
                            @if($pastor->day && $pastor->month)<small class="sh-leader__birthday">🎉 {{ \Carbon\Carbon::create(null, $pastor->month, $pastor->day)->format('F j') }}</small>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="sh-empty"><i class="fa fa-users" aria-hidden="true"></i><strong>No leadership contacts are listed here yet.</strong><span>Contacts will appear when leaders are assigned to your area.</span></div>
            @endif
        </section>
    </div>

    @if($canManagePosts)
        <section class="sh-section sh-publishing" aria-labelledby="sh-publishing-title">
            <div class="sh-section__heading"><div><span class="sh-section__eyebrow">Communication &amp; gatherings</span><h2 id="sh-publishing-title">Posts, programs &amp; events</h2></div><p>Recent updates and upcoming events from your area.</p></div>
            <div class="sh-publishing__grid">
                <article class="sh-publishing__card">
                    <div class="sh-publishing__head"><span class="sh-publishing__icon"><i class="fa fa-newspaper-o" aria-hidden="true"></i></span><div><h3>Blog posts</h3><p>News, reflections and stories</p></div><a href="{{ route('stakeholders.blog.posts.index') }}">View all <span aria-hidden="true">↗</span></a></div>
                    <div class="sh-publishing__list">
                        @forelse($dashboardPosts as $post)
                            <a href="{{ $post->status === 'published' ? route('blog.show', $post->slug) : route('stakeholders.blog.posts.index') }}"><span><strong>{{ $post->title }}</strong><small>Updated {{ $post->updated_at?->format('d M Y') }}</small></span><em class="sh-publishing__status sh-publishing__status--{{ $post->status }}">{{ ucfirst($post->status) }}</em></a>
                        @empty
                            <div class="sh-publishing__empty">Your blog posts will appear here as you create them.</div>
                        @endforelse
                    </div>
                    @if($canCreatePosts)<a class="sh-publishing__footer" href="{{ route('stakeholders.blog.posts.create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Write a post <span aria-hidden="true">→</span></a>@endif
                </article>
                <article class="sh-publishing__card">
                    <div class="sh-publishing__head"><span class="sh-publishing__icon sh-publishing__icon--gold"><i class="fa fa-calendar-o" aria-hidden="true"></i></span><div><h3>Programs &amp; events</h3><p>Gatherings and upcoming moments</p></div><a href="{{ route('stakeholders.events.posts.index') }}">View all <span aria-hidden="true">↗</span></a></div>
                    <div class="sh-publishing__list">
                        @forelse($dashboardEvents as $event)
                            <a href="{{ $event->status === 'published' ? route('events.public.show', $event->slug) : route('stakeholders.events.posts.index') }}"><span><strong>{{ $event->title }}</strong><small>{{ $event->event_starts_at?->format('d M Y · g:i A') ?: 'Date to be confirmed' }}@if($event->event_venue) · {{ $event->event_venue }}@endif</small></span><em class="sh-publishing__status sh-publishing__status--{{ $event->status }}">{{ ucfirst($event->status) }}</em></a>
                        @empty
                            <div class="sh-publishing__empty">Your programs and events will appear here as you add them.</div>
                        @endforelse
                    </div>
                    @if($canCreatePosts)<a class="sh-publishing__footer" href="{{ route('stakeholders.events.posts.create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Add an event <span aria-hidden="true">→</span></a>@endif
                </article>
            </div>
        </section>
    @endif

    @if($isChapter && $chapter)
        @php
            $zoneCampuses = $chapter->relatedByZone()->actualCampus()->orderBy('name')->get();
            $fieldCampuses = $chapter->relatedByField()->actualCampus()->with('zone')->orderBy('name')->get();
        @endphp
        <section class="sh-section sh-network" aria-labelledby="sh-network-title">
            <div class="sh-section__heading"><div><span class="sh-section__eyebrow">Better together</span><h2 id="sh-network-title">Your sister campuses</h2></div><p>Meet the other GSF communities around you.</p></div>
            <div class="sh-network__grid">
                <div class="sh-network__group">
                    <div class="sh-network__heading"><span class="sh-network__icon"><i class="fa fa-map-marker" aria-hidden="true"></i></span><div><h3>In your zone</h3><small>{{ $zone?->name ?: 'Nearby campuses' }}</small></div><span class="sh-network__count">{{ $zoneCampuses->count() }}</span></div>
                    <div class="sh-network__list">
                        @forelse($zoneCampuses as $campus)
                            <a href="{{ route('campus.single', $campus->slug) }}"><strong>{{ $campus->name }}</strong><small>{{ $campus->address ?: 'View campus details' }}</small>@if($campus->phone)<small>{{ $campus->phone }}</small>@endif<span aria-hidden="true">↗</span></a>
                        @empty
                            <div class="sh-network__empty">No other campuses are listed in this zone yet.</div>
                        @endforelse
                    </div>
                </div>
                <div class="sh-network__group">
                    <div class="sh-network__heading"><span class="sh-network__icon sh-network__icon--teal"><i class="fa fa-map-o" aria-hidden="true"></i></span><div><h3>In your field</h3><small>{{ $field?->name ?: 'Nearby campuses' }}</small></div><span class="sh-network__count">{{ $fieldCampuses->count() }}</span></div>
                    <div class="sh-network__list">
                        @forelse($fieldCampuses as $campus)
                            <a href="{{ route('campus.single', $campus->slug) }}"><strong>{{ $campus->name }}</strong><small>{{ $campus->zone?->name ?: 'Zone not listed' }} · {{ $campus->address ?: 'Address not listed' }}</small>@if($campus->phone)<small>{{ $campus->phone }}</small>@endif<span aria-hidden="true">↗</span></a>
                        @empty
                            <div class="sh-network__empty">No other campuses are listed in this field yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    @endif
</main>
@endsection
