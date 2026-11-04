@extends('layouts.dashboard')
@section('title', 'Blog Comments')
@section('active')<li class="breadcrumb-item">Blog Comments</li>@endsection
@section('content')
<div class="content-body blog-comments-admin">
    <header class="bca-hero">
        <div><span class="bca-kicker">GSF News · Community</span><h1>Comments</h1><p>Review conversations across the news network.</p></div>
        <div class="bca-hero-actions">
            @if($canManageSettings)<a href="{{ route('admin.blog.settings.index') }}"><i class="fa fa-cog" aria-hidden="true"></i> Comment settings</a>@endif
            @if($canClearAll && $allCommentsCount > 0)<button type="button" data-clear-comments><i class="fa fa-trash-o" aria-hidden="true"></i> Clear all comments</button>@endif
        </div>
    </header>

    @include('includes.alerts')

    <div class="bca-stats" aria-label="Comment totals">
        @foreach([
            ['key' => 'all', 'label' => 'All comments', 'icon' => 'fa-comments-o', 'hint' => 'Every response', 'tone' => 'blue'],
            ['key' => 'pending', 'label' => 'Awaiting review', 'icon' => 'fa-clock-o', 'hint' => 'Needs a decision', 'tone' => 'amber'],
            ['key' => 'approved', 'label' => 'Approved', 'icon' => 'fa-check-circle-o', 'hint' => 'Visible on posts', 'tone' => 'green'],
            ['key' => 'rejected', 'label' => 'Rejected', 'icon' => 'fa-ban', 'hint' => 'Hidden from readers', 'tone' => 'slate'],
        ] as $card)
            <a class="bca-stat bca-stat--{{ $card['tone'] }} {{ $activeStatus === ($card['key'] === 'all' ? '' : $card['key']) ? 'is-active' : '' }}" href="{{ route('admin.blog.comments.index', array_filter(['post_id' => $activePost?->id, 'status' => $card['key'] === 'all' ? null : $card['key']])) }}">
                <span class="bca-stat__top"><span>{{ $card['label'] }}</span><i class="fa {{ $card['icon'] }}" aria-hidden="true"></i></span>
                <strong>{{ number_format($summary[$card['key']]) }}</strong><small>{{ $card['hint'] }}</small>
            </a>
        @endforeach
    </div>

    <section class="bca-panel" aria-labelledby="bca-list-title">
        <div class="bca-panel__head">
            <div><span class="bca-kicker">Conversation queue</span><h2 id="bca-list-title">{{ $activeStatus ? ucfirst($activeStatus).' comments' : 'All comments' }}</h2>
                @if($activePost)<p>On <strong>{{ $activePost->title }}</strong> · <a href="{{ route('admin.blog.comments.index') }}">Clear post filter</a></p>@else<p>Select comments to apply an action to several at once.</p>@endif
            </div>
            <nav class="bca-tabs" aria-label="Filter comments">
                <a class="{{ $activeStatus === '' ? 'is-active' : '' }}" href="{{ route('admin.blog.comments.index', array_filter(['post_id' => $activePost?->id])) }}">All</a>
                @foreach(['pending', 'approved', 'rejected'] as $status)<a class="{{ $activeStatus === $status ? 'is-active' : '' }}" href="{{ route('admin.blog.comments.index', array_filter(['post_id' => $activePost?->id, 'status' => $status])) }}">{{ ucfirst($status) }}</a>@endforeach
            </nav>
        </div>

        <div class="bca-toolbar">
            <form id="blog-comment-bulk-form" method="POST" action="{{ route('admin.blog.comments.bulk-action') }}">
                @csrf
                <label class="sr-only" for="blog-comment-bulk-action">Bulk action</label>
                <select class="custom-select" id="blog-comment-bulk-action" name="action" required><option value="">Bulk actions</option><option value="approve">Approve</option><option value="reject">Reject</option><option value="delete">Delete</option></select>
                <button class="btn btn-primary" type="submit"><i class="fa fa-check" aria-hidden="true"></i> Apply</button>
                <span data-comment-selection>Choose comments below</span>
            </form>
            <span>{{ number_format($comments->total()) }} {{ \Illuminate\Support\Str::plural('comment', $comments->total()) }}</span>
        </div>

        <div class="table-responsive">
            <table class="bca-table">
                <thead><tr><th class="bca-check"><input type="checkbox" data-select-all-comments aria-label="Select all comments on this page"></th><th>Comment</th><th>Author</th><th>Post</th><th>Status</th><th>IP address</th><th>Submitted</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($comments as $comment)
                    <tr>
                        <td class="bca-check"><input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" form="blog-comment-bulk-form" data-comment-checkbox aria-label="Select comment by {{ $comment->name ?: 'GSF reader' }}"></td>
                        <td class="bca-comment"><a href="{{ route('admin.blog.comments.edit', $comment) }}">{{ \Illuminate\Support\Str::limit($comment->body, 170) }}</a><small>#{{ $comment->id }}</small></td>
                        <td class="bca-author">
                            <strong>{{ $comment->name ?: 'GSF reader' }}</strong>
                            @if($comment->email)<span>{{ $comment->email }}</span>@endif
                            @if($comment->chapter)<span>{{ $comment->chapter->name }}</span>@endif
                        </td>
                        <td class="bca-post">@if($comment->post)<a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank" rel="noopener noreferrer">{{ \Illuminate\Support\Str::limit($comment->post->title, 62) }} <i class="fa fa-external-link" aria-hidden="true"></i></a>@else<span class="text-muted">Post removed</span>@endif</td>
                        <td><span class="bca-status bca-status--{{ $comment->status }}">{{ ucfirst($comment->status) }}</span></td>
                        <td class="bca-ip">{{ $comment->ip_address ?: 'Not recorded' }}</td>
                        <td class="bca-date">{{ $comment->created_at?->format('d M Y') }}<small>{{ $comment->created_at?->format('g:i a') }}</small></td>
                        <td class="bca-actions"><div class="bca-actions__group">
                            <a class="bca-actions__edit" href="{{ route('admin.blog.comments.edit', $comment) }}" aria-label="Edit comment #{{ $comment->id }}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
                            @if($comment->status !== 'approved')<form method="POST" action="{{ route('admin.blog.comments.moderate', $comment) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="approved"><button class="bca-actions__approve" type="submit" aria-label="Approve comment #{{ $comment->id }}"><i class="fa fa-check" aria-hidden="true"></i> Approve</button></form>@endif
                            @if($comment->status !== 'rejected')<form method="POST" action="{{ route('admin.blog.comments.moderate', $comment) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="rejected"><button class="bca-actions__reject" type="submit" aria-label="Reject comment #{{ $comment->id }}"><i class="fa fa-ban" aria-hidden="true"></i> Reject</button></form>@endif
                            <form method="POST" action="{{ route('admin.blog.comments.destroy', $comment) }}" onsubmit="return confirm('Permanently delete this comment?')">@csrf @method('DELETE')<button class="bca-actions__delete" type="submit" aria-label="Delete comment #{{ $comment->id }}"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button></form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="bca-empty"><i class="fa fa-comments-o" aria-hidden="true"></i><strong>No comments here yet</strong><span>Choose another status to see more conversations.</span></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($comments->hasPages())<div class="bca-pagination">{{ $comments->links() }}</div>@endif
    </section>
