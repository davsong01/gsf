@extends('layouts.conference')
@section('title', 'Update Participant')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('conference.participants', ['edition'=>$edition->id]) }}">Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Edit {{ $user->user->name }}</></li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="col-md-1">
                        <div class="media-left pr-0"><img style="width: 70px !important; border-radius: 50%;" class="mr-1" src="{{ asset($user->user->passport ? $user->user->passport : 'frontend/passports/avatar.jpg') }}" alt="avatar" height="20%">
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="card-header">
                            <h4 class="card-title">Update: {{ $user->user->name }}</h4> <small style="color:blue">(Change the gender for hostel and Service Points changes to reflect)</small>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('conference.participants.admin.update', ['edition'=>$edition->id,'id'=>$user->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-bordered" style="line-height: 0.4 !important;">
                                    <thead>
                                        <tr>
                                            <th>Field</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <th>Conference ID</th>
                                            <td>{{ $user->user->family_id }}</td>
                                        </tr>

                                        <tr>
                                            <th style="color:red; font-weight:bold;">Payment Status</th>
                                            <td>{{ $user->status }}</td>
                                        </tr>

                                        <tr>
                                            <th>Registration Status</th>
                                            <td>{{ $user->registration_status }}</td>
                                        </tr>

                                        <tr>
                                            <th>Payment Provider</th>
                                            <td>{{ $user->paymentprovider->name }}</td>
                                        </tr>

                                        <tr>
                                            <th>Transaction ID</th>
                                            <td>{{ $user->transid }}</td>
                                        </tr>

                                        <tr>
                                            <th>Amount Paid</th>
                                            <td>{{ number_format($user->amount_paid) }}</td>
                                        </tr>

                                        <tr>
                                            <th>Provider Charge</th>
                                            <td>{{ number_format($user->provider_charge) }}</td>
                                        </tr>

                                        <tr>
                                            <th>Total Amount Paid</th>
                                            <td>{{ number_format($user->total_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="color:red;">Payment Type</th>
                                            <td>{{ $user->level ?? $user->plan->title }}</td>
                                        </tr>
                                        @if(!empty($user->moderator?->name ))
                                        <tr>
                                            <th>Uploaded By</th>
                                            <td>{{ $user?->moderator?->name  }}</td>
                                        </tr>
                                        @endif
                                        
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <div id="paymentResponseBox"
                                    style="background:#f9f9f9; border:1px solid #ddd; border-radius:6px;
                                            min-height:325px; max-height:325px; overflow:auto;
                                            font-family: monospace; font-size: 13px; padding:10px;">
                                            <pre class="text-muted" style="font-family: monospace; font-size:13px; background:#f9f9f9; padding:10px; border-radius:6px; border:1px solid #ddd;">
                                            {{ json_encode($user->api_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                                            </pre>
                                </div>
                                <div class="d-flex justify-content-between mt-1" style="gap: 10px;">
                                        <button type="button" id="queryPaymentBtn" class="btn btn-info btn-sm w-50 me-2">
                                            Query Payment
                                        </button>
                                        @if($user->status != 'Complete')
                                        <button type="button" id="resolvePaymentBtn" class="btn btn-primary btn-sm w-50">
                                            Resolve
                                        </button>
                                        @endif
                                    </div>

                                <script>
                                    $(document).ready(function () {
                                        const $queryBtn = $('#queryPaymentBtn');
                                        const $resolveBtn = $('#resolvePaymentBtn');
                                        const $box = $('#paymentResponseBox');
                                        const transId = '{{ $user->transid ?? "" }}'; // replace as needed

                                        function showLoading(message = 'Processing...') {
                                            $box.html('<div class="text-center py-3"><i class="fa fa-spinner fa-spin"></i> ' + message + '</div>');
                                        }

                                        function formatJSON(json) {
                                            try {
                                                return '<pre style="white-space: pre-wrap;">' + JSON.stringify(json, null, 2) + '</pre>';
                                            } catch (e) {
                                                return '<span class="text-warning">Invalid JSON response</span>';
                                            }
                                        }

                                        function handleAjax(url, message) {
                                            showLoading(message);

                                            $.ajax({
                                                url: url,
                                                type: 'POST',
                                                data: {
                                                    transid: transId,
                                                    _token: '{{ csrf_token() }}'
                                                },
                                                success: function (response) {
                                                    $box.html(formatJSON(response.message));
                                                    $box.scrollTop($box[0].scrollHeight);
                                                },
                                                error: function (xhr) {
                                                    $box.html('<span class="text-danger">Error: ' + xhr.statusText + '</span>');
                                                }
                                            });
                                        }

                                        $queryBtn.on('click', function () {
                                            handleAjax('{{ route("conference.queryPayment") }}', 'Querying payment...');
                                        });

                                        $resolveBtn.on('click', function () {
                                            handleAjax('{{ route("conference.queryPayment") }}', 'Resolving payment...');
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="row">
                            {{-- <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $user->user->name }}">
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') ?? $user->user->email }}" required>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="phone" id="phone" name="phone" class="form-control" value="{{ old('phone') ?? $user->user->phone }}" required>
                                </fieldset>
                            </div> --}}
                            
                            @foreach($registrationFields as $field)
                                @php
                                    $name = $field['name'];
                                    $label = $field['label'];
                                    $type = $field['type'];
                                    $required = !empty($field['required']);
                                    $hasOnchange = $field['has_onchange'] ?? null;

                                    // pick value from filled fields or old input
                                    $value = $filledFields[$name] ?? old("registration_fields.$name");
                                @endphp

                                <div class="col-md-6">
                                    <fieldset class="form-group mb-2">

                                        <label>{{ $label }}</label>

                                        {{-- SELECT --}}
                                        @if($type === 'select')
                                            <select name="registration_fields[{{ $name }}]"
                                                    class="form-control select2"
                                                    @if($required) required @endif
                                                    @if($hasOnchange) onchange="{{ $hasOnchange }}" @endif>

                                                <option value="">--Select--</option>

                                                @foreach(($field['options'] ?? []) as $key => $option)
                                                    @php
                                                        $optionValue = is_array($option) ? ($option['id'] ?? $key) : $key;
                                                        $optionLabel = is_array($option) ? ($option['name'] ?? ($option['title'] ?? $optionValue)) : $option;
                                                    @endphp

                                                    <option value="{{ $optionValue }}"
                                                            {{ (string)$value === (string)$optionValue ? 'selected' : '' }}>
                                                        {{ $optionLabel }}
                                                    </option>
                                                @endforeach

                                                @if(!empty($field['has_other_option']))
                                                    <option value="other" {{ $value === 'other' ? 'selected' : '' }}>Other</option>
                                                @endif
                                            </select>

                                        {{-- TEXTAREA --}}
                                        @elseif($type === 'textarea')
                                            <textarea name="registration_fields[{{ $name }}]"
                                                    class="form-control h-100px"
                                                    placeholder="{{ $label }}"
                                                    @if($required) required @endif>{{ $value }}</textarea>

                                        {{-- INPUT --}}
                                        @else
                                            <input type="{{ $type }}"
                                                name="registration_fields[{{ $name }}]"
                                                class="form-control"
                                                value="{{ $value }}"
                                                placeholder="{{ $label }}"
                                                @if($required) required @endif>
                                        @endif

                                    </fieldset>
                                </div>
                            @endforeach

                            
                            <div class="col-md-6">
                                <fieldset class="form-group @error('passport')is-invalid @enderror">
                                    <label for="passport">Change Passport</label>
                                    <input type= "file"  accept="image/*" class="form-control" name="passport" id="passport">	
                                </fieldset>           
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="conference_plan_id">Conference Registration Plan</label>
                                    <select class="form-control" name="conference_plan_id" id="conference_plan_id" required>
                                        {{-- //include chapter --}}
                                        <option value="">--Select Plan -- </option>
                                        @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ isset( $user->conference_plan_id) && $user->conference_plan_id == $plan->id ? 'selected' : ''}}>{{ $plan->title }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="hostel_id">Hostel</label>
                                    <select class="form-control" name="hostel_id" id="hostel_id" required>
                                        @foreach($hostels as $hostel)
                                        @if($hostel->capacity > $hostel->allocation || $hostel->id == $user->hostel_id)
                                        <option value="{{ $hostel->id ?? $user->hostel_id }}" {{ $user->hostel_id == $hostel->id ? 'selected' : '' }}>{{ $hostel->name. ' ('.($hostel->capacity - $hostel->allocation). ' participant(s) left) | '.$hostel->type. ' | '.$hostel->level}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="hostel">Service Point</label>
                                    <select class="form-control" name="food_id" id="food_id" required>
                                        @foreach($foods as $food)
                                            @if($food->capacity > $food->allocation || $food->id == $user->food_id)
                                            <option value="{{ $food->id ?? $user->food_id }}" {{ $user->food_id == $food->id ? 'selected' : '' }}>{{ $food->name. ' ('.($food->capacity - $food->allocation). ' participant(s) left) | '.$food->level }}</option>
                                            
                                            @endif
                                        @endforeach
                                        </select>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group">
                                    <label for="password">Password</label><small class="text-muted"><i style="color:red">Leave blank except you want to reset participant's password</i></small>
                                    <input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter password">
                                </fieldset>
                            </div>
                           
                            
                            
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <button class="btn btn-primary" style="width:100%" type="submit">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->          
</div>
@endsection

