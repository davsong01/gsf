@php
    $layout = $isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard';
@endphp

@extends($layout)

@section('title', 'Compliance Analytics')

@if($isAdmin)
    @section('item')
        <li class="breadcrumb-item">
            <a href="{{ route('reports.analytics') }}">Monthly Reports</a>
        </li>
    @endsection
@else
    @section('active')
        <li class="breadcrumb-item">Monthly Reports</li>
    @endsection
@endif

@section('content')
<style>
#reportTable {
    white-space: nowrap;
}
</style>
<div class="row mb-2">
    <div class="col-12">
        @include('admin.reports.analytics.filter')
    </div>
</div>
<div class="row mb-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0" id="reportTable">
                            {{-- {{dd($reports)}} --}}
                        <thead class="table-light">
                            <tr>
                                @foreach($reports['headers'] as $header)
                                    <th>{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($reports['rows'] as $row)
                                <tr>
                                    @foreach($row as $value)
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($reports['headers']) }}"
                                        class="text-center text-muted">
                                        No records found
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
$(document).ready(function () {

    function loadSubSections(sectionIds = [], selected = []) {

        let subSelect = $('.sub-sections-select');

        subSelect.prop('disabled', true).empty();

        if (!sectionIds || sectionIds.length === 0 || sectionIds.includes('all')) {
            subSelect.prop('disabled', false);
            return;
        }

        $.ajax({
            url: "{{ route('get.sub-sections') }}",
            type: "GET",
            dataType: "json",
            data: {
                sections: sectionIds || []
            },
            success: function (res) {

                subSelect.empty();

                if (!Array.isArray(res)) {
                    subSelect.prop('disabled', false);
                    return;
                }

                res.forEach(function (item) {

                    subSelect.append(
                        new Option(
                            item.name,
                            item.id,
                            (selected || []).includes(String(item.id)),
                            (selected || []).includes(String(item.id))
                        )
                    );

                });

                subSelect.prop('disabled', false);
            },
            error: function (xhr) {
                console.error('Subsection load failed:', xhr.responseText);
                subSelect.prop('disabled', false);
            }
        });
    }

    let sectionSelect = $('.sections-select');

    sectionSelect.on('change', function () {
        let selected = $(this).val() || [];
        loadSubSections(selected);
    });

    // initial load
    loadSubSections(
        sectionSelect.val() || [],
        @json(request('sub_sections', []))
    );

});
</script>
@endsection
