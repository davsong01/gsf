@extends('layouts.dashboard')
@section('title', 'Chapters')
@section('active')
<li class="breadcrumb-item">Chapters</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All chapters</h4>
                        <a href="{{ route('chapters.create') }}" class="btn btn-primary mt-1">Add new chapter</a>
                        <a href="{{ route('chapters.export') }}" class="btn btn-primary mt-1">Export</a>
                        {{-- @include('includes.alerts') --}}

                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Chapter Rep</th>
                                            <th>Details</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Statistics</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($chapters as $chapter)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $chapter->name }} <br>
                                               <small style="color:red">Token: {{ $chapter->token }}</small> <br>
                                                <small><a target="_blank" href="{{ route('campus.single', $chapter->id) }}"><i class="fa fa-eye"></i> View on website</a></small>
                                            </td>
                                            <td>
                                                @if ($chapter->stakeholder)
                                                    <a href="{{route('stakeholderpersonnel.edit', $chapter->stakeholder->id)}}">{{ $chapter->stakeholder->name }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <small>
                                                    Field: {{ $chapter->field->name ?? 'N/A' }} <br>
                                                    Zone: {{ $chapter->zone->name ?? 'N/A' }} <br>
                                                </small>
                                            </td>
                                            <td>{{ $chapter->email }}</td>
                                            <td>{{ $chapter->phone }}</td>
                                            <td>
                                                <small>
                                                    Students: {{ $chapter->members()->count() }} <br>
                                                    Alumni: {{ $chapter->alumni()->count() }}
                                                    Stakeholders: {{ $chapter->stakeholders->count() }}
                                                </small>
                                            </td>
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions"
                                                    data-toggle="modal"
                                                    data-target="#moveChapterModal{{ $chapter->id }}"
                                                    title="Move members, alumni & stakeholders">
                                                        <i class="fa fa-exchange actions"></i>
                                                </a>

                                                <a class="actions" data-toggle="tooltip" title="View/Update chapter details" href="{{ route('chapters.edit', $chapter->id) }}"> <i class="bx bxs-edit actions"></i>
                                                </a>
                                                <a class="actions" data-toggle="tooltip" title="Generate new token" href="{{ route('chapter.newtoken', $chapter->id) }}"> <i class="fa fa-refresh actions"></i>
                                                </a>

                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete chapter" href="{{ route('chapters.delete', $chapter->id) }}"> <i class="fa fa-trash actions"></i></
                                                </a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="moveChapterModal{{ $chapter->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-md" role="document">
                                                <form method="POST" action="{{ route('chapters.move-members', $chapter->id) }}">
                                                    @csrf

                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Move Chapter Members</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <div class="modal-body">

                                                            <p class="text-danger">
                                                                This action will move <strong>all members, alumni and stakeholders</strong>
                                                                from <strong>{{ $chapter->name }}</strong> to another chapter.
                                                            </p>

                                                            <p>
                                                                Their zone and field will also be updated to match the selected chapter.
                                                                This action cannot be undone.
                                                            </p>

                                                            <div class="form-group">
                                                                <label>Select destination chapter</label>
                                                                <select name="new_chapter_id" class="form-control select2-chapter" data-modal="#moveChapterModal{{ $chapter->id }}" required>
                                                                    <option value="">-- Select Chapter --</option>
                                                                    @foreach($chapters as $targetChapter)
                                                                        @if($targetChapter->id != $chapter->id)
                                                                            <option value="{{ $targetChapter->id }}">
                                                                                {{ $targetChapter->name }}
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit"
                                                                    onclick="return confirm('Are you sure you want to move all records?');"
                                                                    class="btn btn-danger">
                                                                Move Records
                                                            </button>
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>

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
