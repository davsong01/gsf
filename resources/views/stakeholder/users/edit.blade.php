@extends('layouts.stakeholderdashboard')
@section('title', 'Update user')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('stakeholders.dashboard') }}">Dashboard</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Update User</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-header bg-light">
                        <h4 class="card-title">
                            Update: {{ $user->name }}
                            @if($user->status == 0)
                                <span class="btn btn-primary btn-sm">Student</span>
                                <span class="btn btn-info btn-sm">{{ $user->rolename }}</span>
                            @else
                                <span class="btn btn-danger btn-sm">Alumni</span>
                                <span class="btn btn-dark btn-sm">{{ $user->rolename }}</span>
                            @endif
                        </h4>
                        @include('includes.alerts')
                    </div>

                    <div class="card-body">
                        <form action="{{ route('stakeholders.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            {{-- User Overview --}}
                            <div class="row mb-3">
                                <div class="col-md-3 text-center">
                                    <img src="{{ asset($user->passport ?? 'frontend/passports/avatar.jpg') }}"
                                        class="rounded-circle img-fluid mb-2"
                                        style="width:150px;height:150px;object-fit:cover;"
                                        alt="avatar">
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label">Overview</label><br>
                                    <strong>Login ID:</strong> {{ $user->family_id }} <br>
                                    <strong>Campus:</strong> GSF, {{ $user->campus->name ?? '' }} <br>
                                    <a href="{{ route('user.single', $user->slug) }}" target="_blank" class="btn btn-dark btn-sm mt-1">View Profile</a>
                                </div>
                            </div>

                            {{-- Form Fields --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $user->name }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') ?? $user->email }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') ?? $user->phone }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-control" name="gender" id="gender" required>
                                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Graduation Status</label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="">--Select Status--</option>
                                        <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Student</option>
                                        {{-- <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Alumni</option> --}}
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') ?? $user->dob }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="passport" class="form-label">Change Passport</label>
                                    <input type="file" class="form-control" id="passport" name="passport" accept="image/*">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password <small class="text-muted"><i style="color:red;">Leave blank to use phone number as password</i></small></label>
                                    <input type="text" class="form-control" id="password" name="password" placeholder="Enter password">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="designation_id" class="form-label">Chapter Designation</label>
                                    <select class="form-control" name="designation_id" id="designation_id" required>
                                        <option value="">--Select Portfolio--</option>
                                        @foreach($campusDesignations as $designation)
                                            <option value="{{ $designation->id }}" {{ $user->designation_id == $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="program" class="form-label">Program</label>
                                    <select class="form-control" name="program" required>
                                        <option value="">Select...</option>
                                        @foreach(['PHD','PGD','MSC','BSC','HND','OND','NCE'] as $prog)
                                            <option value="{{ $prog }}" {{ $user->program == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="course" class="form-label">Course of Study</label>
                                    <select class="form-control" name="course" id="course">
                                        <option value="">Select...</option>
                                        @foreach(coursesOfStudy() as $course)
                                            <option value="{{ $course }}" {{ (old('course') ?? $user->course) == $course ? 'selected' : '' }}>{{ $course }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="course_duration" class="form-label">Course Duration (Years)</label>
                                    <input type="number" class="form-control" id="course_duration" name="course_duration" value="{{ old('course_duration') ?? $user->course_duration }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="matric_year" class="form-label">Year of Matriculation</label>
                                    <select class="form-control" name="matric_year" id="matric_year">
                                        @foreach($sessions as $session=>$value)
                                            <option value="{{ $value . '/' . ($value + 1) }}" {{ $user->matric_year == $value . '/' . ($value + 1) ? 'selected' : '' }}>{{ $value . '/' . ($value + 1) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="graduation_year" class="form-label">Year of Graduation</label>
                                    <select class="form-control" name="graduation_year" id="graduation_year">
                                        <option value="">Select...</option>
                                        @foreach($sessions as $session=>$value)
                                            <option value="{{ $value . '/' . ($value + 1) }}" {{ $user->graduation_year == $value . '/' . ($value + 1) ? 'selected' : '' }}>{{ $value . '/' . ($value + 1) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="facebook" class="form-label">Facebook Profile</label>
                                    <input type="url" class="form-control" id="facebook" name="facebook" value="{{ old('facebook') ?? $user->facebook }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="twitter" class="form-label">Twitter Profile</label>
                                    <input type="url" class="form-control" id="twitter" name="twitter" value="{{ old('twitter') ?? $user->twitter }}">
                                </div>

                                {{-- Skills at the bottom, full width --}}
                                <div class="col-12 mb-3">
                                    <label for="skills" class="form-label">Skills</label>
                                    <textarea class="form-control" id="skills" name="skills" rows="4" placeholder="Enter skills separated by commas or new lines">{{ old('skills') ?? $user->skills }}</textarea>
                                    <small class="text-muted">Separate each skill with a comma or put each on a new line.</small>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>
    </section>
</div>
@endsection
