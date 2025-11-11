
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>GSF - {{ $report->chapter->name }} Report for {{ date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year }}</title>
    <style type="text/css">
        .fil {
            background-color: #9F9;
            color: #000;
        }

        .fil2 {
            background-color: #FF6;
            color: #000;
        }

        table {
            width: 100%;
        }

        .style1 {
            border-style: none;
            border-color: inherit;
            border-width: medium;
            width: 100%;
            height: 100px;
        }

        .style2 {
            font-weight: bold;
            border: NONE;
        }

        td.datacellone {
            border: NONE;
            background-color: #FFC;
            color: black;
        }

        td.datacelltwo {
            border: NONE;
            background-color: #fff;
            color: black;
        }

        .style3 {
            width: 72%;
        }
        .logo{
            float:left;
            margin-right: -100px;
            width:100px;
        }
        .beside-logo{
            text-align: center;
            line-height: 0.7;
        }
        .head{
            width:1200px; 
            height :auto;
            padding: 20px;
            border-bottom: none;
        }
        .content-row{
            width:860px; 
        }
        .section-head{
            text-align: left;
            padding : 5px 0 5px 0
        }
        .section{
            width:1200px; 
            height :auto;
            border-bottom:none;
        }  
        .sub-section{
            width:900px; 
            text-align: left;
            padding : 5px 0 5px 0;
            font-weight:bold
        }
        .table-row{
            background-color:#000;
            color: white; 
            border:1px solid #000; 
            font-weight:bold;
        }
        .half-content{
            width: 50%;
        }
        .data-heading{
            font-weight:bold;
        }
        .signatures{
            width:120px;
        }
    </style>
</head>

