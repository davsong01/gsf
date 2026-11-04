<div class="col-12">
    <div class="form-group conference-banner-field">
        <label for="banner">Conference banner</label>
        <label class="conference-banner-dropzone" for="banner" data-banner-dropzone tabindex="0" role="button" aria-describedby="banner-upload-help banner-upload-status">
            <img class="conference-banner-preview" data-banner-preview src="{{ $bannerUrl ?? '' }}" alt="Conference banner preview" @if(empty($bannerUrl)) hidden @endif>
            <span class="conference-banner-icon" aria-hidden="true"><i class="fa fa-cloud-upload"></i></span>
            <strong data-banner-prompt>{{ !empty($bannerUrl) ? 'Drop a new banner here or click to replace it' : 'Drop a banner here or click to choose one' }}</strong>
            <span>JPG, PNG, WebP or GIF, up to 5 MB</span>
        </label>
        <input class="conference-banner-input" type="file" name="ban" id="banner" accept="image/jpeg,image/png,image/webp,image/gif">
        <small id="banner-upload-help" class="text-muted">The banner appears on the conference page.@if(!empty($bannerUrl)) Leave this empty to keep the current image.@endif</small>
        <small id="banner-upload-status" class="d-block {{ $errors->has('ban') ? 'text-danger' : 'text-muted' }}" data-banner-status aria-live="polite">{{ $errors->first('ban') }}</small>
    </div>
</div>

<style>
.conference-banner-field > label:first-child { display:block; margin-bottom:8px; }
.conference-banner-dropzone { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:9px; min-height:190px; padding:22px; border:2px dashed #a9c5e1; border-radius:12px; background:#f6faff; color:#31537d; text-align:center; cursor:pointer; transition:background .2s,border-color .2s; }
.conference-banner-dropzone:hover,.conference-banner-dropzone:focus,.conference-banner-dropzone.is-dragging { background:#eaf4ff; border-color:#3778b4; outline:none; }
.conference-banner-dropzone strong { font-size:15px; }
.conference-banner-dropzone > span:last-child { font-size:12px; }
.conference-banner-icon { display:grid; place-items:center; width:44px; height:44px; border-radius:50%; background:#e3f0fb; color:#2872ad; font-size:20px; }
.conference-banner-preview { display:block; max-width:100%; max-height:260px; border-radius:8px; object-fit:contain; }
.conference-banner-preview[hidden],.conference-banner-preview:not([hidden]) ~ .conference-banner-icon { display:none; }
.conference-banner-input { position:absolute; width:1px; height:1px; opacity:0; overflow:hidden; }
</style>

<script>
(function () {
    var zone = document.querySelector('[data-banner-dropzone]');
    var input = document.querySelector('#banner');
    var preview = document.querySelector('[data-banner-preview]');
    var prompt = document.querySelector('[data-banner-prompt]');
    var status = document.querySelector('[data-banner-status]');
    var savedUrl = preview.getAttribute('src');
    var previewUrl = null;

    function selectFile(file) {
        if (!file) return;
        if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            input.value = '';
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = null;
            preview.src = savedUrl || '';
            preview.hidden = !savedUrl;
            prompt.textContent = savedUrl ? 'Drop a new banner here or click to replace it' : 'Drop a banner here or click to choose one';
            status.textContent = 'Choose a JPG, PNG, WebP or GIF image under 5 MB.';
            status.classList.add('text-danger');
            return;
        }
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = URL.createObjectURL(file);
        preview.src = previewUrl;
        preview.hidden = false;
        prompt.textContent = 'Drop another banner here or click to change it';
        status.classList.remove('text-danger');
        status.textContent = file.name + ' selected. Save to upload it.';
    }

    input.addEventListener('change', function () { selectFile(input.files[0]); });
    zone.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            input.click();
        }
    });
    ['dragenter', 'dragover'].forEach(function (type) {
        zone.addEventListener(type, function (event) { event.preventDefault(); zone.classList.add('is-dragging'); });
    });
    ['dragleave', 'drop'].forEach(function (type) {
        zone.addEventListener(type, function (event) { event.preventDefault(); zone.classList.remove('is-dragging'); });
    });
    zone.addEventListener('drop', function (event) {
        var file = event.dataTransfer.files[0];
        if (!file) return;
        if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            selectFile(file);
            return;
        }
        var transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
        selectFile(file);
    });
    window.addEventListener('beforeunload', function () { if (previewUrl) URL.revokeObjectURL(previewUrl); });
})();
</script>
