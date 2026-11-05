@extends('layouts.stakeholderdashboard')

@section('title', 'Import Members')
@section('item')<li class="breadcrumb-item"><a href="{{ route('stakeholders.users.index') }}">Chapter Members</a></li>@endsection
@section('active')<li class="breadcrumb-item">Import Members</li>@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ versionedAsset('assets/css/stakeholder-members.css') }}">
@endsection

@section('content')
<div class="content-body members-workspace">
    <header class="members-hero members-hero--form">
        <div class="members-hero__copy"><span class="members-eyebrow"><i class="fa fa-upload" aria-hidden="true"></i> Campus directory</span><h1>Import members</h1><p>Bring your fellowship records into the directory with a single spreadsheet.</p></div>
        <div class="members-hero__actions"><a class="members-button members-button--light" href="{{ route('stakeholders.users.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to members</a></div>
    </header>

    @if($errors->any())<div class="members-alert" role="alert"><strong>Please check the import.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="members-import-layout">
        <section class="members-form-panel" aria-labelledby="import-heading">
            <div class="members-form-panel__head"><span class="members-form-panel__icon"><i class="fa fa-file-excel-o" aria-hidden="true"></i></span><div><span class="members-section-label">Upload your list</span><h2 id="import-heading">Choose a spreadsheet</h2><p>New profiles will be assigned to your campus automatically.</p></div></div>
            <form class="members-import-form" action="{{ route('stakeholders.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="members-field"><label for="import-type">Who is in this file? <b>*</b></label><select id="import-type" class="form-control" name="type" required><option value="">Select member category</option><option value="0" @selected((string)old('type') === '0')>Current students</option><option value="1" @selected((string)old('type') === '1')>Alumni</option></select><small>Every row in this file will receive the selected category.</small></div>
                <div class="members-field"><label for="import-file">Excel or CSV file <b>*</b></label><label class="members-file-drop" for="import-file"><i class="fa fa-cloud-upload" aria-hidden="true"></i><strong>Select a file to import</strong><span>Choose an .xlsx or .csv file</span><input id="import-file" type="file" name="file" accept=".xlsx,.csv" required></label><span class="members-selected-file" id="import-file-name" aria-live="polite">No file selected</span></div>
                <div class="members-import-form__footer"><a class="members-button members-button--outline" href="{{ route('stakeholders.users.index') }}">Cancel</a><button class="members-button members-button--submit" type="submit"><i class="fa fa-upload" aria-hidden="true"></i> Import file</button></div>
            </form>
        </section>

        <aside class="members-import-help" aria-labelledby="import-guide-heading">
            <span class="members-section-label">Before you upload</span><h2 id="import-guide-heading">Prepare your spreadsheet</h2>
            <ol><li><strong>Start with the sample.</strong><span>Keep its column headings and follow the example rows.</span></li><li><strong>Check each record.</strong><span>Make sure names, phone numbers and email addresses are correct.</span></li><li><strong>Choose one category.</strong><span>Upload students and alumni in separate files.</span></li></ol>
            <div class="members-import-help__note"><i class="fa fa-lock" aria-hidden="true"></i><span>Each new account starts with the member’s phone number as its password.</span></div>
            <a class="members-button members-button--sample" href="{{ asset('frontend/exportsamples/userssample.xlsx') }}" download><i class="fa fa-download" aria-hidden="true"></i> Download sample spreadsheet</a>
        </aside>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
document.getElementById('import-file')?.addEventListener('change', function () {
    document.getElementById('import-file-name').textContent = this.files[0]?.name || 'No file selected';
});
</script>
@endsection
