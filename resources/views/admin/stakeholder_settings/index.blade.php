@extends('layouts.dashboard')

@section('title', 'Settings')

@section('active')
<li class="breadcrumb-item">Settings</li>
@endsection

@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Settings</h4>
                        <a href="{{ route('stakeholdersetting.create') }}" class="btn btn-primary mt-1">
                            Add new setting
                        </a>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Key</th>
                                            <th>Value</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($settings as $setting)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td>
                                                {{ $setting->key }}
                                            </td>

                                            <td>
                                                {{ $setting->value ?? '-' }}
                                            </td>

                                            <td style="padding-left:5px; padding-right:5px;">
                                                <a class="actions"
                                                   title="Edit setting"
                                                   href="{{ route('stakeholdersetting.edit', $setting->id) }}">
                                                    <i class="bx bxs-edit actions"></i>
                                                </a>

                                                <form action="{{ route('stakeholdersetting.destroy', $setting->id) }}"
                                                      method="POST"
                                                      style="display:inline"
                                                      onsubmit="return confirm('Are you really sure?');">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="actions btn btn-link p-0"
                                                            data-toggle="tooltip"
                                                            title="Delete setting">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
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
@endsection
