@extends('layouts.dashboard')
@section('title', 'Blog Categories')
@section('active')<li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">Posts</a></li><li class="breadcrumb-item active">Categories</li>@endsection
@section('content')
<div class="content-body taxonomy-page">
    <header class="taxonomy-hero">
        <div class="taxonomy-hero__copy"><span class="taxonomy-eyebrow">GSF NEWS · CONTENT LIBRARY</span><h1>Categories</h1><p>Give every story a clear place in the news library.</p></div>
        <a class="taxonomy-hero__link" href="{{ route('admin.blog.posts.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to posts</a>
    </header>
    @include('includes.alerts')
    <div class="taxonomy-summary">
        <div class="taxonomy-summary__icon"><i class="fa fa-folder-open-o" aria-hidden="true"></i></div>
        <div><span>Categories</span><strong>{{ number_format($categories->count()) }}</strong></div>
        <small>{{ number_format($categories->sum('posts_count')) }} posts organized in the library.</small>
    </div>
    <div class="taxonomy-layout">
        <section class="taxonomy-card taxonomy-create">
            <div class="taxonomy-card__heading"><span class="taxonomy-card__icon"><i class="fa fa-folder-plus" aria-hidden="true"></i></span><div><h2>Add a category</h2><p>Group stories under a shared subject.</p></div></div>
            <form method="POST" action="{{ route('admin.blog.categories.store') }}">@csrf
                <div class="form-group"><label for="blog-category-name">Category name</label><input id="blog-category-name" class="form-control" name="name" maxlength="120" required value="{{ old('name') }}" placeholder="e.g. Campus News">@error('name')<small class="taxonomy-error">{{ $message }}</small>@enderror</div>
                <div class="form-group"><label for="blog-category-description">Description <span>(optional)</span></label><textarea id="blog-category-description" class="form-control" name="description" rows="4" placeholder="A short explanation of this category">{{ old('description') }}</textarea>@error('description')<small class="taxonomy-error">{{ $message }}</small>@enderror</div>
                <button class="taxonomy-submit" type="submit"><i class="fa fa-plus" aria-hidden="true"></i> Create category</button>
            </form>
            <div class="taxonomy-note"><i class="fa fa-lightbulb-o" aria-hidden="true"></i><span>Categories with posts attached cannot be deleted.</span></div>
        </section>
        <section class="taxonomy-card taxonomy-list">
            <div class="taxonomy-list__heading"><div><span class="taxonomy-eyebrow">STORY DIRECTORY</span><h2>All categories</h2></div><span class="taxonomy-count">{{ number_format($categories->count()) }} total</span></div>
            <div class="taxonomy-table-wrap"><table class="taxonomy-table"><thead><tr><th>Category</th><th>Slug</th><th>Posts</th><th><span class="sr-only">Actions</span></th></tr></thead><tbody>
                @forelse($categories as $category)
                    <tr><td><span class="taxonomy-term"><i class="fa fa-folder-o" aria-hidden="true"></i><span><strong>{{ $category->name }}</strong>@if($category->description)<small>{{ \Illuminate\Support\Str::limit($category->description, 80) }}</small>@endif</span></span></td><td><code>{{ $category->slug }}</code></td><td><span class="taxonomy-usage">{{ number_format($category->posts_count) }} {{ \Illuminate\Support\Str::plural('post', $category->posts_count) }}</span></td><td class="taxonomy-actions"><button class="taxonomy-edit" type="button" data-edit-category data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-description="{{ $category->description }}" data-edit-url="{{ route('admin.blog.categories.update', ['category' => '__ID__']) }}"><i class="fa fa-pencil" aria-hidden="true"></i><span>Edit</span></button> @if($category->posts_count === 0)<form method="POST" action="{{ route('admin.blog.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')<button type="submit" aria-label="Delete {{ $category->name }}"><i class="fa fa-trash-o" aria-hidden="true"></i><span>Delete</span></button></form>@else<span class="taxonomy-in-use"><i class="fa fa-link" aria-hidden="true"></i> In use</span>@endif</td></tr>
                @empty
                    <tr><td colspan="4"><div class="taxonomy-empty"><i class="fa fa-folder-open-o" aria-hidden="true"></i><strong>No categories yet</strong><span>Create a category to organize your stories.</span></div></td></tr>
                @endforelse
            </tbody></table></div>
        </section>
    </div>
