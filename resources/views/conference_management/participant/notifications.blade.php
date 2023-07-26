@extends('dashboard')
@section('content')

<div class="row">
    <div class="col-lg-12">
        @if($categories->count() <= 0)
        <div class="sparkline12-list shadow-reset mg-t-30">
            <div class="sparkline12-hd">
                <div class="main-sparkline12-hd">
                    <h1>Sorry, No questions available for {{$appraisee->name}} at the moment, please consult a system administrator</h1>
                </div>
            </div>
        </div>
        @endif
        @if($categories->count() > 0)
        <div class="sparkline12-list shadow-reset mg-t-30">
            <div class="sparkline12-hd">
                <div class="main-sparkline12-hd">
                    <h1>Kindly Complete Appraisals For {{$appraisee->name}}</h1><br>
                    <p style="color:red"><strong>Please rate between 1 & 100. 1 being the lowest and 100 the highest.</strong></p>
                     @include('includes.alerts')
                </div>
            </div>
            <div class="sparkline12-graph">
                <div class="basic-login-form-ad">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="all-form-element-inner">
                                <form action="{{ route('submit_appraisal')}}"  method="POST">
                                {{csrf_field()}}
                                @foreach($categories as $category)
                                
                                    @if($category->questions->count() > 0)
                                        <div class="form-group-inner" style="border-bottom: solid 1px #ccc">
                                        <h1 style="text-align:left; background-color:#4f1747; color:#fff; padding:5px"> {{$category->type}}</h1>
                                        
                                        @foreach($category->questions as $question) 
                                        <div class="row">
                                            <div class="col-lg-12" style="margin-bottom:10px;">
                                            <label class="" style="color:#4f1747; text-align: left !important; font-weight:normal; display: block;">{{ $i ++ }}. {!! $question->question !!}</label>
                                            </div>
                                            <div class="col-lg-12" style="margin-bottom:20px;">
                                                <input value="{{ old($category->id.'_'.$question->id) }}" type="number" name="{{$category->id.'_'.$question->id}}" min="1" max="100" class="form-control" placeholder="Rate between 1 and 100" required />
                                            </div>
                                        </div>
                                        
                                        @endforeach
                                        </div>
                                    @endif
                                @endforeach
                                <div class="row">
                                    <div class="col-lg-12" style="margin-bottom:20px;">
                                        <label class="" style="color:#4f1747; text-align: left !important; font-weight:normal; display: block;">- List 2 key strengths of {{$appraisee->name}}</label>
                                   
                                        <textarea name="strength" id="" cols="30" rows="2" class="form-control" required></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12" style="margin-bottom:20px;">
                                        <label class="" style="color:#4f1747; text-align: left !important; font-weight:normal; display: block;">- List 2 weaknesses of {{$appraisee->name}}</label>
                                   
                                        <textarea name="weakness" id="" cols="30" rows="2" class="form-control" required></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12" style="margin-bottom:20px;">
                                        <label class="" style="color:#4f1747; text-align: left !important; font-weight:normal; display: block;">- Recommend two (2) capacity building programs for {{$appraisee->name}}</label>
                                   
                                        <textarea name="recommendedprogram" id="" cols="30" rows="2" class="form-control" required></textarea>
                                    </div>
                                </div>
                                <input type="hidden" name="allocation_id" value = {{$allocation_id}} class="form-control" />
                                <input type="hidden" name="appraisee_id" value = {{$appraisee->id}} class="form-control" />
                                
                                    <div class="form-group-inner">
                                        <div class="login-btn-inner">
                                            <div class="row">
                                                <div class="col-md-12"></div>
                                              
                                                    <div class="">
                                                        {{-- <a href="{{ route('index') }}" class="btn btn-white" >Cancel</a> --}}
                                                        <button class="btn btn-sm btn-primary submit-button" style="padding:15px">Submit Appraisal</button>
                                                    </div>
                                              
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection