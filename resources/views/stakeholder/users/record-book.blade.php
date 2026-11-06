@extends('layouts.stakeholderdashboard')

@section('title', 'Upload Record Book')
@section('item')<li class="breadcrumb-item"><a href="{{ $memberIndexUrl }}">{{ $recordType === 'alumni' ? 'Chapter Alumni' : 'Chapter Members' }}</a></li>@endsection
@section('active')<li class="breadcrumb-item">Upload Record Book</li>@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ versionedAsset('assets/css/stakeholder-members.css') }}">
<style>
    .record-dropzone{display:flex;min-height:260px;align-items:center;justify-content:center;flex-direction:column;gap:10px;padding:28px;border:2px dashed #b9c9da;border-radius:20px;background:#f7fafc;text-align:center;cursor:pointer;transition:.2s}
    .record-dropzone.is-over{border-color:#1678b7;background:#edf7ff}.record-dropzone i{font-size:38px;color:#1678b7}.record-dropzone strong{font-size:19px;color:#172b4d}.record-dropzone span{color:#67758b}.record-dropzone input{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none}
    .record-preview-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;margin-top:16px}.record-preview{position:relative;overflow:hidden;border:1px solid #dce4ec;border-radius:12px;background:white}.record-preview img{display:block;width:100%;height:115px;object-fit:cover}.record-preview span{display:block;padding:8px 34px 8px 9px;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.record-preview-remove{position:absolute;top:7px;right:7px;display:grid;place-items:center;width:30px;height:30px;padding:0;border:0;border-radius:50%;background:rgba(20,35,55,.82);color:#fff;cursor:pointer}.record-preview-remove:hover,.record-preview-remove:focus{background:#b8323c;color:#fff;outline:2px solid #fff}
    .record-preview-type{display:flex;align-items:center;gap:5px;padding:0 8px 8px;font-size:11px;color:#68778d}.record-preview-type select{min-width:0;flex:1;padding:5px 7px;border:1px solid #d7e0e8;border-radius:7px;background:#fff;color:#263955;font-size:12px}
    .record-status{margin-top:14px;padding:12px 15px;border-radius:10px;display:none}.record-status.is-visible{display:block}.record-status.is-success{background:#e8f6ee;color:#17633c}.record-status.is-error{background:#fff0ef;color:#a52e27}
    .record-upload-list{margin-top:30px;padding:24px;border:1px solid #e1e8ef;border-radius:16px;background:#f8fafc}.record-upload-list__head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px}.record-upload-list__head h3{margin:0;color:#172b4d;font-size:18px;font-weight:700}.record-upload-count{padding:5px 10px;border-radius:20px;background:#e9f2f9;color:#2877a5;font-size:12px;font-weight:700;white-space:nowrap}.record-upload-list ul{margin:0;padding:0;list-style:none}.record-upload-list li{display:grid;grid-template-columns:54px minmax(0,1fr) auto auto;align-items:center;gap:13px;padding:10px;border:1px solid #e4e9ef;border-radius:10px;background:#fff}.record-upload-list li+li{margin-top:8px}.record-upload-thumb{width:54px;height:54px;object-fit:cover;border-radius:7px;background:#edf1f5}.record-upload-list li strong{min-width:0;color:#263955;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.record-upload-list li>small{grid-column:2 / -1;color:#758198;text-align:left;white-space:nowrap}.record-status-badge{display:inline-flex;align-items:center;padding:5px 9px;border-radius:20px;background:#fff3d8;color:#85620c;font-size:11px;font-weight:700;text-transform:capitalize;white-space:nowrap}.record-status-badge--processing{background:#e4f2fd;color:#17699a}.record-status-badge--extracted{background:#e3f5e9;color:#17633c}.record-status-badge--rejected{background:#fde8e7;color:#a3322e}.record-status-badge--duplicate{background:#eee8fb;color:#6543a0}.record-row-delete{border:0;border-radius:8px;background:#fff0ef;color:#ad3c35;padding:8px 10px;cursor:pointer}.record-row-delete:hover,.record-row-delete:focus{background:#ad3c35;color:#fff}.record-upload-empty{display:flex;align-items:center;gap:12px;padding:16px;border:1px dashed #ccd8e4;border-radius:11px;background:#fff;color:#67758b}.record-upload-empty i{color:#8296aa;font-size:18px}
    @media(max-width:700px){.record-dropzone{min-height:210px;padding:20px}.record-upload-list li{grid-template-columns:48px minmax(0,1fr) auto}.record-upload-thumb{width:48px;height:48px}.record-upload-list li small{grid-column:2 / 4;text-align:left}.record-row-delete{grid-column:3;grid-row:1}.record-status-badge{grid-column:2 / 4;justify-self:start}}
</style>
@endsection

@section('content')
<div class="content-body members-workspace">
    <header class="members-hero members-hero--form">
        <div class="members-hero__copy"><span class="members-eyebrow"><i class="fa fa-book" aria-hidden="true"></i> Campus directory</span><h1>Upload Record Book</h1><p>Upload clear photos of your record book pages. You can select or drop several images at once.</p></div>
        <div class="members-hero__actions"><a class="members-button members-button--light" href="{{ $memberIndexUrl }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to {{ $recordType === 'alumni' ? 'alumni' : 'members' }}</a></div>
    </header>

    <div class="members-import-layout">
        <section class="members-form-panel" aria-labelledby="record-book-heading">
            <div class="members-form-panel__head"><span class="members-form-panel__icon"><i class="fa fa-book" aria-hidden="true"></i></span><div><span class="members-section-label">Book of Life</span><h2 id="record-book-heading">Add record book pages</h2><p>Images are saved securely for your campus. Upload up to 20 JPG, PNG or WebP images at a time, up to 10 MB each.</p></div></div>
            <form id="record-book-form" class="members-import-form" action="{{ route('stakeholders.users.record-book.store', ['type' => $recordType]) }}" method="post" enctype="multipart/form-data" data-default-type="{{ $recordType }}">
                @csrf
                <label class="record-dropzone" id="record-dropzone" for="record-book-files">
                    <i class="fa fa-cloud-upload" aria-hidden="true"></i><strong>Drop record book images here</strong><span>or click to choose images from your device</span>
                    <input id="record-book-files" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
                </label>
                <div class="record-preview-grid" id="record-preview-grid" aria-live="polite"></div>
                <div class="record-status" id="record-status" role="status" aria-live="polite"></div>
                <div class="members-import-form__footer"><a class="members-button members-button--outline" href="{{ $memberIndexUrl }}">Back to {{ $recordType === 'alumni' ? 'alumni' : 'members' }}</a><button class="members-button members-button--submit" id="record-upload-submit" type="submit" disabled><i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload images</button></div>
            </form>

            <div class="record-upload-list" id="record-upload-list">
                <div class="record-upload-list__head"><h3>Recently uploaded pages</h3><span class="record-upload-count" id="record-upload-count">{{ $uploads->count() }} {{ Str::plural('page', $uploads->count()) }}</span></div>
                @if($uploads->isEmpty())
                    <div class="record-upload-empty" id="record-empty-state"><i class="fa fa-folder-open-o" aria-hidden="true"></i><span>You haven’t uploaded any pages yet. Uploaded images will appear here.</span></div>
                @else
                    <ul>
                        @foreach($uploads as $upload)
                            <li data-upload-id="{{ $upload->id }}">
                                <a href="{{ route('protected.download', ['file' => $upload->image_path]) }}" target="_blank" rel="noopener" aria-label="View uploaded page"><img class="record-upload-thumb" src="{{ route('protected.download', ['file' => $upload->image_path]) }}" alt="Record book page"></a>
                                <strong title="{{ $upload->original_name }}">{{ $upload->original_name }}</strong>
                                <div><span class="record-status-badge record-status-badge--{{ $upload->status }}">{{ $upload->status }}</span><small class="d-block text-capitalize">{{ $upload->record_type }}</small>@if($upload->status === 'extracted')<small class="d-block">{{ $upload->extracted_records_count ?? 0 }} records</small>@endif</div>
                                @if($upload->status !== 'extracted' && $upload->stakeholder_id === auth()->guard('stakeholder')->id())<button class="record-row-delete" type="button" data-delete-url="{{ route('stakeholders.users.record-book.destroy', $upload) }}" aria-label="Delete {{ $upload->original_name }}" title="Delete uploaded page"><i class="fa fa-trash" aria-hidden="true"></i></button>@else<span title="{{ $upload->status === 'extracted' ? 'Extracted images are kept for the campus record.' : 'Only the stakeholder who uploaded this image can delete it.' }}" aria-label="Image cannot be deleted"><i class="fa fa-lock" aria-hidden="true"></i></span>@endif
                                <small>{{ number_format($upload->file_size / 1048576, 2) }} MB · Uploaded {{ $upload->created_at->format('M j, Y g:i A') }}@if($upload->completed_at) · Completed {{ $upload->completed_at->format('M j, Y g:i A') }}@endif</small>
                            </li>
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
    const defaultType = form.dataset.defaultType || 'member';
    let selected = [];
    const types = new Map();
    const keyFor = (file) => `${file.name}:${file.size}:${file.lastModified}`;

    const showStatus = (message, type) => {
        status.textContent = message;
        status.className = `record-status is-visible is-${type}`;
    };
    const renderFiles = () => {
        preview.replaceChildren();
        selected.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'record-preview';
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = '';
            const name = document.createElement('span');
            name.textContent = file.name;
            name.title = file.name;
            const typeWrap = document.createElement('label');
            typeWrap.className = 'record-preview-type';
            typeWrap.append(document.createTextNode('Type'));
            const typeSelect = document.createElement('select');
            typeSelect.setAttribute('aria-label', `Record type for ${file.name}`);
            [['member', 'Member'], ['alumni', 'Alumni']].forEach(([value, label]) => {
                const option = document.createElement('option'); option.value = value; option.textContent = label; typeSelect.append(option);
            });
            typeSelect.value = types.get(keyFor(file)) || defaultType;
            typeSelect.addEventListener('change', () => types.set(keyFor(file), typeSelect.value));
            typeWrap.append(typeSelect);
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'record-preview-remove';
            remove.setAttribute('aria-label', `Remove ${file.name}`);
            remove.title = 'Remove image';
            remove.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>';
            remove.addEventListener('click', () => {
                selected.splice(index, 1);
                renderFiles();
            });
            card.append(image, name, typeWrap, remove);
            preview.append(card);
        });
        submit.disabled = selected.length === 0;
    };
    const acceptFiles = (files) => {
        selected = Array.from(files).filter((file) => /^image\/(jpeg|png|webp)$/.test(file.type)).slice(0, 20);
        selected.forEach((file) => { if (!types.has(keyFor(file))) types.set(keyFor(file), defaultType); });
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
        selected.forEach((file, index) => {
            payload.append(`images[${index}]`, file);
            payload.append(`types[${index}]`, types.get(keyFor(file)) || defaultType);
        });
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
                row.dataset.uploadId = file.id;
                const thumbnailLink = document.createElement('a');
                thumbnailLink.href = file.preview_url; thumbnailLink.target = '_blank'; thumbnailLink.rel = 'noopener'; thumbnailLink.setAttribute('aria-label', 'View uploaded page');
                const thumbnail = document.createElement('img'); thumbnail.className = 'record-upload-thumb'; thumbnail.src = file.preview_url; thumbnail.alt = 'Record book page'; thumbnailLink.append(thumbnail);
                const title = document.createElement('strong'); title.textContent = file.name; title.title = file.name;
                const statusCell = document.createElement('div');
                const badge = document.createElement('span'); badge.className = `record-status-badge record-status-badge--${file.status}`; badge.textContent = file.status; statusCell.append(badge);
                const typeBadge = document.createElement('small'); typeBadge.className = 'd-block text-capitalize'; typeBadge.textContent = file.record_type; statusCell.append(typeBadge);
                const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'record-row-delete'; remove.dataset.deleteUrl = file.delete_url; remove.setAttribute('aria-label', `Delete ${file.name}`); remove.title = 'Delete uploaded page'; remove.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i>';
                const details = document.createElement('small'); details.textContent = `${(file.size / 1048576).toFixed(2)} MB · Uploaded ${file.uploaded_at}`;
                row.append(thumbnailLink, title, statusCell, remove, details); ul.prepend(row);
            });
            const count = ul.querySelectorAll('li').length;
            document.getElementById('record-upload-count').textContent = `${count} ${count === 1 ? 'page' : 'pages'}`;
            selected = []; input.value = ''; renderFiles();
        } catch (error) {
            showStatus(error.message, 'error');
        } finally {
            submit.innerHTML = '<i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload images';
            submit.disabled = selected.length === 0;
        }
    });

    document.getElementById('record-upload-list').addEventListener('click', async (event) => {
        const button = event.target.closest('.record-row-delete');
        if (!button || !confirm('Delete this uploaded record book image?')) return;
        button.disabled = true;
        try {
            const response = await fetch(button.dataset.deleteUrl, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': form.querySelector('[name=_token]').value, 'Accept': 'application/json' } });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Could not delete this image.');
            button.closest('li').remove();
            const count = document.querySelectorAll('#record-upload-list ul li').length;
            document.getElementById('record-upload-count').textContent = `${count} ${count === 1 ? 'page' : 'pages'}`;
            if (!count) {
                document.querySelector('#record-upload-list ul')?.remove();
                const empty = document.createElement('div'); empty.id = 'record-empty-state'; empty.className = 'record-upload-empty'; empty.innerHTML = '<i class="fa fa-folder-open-o" aria-hidden="true"></i><span>You haven’t uploaded any pages yet. Uploaded images will appear here.</span>';
                document.getElementById('record-upload-list').append(empty);
            }
            showStatus(result.message, 'success');
        } catch (error) {
            showStatus(error.message, 'error'); button.disabled = false;
        }
    });
})();
</script>
@endsection
