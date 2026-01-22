@extends('layouts.dashboard')

@php
    $isEdit = isset($zone);
@endphp

@section('title', $isEdit ? 'Edit Zone' : 'Create Zone')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('zones.index') }}">Zones</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">
    {{ $isEdit ? 'Update Zone' : 'Create Zone' }}
</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        @include('includes.alerts')
                    </div>

                    <div class="card-content">
                        <div class="card-body">

                            <form
                                action="{{ $isEdit ? route('zones.update', $zone->id) : route('zones.store') }}"
                                method="POST"
                            >
                                @csrf
                                @if($isEdit)
                                    @method('PATCH')
                                @endif

                                <div class="row">

                                    {{-- Zone Name --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="name">Name</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="name"
                                                name="name"
                                                value="{{ old('name', $zone->name ?? '') }}"
                                                placeholder="Enter name"
                                                required
                                            >
                                        </fieldset>
                                    </div>

                                    {{-- Parent Field --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="field_id">Parent Field</label>
                                            <select class="form-control" name="field_id" id="field_id" required>
                                                <option value="">-- Select Field --</option>
                                                @foreach($fields as $field)
                                                    <option
                                                        value="{{ $field->id }}"
                                                        {{ (int) old('field_id', $zone->field_id ?? null) === (int) $field->id ? 'selected' : '' }}
                                                    >
                                                        {{ $field->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    {{-- Zonal Pastor --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="stakeholder_id">Zonal Pastor(s)</label>

                                            @php
                                                $selectedStakeholders = collect(
                                                    old(
                                                        'stakeholder_id',
                                                        isset($zone) && $zone->zonalCords
                                                            ? $zone->zonalCords->pluck('id')->toArray()
                                                            : []
                                                    )
                                                )->map(fn ($id) => (int) $id)->toArray();
                                            @endphp

                                            <select
                                                class="form-control"
                                                name="stakeholder_id[]"
                                                id="stakeholder_id"
                                                multiple
                                                required
                                            >
                                                @foreach ($pastors as $pastor)
                                                    <option
                                                        value="{{ $pastor->id }}"
                                                        {{ in_array((int) $pastor->id, $selectedStakeholders, true) ? 'selected' : '' }}
                                                    >
                                                        {{ $pastor->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>



                                    {{-- Submit --}}
                                    <div class="col-md-12 col-sm-12">
                                        <button class="btn btn-primary w-100" type="submit">
                                            {{ $isEdit ? 'Update Zone' : 'Create Zone' }}
                                        </button>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection
