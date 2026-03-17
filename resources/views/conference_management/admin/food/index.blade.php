@extends('layouts.conference')
@section('title', 'Service Points')
@section('active')
<li class="breadcrumb-item">Service Points</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Service Points for {{ $edition->conference_theme }}</h4>
                        @if(auth()->user()->conference_role == 'superadmin')
                            <a href="{{ route('foods.create',['edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Add new Service Point</a>
                            <a href="{{ route('sp.auto.allocate',['edition'=>$edition->id]) }}" onclick="return confirm('Are you sure?')" class="btn btn-success mt-1">Auto Allocate ({{ $unallocatedSp }})</a>
                            <a href="{{ route('servicepoint.repair.allocation',['edition'=>$edition->id]) }}" onclick="return confirm('Are you sure?')" class="btn btn-info mt-1">Repair Service Point Allocation</a>
                            <button style="" class="btn btn-dark mt-1" data-toggle="modal"  data-target="#sp-merger">Service Point Merger</button>

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
                                            <th>Conference Plan</th>
                                            <th>Fields</th>
                                            <th>Chapters</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($foods as $food)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $food->name }} <br>
                                               <strong>Capacity: </strong>{{$food->capacity}} <br>
                                               <strong>Allocation: </strong>{{$food->allocation}} <br>
                                               <strong>Allotted to Payment: </strong>{{$food->payments->count()}}
                                            </td>

                                            <td>{{$food->level}}</td>
                                            <td>
                                                @if($food->fields)
                                                @foreach ($food->fields as $field)
                                                   <small> {{" - " . $field->name . "\n"}} <br></small>
                                                @endforeach
                                                <br>
                                                @endif

                                            </td>
                                            <td>
                                                @if($food->chapters)
                                                @foreach ($food->chapters as $chapter)
                                                   <small> {{" - " . $chapter->name . "\n"}}<br></small>
                                                @endforeach
                                                <br>
                                                @endif
                                            </td>


                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update food details" href="{{ route('foods.edit', ['food'=>$food->id,'edition'=>$edition->id]) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                            @if(auth()->user()->conference_role == 'superadmin')
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete food" href="{{ route('foods.delete', ['id'=>$food->id,'edition'=>$edition->id]) }}"> <i class="fa fa-trash"></i></
                                            </a>
                                            @endif
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
<div class="modal" id="sp-merger">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal body -->
            <div class="modal-body">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <form action="{{ route('servicepoint.merge',['edition'=>$edition->id]) }}" method="POST">
                                @csrf
                                <!-- Deallocate Dropdown -->
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="deallocate">Select Service Point to Deallocate</label>
                                        <select class="form-control" name="deallocate" id="deallocate" required>
                                            <option value="">-- Select Service Point to Deallocate --</option>
                                            @foreach($servicePointsToMerge as $sp)
                                                <?php $remaining = $sp->capacity - $sp->payments->count(); ?>
                                                <option value="{{ $sp->id }}" data-remaining="{{ $remaining }}">
                                                    {{ $sp->name }} ({{ $sp->allocation }} Allocated | {{ $remaining }} Remaining)
                                                </option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                                <!-- Allocate Dropdown (Initially Hidden) -->
                                <div class="col-md-12 col-sm-12" id="allocateContainer" style="display:none;">
                                    <fieldset class="form-group">
                                        <label for="allocate">Select Service Point to Allocate</label>
                                        <select class="form-control" name="allocate" id="allocate" required>
                                            <option value="">-- Select Service Point to Allocate --</option>
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
        var servicePointId = $(this).val();
        var remaining = $('option:selected', this).data('remaining');

        if (servicePointId) {
            $.ajax({
                url: '{{ route("get.available.service_point") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    deallocated_service_point_id: servicePointId,
                    edition_id: {{ $edition->id }}
                },
                success: function (response) {
                    if (response.status && response.foods.length > 0) {
                        let options = '<option value="">-- Select Service Point to Allocate --</option>';
                        response.foods.forEach(function (food) {
                            let remaining = food.capacity - food.payments_count;
                            options += `<option value="${food.id}" data-remaining="${remaining}">
                                            ${food.name} (${food.allocation} Allocated | ${remaining} Remaining)
                                        </option>`;
                        });

                        $('#allocate').html(options);
                        $('#allocateContainer').slideDown();

                        $('#number').slideDown();
                        $('#amount').val('').removeAttr('max'); // reset
                    } else {
                        $('#allocateContainer').hide();
                        $('#number').hide();
                        $('#allocate').html('<option value="">No available service point</option>');
                        $('#amount').val('').removeAttr('max');
                    }
                }
            });
        } else {
            $('#allocateContainer').hide();
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