</div>
<div class="taxonomy-modal" data-taxonomy-modal hidden>
    <div class="taxonomy-modal__backdrop" data-modal-close></div>
    <section class="taxonomy-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="taxonomy-modal-title" tabindex="-1">
        <button class="taxonomy-modal__close" type="button" data-modal-close aria-label="Close edit category dialog">×</button>
        <span class="taxonomy-modal__icon"><i class="fa fa-folder-o" aria-hidden="true"></i></span>
        <h2 id="taxonomy-modal-title">Edit category</h2><p>Update the category details used to organize stories.</p>
        <form method="POST" data-taxonomy-edit-form>@csrf @method('PUT')
            <label for="taxonomy-edit-name">Category name</label><input class="form-control" id="taxonomy-edit-name" name="name" maxlength="120" required>
            <label for="taxonomy-edit-description" class="taxonomy-modal__description-label">Description <span>(optional)</span></label><textarea class="form-control" id="taxonomy-edit-description" name="description" rows="4" maxlength="2000"></textarea>
            <small>The category URL slug updates automatically when you save.</small>
            <div class="taxonomy-modal__actions"><button class="taxonomy-modal__cancel" type="button" data-modal-close>Cancel</button><button class="taxonomy-submit" type="submit"><i class="fa fa-check" aria-hidden="true"></i> Save changes</button></div>
        </form>
    </section>
