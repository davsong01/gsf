@extends($isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard')
@section('title', 'Bulk Edit Extracted Records')
@section('extra_styles')<link rel="stylesheet" href="{{ versionedAsset('assets/css/admin-user-extraction.css') }}">@endsection
@section('content')
<div class="content-body extraction-workspace"><header class="extraction-hero"><div class="extraction-hero__copy"><span class="extraction-kicker">Record book review · Batch #{{ $upload->id }}</span><h1>Edit selected records</h1><p>Update the selected extracted rows, then save them together.</p></div><a class="extraction-hero__link" href="{{ route($routeNames['index'], $upload) }}">Back to batch</a></header>
<form method="POST" action="{{ route($routeNames['bulk_update'], $upload) }}" class="extraction-bulk-edit">@csrf
@foreach($records as $record)@php($data = $record->data ?? [])<section class="extraction-review-panel"><h2>{{ $data['name'] ?? 'New record' }}</h2><div class="extraction-add-form">@foreach(['name'=>'Name','email'=>'Email','phone'=>'Phone','gender'=>'Gender','dob'=>'Date of birth','matric_year'=>'Matric year','graduation_year'=>'Graduation year','program'=>'Program','course'=>'Course','course_duration'=>'Course duration','notes'=>'Notes'] as $field=>$label)<label>{{ $label }}<input class="form-control" name="records[{{ $record->id }}][{{ $field }}]" value="{{ $data[$field] ?? '' }}" @if($field==='name') required @endif></label>@endforeach</div></section>@endforeach
<button class="extraction-button extraction-button--apply" type="submit">Save selected records</button></form></div>
@endsection
