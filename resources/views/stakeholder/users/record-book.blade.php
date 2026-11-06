@extends('layouts.stakeholderdashboard')

@section('title', 'Upload Record Book')
@section('item')<li class="breadcrumb-item"><a href="{{ route('stakeholders.users.index') }}">Chapter Members</a></li>@endsection
@section('active')<li class="breadcrumb-item">Upload Record Book</li>@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ versionedAsset('assets/css/stakeholder-members.css') }}">
<style>
    .record-dropzone{display:flex;min-height:260px;align-items:center;justify-content:center;flex-direction:column;gap:10px;padding:28px;border:2px dashed #b9c9da;border-radius:20px;background:#f7fafc;text-align:center;cursor:pointer;transition:.2s}
    .record-dropzone.is-over{border-color:#1678b7;background:#edf7ff}.record-dropzone i{font-size:38px;color:#1678b7}.record-dropzone strong{font-size:19px;color:#172b4d}.record-dropzone span{color:#67758b}.record-dropzone input{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none}
    .record-preview-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px;margin-top:16px}.record-preview{overflow:hidden;border:1px solid #dce4ec;border-radius:12px;background:white}.record-preview img{display:block;width:100%;height:105px;object-fit:cover}.record-preview span{display:block;padding:7px;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .record-status{margin-top:14px;padding:12px 15px;border-radius:10px;display:none}.record-status.is-visible{display:block}.record-status.is-success{background:#e8f6ee;color:#17633c}.record-status.is-error{background:#fff0ef;color:#a52e27}.record-upload-list{margin-top:28px}.record-upload-list ul{padding:0;list-style:none}.record-upload-list li{display:flex;justify-content:space-between;gap:15px;padding:13px 0;border-bottom:1px solid #e4e9ef}.record-upload-list small{color:#758198}
    @media(max-width:600px){.record-dropzone{min-height:210px;padding:20px}.record-upload-list li{flex-direction:column;gap:4px}}
</style>
@endsection

@section('content')
<div class="content-body members-workspace">
    <header class="members-hero members-hero--form">
        <div class="members-hero__copy"><span class="members-eyebrow"><i class="fa fa-book" aria-hidden="true"></i> Campus directory</span><h1>Upload Record Book</h1><p>Upload clear photos of your record book pages. You can select or drop several images at once.</p></div>
        <div class="members-hero__actions"><a class="members-button members-button--light" href="{{ route('stakeholders.users.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to members</a></div>
    </header>

    <div class="members-import-layout">
        <section class="members-form-panel" aria-labelledby="record-book-heading">
            <div class="members-form-panel__head"><span class="members-form-panel__icon"><i class="fa fa-book" aria-hidden="true"></i></span><div><span class="members-section-label">Book of Life</span><h2 id="record-book-heading">Add record book pages</h2><p>Images are saved securely for your campus. Upload up to 20 JPG, PNG or WebP images at a time, up to 10 MB each.</p></div></div>
            <form id="record-book-form" class="members-import-form" action="{{ route('stakeholders.users.record-book.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <label class="record-dropzone" id="record-dropzone" for="record-book-files">
                    <i class="fa fa-cloud-upload" aria-hidden="true"></i><strong>Drop record book images here</strong><span>or click to choose images from your device</span>
                    <input id="record-book-files" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
                </label>
                <div class="record-preview-grid" id="record-preview-grid" aria-live="polite"></div>
                <div class="record-status" id="record-status" role="status" aria-live="polite"></div>
                <div class="members-import-form__footer"><a class="members-button members-button--outline" href="{{ route('stakeholders.users.index') }}">Back to members</a><button class="members-button members-button--submit" id="record-upload-submit" type="submit" disabled><i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload images</button></div>
            </form>

            <div class="record-upload-list" id="record-upload-list">
                <h3>Recently uploaded pages</h3>
                @if($uploads->isEmpty())
                    <p id="record-empty-state">You haven’t uploaded any pages yet.</p>
                @else
                    <ul>
                        @foreach($uploads as $upload)
                            <li><strong>{{ $upload->original_name }}</strong><small>{{ number_format($upload->file_size / 1048576, 2) }} MB · Uploaded {{ $upload->created_at->format('M j, Y g:i A') }}</small></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>

        <aside class="members-import-help" aria-labelledby="record-book-help-heading">
            <span class="members-section-label">For best results</span><h2 id="record-book-help-heading">Take a clear photo of each page</h2>
            <p>Keep the page flat, make sure the writing is in focus, and avoid glare or shadows. Upload one image per page so each record can be reviewed clearly.</p>
        </aside>
    </div>
</div>
<script>
(() => {
    const form = document.getElementById('record-book-form');
    const input = document.getElementById('record-book-files');
    const dropzone = document.getElementById('record-dropzone');
    const preview = document.getElementById('record-preview-grid');
    const status = document.getElementById('record-status');
    const submit = document.getElementById('record-upload-submit');
    let selected = [];

    const showStatus = (message, type) => {
        status.textContent = message;
        status.className = `record-status is-visible is-${type}`;
    };
    const renderFiles = () => {
        preview.replaceChildren();
        selected.forEach((file) => {
            const card = document.createElement('div');
            card.className = 'record-preview';
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = '';
            const name = document.createElement('span');
            name.textContent = file.name;
            card.append(image, name);
            preview.append(card);
        });
        submit.disabled = selected.length === 0;
    };
    const acceptFiles = (files) => {
        selected = Array.from(files).filter((file) => /^image\/(jpeg|png|webp)$/.test(file.type)).slice(0, 20);
        renderFiles();
        if (!selected.length) showStatus('Choose JPG, PNG or WebP image files.', 'error');
        else if (files.length > 20) showStatus('Only the first 20 images were selected. Upload the rest in another batch.', 'error');
        else status.className = 'record-status';
    };

    input.addEventListener('change', () => acceptFiles(input.files));
    ['dragenter', 'dragover'].forEach((name) => dropzone.addEventListener(name, (event) => { event.preventDefault(); dropzone.classList.add('is-over'); }));
    ['dragleave', 'drop'].forEach((name) => dropzone.addEventListener(name, (event) => { event.preventDefault(); dropzone.classList.remove('is-over'); }));
    dropzone.addEventListener('drop', (event) => acceptFiles(event.dataTransfer.files));

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!selected.length) return;
        const payload = new FormData();
        selected.forEach((file) => payload.append('images[]', file));
        submit.disabled = true;
        submit.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Uploading…';
        try {
            const response = await fetch(form.action, { method: 'POST', body: payload, headers: { 'X-CSRF-TOKEN': form.querySelector('[name=_token]').value, 'Accept': 'application/json' } });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || Object.values(result.errors || {}).flat().join(' ') || 'Upload failed. Please try again.');
            showStatus(result.message, 'success');
            const list = document.getElementById('record-upload-list');
            let ul = list.querySelector('ul');
            if (!ul) { ul = document.createElement('ul'); list.append(ul); }
            document.getElementById('record-empty-state')?.remove();
            result.uploads.forEach((file) => {
                const row = document.createElement('li');
                const title = document.createElement('strong'); title.textContent = file.name;
                const details = document.createElement('small'); details.textContent = `${(file.size / 1048576).toFixed(2)} MB · Uploaded ${file.uploaded_at}`;
                row.append(title, details); ul.prepend(row);
            });
            selected = []; input.value = ''; renderFiles();
        } catch (error) {
            showStatus(error.message, 'error');
        } finally {
            submit.innerHTML = '<i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload images';
            submit.disabled = selected.length === 0;
        }
    });
})();
</script>
@endsection
