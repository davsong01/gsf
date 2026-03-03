@extends('layouts.conference')
@section('title', 'Hostel')
@section('active')
<li class="breadcrumb-item">Hostels</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Hostels for {{ $edition->conference_theme }}</h4>
                        @if(auth()->user()->conference_role == 'superadmin')
                            <a href="{{ route('hostels.create',['edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Add new Hostel</a>
                            <button style="" class="btn btn-dark mt-1" data-toggle="modal"  data-target="#hostel-merger">Hostel Merger</button>
                        @endif
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Level</th>
                                            <th>Fields</th>
                                            <th>Chapters</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hostels as $hostel)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                           <td>
                                                {{ $hostel->name }} <br>
                                               <strong>Capacity: </strong>{{$hostel->capacity}} <br>
                                               <strong>Allocation: </strong>{{$hostel->allocation}} <br>
                                               <strong>Allotted: </strong>{{$hostel->payments->count()}}
                                            </td>
                                            <td>{{ $hostel->type }}</td>
                                            <td>{{ $hostel->level }}</td>
                                            <td>
                                                @if($hostel->fields)
                                                @foreach ($hostel->fields as $field)
                                                   <small> {{" - " . $field->name . "\n"}} <br></small>
                                                @endforeach
                                                <br>
                                                @endif
                                            </td>
                                            <td>
                                                @if($hostel->chapters)
                                                @foreach ($hostel->chapters as $chapter)
                                                   <small> {{" - " . $chapter->name . "\n"}}<br></small>
                                                @endforeach
                                                <br>
                                                @endif
                                            </td>
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="View/Update hostel details" href="{{ route('hostels.edit', ['hostel'=>$hostel->id, 'edition'=>$edition->id, ]) }}"> <i class="bx bxs-edit actions"></i></a>

                                                @if(auth()->user()->conference_role == 'superadmin')
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Hostel" href="{{ route('hostels.delete', ['id'=>$hostel->id,'edition'=>$edition->id]) }}"> <i style="padding: 5px;" class="fa fa-trash"></i></a>
                                                @endif
                                            </td>

                                        </tr>

                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Zero configuration table -->
</div>
<div class="modal" id="hostel-merger">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal body -->
            <div class="modal-body">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <form action="{{ route('hostels.merge',['edition'=>$edition->id]) }}" method="POST">
                                @csrf
                                <!-- Deallocate Dropdown -->
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="deallocate">Select Hostel to Deallocate</label>
                                        <select class="form-control" name="deallocate" id="deallocate" required>
                                            <option value="">-- Select Hostel to Deallocate --</option>
                                            @foreach($hostelsToMerge as $hostel)
                                                <?php $remaining = $hostel->capacity - $hostel->payments->count(); ?>
                                                <option value="{{ $hostel->id }}" data-remaining="{{ $remaining }}">
                                                    {{ $hostel->name }} ({{ $hostel->allocation }} Allocated | {{ $remaining }} Remaining)
                                                </option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                                <!-- Allocate Dropdown (Initially Hidden) -->
                                <div class="col-md-12 col-sm-12" id="allocateContainer" style="display:none;">
                                    <fieldset class="form-group">
                                        <label for="allocate">Select Hostel to Allocate</label>
                                        <select class="form-control" name="allocate" id="allocate" required>
                                            <option value="">-- Select Hostel to Allocate --</option>
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-md-12 col-sm-12" id="number" style="display: none;">
                                    <fieldset class="form-group">
                                        <label for="amount">Amount</label>
                                        <input type="number" class="form-control" id="amount" min="1" name="amount" value="{{ old('amount') }}" placeholder="Enter amount">
                                    </fieldset>
                                </div>

                                <div class="col-md-12 col-sm-12">
                                    <button class="btn btn-primary" style="width:100%" type="submit">Update</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    $('#deallocate').on('change', function () {
    var hostelId = $(this).val();

    if (hostelId) {
        $.ajax({
            url: '{{ route("get.available.hostels", ["edition" => $edition->id]) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                deallocated_hostel_id: hostelId,
                edition_id: {{ $edition->id }}
            },
            success: function (response) {

                if (response.status && response.hostels.length > 0) {
                    let options = '<option value="">-- Select Hostel to Allocate --</option>';
                    response.hostels.forEach(function (hostel) {
                        let remaining = hostel.capacity - hostel.allocation;
                        options += `<option value="${hostel.id}" data-remaining="${remaining}">
                                        ${hostel.name} (${hostel.allocation} Allocated | ${remaining} Remaining)
                                    </option>`;
                    });

                    $('#allocate').html(options);
                    $('#allocateContainer').slideDown();

                    // ADD required
                    $('#allocate').attr('required', true);

                    $('#number').slideDown();
                    $('#amount').val('').removeAttr('max');

                } else {

                    $('#allocateContainer').hide();
                    $('#number').hide();
                    $('#allocate').html('<option value="">No available hostels</option>');

                    // REMOVE required because it's hidden
                    $('#allocate').removeAttr('required');

                    $('#amount').val('').removeAttr('max');
                }
            }
        });
    } else {
        $('#allocateContainer').hide();
        $('#allocate').removeAttr('required');
        }
    });


    $('#allocate').on('change', function () {
        let remaining = $('option:selected', this).data('remaining');

        if (remaining && remaining > 0) {
            $('#amount').val(remaining);
            $('#amount').attr('max', remaining);
        } else {
            $('#amount').val('');
            $('#amount').removeAttr('max');
        }
    });
});

</script>
@endsection