</div>
<style>
.taxonomy-page { --tx-ink:#172642; --tx-muted:#718096; --tx-line:#e5eaf1; --tx-blue:#214f83; color:var(--tx-ink); }
.taxonomy-hero { display:flex; align-items:center; justify-content:space-between; gap:20px; margin:0 0 22px; padding:30px 34px; border-radius:17px; color:#fff; background:radial-gradient(circle at 88% 15%,rgba(255,255,255,.13),transparent 25%),linear-gradient(120deg,#17375f,#24558b); box-shadow:0 14px 32px rgba(24,57,96,.17); }.taxonomy-eyebrow { color:#9eb8d6; font-size:10px; font-weight:850; letter-spacing:.15em; }.taxonomy-hero h1 { margin:8px 0 5px; color:#fff; font-size:30px; font-weight:850; }.taxonomy-hero p { margin:0; color:#cfdaea; font-size:13px; }.taxonomy-hero__link { display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border:1px solid rgba(255,255,255,.28); border-radius:9px; color:#fff; background:rgba(255,255,255,.08); font-size:12px; font-weight:750; text-decoration:none; white-space:nowrap; }.taxonomy-hero__link:hover { color:#fff; background:rgba(255,255,255,.16); }
.taxonomy-summary { display:flex; align-items:center; gap:14px; margin-bottom:20px; padding:17px 20px; border:1px solid var(--tx-line); border-radius:13px; background:#fff; }.taxonomy-summary__icon,.taxonomy-card__icon { display:grid; place-items:center; flex:0 0 auto; width:43px; height:43px; border-radius:12px; color:#245b91; background:#edf4fb; font-size:18px; }.taxonomy-summary div:nth-child(2) span,.taxonomy-summary div:nth-child(2) strong { display:block; }.taxonomy-summary div:nth-child(2) span { color:var(--tx-muted); font-size:11px; font-weight:700; }.taxonomy-summary div:nth-child(2) strong { margin-top:2px; font-size:23px; line-height:1.1; }.taxonomy-summary small { margin-left:auto; color:var(--tx-muted); font-size:11px; }
.taxonomy-layout { display:grid; grid-template-columns:minmax(255px,.75fr) minmax(0,1.65fr); align-items:start; gap:18px; }.taxonomy-card { overflow:hidden; border:1px solid var(--tx-line); border-radius:15px; background:#fff; box-shadow:0 7px 22px rgba(25,45,75,.045); }.taxonomy-create { padding:23px; }.taxonomy-card__heading { display:flex; align-items:center; gap:12px; margin-bottom:22px; }.taxonomy-card__icon { color:#735d22; background:#fbf5e6; }.taxonomy-card h2 { margin:0; color:var(--tx-ink); font-size:17px; font-weight:800; }.taxonomy-card__heading p { margin:4px 0 0; color:var(--tx-muted); font-size:11px; }.taxonomy-create label { margin-bottom:7px; color:#34435b; font-size:11px; font-weight:800; }.taxonomy-create label span { color:var(--tx-muted); font-weight:500; }.taxonomy-create .form-control { min-height:43px; border-color:#dce3ed; border-radius:8px; box-shadow:none; font-size:12px; }.taxonomy-create .form-control:focus { border-color:#5381b3; box-shadow:0 0 0 3px rgba(44,96,150,.1); }.taxonomy-error { display:block; margin-top:6px; color:#bf3446; font-size:10px; }.taxonomy-submit { display:inline-flex; align-items:center; gap:8px; min-height:40px; padding:0 14px; border:0; border-radius:8px; color:#fff; background:var(--tx-blue); font-size:11px; font-weight:800; cursor:pointer; }.taxonomy-submit:hover { background:#163a65; }.taxonomy-note { display:flex; gap:8px; margin-top:22px; padding-top:15px; border-top:1px solid #edf0f4; color:var(--tx-muted); font-size:10px; line-height:1.5; }.taxonomy-note i { color:#ae8b2f; }
.taxonomy-list__heading { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:21px 23px 16px; }.taxonomy-list__heading .taxonomy-eyebrow { color:#7c8ca4; }.taxonomy-list__heading h2 { margin:5px 0 0; color:var(--tx-ink); font-size:18px; font-weight:850; }.taxonomy-count { padding:6px 10px; border-radius:999px; color:#315d8a; background:#eef4fa; font-size:10px; font-weight:800; white-space:nowrap; }.taxonomy-table { width:100%; border-collapse:collapse; }.taxonomy-table th { padding:11px 15px; border-top:1px solid #edf0f4; border-bottom:1px solid #edf0f4; color:#7b889b; background:#f8fafc; font-size:9px; font-weight:850; letter-spacing:.1em; text-align:left; text-transform:uppercase; }.taxonomy-table td { padding:13px 15px; border-bottom:1px solid #edf0f4; color:#52617a; font-size:11px; vertical-align:middle; }.taxonomy-table tbody tr:hover { background:#fbfcfe; }.taxonomy-table tbody tr:last-child td { border-bottom:0; }.taxonomy-term { display:inline-flex; align-items:center; gap:9px; }.taxonomy-term>i { color:#5681ae; }.taxonomy-term strong,.taxonomy-term small { display:block; }.taxonomy-term strong { color:#263650; font-size:12px; }.taxonomy-term small { max-width:240px; margin-top:3px; color:var(--tx-muted); font-size:10px; line-height:1.4; }.taxonomy-table code { padding:4px 6px; border-radius:5px; color:#687992; background:#f3f5f8; font-size:10px; }.taxonomy-usage { color:#5e6d83; font-weight:700; white-space:nowrap; }.taxonomy-actions { text-align:right; white-space:nowrap; }.taxonomy-actions form { display:inline; }.taxonomy-actions button { display:inline-flex; align-items:center; gap:6px; min-height:31px; padding:5px 9px; border:1px solid #f0d6da; border-radius:7px; color:#a53c4d; background:#fff7f8; font-size:10px; font-weight:800; cursor:pointer; }.taxonomy-actions button:hover { border-color:#e6aeb8; background:#ffedef; }.taxonomy-in-use { color:#748399; font-size:10px; font-weight:700; }.taxonomy-in-use i { margin-right:4px; color:#668b74; }.taxonomy-empty { display:flex; align-items:center; flex-direction:column; gap:5px; padding:42px 15px; color:var(--tx-muted); text-align:center; }.taxonomy-empty i { margin-bottom:5px; color:#9aabc0; font-size:25px; }.taxonomy-empty strong { color:#33445f; font-size:13px; }.taxonomy-empty span { font-size:11px; }
@media(max-width:900px) { .taxonomy-layout { grid-template-columns:1fr; } }.taxonomy-list { min-width:0; }
@media(max-width:600px) { .taxonomy-hero { align-items:flex-start; flex-direction:column; padding:23px; }.taxonomy-summary { align-items:flex-start; flex-wrap:wrap; }.taxonomy-summary small { flex-basis:100%; margin:0 0 0 57px; }.taxonomy-table { min-width:580px; }.taxonomy-table-wrap { overflow-x:auto; }.taxonomy-list__heading { padding:18px; } }
.taxonomy-actions .taxonomy-edit { border-color:#d9e5f1; color:#315d8a; background:#f3f7fb; }.taxonomy-actions .taxonomy-edit:hover { border-color:#aac2db; background:#eaf2fa; }
.taxonomy-modal[hidden] { display:none; }.taxonomy-modal { position:fixed; z-index:4000; inset:0; display:grid; place-items:center; padding:18px; }.taxonomy-modal__backdrop { position:absolute; inset:0; background:rgba(13,27,49,.62); backdrop-filter:blur(3px); }.taxonomy-modal__dialog { position:relative; z-index:1; width:min(100%,480px); max-height:calc(100vh - 36px); overflow:auto; padding:28px; border:1px solid #e4eaf1; border-radius:17px; background:#fff; box-shadow:0 28px 85px rgba(10,24,47,.3); }.taxonomy-modal__close { position:absolute; top:10px; right:15px; border:0; color:#7a879a; background:transparent; font-size:27px; cursor:pointer; }.taxonomy-modal__icon { display:grid; place-items:center; width:43px; height:43px; border-radius:12px; color:#245b91; background:#edf4fb; font-size:18px; }.taxonomy-modal__dialog h2 { margin:16px 0 4px; color:#172642; font-size:21px; font-weight:850; }.taxonomy-modal__dialog p,.taxonomy-modal__dialog form>small { color:#718096; font-size:11px; }.taxonomy-modal__dialog p { margin-bottom:20px; }.taxonomy-modal__dialog label { display:block; margin:0 0 7px; color:#34435b; font-size:11px; font-weight:800; }.taxonomy-modal__dialog input,.taxonomy-modal__dialog textarea { border-color:#dce3ed; border-radius:8px; box-shadow:none; font-size:12px; }.taxonomy-modal__dialog input { min-height:43px; }.taxonomy-modal__description-label { margin-top:15px!important; }.taxonomy-modal__description-label span { color:#718096; font-weight:500; }.taxonomy-modal__dialog form>small { display:block; margin-top:8px; }.taxonomy-modal__actions { display:flex; justify-content:flex-end; gap:9px; margin-top:22px; }.taxonomy-modal__cancel { min-height:40px; padding:0 13px; border:1px solid #dce3ed; border-radius:8px; color:#52617a; background:#fff; font-size:11px; font-weight:750; cursor:pointer; }
.taxonomy-modal__actions .taxonomy-submit { color:#fff; background:#214f83; }.taxonomy-modal__actions .taxonomy-submit:hover { color:#fff; background:#173b66; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.querySelector('[data-taxonomy-modal]');
    const form = modal.querySelector('[data-taxonomy-edit-form]');
    const name = document.getElementById('taxonomy-edit-name');
    const description = document.getElementById('taxonomy-edit-description');
    let returnFocus = null;
    document.querySelectorAll('[data-edit-category]').forEach(function (button) {
        button.addEventListener('click', function () {
            returnFocus = button;
            name.value = button.dataset.name;
            description.value = button.dataset.description || '';
            form.action = button.dataset.editUrl.replace('__ID__', encodeURIComponent(button.dataset.id));
            modal.hidden = false;
            document.body.style.overflow = 'hidden';
            name.focus();
        });
    });
    function closeModal() { modal.hidden = true; document.body.style.overflow = ''; if (returnFocus) returnFocus.focus(); }
    modal.querySelectorAll('[data-modal-close]').forEach(button => button.addEventListener('click', closeModal));
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && !modal.hidden) closeModal(); });
});
</script>
@endsection
