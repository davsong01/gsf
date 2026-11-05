@extends('layouts.stakeholderdashboard')
@section('title', 'Campus Gallery')
@section('active')<li class="breadcrumb-item">Campus Gallery</li>@endsection

@section('extra_styles')
<style>
    .gallery-workspace { --gallery-navy:#17385d; --gallery-blue:#237b98; --gallery-gold:#d6a84b; }
    .gallery-hero { position:relative; overflow:hidden; display:flex; align-items:center; justify-content:space-between; gap:24px; margin-bottom:24px; padding:30px 34px; border-radius:22px; color:#fff; background:linear-gradient(125deg,var(--gallery-navy),var(--gallery-blue)); box-shadow:0 18px 42px rgba(23,56,93,.18); }
    .gallery-hero::after { position:absolute; right:-70px; bottom:-145px; width:300px; height:300px; border:1px solid rgba(255,255,255,.18); border-radius:50%; box-shadow:0 0 0 42px rgba(255,255,255,.04),0 0 0 84px rgba(255,255,255,.025); content:""; }
    .gallery-hero > * { position:relative; z-index:1; }
    .gallery-hero__eyebrow { display:block; margin-bottom:7px; color:#bde8f3; font-size:11px; font-weight:800; letter-spacing:.16em; text-transform:uppercase; }
    .gallery-hero h1 { margin:0 0 7px; color:#fff; font-size:30px; font-weight:800; }
    .gallery-hero p { max-width:650px; margin:0; color:rgba(255,255,255,.78); }
    .gallery-hero__count { flex:0 0 auto; min-width:112px; padding:14px 18px; border:1px solid rgba(255,255,255,.25); border-radius:16px; text-align:center; background:rgba(255,255,255,.09); backdrop-filter:blur(8px); }
    .gallery-hero__count strong { display:block; color:#fff; font-size:26px; line-height:1; }
    .gallery-hero__count span { display:block; margin-top:6px; color:#ccebf2; font-size:10px; font-weight:800; letter-spacing:.09em; text-transform:uppercase; }
    .gallery-panel { margin-bottom:24px; border:1px solid #dfe7ee; border-radius:20px; background:#fff; box-shadow:0 12px 34px rgba(25,52,78,.07); }
    .gallery-panel__head { padding:22px 25px 14px; }
    .gallery-panel__head h2 { margin:0 0 5px; color:#17385d; font-size:19px; font-weight:800; }
    .gallery-panel__head p { margin:0; color:#718096; font-size:13px; }
    .gallery-upload { display:grid; grid-template-columns:minmax(220px,.8fr) minmax(280px,1.2fr) auto; align-items:end; gap:16px; padding:8px 25px 25px; }
    .gallery-upload label { display:block; margin-bottom:7px; color:#324c68; font-size:12px; font-weight:700; }
    .gallery-upload .form-control { min-height:46px; border-color:#d7e1ea; border-radius:10px; }
    .gallery-upload button { height:46px; padding:0 22px; border:0; border-radius:11px; color:#fff; font-weight:800; background:#237b98; box-shadow:0 8px 18px rgba(35,123,152,.2); }
    .gallery-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:18px; padding:8px 25px 25px; }
    .gallery-card { overflow:hidden; border:1px solid #dfe7ee; border-radius:16px; background:#f9fbfc; }
    .gallery-card img { display:block; width:100%; aspect-ratio:4/3; object-fit:cover; background:#eaf0f4; }
    .gallery-card__body { padding:14px; }
    .gallery-card__body textarea { min-height:64px; resize:vertical; border-radius:9px; font-size:12px; }
    .gallery-card__actions { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:10px; }
    .gallery-card__actions button { border:0; border-radius:8px; padding:8px 11px; font-size:11px; font-weight:800; }
    .gallery-card__save { color:#fff; background:#237b98; }
    .gallery-card__delete { color:#9b3434; background:#fbeaea; }
    .gallery-empty { margin:8px 25px 25px; padding:38px 20px; border:1px dashed #ccd9e4; border-radius:16px; color:#708197; text-align:center; background:#f8fafc; }
    @media(max-width:991.98px){.gallery-upload{grid-template-columns:1fr 1fr}.gallery-upload button{grid-column:1/-1}.gallery-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:575.98px){.gallery-hero{align-items:flex-start; flex-direction:column; padding:25px 22px}.gallery-hero__count{min-width:95px}.gallery-upload,.gallery-grid{grid-template-columns:1fr;padding-inline:18px}.gallery-upload button{grid-column:auto}.gallery-panel__head{padding-inline:18px}}
</style>
@endsection

@section('content')
<div class="content-body gallery-workspace">
    <section class="gallery-hero">
        <div><span class="gallery-hero__eyebrow">{{ $chapter->name }}</span><h1>Campus Gallery</h1><p>Share fellowship moments with students and visitors. Add up to 20 clear photos, each with a short caption.</p></div>
        <div class="gallery-hero__count"><strong>{{ $photos->count() }}/20</strong><span>Photos used</span></div>
    </section>

    @if($photos->count() < 20)
    <section class="gallery-panel">
        <div class="gallery-panel__head"><h2>Add a fellowship photo</h2><p>JPG, PNG or WebP, up to 5 MB. Landscape photos generally look best.</p></div>
        <form class="gallery-upload" action="{{ route('stakeholders.gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div><label for="gallery-photo">Photo</label><input class="form-control @error('photo') is-invalid @enderror" id="gallery-photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" required>@error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div><label for="gallery-caption">Caption</label><input class="form-control @error('caption') is-invalid @enderror" id="gallery-caption" name="caption" maxlength="255" value="{{ old('caption') }}" placeholder="e.g. Students during our weekly Bible study" required>@error('caption')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <button type="submit"><i class="fa fa-plus mr-50" aria-hidden="true"></i>Add photo</button>
        </form>
    </section>
    @endif

    <section class="gallery-panel">
        <div class="gallery-panel__head"><h2>Published photos</h2><p>These photos appear on your public campus fellowship page.</p></div>
        @if($photos->isEmpty())
            <div class="gallery-empty"><i class="fa fa-picture-o fa-2x mb-1" aria-hidden="true"></i><br>No gallery photos yet. Add your first fellowship moment above.</div>
        @else
            <div class="gallery-grid">
                @foreach($photos as $photo)
                    <article class="gallery-card">
                        <img src="{{ versionedAsset($photo->image_path) }}" alt="{{ $photo->caption ?: $chapter->name.' fellowship photo' }}">
                        <div class="gallery-card__body">
                            <form action="{{ route('stakeholders.gallery.update', $photo) }}" method="POST">@csrf @method('PUT')<label class="sr-only" for="caption-{{ $photo->id }}">Photo caption</label><textarea class="form-control" id="caption-{{ $photo->id }}" name="caption" maxlength="255" placeholder="Add a caption" required>{{ $photo->caption }}</textarea><div class="gallery-card__actions"><button class="gallery-card__save" type="submit">Save caption</button></div></form>
                            <form class="gallery-card__actions" action="{{ route('stakeholders.gallery.destroy', $photo) }}" method="POST" onsubmit="return confirm('Remove this photo from the campus gallery?')">@csrf @method('DELETE')<small class="text-muted">Added {{ $photo->created_at->format('d M Y') }}</small><button class="gallery-card__delete" type="submit"><i class="fa fa-trash" aria-hidden="true"></i> Remove</button></form>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
