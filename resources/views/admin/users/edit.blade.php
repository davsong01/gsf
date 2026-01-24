@extends('layouts.dashboard')

@php
    $isEdit = isset($user);
@endphp

@section('title', $isEdit ? 'Update User' : 'Create User')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('users.index') }}">Community users</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ $isEdit ? 'Update User' : 'Create User' }}</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ $isEdit ? 'Update: '.$user->name : 'Create New User' }}
                            @if($isEdit)
                                @if($user->is_graduated == 0)
                                    <span class="btn btn-primary btn-sm">Student</span>
                                @else
                                    <span class="btn btn-danger btn-sm">Alumni</span>
                                @endif
                                @if($user->designation)
                                    <span class="btn btn-dark btn-sm">{{ $user->designation->name }}</span>
                                @endif
                            @endif
                        </h4>
                        @include('includes.alerts')
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ $isEdit ? route('users.update', $user->id) : route('users.store') }}"
                                  method="POST" enctype="multipart/form-data">
                                @csrf
                                @if($isEdit)
                                    @method('PATCH')
                                @endif

                                {{-- Overview only in edit --}}
                                @if($isEdit)
                                <div class="row mb-3">
                                    <div class="col-md-3 text-center">
                                        <img src="{{ asset($user->passport ?? 'frontend/passports/avatar.jpg') }}"
                                            class="rounded-circle img-fluid mb-2" style="width:150px;height:150px;object-fit:cover;">
                                    </div>
                                    <div class="col-md-9">
                                        <label>Overview</label><br>
                                        <strong>Login ID:</strong> {{ $user->family_id }} <br>
                                        <strong>Campus:</strong> GSF, {{ $user->campus->name ?? '' }} <br>
                                        
                                        @if($user->campus->chapterPresident)
                                            <strong>President:</strong> {{ $user->campus->chapterPresident->name }} <br>
                                            <strong>President's Phone:</strong> {{ $user->campus->chapterPresident->phone }} <br>
                                        @endif

                                        <a href="{{ route('user.single', $user->slug) }}" target="_blank" class="btn btn-dark mt-1">View Profile</a>
                                    </div>
                                </div>
                                @endif

                                <div class="row">
                                    {{-- Each input in its own col-md-6 --}}
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                   value="{{ old('name', $user->name ?? '') }}" placeholder="Enter name">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                   value="{{ old('email', $user->email ?? '') }}" required>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                   value="{{ old('phone', $user->phone ?? '') }}" required>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="gender">Gender</label>
                                            <select class="form-control" name="gender" id="gender" required>
                                                <option value="Male" {{ old('gender', $user->gender ?? '')=='Male'?'selected':'' }}>Male</option>
                                                <option value="Female" {{ old('gender', $user->gender ?? '')=='Female'?'selected':'' }}>Female</option>
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="is_graduated">Undergraduate Status</label>
                                            <select class="form-control" name="is_graduated" id="is_graduated" required>
                                                <option value="">--Select Status--</option>
                                                <option value="0" {{ old('is_graduated', $user->is_graduated ?? '')=='0'?'selected':'' }}>Student</option>
                                                <option value="1" {{ old('is_graduated', $user->is_graduated ?? '')=='1'?'selected':'' }}>Alumni</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="status">Portal Status</label>
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="">--Select Status--</option>
                                                <option value="active" {{ old('status', $user->status ?? '')=='active'?'selected':'' }}>Active</option>
                                                <option value="inactive" {{ old('status', $user->status ?? '')=='inactive'?'selected':'' }}>Inactive</option>
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="open_to_work">Open to work?</label>
                                            <select class="form-control" name="open_to_work" id="open_to_work" required>
                                                <option value="1" {{ old('open_to_work', $user->open_to_work ?? '')==1?'selected':'' }}>Yes</option>
                                                <option value="0" {{ old('open_to_work', $user->open_to_work ?? '')==0?'selected':'' }}>No</option>
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="dob">Date of Birth</label>
                                            <input type="date" class="form-control" name="dob"
                                                   value="{{ old('dob', $user->dob ?? '') }}">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group @error('passport') is-invalid @enderror">
                                            <label for="passport">Change Passport</label>
                                            <input type="file" accept="image/*" class="form-control" name="passport" id="passport">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="password">Password</label>
                                            <small class="text-muted"><i style="color:red">Leave blank to use phone number as password</i></small>
                                            <input type="text" class="form-control" name="password"
                                                   value="{{ old('password') }}" placeholder="Enter password">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        @if(auth()->user()->isAdmin())
                                        <fieldset class="form-group">
                                            <label for="chapter_id">Campus</label>
                                            <select class="form-control" name="chapter_id" id="chapter_id" required>
                                                <option value="">--Select Campus--</option>
                                                @foreach($chapters as $chapter)
                                                    <option value="{{ $chapter->id }}" {{ old('chapter_id', $user->chapter_id ?? '')==$chapter->id?'selected':'' }}>{{ $chapter->name }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="role">Directory Role</label>
                                            <select class="form-control" name="role" id="role" required>
                                                @foreach($portfolios as $portfolio=>$value)
                                                    @if(auth()->user()->isSubAdmin() && $portfolio <> 1)
                                                        <option value="{{ $portfolio }}" {{ old('role',$user->role ?? '')==$portfolio?'selected':'' }}>{{ $value }}</option>
                                                    @endif
                                                    @if(auth()->user()->isAdmin())
                                                        <option value="{{ $portfolio }}" {{ old('role',$user->role ?? '')==$portfolio?'selected':'' }}>{{ $value }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="designation_id">Campus Designation</label>
                                            <select class="form-control" name="designation_id" id="designation_id">
                                                <option value="">--Select Designation--</option>
                                                @foreach($campusDesignations as $designation)
                                                    <option value="{{ $designation->id }}" {{ old('designation_id',$user->designation_id ?? '')==$designation->id?'selected':'' }}>{{ $designation->name }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="portfolio_session">Portfolio session</label>
                                            <select class="form-control" name="portfolio_session" id="portfolio_session">
                                                @foreach($sessions as $session=>$value)
                                                    <option value="{{ $value . '/' . ($value + 1) }}" {{ old('portfolio_session',$user->portfolio_session ?? '') == $value . '/' . ($value + 1) ? 'selected' : '' }}>{{ $value . '/' . ($value + 1) }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="program">Program</label>
                                            <select class="form-control" name="program" required>
                                                @foreach(['PHD','PGD','MSC','BSC','HND','OND','NCE'] as $prog)
                                                    <option value="{{ $prog }}" {{ old('program',$user->program ?? '')==$prog?'selected':'' }}>{{ $prog }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="course">Course of study</label>
                                            <select class="form-control" name="course" id="course">
                                                <option value="">Select...</option>
                                                @foreach(coursesOfStudy() as $course)
                                                    <option value="{{$course}}" {{ old('course',$user->course ?? '')==$course?'selected':'' }}>{{$course}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="course_duration">Course Duration (Years)</label>
                                            <input type="number" id="course_duration" name="course_duration" class="form-control" value="{{ old('course_duration',$user->course_duration ?? '') }}" required>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="matric_year">Year of Matriculation</label>
                                            <select class="form-control" name="matric_year" id="matric_year">
                                                @foreach($sessions as $session=>$value)
                                                    <option value="{{ $value . '/' . ($value + 1) }}" {{ old('matric_year',$user->matric_year ?? '')==$value . '/' . ($value + 1)?'selected':'' }}>{{ $value . '/' . ($value + 1) }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="graduation_year">Year of Graduation</label>
                                            <select class="form-control" name="graduation_year" id="graduation_year">
                                                <option value="">Select...</option>
                                                @foreach($sessions as $session=>$value)
                                                    <option value="{{ $value . '/' . ($value + 1) }}" {{ old('graduation_year',$user->graduation_year ?? '')==$value . '/' . ($value + 1)?'selected':'' }}>{{ $value . '/' . ($value + 1) }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="facebook">Facebook Profile</label>
                                            <input type="url" id="facebook" name="facebook" class="form-control" value="{{ old('facebook',$user->facebook ?? '') }}">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="twitter">Twitter Profile</label>
                                            <input type="url" id="twitter" name="twitter" class="form-control" value="{{ old('twitter',$user->twitter ?? '') }}">
                                        </fieldset>
                                    </div>
                                </div>

                                {{-- Skills textarea on a new row --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <fieldset class="form-group">
                                            <label for="skills">Skills</label>
                                            <textarea class="form-control" name="skills" id="skills" rows="4">{{ old('skills',$user->skills ?? '') }}</textarea>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary w-100">{{ $isEdit ? 'Update' : 'Create' }}</button>
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
