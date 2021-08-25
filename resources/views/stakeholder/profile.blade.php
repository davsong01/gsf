@extends('layouts.stakeholderdashboard')
@section('title', 'Profile')

@section('active')
<li class="breadcrumb-item">Profile</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Profile</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('stakeholder.saveprofile') }}"
                                onsubmit="return confirm('You are about to update your profile');"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? Auth::guard('stakeholder')->user()->name }}" placeholder="Enter name" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') ?? Auth::guard('stakeholder')->user()->phone }}" placeholder="Enter phone" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email" value="{{ old('email') ?? Auth::guard('stakeholder')->user()->email }}" placeholder="Enter email address" required>
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-md-12 col-sm-12">
                                        <label>Birthday Details</label> <br>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="day">Day</label>
                                            <input type="number" min="1" max="31" class="form-control" id="day" name="day" value="{{ old('day') ?? Auth::guard('stakeholder')->user()->day }}" placeholder="Enter day" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="month">Month</label>
                                            <input type="number" min="1" max="12" class="form-control" id="month" name="month" value="{{ old('month') ?? Auth::guard('stakeholder')->user()->month }}" placeholder="Enter month" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="year">Year (Optional), e.g. {{ date('Y') }}</label>
                                            <input type="text" class="form-control" id="year" pattern="^\d{4}$" name="year" value="{{ old('year') ?? Auth::guard('stakeholder')->user()->year}}" placeholder="Enter year">
                                        </fieldset>
                                    </div>
                                    @if(Auth::guard('stakeholder')->user()->role == 'President')
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <img src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->signature }}" style="width:60px" alt=""><br>
                                                <label for="signature">Replace President Signature</label>
                                                <input type="file" value="{{ old('signature') }}" class="form-control" name="signature">
                                        </fieldset>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <img src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->gen_sec_signature }}" style="width:60px" alt=""> <br>
                                                <label for="signature">Replace Gen Sec's Signature</label>
                                                <input type="file" value="{{ old('gen_sec_signature') }}" class="form-control" name="gen_sec_signature"
                                                id="gen_sec_signature">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <img src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->fin_sec_signature }}" style="width:60px" alt=""> <br>
                                                <label for="signature">Replace Fin Sec's Signature</label>
                                                <input type="file" value="{{ old('fin_sec_signature') }}" class="form-control" name="fin_sec_signature"
                                                id="fin_sec_signature">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <img src="/stakeholdersignature/{{ Auth::guard('stakeholder')->user()->evang_sec_signature }}" style="width:60px" alt=""> <br>
                                                <label for="signature">Replace evang Sec's Signature</label>
                                                <input type="file" value="{{ old('evang_sec_signature') }}" class="form-control" name="evang_sec_signature"
                                                id="evang_sec_signature">
                                        </fieldset>
                                    </div>
                                    @endif
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="password">Change Password(Default: 12345@GSF2021)</label>
                                            <input type="text" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="Enter password or leave blank to use default">
                                        </fieldset>
                                    </div>
                                    
                                </div>
                            </div>
                                                   
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <button class="btn btn-primary" style="width:100%" type="submit">Save</button>
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