<body>
    <div class="container-fluid" style="border: solid 1px; width:1200px; padding: 10px;">
        <div class="card">
            <div class="card-body">
                <div class="head">
                    <div class="head-content">
                        <img class="logo" src="{{ asset('frontend/img/logo.png') }}">
                        <div class="beside-logo">
                            <h1>GOFAMINT STUDENTS’ FELLOWSHIP</h2>
                                <strong>GOFAMINT International Headquarters, Aseese, Ogun State, Nigeria</strong>
                                <p>MONTHLY REPORT for <strong>{{ $report->chapter->name }}</strong> for <strong>{{ date("F", mktime(0, 0, 0, $report->month, 10)) . ', ' . $report->year }}</strong> </p>
                        </div>
                    </div>
                    <div>
                </div>
            </div>
            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table cellpadding='1' class="table">
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 1 - REPORT PERIOD</th>
                        </tr>                               
                    </table>
                    
                </div>
                <div class="section-body">
                    <table cellpadding='3' cellspacing='2'>
                        <tr style="font-weight:bold;text-align: left;">
                            <th>MONTH : {{ date("F", mktime(0, 0, 0, $report->month, 10)) }}</th>
                            <th>YEAR : {{ $report->session }}</th>
                            <th>ACADEMIC SESSION : {{ $report->semester }}</th>
                            
                            <th>SEMESTER : {{ $report->semester }}</th>
                            <th>DATE SUBMITTED: {{ $report->created_at }}</th> 
                        </tr>
                    </table>
                </div>
            </div>

            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 2 - CHAPTER DETAILS</th>
                        </tr>                               
                    </table>
                    
                </div>
                <div class="section-body">
                    <table>
                        <tr>
                            <td><span class="data-heading">Name of President </span> : {{ $report->president_name }}</td>
                        </tr>
                        <tr><td><span class="data-heading">Number of President</span> : {{ $report->president_number }}</td></tr>
                        <tr><td><span class="data-heading">Name of General Secretary</span> : {{ $report->gen_sec_name }}</td></tr>
                        <tr><td><span class="data-heading">Number of General Secretary</span> : {{ $report->gen_sec_number }}</td></tr>
                        <tr><td><span class="data-heading">Name of Evangelism Secretary</span> : {{ $report->evang_sec_name }}</td></tr>
                        <tr><td><span class="data-heading">Number of Evangelism Secretary</span> : {{ $report->evang_sec_number }}</td></tr>
                        <tr><td><span class="data-heading">Name of Financial Secretary</span> : {{ $report->fin_sec_name }}</td></tr>
                        <tr><td><span class="data-heading">Number of Financial Secretary</span> : {{ $report->fin_sec_number }}</td></tr>
                    </table>
                </div>

            </div>
            
            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 3 - WEEKLY PROGRAMS - Bible Study </th>
                        </tr>                               
                    </table>
                </div>
                <div class="section-body">
                    <table>
                        <tr>
                           <td><span class="data-heading">Bible study Venue </span> : {{ $report->bible_study_venue }}</td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">Bible study Time </span> : {{ $report->bible_study_time }}</td>
                            <td class="half-content"><span class="data-heading">Bible study Highest attendance </span> : {{ $report->bible_study_highest_attendance }}</td>
                        </tr>
                        <tr>
                            <td class="half-content"><span class="data-heading">Bible study lowest attendance </span> : {{ $report->bible_study_lowest_attendance }}</td>
                            <td class="half-content"><span class="data-heading">Bible study lowest attendance </span> : {{ $report->bible_study_lowest_attendance }}</td>
                        </tr>
                    </table>
                    <table>
                        <tr><th class="sub-section">PRAYER MEETINGS </th></tr> 
                    </table>
                    <table>
                        <tr><td><span class="data-heading">Prayer Meeting venue </span>: {{ $report->prayer_meeting_venue }}</td></tr>
                    </table>
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">Prayer Meeting time </span> : {{ $report->prayer_meeting_time }}</td>
                            <td class="half-content"><span class="data-heading">Prayer Meeting highest attendance </span> : {{ $report->prayer_meeting_highest_attendance }}</td>
                        </tr>
                      
                        <tr>
                            <td class="half-content"><span class="data-heading">Prayer Meeting lowest attendance </span> : {{ $report->prayer_meeting_lowest_attendance }}</td>
                        </tr>
                    </table>
                    <table>
                        <tr><th class="sub-section">BELIEVER'S FOUNDATION CLASS</th></tr>     
                        <tr><td>
                        <span class="data-heading">Believer's foundation class Venue </span> : {{ $report->believer_foundation_class_venue }}</td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">Believer's foundation class time </span> : {{ $report->believer_foundation_class_time }}</td>
                            <td class="half-content"><span class="data-heading">Believer's foundation highest attendance </span> : {{ $report->believer_foundation_highest_attendance }}</td>
                        </tr>
                      
                        <tr><td class="half-content"><span class="data-heading">Believer's foundation lowest attendance </span> : {{ $report->believer_foundation_lowest_attendance }}</td></tr>

                        <tr ><th class="sub-section">SUNDAY SCHOOL</th></tr>     
                        <tr>
                            <td class="half-content"><span class="data-heading">Sunday school highest attendance </span> : {{ $report->sunday_school_highest_attendance }}</td>
                            <td class="half-content"><span class="data-heading">Sunday school lowest attendance </span> : {{ $report->sunday_school_lowest_attendance }}</td>
                        </tr>
                       
                        <tr><th class="sub-section">SUNDAY WORDHIP SERVICE</th></tr>     
                        <tr>
                            <td class="half-content"><span class="data-heading">Sunday worship highest attendance </span> : {{ $report->sunday_highest_attendance }}</td>
                            <td class="half-content"><span class="data-heading">Sunday worship lowest attendance </span> : {{ $report->sunday_lowest_attendance }}</td>
                        </tr>
                    </table>
                </div>
            </div>
          
            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 4 - VISIT TO GOFAMINT ASSEMBLY </th>
                        </tr>                               
                    </table>
                    
                </div>
                <div class="section-body">
                    <table>
                        <tr><td><span class="data-heading">Venue </span> : {{ $report->visit_to_assembly_venue }}</td></tr>
                    </table>
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">Time </span> : {{ $report->visit_to_assembly_time }}</td>
                            <td class="half-content"><span class="data-heading">Fellowship attendance </span> :  {{ $report->visit_to_assembly_fellowship_attendance }}</td>
                        </tr>
                    </table>
                    <table>
                        <tr><td><span class="data-heading">Fellowship's activity in the assembly </span> : {{ $report->visit_to_assembly_fellowship_activity }}</td></tr>
                        
                    </table>
                </div>
            </div>

            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 5 - SPECIAL PROGRAMS </th>
                        </tr>                               
                    </table>
                    
                </div>
                <div class="section-body">
                    <table>
                        <tr><td><span class="data-heading">Name & Objectives - List each on a new line with Date/Venue/Time/Attendance </span> : {{ $report->special_programs }}</td>
                        </tr>
                    </table>
                   
                </div>
            </div>

            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 6 - HOLY COMMUNION SERVICE </th>
                        </tr>                               
                    </table>
                    
                </div>
                <div class="section-body">
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">Any Holy communion service conducted? </span> : {{ !is_null($report->holy_communion) ? 'YES' : 'NO' }}</td>
                            <td class="half-content"><span class="data-heading">Name of minister </span> : {{ $report->holy_communion_minister }}</td>
                        </tr>
                        <tr>
                            
                            <td class="half-content"><span class="data-heading">Rank of minister </span> :  {{ $report->holy_communion_minister_rank }}</td>
                            <td class="half-content"><span class="data-heading">Holy communion attendance </span> :  {{ $report->holy_communion_attendance }}</td>
                        </tr>
                    </table>
                   
                </div>
            </div>

            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 7 - EVANGELISM </th>
                        </tr>                               
                    </table>
                    
                </div>
                <div class="section-body">
                    <table>
                        <tr>
                            <td><span class="data-heading">Give a brief report of the fellowship corporate evangelism this month </span> : {{ $report->details }}</td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">No of souls won </span> : {{ $report->evangelism_number_of_souls }}</td>
                            <td class="half-content"><span class="data-heading">No of souls who joined the fellowship </span> :  {{ $report->evangelism_number_of_souls_who_joined_fellowship }}</td>
                        </tr>
                        <tr>
                            <td class="half-content"><span class="data-heading">Number of converts baptized </span> :  {{ $report->evangelism_number_of_converts_baptized }}</td>
                        </tr>
                    </table>
                   
                </div>
            </div>

            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 8 - OFFERING </th>
                        </tr>                               
                    </table>
                    
                </div>
                <div class="section-body">
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">Total Bible Study Offering for the Month  </span> : &#8358;{{ $report->bible_study_offering }}</td>
                            <td class="half-content"><span class="data-heading">Total Prayer Meeting Offering for the Month </span> : &#8358; {{ $report->prayer_meeting_offering }}</td>
                        </tr>
                        <tr>
                            <td class="half-content"><span class="data-heading">Total Special Programme Offering </span> :  &#8358;{{ $report->special_program_offering }}</td>
                            <td class="half-content"><span class="data-heading">Other Special Programme Offering  </span> :  &#8358;{{ $report->other_special_program_offering }}</td>
                        </tr>
                        <tr>
                            <td class="half-content"><span class="data-heading">Thanksgiving Offering (First Sunday Service) for the Month </span> :  &#8358;{{ $report->thanksgiving_offering }}</td>
                            <td class="half-content"><span class="data-heading">Total Sunday worship offering (excluding First Sunday)  </span> :  &#8358;{{ $report->total_sunday_worship_offering }}</td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">Grand Total Offering </span> :  &#8358;{{ $report->grand_total_offering }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 9 - TITHE </th>
                        </tr>                               
                    </table>
                    
                </div>
                <div class="section-body">
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">President's Tithe</span> : &#8358;{{ $report->president_tithe }}</td>
                            <td class="half-content"><span class="data-heading">Total Executive Tithe </span> : &#8358; {{ $report->total_executive_tithe }}</td>
                        </tr>
                        <tr>
                            <td class="half-content"><span class="data-heading">Total Workers Tithe </span> :  &#8358;{{ $report->total_workers_tithe }}</td>
                            <td class="half-content"><span class="data-heading">Total Members Tithe </span> :  &#8358;{{ $report->total_members_tithe }}</td>
                        </tr>
                        <tr>
                            <td class="half-content"><span class="data-heading">Grand Total Tithe </span> :  &#8358;{{ $report->grand_total_tithe }}</td>
                            <td class="half-content"><span class="data-heading">Tithe of Tithe (to be remitted to National Secretariat) </span> :  &#8358;{{ $report->grand_total_tithe }}</td>
                        </tr>
                    </table>
                   
                </div>
            </div>

            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 10 - OTHER CHAPTER LEVIES/CONTRIBUTION </th>
                        </tr>                               
                    </table>
                    
                </div>
                <div class="section-body">
                    <table>
                        <tr>
                            <td><span class="data-heading">Purpose</span> : &#8358;{{ $report->other_levies_purpose }}</td>
                    </table>
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">Projection</span> : &#8358;{{ $report->other_levies_projection }}</td>
                            <td class="half-content"><span class="data-heading">Period of collection </span> : &#8358;{{ $report->other_levies_period_of_collection }}</td>
                        </tr>
                        <tr>
                            <td class="half-content"><span class="data-heading">Total amount collected this month </span> :  &#8358;{{ $report->other_levies_total_amount }}</td>
                            <td class="half-content"><span class="data-heading">Total Accumulation since program began </span> :  &#8358;{{ $report->other_levies_total_accumulation }}</td>
                        </tr>
                       
                    </table>
                   
                </div>
            </div>

            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 11 - EXPENSES </th>
                        </tr>                               
                    </table>
                </div>
                <div class="section-body">
                   
                    <table>
                        <tr>
                            <td class="half-content"><span class="data-heading">Capital Projects</span> : &#8358;{{ $report->capital_projects }}</td>
                            <td class="half-content"><span class="data-heading">Recurrent Expenses </span> : &#8358;{{ $report->recurrent_expenses }}</td>
                        </tr>
                        <tr>
                            <td class="half-content"><span class="data-heading">Maintenance </span> :  &#8358;{{ $report->maintenance }}</td>
                            <td class="half-content"><span class="data-heading">Miscellaneous </span> :  &#8358;{{ $report->misc }}</td>
                        </tr>
                        <tr>
                            <td class="half-content"><span class="data-heading">Grand Total </span> :  &#8358;{{ $report->expenses_grand_total }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 12 - SUMMARY </th>
                        </tr>                               
                    </table>
                </div>
                <div class="section-body">
                   
                    <table>
                        <tr>
                            <td><span class="data-heading">Give a brief summary of the spiritual state of the fellowship (may include outstanding testimonies) in the month</span> : {{ $report->spiritual_state }}</td>
                        </tr>
                        <tr>
                            <td><span class="data-heading">Any Challenge(s) or development which you want the NCP to be aware of?</span> : {{ $report->spiritual_state }}</td>
                        </tr>
                        <tr>
                            <td><span class="data-heading">Any proposed Capital Project </span> :  {{ $report->proposed_capital_project }}</td>
                        </tr>
                        <tr>
                            <td><span class="data-heading">Any completed Capital Project: </span> :  {{ $report->completed_capital_project }}</td>
                        </tr>
                        
                    </table>
                </div>
            </div>

            <div class="section">
                <span id="Lblcontent1">
                <div class="section-head">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 13 - SIGNATURES AND DATES </th>
                        </tr>                               
                    </table>
                </div>
                <div class="section-body">
                   
                    <table>
                       
                        <tr>
                            <td class="half-content"><span class="data-heading">President's Signature</span><br>
                                <img class="signatures" src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->signature }}" alt="">
                            </td>
                            <td class="half-content"><span class="data-heading">Gen Sec's signature</span> <br>
                                <img class="signatures" src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->gen_sec_signature }}" alt="">
                            </td>
                        </tr>
                        <tr>
                            <td class="half-content"><span class="data-heading">Fin Sec's Signature</span><br>
                                <img class="signatures" src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->fin_sec_signature }}" alt="">
                            </td>
                            <td class="half-content"><span class="data-heading">Evang Sec's signature</span> <br>
                                <img class="signatures" src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->evang_sec_signature }}"  alt="">
                            </td>
                        </tr>
                        <tr ><th class="sub-section">ZONAL PASTOR'S APPROVAL</th></tr>     
                        <tr>
                            <td class="half-content"><span class="data-heading">I, {{  $report->zonal_pastor_approval }} </td>
                            <td class="half-content"><span class="data-heading">strongly affirm that the above information is true and agrees with my own records. </span> : {{ $report->sunday_school_lowest_attendance }}</td>
                        </tr>
                    </table>
                </div>
            </div>



            <div class="section">
                <span id="Lblcontent1">
                <div class="sub-section">
                    <table>
                        <tr class="table-row">
                            <th style="padding: 10px 0 10px 5px">SECTION 14: OFFICIAL</th>
                        </tr>                               
                    </table>
                </div>
                <div class="section-body">
                    <table>
                        <tr>
                            <td><span class="data-heading">Field Pastor comment</span><br>
                                {{ $report->field_pastor_comment }}
                            </td>
                        </tr><br>
                        <tr>
                            <td><span class="data-heading">NCP's comment</span><br>
                                {{ $report->ncp_comment }}
                            </td>
                        </tr>                      
                        
                    </table>
                </div>
            </div>
            
        </div>
        <div style="padding:20px">
            <center>
                <a class="buttons" id="lnkclose" href="{{ route('stakeholder.dashboard') }}">BACK</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a onclick="javascript:window.print();" id="LinkButton1" href="javascript:__doPostBack(&#39;LinkButton1&#39;,&#39;&#39;)">PRINT</a>
            </center>
        </div>
    </div>
</body>

</html>