<div class="content-body" id="section-to-print">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body"> 
                            <div class="row">
                                <div class="col-md-2 col-sm-4">
                                    <img style="text-align: center; width:100%; display: block; margin-left: 20px;margin-right: auto;" class="logo" src="{{ asset('frontend/img/logo.png') }}">
                                </div>
                                <div class="col-md-10 col-sm-8">
                                    
                                    
                                </div>
                            </div>  
                            <hr style="border-top: 1px solid red;">                           
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label class="sections"><h6>Section 1: <br>Report Period </h6></label>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="session">Academic Session</label>
                                        <p>{{ $report->session }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="semester">Semester</label>
                                        <p>{{ $report->semester }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="day">Day</label>
                                        <p>{{ $report->day }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="month">Month</label>
                                        <p>{{ date("F", mktime(0, 0, 0, $report->month, 10)) }}</p>
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
                                        <p>{{ $report->president_name }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="president_number">Number of President</label>
                                        <p>{{ $report->president_number }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="gen_sec_name">Name of General Secretary</label>
                                        <p>{{ $report->gen_sec_name }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="gen_sec_number">Number of General Secretary</label>
                                        <p>{{ $report->gen_sec_number }}</p>
                                    </fieldset>
                                </div>
                                
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="evang_sec_name">Name of Evangelism Secretary</label>
                                        <p>{{ $report->evang_sec_name }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="evang_sec_number">Number of Evangelism Secretary</label>
                                        <p>{{ $report->evang_sec_number }}</p> 
                                        
                                    </fieldset>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="fin_sec_name">Name of Financial Secretary</label>
                                        <p>{{ $report->fin_sec_name }}</p>     
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="fin_sec_number">Number of Financial Secretary</label>
                                        <p>{{ $report->fin_sec_number }}</p>
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
                                        <p>{{ $report->bible_study_venue }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="bible_study_time">Bible study Time</label>
                                        <p>{{ $report->bible_study_time }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="bible_study_highest_attendance">Bible study Highest attendance</label>
                                        <p>{{ $report->bible_study_highest_attendance }}</p>
                    
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="bible_study_lowest_attendance">Bible study lowest attendance</label>
                                        <p>{{ $report->bible_study_lowest_attendance }}</p>
                                            
                                    </fieldset>
                                </div>

                                <div class="col-md-12 col-sm-12">
                                    <label class="sections"><h6>WEEKLY PROGRAMS - Prayer Meeting</h6></label>
                                </div> 
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="prayer_meeting_venue">Prayer meeting Venue</label>
                                        <p>{{ $report->prayer_meeting_venue }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="prayer_meeting_time">Prayer Meeting Time</label>
                                        <p>{{ $report->prayer_meeting_time }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="prayer_meeting_highest_attendance">Prayer Meeting Highest attendance</label>
                                        <p>{{ $report->prayer_meeting_highest_attendance }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="prayer_meeting_lowest_attendance">Prayer meeting lowest attendance</label>
                                        <p>{{ $report->prayer_meeting_lowest_attendance }}</p>
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
                                        <p>{{ $report->believer_foundation_class_venue }}</p>
                                            
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="believer_foundation_class_time">Believer's foundation cass Time</label>
                                        <p>{{ $report->believer_foundation_class_time }}</p>
                                            
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="believer_foundation_class_highest_attendance">Believer's foundation Highest attendance</label>
                                        <p>{{ $report->believer_foundation_class_highest_attendance }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="believer_foundation_class_lowest_attendance">Beliver foundation class lowest attendance</label>
                                        <p>{{ $report->believer_foundation_class_lowest_attendance }}</p>
                                            
                                    </fieldset>
                                </div>

                                <div class="col-md-12 col-sm-12">
                                    <label class="sections"><h6>WEEKLY PROGRAMS - Sunday School</h6></label>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="sunday_school_highest_attendance">Sunday school Highest attendance</label>
                                        <p>{{ $report->sunday_school_highest_attendance }}</p>
                                            
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="sunday_school_lowest_attendance">Sunday School lowest attendance</label>
                                        <p>{{ $report->sunday_school_lowest_attendance }}</p>
                                
                                    </fieldset>
                                </div>

                                <div class="col-md-12 col-sm-12">
                                    <label class="sections"><h6>WEEKLY PROGRAMS - Sunday Worship Service</h6></label>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="sunday_highest_attendance">Sunday Worship Highest attendance</label>
                                        <p>{{ $report->sunday_highest_attendance }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="sunday_lowest_attendance">Sunday lowest attendance</label>
                                        <p>{{ $report->sunday_lowest_attendance }}</p>
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
                                        <p>{{ $report->visit_to_assembly_venue }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="visit_to_assembly_time">Time</label>
                                        <p>{{ $report->visit_to_assembly_time }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="visit_to_assembly_fellowship_attendance">Fellowship attendance</label>
                                        <p>{{ $report->visit_to_assembly_fellowship_attendance }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="visit_to_assembly_fellowship_activity">Fellowship's activity in the assembly</label><br>
                                        <p class="report-details">{{ $report->visit_to_assembly_fellowship_activity }}</p>
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
                                        <p class="report-details">{{ $report->special_programs }}</p>
                                    </fieldset>
                                </div>
                            </div>

                            <hr style="border-top: 1px solid red;">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label class="sections"><h6>Section 6: <br>HOLY COMMUNION SERVICE</h6></label>
                                </div>  
                                <div class="col-md-12 col-sm-12">
                                    <label for="">Any Holy communion service conducted?</label>
                                    <p class="report-details">{{ $report->communion }}</p>
                                    
                                </div>
                                <div class="communion-details">                      
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="holy_communion_minister">Name of minister</label><br>
                                            <p class="report-details">{{ $report->holy_communion_minister }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="holy_communion_minister_rank">Rank of minister</label><br>
                                            <p class="report-details">{{ $report->holy_communion_minister_rank }}</p>
                                        </fieldset>
                                    </div>
                                
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="holy_communion_attendance">Holy Communion Attendance</label><br>
                                            <p class="report-details">{{ $report->holy_communion_attendance }}</p>
                                        </fieldset>
                                    </div>
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
                                        <p class="report-details">{{ $report->details }}</p>
                                    </fieldset>
                                </div>
                                
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="evangelism_number_of_souls">No of souls won</label><br>
                                        <p class="report-details">{{ $report->evangelism_number_of_souls }}</p>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="evangelism_number_of_souls_who_joined_fellowship">No of souls who joined the fellowship</label><br>
                                        <p class="report-details">{{ $report->evangelism_number_of_souls_who_joined_fellowship }}</p>
                                    </fieldset>
                                </div>
                                
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="evangelism_number_of_converts_baptized">No of converts baptize</label><br>
                                        <p class="report-details">{{ $report->evangelism_number_of_converts_baptized }}</p>
                                        
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
                                            <label for="bible_study_offering">Total Bible Study Offering for the Month (&#8358;)</label>
                                            <p class="report-details">{{ $report->bible_study_offering }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="prayer_meeting_offering">Total Prayer Meeting Offering for the Month (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->prayer_meeting_offering }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="special_program_offering">Total Special Programme Offering (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->special_program_offering }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_special_program_offering">Other Special Programme Offering (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->other_special_program_offering }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="thanksgiving_offering">Thanksgiving Offering (First Sunday Service) for the Month (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->thanksgiving_offering }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="total_sunday_worship_offering">Total Sunday worship offering (excluding First Sunday) (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->total_sunday_worship_offering }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="grand_total_offering">Grand Total Offering (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->grand_total_offering }}</p>
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
                                            <p class="report-details">{{ $report->president_tithe }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="total_executive_tithe">Total Executive Tithe (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->total_executive_tithe }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="total_workers_tithe">Total Workers Tithe (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->total_workers_tithe }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="total_members_tithe">Total Members Tithe (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->total_members_tithe }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="grand_total_tithe">Grand Total Tithe (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->grand_total_tithe }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="grand_total_tithe">Tithe of Tithe (to be remitted to National Secretariat) (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->grand_total_tithe }}</p>
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
                                            <p class="report-details">{{ $report->other_levies_purpose }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_levies_projection">Projection</label><br>
                                            <p class="report-details">{{ $report->other_levies_projection }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_levies_period_of_collection">Period of Collection</label><br>
                                            <p class="report-details">{{ $report->other_levies_period_of_collection }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_levies_total_amount">Total Amount collected this Month (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->other_levies_total_amount }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="other_levies_total_accumulation">Total Accumulation since Program began (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->other_levies_total_accumulation }}</p>
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
                                            <p class="report-details">{{ $report->capital_projects }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="recurrent_expenses">Recurrent Expenses (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->recurrent_expenses }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="maintenance">Maintenance (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->maintenance }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="misc">Miscellaneous (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->misc }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="expenses_grand_total">Grand Total (&#8358;)</label><br>
                                            <p class="report-details">{{ $report->expenses_grand_total }}</p>
                                        </fieldset>
                                    </div>
                                    
                                   
                                </div>
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 12: <br>SUMMARY</h6></label>
                                    </div>  
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="spiritual_state">Give a brief summary of the spiritual state of the fellowship (may include outstanding testimonies) in the month</label><br>
                                            <p class="report-details">{{ $report->spiritual_state }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="challenges">Any Challenge(s) or development which you want the NCP to be aware of? </label><br>
                                            <p class="report-details"> {{ $report->spiritual_state }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="proposed_capital_project">Any proposed Capital Project </label><br>
                                            <p class="report-details">{{ $report->proposed_capital_project }}</p>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="completed_capital_project">Any completed Capital Project:  </label><br>
                                            <p class="report-details">{{ $report->completed_capital_project }}</p>
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

                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>ZONAL PASTOR APPROVAL</h6></label>
                                    </div>  
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="zonal_pastor_approval">I </label>
                                            <input type="text" name="zonal_pastor_approval" readonly value="{{  $report->zonal_pastor_approval }}"> strongly affirm that the above information is true and agrees with my own records. 
                                        </fieldset>
                                    </div>
                                   
                                </div>

                                {{-- Official use only --}}
                              
                                <hr style="border-top: 1px solid red;">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <label class="sections"><h6>Section 14: <br>OFFICIAL USE</h6></label>
                                    </div>  
                                   
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="field_pastor_comment">FIELD PASTOR COMMENT</label>
                                            <p class="report-details">{{ $report->field_pastor_comment }}</p>
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="ncp_comment">NCP's COMMENT</label>
                                            <p class="report-details">{{ $report->ncp_comment }}</p>

                                        </fieldset>
                                    </div>
                                </div>
                                
                          
                            </div>
                        
                           
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <a class="btn btn-primary" onclick="printDiv()" style="width:100%" type="submit"> <i class="fa fa-print"></i>
            PRINT</a>
    </div>
</div>
