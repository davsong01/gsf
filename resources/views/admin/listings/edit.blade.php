@extends('layouts.dashboard')
@section('title', 'Update user')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('users.index') }}">Community users</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Update User</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Update: {{ $user->name }} 
                            @if($user->status == 0)
                            <span class="btn btn-primary btn-sm">Student</span>
                            <span class="btn btn-info btn-sm">{{ $user->rolename }}</span>
                            @else
                            <span class="btn btn-danger btn-sm">Alumni</span>
                            <span class="btn btn-dark btn-sm">{{ $user->rolename }}</span>
                            @endif
                        </h4>
                        {{-- @include('includes.alerts') --}}
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                         <div class="row">
                             <div class="col-md-3">
                                <div class="media-left pr-0"><img style="width: 150px !important; border-radius: 50%;" class="mr-1" src="{{ asset($user->passport ? $user->passport : 'frontend/passports/avatar.jpg') }}" alt="avatar" height="20%">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <label for="">Overview</label> <br>
                                <span><strong>Family ID:</strong> {{ $user->family_id }}</span> <br>
                                <span><strong>Campus:</strong> GSF, {{ $user->campus->name ?? '' }} </span> <br>
                                @if($user->status == 0)
                                @if(!is_null($president))
                                <span><em><b>President:</b></em> {{ $president->name }}</span><br>
                               
                                <span><em><b>President's Phone:</b></em> {{  $president->phone }}</span><br>
                                @endif
                                @endif
                                @if(auth()->user()->status == 0)
                                <a href="{{ route('user.single', $user->slug) }}" target="_blank" class="btn btn-dark">View Profile</a>
                                @endif
                                @if(auth()->user()->status == 1)
                                <a href="{{ route('user.single', $user->slug)  }}" target="_blank" class="btn btn-dark">View Profile</a>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <fieldset class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $user->name }}" placeholder="Enter name">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') ?? $user->email }}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="phone" id="phone" name="phone" class="form-control" value="{{ old('phone') ?? $user->phone }}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="sex">Gender</label>
                                    <select class="form-control" name="sex" id="sex" required>
                                        <option value="Male" {{ $user->sex == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ $user->sex == 'Female' ? 'selected' : ''}}>Female</option>
                                    </select>
                                </fieldset>
                            
                                <fieldset class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="">--Select Status--</option>
                                        <option value="0" {{ $user->status == 0 ? 'selected' : ''}}>Student</option>
                                        <option value="1" {{ $user->status == 1 ? 'selected' : ''}}>Alumni</option>                                    
                                    </select>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="open_to_work">Open to work? (this will add an open to work label on user profile)</label>
                                    <select class="form-control" name="open_to_work" id="open_to_work" required>
                                        <option value="1" {{ $user->open_to_work == 1 ? 'selected' : ''}}>Yes</option>
                                        <option value="0" {{ $user->open_to_work == 0 ? 'selected' : ''}}>No</option>
                                    </select>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="dob">Date of birth</label>
                                    <input type="date" id="dob" name="dob" class="form-control" value="{{ old('dob') ?? $user->dob }}">
                                </fieldset>
                            
                            <fieldset class="form-group @error('passport')is-invalid @enderror">
                                <label for="passport">Change Passport</label>
                                <input type= "file"  accept="image/*" class="form-control" name="passport" id="passport">	
                            </fieldset>           
                            <fieldset class="form-group">
                                <label for="password">Password</label><small class="text-muted"><i style="color:red">Leave blank to use this user's phone number as password</i></small>
                                <input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter password">
                            </fieldset> 
                        </div>
                        <div class="col-md-6 col-sm-12">    
                                @if(auth()->user()->isAdmin())                           
                                <fieldset class="form-group">
                                    <label for="chapter_id">Campus</label>
                                    <select class="form-control" name="chapter_id" id="chapter_id" required>
                                        {{-- //include chapter --}}
                                        <option value="">--Select Campus--</option>
                                        @foreach($chapters as $chapter)
                                        <option value="{{ $chapter->id ?? old('chapter_id')}}" {{ $user->chapter_id == $chapter->id ? 'selected' : ''}}>{{ $chapter->name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                @endif
                                <fieldset class="form-group">
                                    <label for="portfolio">Portfolio</label>
                                    <select class="form-control" name="role" id="role" required>
                                        <option value="">--Select Portfolio--</option>
                                        @foreach($portfolios as $portfolio=>$value)
                                            @if(auth()->user()->isSubAdmin() && $portfolio <> 1)  
                                                <option value="{{ $portfolio }}" {{ $user->role == $portfolio ? 'selected' : '' }}>{{ $value }}</option>
                                            @endif
                                            @if(auth()->user()->isAdmin())  
                                                <option value="{{ $portfolio }}" {{ $user->role == $portfolio ? 'selected' : '' }}>{{ $value }}</option>
                                            @endif
                                        @endforeach                               
                                    </select>
                                </fieldset>
                                
                                <fieldset class="form-group" id="session" style="display: {{ ($user->role == 1 || $user->role == 2) ? 'none' : '' }}">
                                    <label for="portfolio">Portfolio session</label>
                                    <select class="form-control" name="portfolio_session" id="portfolio_session">
                                        @foreach($sessions as $session=>$value)
                                            <option value="{{ $value . '/' . ($value + 1) }}" {{ $user->portfolio_session == $value . '/' . ($value + 1) ? 'selected' : '' }}>{{ $value . '/' . ($value + 1) }}</option>
                                        @endforeach                               
                                    </select>
                                </fieldset>
                                
                                <fieldset class="form-group">
                                    <label for="program">Program</label>
                                    <select class="form-control" name="program" required>
                                        <option value="">Select...</option>
                                        <option value="PHD"  {{ $user->program == 'PHD' ? 'selected' : '' }}>PHD</option>
                                        <option value="PGD"  {{ $user->program == 'PGD' ? 'selected' : '' }}>PGD</option>
                                        <option value="MSC"  {{ $user->program == 'MSC' ? 'selected' : '' }}>MSC</option>
                                        <option value="BSC"  {{ $user->program == 'BSC' ? 'selected' : '' }}>BSC</option>
                                        <option value="HND"  {{ $user->program == 'HND' ? 'selected' : '' }}>HND</option>
                                        <option value="OND"  {{ $user->program == 'OND' ? 'selected' : '' }}>OND</option>
                                        <option value="NCE"  {{ $user->program == 'NCE' ? 'selected' : '' }}>NCE</option>
                                    </select>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="course">Course of study</label>
                                    <input type="text" id="course" name="course" class="form-control" value="{{ old('course') ?? $user->course }}" required>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="skills">Skills</label>
                                    <input type="text" id="skills" name="skills" class="form-control" value="{{ old('skills') ?? $user->skills }}">
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="course_duration">Course Duration(Years)</label>
                                    <input type="number" id="course_duration" name="course_duration" class="form-control" value="{{ old('course_duration') ?? $user->course_duration }}" required>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="matric_year">Year of Matriculation</label>
                                    <select class="form-control" name="matric_year" id="matric_year">
                                        @foreach($sessions as $session=>$value)
                                        <option value="{{ $value . '/' . ($value + 1) }}" {{ $user->matric_year == $value . '/' . ($value + 1) ? 'selected' : '' }} required>{{ $value . '/' . ($value + 1) }}</option>
                                    @endforeach                                
                                    </select>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="graduation_year">Year of graduation</label>
                                    <select class="form-control" name="graduation_year" id="graduation_year">
                                        <option value="">Select...</option>
                                        @foreach($sessions as $session=>$value)
                                        <option value="{{ $value . '/' . ($value + 1) }}" {{ $user->graduation_year == $value . '/' . ($value + 1) ? 'selected' : '' }}>{{ $value . '/' . ($value + 1) }}</option>
                                    @endforeach                                
                                    </select>
                                </fieldset>
                                
                                <fieldset class="form-group">
                                    <label for="facebook">Facebook Profile</label>
                                    <input type="link" id="facebook" name="facebook" class="form-control" value="{{ old('facebook') ?? $user->facebook }}">
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="twitter">Twitter Profile</label>
                                    <input type="link" id="twitter" name="twitter" class="form-control" value="{{ old('twitter') ?? $user->twitter }}">
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