</div>

@if($canClearAll && $allCommentsCount > 0)
<div class="bca-modal" data-clear-modal hidden>
    <div class="bca-modal__backdrop" data-clear-close></div>
    <section class="bca-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="bca-clear-title" tabindex="-1">
        <button class="bca-modal__close" type="button" data-clear-close aria-label="Close">×</button>
        <span class="bca-modal__icon"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
        <h2 id="bca-clear-title">Delete every comment?</h2>
        <p>This permanently removes all <strong>{{ number_format($allCommentsCount) }}</strong> comments across every post, including approved and pending comments.</p>
        <form method="POST" action="{{ route('admin.blog.comments.clear-all') }}" data-clear-form>
            @csrf @method('DELETE')
            <label for="bca-clear-confirmation">Type <strong>DELETE ALL COMMENTS</strong> to confirm</label>
            <input class="form-control" id="bca-clear-confirmation" name="confirmation" autocomplete="off" required>
            <div class="bca-modal__actions"><button class="btn btn-outline-secondary" type="button" data-clear-close>Cancel</button><button class="btn btn-danger" type="submit">Delete all comments</button></div>
        </form>
    </section>
</div>
@endif

<style>
.blog-comments-admin { --bca-ink:#17223b; --bca-muted:#718096; --bca-line:#e3e9f1; color:var(--bca-ink); }
.bca-hero { display:flex; align-items:center; justify-content:space-between; gap:22px; padding:29px 34px; border-radius:19px; color:#fff; background:linear-gradient(120deg,#172a51,#234b7d 70%,#306495); box-shadow:0 15px 35px rgba(24,49,87,.14); }
.bca-kicker { color:#2d75ac; font-size:10px; font-weight:850; letter-spacing:.14em; text-transform:uppercase; }.bca-hero .bca-kicker { color:#bcd8f4; }
.bca-hero h1 { margin:7px 0 4px; color:#fff; font-size:30px; font-weight:800; }.bca-hero p { margin:0; color:#d2e0ef; font-size:13px; }
.bca-hero-actions { display:flex; flex-wrap:wrap; gap:9px; }.bca-hero-actions a,.bca-hero-actions button { display:inline-flex; align-items:center; gap:9px; min-height:40px; padding:9px 14px; border:1px solid rgba(255,255,255,.35); border-radius:9px; color:#fff; background:rgba(255,255,255,.09); font-size:12px; font-weight:750; text-decoration:none; cursor:pointer; }.bca-hero-actions a:hover { color:#fff; background:rgba(255,255,255,.19); }.bca-hero-actions button[data-clear-comments] { border-color:#f7d4d6; color:#8c2835; background:#fff1f1; }
.bca-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:15px; margin:22px 0; }.bca-stat { display:flex; min-width:0; flex-direction:column; padding:20px 21px; border:1px solid var(--bca-line); border-radius:15px; background:#fff; box-shadow:0 6px 18px rgba(26,43,73,.035); text-decoration:none; transition:transform .2s ease,box-shadow .2s ease; }.bca-stat:hover { transform:translateY(-2px); box-shadow:0 12px 24px rgba(26,43,73,.09); }.bca-stat.is-active { outline:2px solid currentColor; outline-offset:-2px; }
.bca-stat__top { display:flex; align-items:center; justify-content:space-between; gap:10px; font-size:12px; font-weight:800; }.bca-stat__top i { display:grid; place-items:center; width:33px; height:33px; border-radius:10px; font-size:16px; }.bca-stat strong { margin:12px 0 4px; color:var(--bca-ink); font-size:35px; font-weight:800; line-height:1; }.bca-stat small { color:var(--bca-muted); font-size:11px; }.bca-stat--blue { color:#23599a; }.bca-stat--blue i { background:#eaf2ff; }.bca-stat--amber { color:#aa6915; }.bca-stat--amber i { background:#fff2d9; }.bca-stat--green { color:#16845e; }.bca-stat--green i { background:#e6f7ee; }.bca-stat--slate { color:#64748b; }.bca-stat--slate i { background:#edf1f5; }
.bca-panel { overflow:hidden; border:1px solid var(--bca-line); border-radius:17px; background:#fff; box-shadow:0 8px 24px rgba(26,43,73,.04); }.bca-panel__head { display:flex; align-items:end; justify-content:space-between; gap:20px; padding:24px 27px 20px; }.bca-panel h2 { margin:5px 0; font-size:22px; font-weight:800; }.bca-panel__head p { margin:0; color:var(--bca-muted); font-size:12px; line-height:1.5; }
.bca-tabs { display:flex; flex-wrap:wrap; gap:4px; padding:4px; border:1px solid var(--bca-line); border-radius:10px; background:#f7f9fc; }.bca-tabs a { padding:8px 12px; border-radius:7px; color:#65728a; font-size:12px; font-weight:750; text-decoration:none; }.bca-tabs a.is-active { color:#fff; background:#224d80; box-shadow:0 2px 8px rgba(28,68,117,.15); }
.bca-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:15px 27px; border-top:1px solid var(--bca-line); color:var(--bca-muted); background:#fbfcfe; font-size:12px; }#blog-comment-bulk-form { display:grid; grid-template-columns:minmax(150px,190px) max-content; align-items:center; gap:7px 9px; margin:0; }#blog-comment-bulk-form select { grid-column:1; width:100%; min-width:0; height:39px; font-size:12px; }#blog-comment-bulk-form .btn { grid-column:2; min-height:39px; font-size:12px; font-weight:750; white-space:nowrap; }#blog-comment-bulk-form [data-comment-selection] { grid-column:1/-1; font-size:11px; }
.bca-table { width:100%; min-width:1320px; border-collapse:collapse; table-layout:fixed; }.bca-table th { padding:13px; border-bottom:1px solid var(--bca-line); color:#69778f; background:#f8fafc; font-size:10px; font-weight:850; letter-spacing:.1em; text-align:left; text-transform:uppercase; }.bca-table td { padding:17px 13px; border-bottom:1px solid #edf0f4; color:#34435b; font-size:12px; line-height:1.5; vertical-align:top; }.bca-table tbody tr:hover { background:#fafcff; }.bca-table tbody tr:last-child td { border-bottom:0; }
.bca-table th:nth-child(2) { width:20%; }.bca-table th:nth-child(3) { width:14%; }.bca-table th:nth-child(4) { width:15%; }.bca-table th:nth-child(5) { width:8%; }.bca-table th:nth-child(6) { width:9%; }.bca-table th:nth-child(7) { width:9%; }.bca-table th:nth-child(8) { width:20%; }
.bca-check { width:42px; padding-left:18px!important; }.bca-check input { width:16px; height:16px; cursor:pointer; accent-color:#24558e; }.bca-comment a { display:block; color:#1d3151; font-size:13px; font-weight:750; line-height:1.5; text-decoration:none; white-space:pre-line; }.bca-comment a:hover { color:#2368a1; }.bca-comment small,.bca-author span,.bca-date small { display:block; margin-top:5px; color:var(--bca-muted); font-size:11px; font-weight:500; }.bca-author strong { display:block; color:#24334d; font-size:12px; }.bca-author span,.bca-ip { overflow-wrap:anywhere; }.bca-post a { color:#24578d; font-weight:700; text-decoration:none; }.bca-post a i { margin-left:3px; font-size:10px; }
.bca-status { display:inline-flex; padding:5px 9px; border-radius:999px; font-size:10px; font-weight:800; text-transform:uppercase; }.bca-status--pending { color:#926214; background:#fff0d2; }.bca-status--approved { color:#117548; background:#e0f5e8; }.bca-status--rejected { color:#7a4552; background:#f6e8eb; }.bca-date { white-space:nowrap; }.bca-actions { display:flex; flex-wrap:wrap; gap:4px 10px; }.bca-actions form { display:inline; margin:0; }.bca-actions a,.bca-actions button { padding:0; border:0; color:#255e99; background:none; font-size:11px; font-weight:750; text-decoration:none; cursor:pointer; }.bca-actions a:hover,.bca-actions button:hover { text-decoration:underline; }.bca-actions .bca-delete { color:#b33d4f; }.bca-empty { padding:50px 20px!important; text-align:center; }.bca-empty i,.bca-empty strong,.bca-empty span { display:block; }.bca-empty i { margin-bottom:10px; color:#aab8c9; font-size:30px; }.bca-empty strong { color:#253653; font-size:16px; }.bca-empty span { margin-top:5px; color:var(--bca-muted); }.bca-pagination { display:flex; justify-content:center; padding:18px; }
.bca-modal[hidden] { display:none; }.bca-modal { position:fixed; z-index:3000; inset:0; display:grid; place-items:center; padding:20px; }.bca-modal__backdrop { position:absolute; inset:0; background:rgba(13,27,49,.62); backdrop-filter:blur(3px); }.bca-modal__dialog { position:relative; z-index:1; width:min(100%,500px); padding:30px; border-radius:18px; background:#fff; box-shadow:0 30px 90px rgba(10,24,47,.32); }.bca-modal__close { position:absolute; top:14px; right:17px; border:0; background:none; color:#6d7a8d; font-size:26px; }.bca-modal__icon { display:grid; place-items:center; width:44px; height:44px; border-radius:12px; color:#a52338; background:#ffe9eb; font-size:20px; }.bca-modal__dialog h2 { margin:17px 0 7px; color:#1b2a43; font-size:24px; }.bca-modal__dialog p { color:#617087; line-height:1.6; }.bca-modal__dialog label { display:block; margin:18px 0 7px; font-weight:700; }.bca-modal__actions { display:flex; justify-content:flex-end; gap:9px; margin-top:20px; }
.bca-actions { display:table-cell; min-width:250px; }
.bca-actions__group { display:flex; flex-wrap:wrap; align-items:center; gap:6px; }
.bca-actions form { display:inline-flex; margin:0; }
.bca-actions a,.bca-actions button { display:inline-flex; align-items:center; justify-content:center; gap:5px; min-height:32px; margin:0; padding:6px 9px; border:1px solid transparent; border-radius:7px; font-size:11px; font-weight:800; line-height:1; text-decoration:none; white-space:nowrap; cursor:pointer; transition:background .2s ease,border-color .2s ease,transform .2s ease; }
.bca-actions a:hover,.bca-actions button:hover { transform:translateY(-1px); text-decoration:none; }
.bca-actions a:focus-visible,.bca-actions button:focus-visible { outline:3px solid rgba(42,111,180,.25); outline-offset:2px; }
.bca-actions .bca-actions__edit { border-color:#224d80; color:#fff; background:#224d80; }.bca-actions .bca-actions__edit:hover { color:#fff; background:#163a66; }
.bca-actions .bca-actions__approve { border-color:#b7e1ca; color:#157247; background:#edf9f1; }.bca-actions .bca-actions__approve:hover { border-color:#77c49b; background:#dff3e7; }
.bca-actions .bca-actions__reject { border-color:#ead8ad; color:#8b6821; background:#fff8e7; }.bca-actions .bca-actions__reject:hover { border-color:#d3b972; background:#fff0c6; }
.bca-actions .bca-actions__delete { border-color:#f0cdd3; color:#a83d50; background:#fff2f3; }.bca-actions .bca-actions__delete:hover { border-color:#df9cab; background:#ffe5e9; }
@media(max-width:1120px) { .bca-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }.bca-hero { align-items:flex-start; flex-direction:column; } }@media(max-width:640px) { .bca-stats { gap:9px; }.bca-stat { padding:15px; }.bca-stat strong { font-size:28px; }.bca-hero { padding:23px; }.bca-panel__head { align-items:flex-start; flex-direction:column; padding:21px; }.bca-toolbar { align-items:flex-start; flex-direction:column; padding:14px 21px; }#blog-comment-bulk-form { grid-template-columns:minmax(150px,1fr) max-content; width:100%; } }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.querySelector('[data-select-all-comments]');
    const checkboxes = [...document.querySelectorAll('[data-comment-checkbox]')];
    const selection = document.querySelector('[data-comment-selection]');
    const bulkForm = document.getElementById('blog-comment-bulk-form');
    const bulkAction = document.getElementById('blog-comment-bulk-action');
    const refreshSelection = () => {
        const count = checkboxes.filter(box => box.checked).length;
        if (selectAll) {
            selectAll.checked = checkboxes.length > 0 && count === checkboxes.length;
            selectAll.indeterminate = count > 0 && count < checkboxes.length;
        }
        selection.textContent = count ? `${count} selected` : 'Choose comments below';
    };
    if (selectAll) selectAll.addEventListener('change', function () { checkboxes.forEach(box => { box.checked = selectAll.checked; }); refreshSelection(); });
    checkboxes.forEach(box => box.addEventListener('change', refreshSelection));
    bulkForm.addEventListener('submit', function (event) {
        if (!checkboxes.some(box => box.checked)) { event.preventDefault(); alert('Select at least one comment.'); return; }
        if (bulkAction.value === 'delete' && !confirm('Permanently delete the selected comments?')) event.preventDefault();
    });

    const modal = document.querySelector('[data-clear-modal]');
    if (!modal) return;
    const openButton = document.querySelector('[data-clear-comments]');
    const confirmation = document.getElementById('bca-clear-confirmation');
    const closeModal = () => { modal.hidden = true; document.body.style.overflow = ''; openButton.focus(); };
    openButton.addEventListener('click', function () { modal.hidden = false; document.body.style.overflow = 'hidden'; confirmation.value = ''; confirmation.focus(); });
    modal.querySelectorAll('[data-clear-close]').forEach(button => button.addEventListener('click', closeModal));
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && !modal.hidden) closeModal(); });
    modal.querySelector('[data-clear-form]').addEventListener('submit', function (event) {
        if (confirmation.value.trim() !== 'DELETE ALL COMMENTS') { event.preventDefault(); confirmation.setCustomValidity('Type DELETE ALL COMMENTS exactly.'); confirmation.reportValidity(); return; }
        if (!confirm('Final confirmation: permanently delete every blog comment?')) event.preventDefault();
    });
    confirmation.addEventListener('input', () => confirmation.setCustomValidity(''));
});
</script>
@endpush
@endsection
