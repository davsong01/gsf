@extends('layouts.dashboard')
@section('title', 'New Stakeholder')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('staff.index') }}">All Stakeholders</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Create stakeholder</li>
@endsection
@section('content')
 <div class="content-body">
     <!-- Basic Inputs start -->
     <section id="basic-input">
         <div class="row">
             <div class="col-md-12">
                 <div class="card">
                     <div class="card-header">
                         @include('includes.alerts')
                     </div>
                     <div class="card-content">
                         <div class="card-body">
                             <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
                             @csrf
                            
                             <div class="row">
                                 <div class="col-md-12 col-sm-12">
                                     <label class="sections">Personal Details</label> <br>
                                 </div>
                                 <div class="col-md-4 col-sm-12">
                                     <fieldset class="form-group">
                                         <label for="name">Name</label>
                                         <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter name" required>
                                     </fieldset>
                                 </div>
                                 <div class="col-md-4 col-sm-12">
                                     <fieldset class="form-group">
                                         <label for="phone">Phone</label>
                                         <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter phone">
                                     </fieldset>
                                 </div>
                                 <div class="col-md-4 col-sm-12">
                                     <fieldset class="form-group">
                                         <label for="email">Email</label>
                                         <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter address" required>
                                     </fieldset>
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-12 col-sm-12">
                                     <label class="sections">Birthday Details</label> <br>
                                 </div>
                                 <div class="col-md-4 col-sm-12">
                                     <fieldset class="form-group">
                                         <label for="day">Day</label>
                                         <select class="form-control" name="day"
                                             id="day" required>
                                             <option value="">--Select Option--</option>
                                             @foreach(range(1,31) as $day)
                                             <option value="{{ $day}}" {{ old('day') == $day ? 'selected' : '' }}>{{ $day }}</option>
                                             @endforeach
                                         </select>
                                     </fieldset>
                                 </div>
                                 <div class="col-md-4 col-sm-12">
                                     <fieldset class="form-group">
                                         <label for="month">Month</label>
                                         <select class="form-control" name="month"
                                             id="month" required>
                                             <option value="">--Select Option--</option>
                                             @foreach($months as $month=>$value)
                                             <option value="{{ $value }}" {{ old('month') == $value ? 'selected' : '' }}>{{ $month }}</option>
                                             @endforeach
                                         </select>
                                     </fieldset>
                                 </div>
                                 <div class="col-md-4 col-sm-12">
                                     <fieldset class="form-group">
                                         <label for="year">Year (Optional), e.g. {{ date('Y') }}</label>
                                         <input type="text" class="form-control" id="year" pattern="^\d{4}$" name="year" value="{{ old('year') }}" placeholder="Enter year">
                                     </fieldset>
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-12 col-sm-12">
                                     <label class="sections">Official Details</label> <br>
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-6 col-sm-12">
                                     <fieldset class="form-group">
                                         <label for="role">Role</label>
                                         <select class="form-control" name="role" id="role" required>
                                             <option value="">--Select--</option>
                                             <option value="President" {{ old('role') == 'President' ? 'selected' : ''}}>President</option>
                                             <option value="Zonal Pastor" {{ old('role') == 'Zonal Pastor' ? 'selected' : ''}}>Zonal Pastor</option>
                                             <option value="Field Pastor" {{ old('role') == 'Field Pastor' ? 'selected' : ''}}>Field Pastor</option>
                                             <option value="Secretariat" {{ old('role') == 'Secretariat' ? 'selected' : ''}}>Secretariat</option>
                                             <option value="Financial Secretary" {{ old('role') == 'Financial Secretary' ? 'selected' : ''}}>Financial Secretary</option>
                                             <option value="Portfolio" {{ old('role') == 'Portfolio' ? 'selected' : ''}}>Portfolio</option>
                                         </select>
                                     </fieldset>
                                 </div>
                                 <div class="col-md-6 col-sm-12">
                                     <fieldset class="form-group selectfield" style="display:none"}}>
                                         <label for="field_id">Field</label>
                                         <select class="form-control" name="field_id" id="field_id">
                                             <option value="">--Select--</option>
                                             @foreach($fields as $field)
                                             <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : ''}}>{{ $field->name }} </option>
                                             @endforeach
                                         </select>
                                     </fieldset>
                                     <fieldset class="form-group selectzone" style="display:none">
                                         <label for="zone_id">Zone</label>
                                         <select class="form-control" name="zone_id" id="zone_id">
                                             <option value="">--Select--</option>
                                             @foreach($zones as $zone)
                                             <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : ''}}>{{ $zone->name }}</option>
                                             @endforeach
                                         </select>
                                     </fieldset>
                                     <fieldset class="form-group selectportfolio" style="display:none">
                                         <label for="portfolio">Portfolio</label>
                                         <select class="form-control" name="portfolio" id="portfolio">
                                             <option value="">--Select--</option>
                                             @foreach($portfolios as $portfolio)
                                             <option value="{{ $portfolio }}" {{ old('portfolio') == $portfolio ? 'selected' : ''}}>{{ $portfolio }}</option>
                                             @endforeach
                                         </select>
                                     </fieldset>
                                     <fieldset class="form-group selectchapter" style="display:none">
                                         <label for="chapter_id">Chapter</label>
                                         <select class="form-control" name="chapter_id" id="chapter_id">
                                             <option value="">--Select--</option>
                                             @foreach($chapters as $chapter)
                                             <option value="{{ $chapter->id }}" {{ old('chapter_id')== $chapter->id ? 'selected' : ''}}>{{ $chapter->name }}</option>
                                             @endforeach
                                         </select>
                                     </fieldset>
                                     
                                 </div>
                             </div>
 
                             <div class="row">
                                 <div class="col-md-12 col-sm-12">
                                     <fieldset class="form-group">
                                         <label for="password">Password(Default: 12345@GSF2021)</label>
                                         <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="Enter password or leave blank to use default">
                                     </fieldset>
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-6 col-sm-12">
                                     <fieldset class="form-group">
                                         <label for="signature">Upload Signature</label><br>
                                         <input type="file" class="form-control" id="signature" name="signature" value="{{ old('signature') }}" placeholder="Upload signature">
                                     </fieldset>
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-12 col-sm-12">
                                     <button class="btn btn-primary" style="width:100%" type="submit">Save</button>
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
@section('extra_scripts')
<script> 
$('#role').on('change', function(){
        console.log($('#role').val());
        if($('#role').val() == 'President'){
            $('.selectchapter').css('display','block');
            $('.selectzone').css('display','none');
            $('.selectfield').css('display','none');
            
        }else $('.selectchapter').css('display','none');  

        if($('#role').val() == 'Zonal Pastor'){
            $('.selectzone').css('display','block');
            $('.selectchapter').css('display','none');
            $('.selectfield').css('display','none');
            
        }else {
            $('.selectzone').css('display','none'); 
            
        } 

        if($('#role').val()=='Field Pastor'){
            $('.selectfield').css('display','block');
            $('.selectchapter').css('display','none');
            $('.selectzone').css('display','none');
            
        }else $('.selectfield').css('display','none'); 

        if($('#role').val() == 'Portfolio'){
            $('.selectportfolio').css('display','block');
            $('.selectfield').css('display','none');
            $('.selectchapter').css('display','none');
            $('.selectzone').css('display','none');
            
        }else $('.selectportfolio').css('display','none'); 

        if($('#role').val() == 'Secretariat' || $('#role').val() == 'Financial Secretary' ){
            $('.selectportfolio').css('display','none');
            $('.selectfield').css('display','none');
            $('.selectchapter').css('display','none');
            $('.selectzone').css('display','none');
        }

});               

</script>
@endsection