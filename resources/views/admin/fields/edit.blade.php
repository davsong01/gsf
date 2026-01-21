@extends('layouts.dashboard')

@php
    $isEdit = isset($field);
@endphp

@section('title', $isEdit ? 'Edit Field' : 'Create Field')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('fields.index') }}">Fields</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">
    {{ $isEdit ? 'Update Field' : 'Create Field' }}
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
                                action="{{ $isEdit ? route('fields.update', $field->id) : route('fields.store') }}"
                                method="POST"
                            >
                                @csrf
                                @if($isEdit)
                                    @method('PATCH')
                                @endif

                                <div class="row">

                                    {{-- Field Name --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="name">Name</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="name"
                                                name="name"
                                                value="{{ old('name', $field->name ?? '') }}"
                                                placeholder="Enter field name"
                                                required
                                            >
                                        </fieldset>
                                    </div>

                                    {{-- Field Pastor --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="stakeholder_id">Field Pastor</label>

                                            <select class="form-control" name="stakeholder_id" id="stakeholder_id" required>
                                                <option value="">-- Select Field Pastor --</option>

                                                @foreach ($pastors as $pastor)
                                                    <option
                                                        value="{{ $pastor->id }}"
                                                        {{ (int) old('stakeholder_id', $field->stakeholder->id ?? null) === (int) $pastor->id ? 'selected' : '' }}
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
                                            {{ $isEdit ? 'Update Field' : 'Create Field' }}
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
