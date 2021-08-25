@extends('layouts.stakeholderdashboard')
@section('title', 'Edit report')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('stakeholder.dashboard') }}">Report</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Add New report</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Approve {{ date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year }}'s report from {{ $report->chapter->name }}</h4>
                        <p style="color:red"><strong>Please go through the report and click the approve below</strong></p>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('reports.update', $report->id) }}"
                                onsubmit="return confirm('You are about to approve this report, action is irreversible');"
                                method="POST" enctype="multipart/form-data">
                                @method('PATCH')
                                @csrf
                              
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 1: <br>Report Period </h6></label>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="session">Academic Session</label>
                                                <input type="text" value="{{ old('session') ?? $report->session }}" class="form-control" name="session"
                                                id="session" {{ $editStatus }}>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="semester">Semester</label>
                                            <select class="form-control" name="semester"
                                                id="semester" {{ $editStatus }}>
                                                <option value="1" {{ $report->semester == 1 ?? 'selected' }}>1st</option>
                                                <option value="2" {{ $report->semester == 2 ?? 'selected' }}>2nd</option>
                                                <option value="3" {{ $report->semester == 3 ?? 'selected' }}>3rd</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="day">Day</label>
                                            <select class="form-control" {{ $editStatus }} name="day"
                                                id="day" required>
                                                @foreach(range(1,31) as $day)
                                                <option value="{{ $day}}" {{ $report->day == $day ? 'selected' : '' }}>{{ $day }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="month">Month</label>
                                            <select class="form-control" {{ $editStatus }} name="month"
                                                id="month" required>
                                                <option value="">--Select Option--</option>
                                                @foreach($months as $month=>$value)
                                                <option value="{{ $value }}" {{ $report->month == $value ? 'selected' : '' }}>{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    
                                </div>
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 2:<br> CHAPTER DETAILS </h6></label>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="president_name">Name of President</label>
                                                <input type="text" name="president_name" class="form-control" {{ $editStatus }} value="{{ old('president_name') ?? $report->president_name }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="president_number">Number of President</label>
                                                <input type="text" name="president_number" class="form-control" {{ $editStatus }} value="{{ old('president_number') ?? $report->president_number }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="gen_sec_name">Name of General Secretary</label>
                                                <input type="text" name="gen_sec_name" class="form-control" {{ $editStatus }} value="{{ old('gen_sec_name') ?? $report->gen_sec_name }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="gen_sec_number">Number of General Secretary</label>
                                                <input type="text" name="gen_sec_number" class="form-control" {{ $editStatus }} value="{{ old('gen_sec_number') ?? $report->gen_sec_number }}">
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="evang_sec_name">Name of Evangelism Secretary</label>
                                                <input type="text" {{ $editStatus }} name="evang_sec_name" class="form-control" value="{{ old('evang_sec_name') ?? $report->evang_sec_name }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="evang_sec_number">Number of Evangelism Secretary</label>
                                                <input type="text" {{ $editStatus }}  name="evang_sec_number" class="form-control" value="{{ old('evang_sec_number') ?? $report->evang_sec_number}}">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="fin_sec_name">Name of Financial Secretary</label>
                                                <input type="text" {{ $editStatus }}   name="fin_sec_name" class="form-control" value="{{ old('fin_sec_name') ?? $report->fin_sec_name }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="fin_sec_number">Number of Financial Secretary</label>
                                                <input type="text" name="fin_sec_number" {{ $editStatus }} class="form-control" value="{{ old('fin_sec_number') ?? $report->fin_sec_number}}">
                                        </fieldset>
                                    </div>

                                </div>
                                    <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 3: <br>WEEKLY PROGRAMS - Bible Study </h6></label>
                                    </div>                        
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="bible_study_venue">Bible study Venue</label>
                                                <input type="text" name="bible_study_venue" {{ $editStatus }} class="form-control" value="{{ old('bible_study_venue') ?? $report->bible_study_venue }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="bible_study_time">Bible study Time</label>
                                                <input type="time" name="bible_study_time" {{ $editStatus }} class="form-control" value="{{ old('bible_study_time') ?? $report->bible_study_time }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="bible_study_highest_attendance">Bible study Highest attendance</label>
                                                <input type="number" min="1" name="bible_study_highest_attendance" {{ $editStatus }} class="form-control" value="{{ old('bible_study_highest_attendance') ?? $report->bible_study_highest_attendance }}" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="bible_study_lowest_attendance">Bible study lowest attendance</label>
                                                <input type="number" min="1" name="bible_study_lowest_attendance" {{ $editStatus }} class="form-control" value="{{ old('bible_study_lowest_attendance') ?? $report->bible_study_lowest_attendance }}">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>WEEKLY PROGRAMS - Prayer Meeting</h6></label>
                                    </div> 
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="prayer_meeting_venue">Prayer meeting Venue</label>
                                                <input type="text" name="prayer_meeting_venue" {{ $editStatus }} class="form-control" value="{{ old('prayer_meeting_venue') ?? $report->prayer_meeting_venue }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="prayer_meeting_time">Prayer Meeting Time</label>
                                                <input type="time" name="prayer_meeting_time" {{ $editStatus }} class="form-control" value="{{ old('prayer_meeting_time') ?? $report->prayer_meeting_time }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="prayer_meeting_highest_attendance">Prayer Meeting Highest attendance</label>
                                                <input type="number" min="1" name="prayer_meeting_highest_attendance" {{ $editStatus }} class="form-control" value="{{ old('prayer_meeting_highest_attendance') ?? $report->prayer_meeting_highest_attendance }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="prayer_meeting_lowest_attendance">Prayer meeting lowest attendance</label>
                                                <input type="number"min="1" name="prayer_meeting_lowest_attendance" {{ $editStatus }} class="form-control" value="{{ old('prayer_meeting_lowest_attendance') ?? $report->prayer_meeting_lowest_attendance }}">
                                        </fieldset>
                                    </div>
                                    
                                </div>
                                   
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>WEEKLY PROGRAMS - BELIEVER'S FOUNDATION CLASS </h6></label>
                                    </div>                        
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="believer_foundation_class_venue">Believer's foundation class Venue</label>
                                                <input type="text" {{ $editStatus }} class="form-control" name="believer_foundation_class_venue" value="{{ old('believer_foundation_class_venue') ?? $report->believer_foundation_class_venue }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="believer_foundation_class_time">Believer's foundation cass Time</label>
                                                <input type="time" {{ $editStatus }} class="form-control" value="{{ old('believer_foundation_class_time') ?? $report->believer_foundation_class_time }}" name="believer_foundation_class_time">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="believer_foundation_class_highest_attendance">Believer's foundation Highest attendance</label>
                                                <input type="number" min="1" {{ $editStatus }} class="form-control" name="believer_foundation_class_highest_attendance" value="{{ old('believer_foundation_class_highest_attendance') ?? $report->believer_foundation_class_highest_attendance }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="believer_foundation_class_lowest_attendance">Beliver foundation class lowest attendance</label>
                                                <input type="number" min="1" {{ $editStatus }} class="form-control" name="believer_foundation_class_lowest_attendance" value="{{ old('believer_foundation_class_lowest_attendance') ?? $report->believer_foundation_class_lowest_attendance}}">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>WEEKLY PROGRAMS - Sunday School</h6></label>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="sunday_school_highest_attendance">Sunday school Highest attendance</label>
                                                <input type="number" min="1" name="sunday_school_highest_attendance" {{ $editStatus }} class="form-control" value="{{ old('sunday_school_highest_attendance') ?? $report->sunday_school_highest_attendance }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="sunday_school_lowest_attendance">Sunday School lowest attendance</label>
                                                <input type="number"min="1" name="sunday_school_lowest_attendance" {{ $editStatus }} class="form-control" value="{{ old('sunday_school_lowest_attendance') ?? $report->sunday_school_lowest_attendance }}">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>WEEKLY PROGRAMS - Sunday Worship Service</h6></label>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="sunday_highest_attendance">Sunday Worship Highest attendance</label>
                                                <input type="number" min="1" name="sunday_highest_attendance" {{ $editStatus }} class="form-control" value="{{ old('sunday_highest_attendance') ?? $report->sunday_highest_attendance }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="sunday_lowest_attendance">Sunday lowest attendance</label>
                                                <input type="number"min="1" name="sunday_lowest_attendance" {{ $editStatus }} class="form-control" value="{{ old('sunday_lowest_attendance') ?? $report->sunday_lowest_attendance }}">
                                        </fieldset>
                                    </div>
                                    
                                </div>
                              
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 4: <br>VISIT TO GOFAMINT ASSEMBLY </h6></label>
                                    </div>                        
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="visit_to_assembly_venue">Venue</label>
                                                <input type="text" name="visit_to_assembly_venue" {{ $editStatus }} class="form-control" value="{{ old('visit_to_assembly_venue') ?? $report->visit_to_assembly_venue }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="visit_to_assembly_time">Time</label>
                                                <input type="time" name="visit_to_assembly_time" {{ $editStatus }} class="form-control" value="{{ old('visit_to_assembly_time') ?? $report->visit_to_assembly_time }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="visit_to_assembly_fellowship_attendance">Fellowship attendance</label>
                                                <input type="number" name="visit_to_assembly_fellowship_attendance" min="1" {{ $editStatus }} class="form-control" value="{{ old('visit_to_assembly_fellowship_attendance') ?? $report->visit_to_assembly_fellowship_attendance }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="visit_to_assembly_fellowship_activity">Fellowship's activity in the assembly</label><br>
                                            <textarea {{ $editStatus }} name="visit_to_assembly_fellowship_activity" id="visit_to_assembly_fellowship_activity" style="width:100%" rows="5" value="{{ old('visit_to_assembly_fellowship_activity') ?? $report->visit_to_assembly_fellowship_activity }}">{{ old('visit_to_assembly_fellowship_activity') ?? $report->visit_to_assembly_fellowship_activity }}</textarea>
                                        </fieldset>
                                    </div>
                                </div>

                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 5: <br>SPECIAL PROGRAMS </h6></label>
                                    </div>                        
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="special_programs">Name & Objectives - List each on a new line with Date/Venue/Time/Attendance</label><br>
                                            <textarea {{ $editStatus }} name="special_programs" id="special_programs" style="width:100%" rows="5" value="{{ old('special_programs') ?? $report->special_programs }}">{{ old('special_programs') ?? $report->special_programs }}</textarea>
                                        </fieldset>
                                    </div>
                                </div>

                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 6: <br>HOLY COMMUNION SERVICE</h6></label>
                                    </div>  
                                    <div class="col-md-6 col-sm-12">
                                        <label for="">Any Holy communion service conducted?</label>
                                        <fieldset class="form-group">
                                            <select name="holy_communion" id="holy_communion" class="form-control communion" {{ $editStatus }}>
                                                <option value="No"  {{ $report->holy_communion == 'No' ?? 'selected' }}>No</option>
                                                <option value="Yes" {{ $report->holy_communion == 'Yes' ?? 'selected' }}>Yes</option>
                                            </select>
                                        </fieldset>
                                    </div>                     
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="holy_communion_minister">Name of minister</label><br>
                                            <input type="text" {{ $editStatus }} name="holy_communion_minister" class="form-control" value="{{ old('holy_communion_minister') ?? $report->holy_communion_minister }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="holy_communion_minister_rank">Rank of minister</label><br>
                                            <input type="text" name="holy_communion_minister_rank" {{ $editStatus }} class="form-control" value="{{ old('holy_communion_minister_rank') ?? $report->holy_communion_minister_rank }}">
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="holy_communion_attendance">Holy Communion Attendance</label><br>
                                            <input type="text" {{ $editStatus }} name="holy_communion_attendance" class="form-control" value="{{ old('holy_communion_attendance') ?? $report->holy_communion_attendance }}">
                                        </fieldset>
                                    </div>
                                </div>
                                
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 7: <br>EVANGELISM</h6></label>
                                    </div>  
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="">Give a brief report of the fellowship corporate evangelism this month</label><br>
                                            <textarea {{ $editStatus }} name="evangelism_report" id="evangelism_report" style="width:100%" rows="5" value="{{ old('evangelism_report') ?? $report->evangelism_report }}">{{ old('evangelism_report') ?? $report->evangelism_report }}</textarea>
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="evangelism_number_of_souls">No of souls won</label><br>
                                            <input type="number" {{ $editStatus }} name="evangelism_number_of_souls" class="form-control" value="{{ old('evangelism_number_of_souls') ?? $report->evangelism_number_of_souls }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="evangelism_number_of_souls_who_joined_fellowship">No of souls who joined the fellowship</label><br>
                                            <input type="number" {{ $editStatus }} name="evangelism_number_of_souls_who_joined_fellowship" class="form-control" value="{{ old('evangelism_number_of_souls_who_joined_fellowship') ?? $report->evangelism_number_of_souls_who_joined_fellowship }}">
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="evangelism_number_of_converts_baptized">No of converts baptized</label><br>
                                            <input name="evangelism_number_of_converts_baptized" type="number" {{ $editStatus }} class="form-control" value="{{ old('evangelism_number_of_converts_baptized') ?? $report->evangelism_number_of_converts_baptized }}">
                                        </fieldset>
                                    </div>
                                </div>
                                {{-- Offering --}}
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 8: <br>OFFERING</h6></label>
                                    </div>  
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="bible_study_offering">Total Bible Study Offering for the Month (&#8358;)</label><br>
                                            <input type="number" min="1" step=".01" {{ $editStatus }} class="form-control" name="bible_study_offering" id="bible_study_offering" value="{{ old('bible_study_offering') ?? $report->bible_study_offering }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="prayer_meeting_offering">Total Prayer Meeting Offering for the Month (&#8358;)</label><br>
                                            <input type="number" min="1" step=".01" {{ $editStatus }} class="form-control" name="prayer_meeting_offering" id="prayer_meeting_offering" value="{{ old('prayer_meeting_offering') ?? $report->prayer_meeting_offering }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="special_program_offering">Total Special Programme Offering (&#8358;)</label><br>
                                            <input type="number" min="1" step=".01" {{ $editStatus }} class="form-control" name="special_program_offering" id="special_program_offering" value="{{ old('special_program_offering') ?? $report->special_program_offering }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_special_program_offering">Other Special Programme Offering (&#8358;)</label><br>
                                            <input type="number" min="1" {{ $editStatus }} class="form-control" name="other_special_program_offering" id="other_special_program_offering" value="{{ old('other_special_program_offering') ?? $report->other_special_program_offering }}">
                                            
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="thanksgiving_offering">Thanksgiving Offering (First Sunday Service) for the Month (&#8358;)</label><br>
                                            <input type="number" min="1" step=".01" {{ $editStatus }} class="form-control" name="thanksgiving_offering" id="thanksgiving_offering" value="{{ old('thanksgiving_offering') ?? $report->thanksgiving_offering }}">
                                            
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="total_sunday_worship_offering">Total Sunday worship offering (excluding First Sunday) (&#8358;)</label><br>
                                            <input type="number" step=".01" {{ $editStatus }} class="form-control" name="total_sunday_worship_offering" id="total_sunday_worship_offering" value="{{ old('total_sunday_worship_offering') ?? $report->total_sunday_worship_offering }}">
                                            
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="grand_total_offering">Grand Total Offering (&#8358;)</label><br>
                                            <input type="number" {{ $editStatus }} class="form-control" name="grand_total_offering" id="grand_total_offering" step=".01" value="{{ old('grand_total_offering') ?? $report->grand_total_offering }}">
                                        </fieldset>
                                    </div>
                                </div>

                                {{-- Tithe --}}
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 9: <br>TITHE</h6></label>
                                    </div>  
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="president_tithe">President's Tithe (&#8358;)</label><br>
                                            <input type="number" step=".01" min="1" {{ $editStatus }} class="form-control" name="president_tithe" id="president_tithe" value="{{ old('president_tithe') ?? $report->president_tithe }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="total_executive_tithe">Total Executive Tithe (&#8358;)</label><br>
                                            <input type="number" min="1" step=".01" {{ $editStatus }} class="form-control" name="total_executive_tithe" id="total_executive_tithe" value="{{ old('total_executive_tithe') ?? $report->total_executive_tithe }}">
                                            
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="total_workers_tithe">Total Workers Tithe (&#8358;)</label><br>
                                            <input type="number" min="1" step=".01" {{ $editStatus }} class="form-control" name="total_workers_tithe" id="total_workers_tithe" value="{{ old('total_workers_tithe') ?? $report->total_workers_tithe }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="total_members_tithe">Total Members Tithe (&#8358;)</label><br>
                                            <input type="number" min="1" step=".01" {{ $editStatus }} class="form-control" name="total_members_tithe" id="total_members_tithe" value="{{ old('total_members_tithe') ?? $report->total_members_tithe }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="grand_total_tithe">Grand Total Tithe (&#8358;)</label><br>
                                            <input type="number" min="1" step=".01" {{ $editStatus }} class="form-control" name="grand_total_tithe" id="grand_total_tithe" value="{{ old('grand_total_tithe') ?? $report->grand_total_tithe }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="grand_total_tithe">Tithe of Tithe (to be remitted to National Secretariat) (&#8358;)</label><br>
                                            <input type="number" min="1" step=".01" {{ $editStatus }} class="form-control" name="tithe_of_tithe" id="tithe_of_tithe" value="{{ old('tithe_of_tithe') ?? $report->tithe_of_tithe }}">
                                        </fieldset>
                                    </div>
                                </div>

                                {{-- Other Chapter levies --}}
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 10: <br>OTHER CHAPTER LEVIES/CONTRIBUTION</h6></label>
                                    </div>  
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_levies_purpose">Purpose</label><br>
                                            <input type="text" {{ $editStatus }} class="form-control" name="other_levies_purpose" id="other_levies_purpose" value="{{ old('other_levies_purpose') ?? $report->other_levies_purpose }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_levies_projection">Projection</label><br>
                                            <input type="text" {{ $editStatus }} class="form-control" name="other_levies_projection" id="other_levies_projection" value="{{ old('other_levies_projection') ?? $report->other_levies_projection }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_levies_period_of_collection">Period of Collection</label><br>
                                            <input type="text" {{ $editStatus }} class="form-control" name="other_levies_period_of_collection" id="other_levies_period_of_collection" value="{{ old('other_levies_period_of_collection') ?? $report->other_levies_period_of_collection }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_levies_total_amount">Total Amount collected this Month (&#8358;)</label><br>
                                            <input type="number" step=".01" min="1" {{ $editStatus }} class="form-control" name="other_levies_total_amount" id="other_levies_total_amount" value="{{ old('other_levies_total_amount') ?? $report->other_levies_total_amount }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_levies_total_accumulation">Total Accumulation since Program began (&#8358;)</label><br>
                                            <input type="number" step=".01" min="1" {{ $editStatus }} class="form-control" name="other_levies_total_accumulation" id="other_levies_total_accumulation" value="{{ old('other_levies_total_accumulation') ?? $report->other_levies_total_accumulation }}">
                                        </fieldset>
                                    </div>
                                </div>

                                {{-- Expenses --}}
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 11: <br>EXPENSES</h6></label>
                                    </div>  
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="capital_projects">Capital Projects (&#8358;)</label><br>
                                            <input type="number" {{ $editStatus }} class="form-control" step=".01" min="1" name="capital_projects" id="capital_projects" value="{{ old('capital_projects') ?? $report->capital_projects }}">
                                            
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="recurrent_expenses">Recurrent Expenses (&#8358;)</label><br>
                                            <input type="number" {{ $editStatus }} class="form-control" step=".01" min="1" name="recurrent_expenses" id="recurrent_expenses" value="{{ old('recurrent_expenses') ?? $report->recurrent_expenses }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="maintenance">Maintenance (&#8358;)</label><br>
                                            <input type="number" {{ $editStatus }} class="form-control" step=".01" min="1" name="maintenance" id="maintenance" value="{{ old('maintenance') ?? $report->maintenance }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="misc">Miscellaneous (&#8358;)</label><br>
                                            <input type="number" {{ $editStatus }} class="form-control" step=".01" min="1" name="misc" id="misc" value="{{ old('misc') ?? $report->misc }}">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="expenses_grand_total ">Grand Total (&#8358;)</label><br>
                                            <input type="number" {{ $editStatus }} class="form-control" step=".01" min="1" name="expenses_grand_total" id="expenses_grand_total" value="{{ old('expenses_grand_total') ?? $report->expenses_grand_total }}">
                                        </fieldset>
                                    </div>
                                                                       
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_levies_total_accumulation">Total Accumulation since Program began (&#8358;)</label><br>
                                            <input type="number" step=".01" min="1" {{ $editStatus }} class="form-control" name="other_levies_total_accumulation" id="other_levies_total_accumulation" value="{{ old('other_levies_total_accumulation') ?? $report->other_levies_total_accumulation }}">
                                        </fieldset>
                                    </div>
                                </div>

                                {{-- Summary --}}
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 12: <br>SUMMARY</h6></label>
                                    </div>  
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="spiritual_state">Give a brief summary of the spiritual state of the fellowship (may include outstanding testimonies) in the month</label><br>
                                            <textarea {{ $editStatus }} name="spiritual_state" id="spiritual_state" style="width:100%" rows="5" value="{{ old('spiritual_state') }}">{{ old('spiritual_state') ?? $report->spiritual_state }}</textarea>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="challenges">Any Challenge(s) or development which you want the NCP to be aware of </label><br>
                                            <textarea {{ $editStatus }} name="challenges" id="challenges" style="width:100%" rows="5" value="{{ old('challenges') ?? $report->challenges }}">{{ old('challenges') }}</textarea>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="proposed_capital_project">Any proposed Capital Project </label><br>
                                            <textarea {{ $editStatus }} name="proposed_capital_project" id="proposed_capital_project" style="width:100%" rows="5" value="{{ old('proposed_capital_project') }}">{{ old('proposed_capital_project') ?? $report->proposed_capital_project }}</textarea>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="completed_capital_project">Any completed Capital Project:  </label><br>
                                            <textarea {{ $editStatus }} name="completed_capital_project" id="completed_capital_project" style="width:100%" rows="5" value="{{ old('completed_capital_project') }}">{{ old('completed_capital_project') ?? $report->completed_capital_project }}</textarea>
                                        </fieldset>
                                    </div>
                                </div>

                                {{-- Signatures and Dates --}}
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 13: <br>SIGNATURES AND DATES</h6></label>
                                    </div>  
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label>President's Signature</label><br>
                                            <img src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->signature }}" style="width:60px" alt="">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label>Gen Sec's Signature</label><br>
                                            <img src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->gen_sec_signature }}" style="width:60px" alt="">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label>Fin Sec's Signature</label><br>
                                            <img src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->fin_sec_signature }}" style="width:60px" alt="">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label>Evang Sec's Signature</label><br>
                                            <img src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->evang_sec_signature }}" style="width:60px" alt="">
                                        </fieldset>
                                    </div>

                                    @if(Auth::guard('stakeholder')->user()->role == 'Zonal Pastor')
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>ZONAL PASTOR APPROVAL</h6></label>
                                    </div>  
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="zonal_pastor_approval">I, </label>
                                            <input required type="text" name="zonal_pastor_approval" value="{{  Auth::guard('stakeholder')->user()->name }}"> strongly affirm that the above information is true and agrees with my own records. 
                                        </fieldset>
                                    </div>
                                    @endif
                                    
                                </div>

                                {{-- Official use only --}}
                                @if(Auth::guard('stakeholder')->user()->role == 'Field Pastor' || Auth::guard('stakeholder')->user()->role == 'Secretariat' )
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 14: <br>OFFICIAL USE</h6></label>
                                    </div>  
                                   
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="field_pastor_comment">FIELD PASTOR COMMENT</label>
                                            <textarea {{ $editStatus }} name="field_pastor_comment" id="field_pastor_comment" style="width:100%" rows="5" value="{{ old('field_pastor_comment') ?? $report->field_pastor_comment }}">{{ old('field_pastor_comment') }}</textarea>
                                        </fieldset>
                                    </div>
                                    @if(Auth::guard('stakeholder')->user()->role == 'Secretariat' )
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="ncp_comment">NCP's COMMENT</label>
                                            <textarea {{ $editStatus }} name="ncp_comment" id="ncp_comment" style="width:100%" rows="5" value="{{ old('ncp_comment') }}">{{ old('ncp_comment') ?? $report->ncp_comment }}</textarea>
                                        </fieldset>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <button class="btn btn-primary" style="width:100%" type="submit">Approve Report</button>
                                </div>
                            </div>
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
<script> 
$(document).ready(function(){
    var value = $('#communion').find(':selected');
    console.log(value);
    alert(value);
});
})


    $('#communion').on('change', function(){
        alert('as');
        console.log($('#communion').val());
           
            if($('#communion').val()=='Yes'){
                $('.communion-details').css('display','block');
               
            }else if($('#communion').val()=='No'){
                $('.communion-details').css('display','none');
               
            }
    }); 
    

</script>

@endsection