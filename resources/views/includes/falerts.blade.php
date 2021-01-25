@if(session()->get('message'))
<div class="alert alert-success" role="alert" style="width: 100%;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Success!</strong> {{ session()->get('message')}}
      </div>
@endif

@if(session()->get('error'))
<div class="alert alert-danger" role="alert" style="width: 100%;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Whoops!</strong> {{ session()->get('error')}} </strong>
      </div>
@endif
@if(session()->get('id_not_found'))
<div class="alert alert-danger" role="alert" style="width: 100%;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Whoops!</strong> {{ session()->get('id_not_found')}}Please click <a target="_blank" href="https://thewaacsp.com/english/get-started"> <b>HERE</b></a> to get a WAACSP membership ID! </strong>
</div>
@endif

@if(session()->get('warning'))
<div class="alert alert-warning" role="alert" style="width: 100%;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong style="color:black">Hey!</strong> <span style="color:black">{{ session()->get('warning')}}</span>
      </div>
@endif

@if(session()->get('response'))
@foreach(session()->get('response')  as $in)
  <div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="user-profile-social-list">
            <table class="table small m-b-xs">
                <tbody>
                  <tr>
                    <td>
                        Status: {!! $in['status'] !!}
                    </td>
                  </tr>
                  <tr>
                    <td>
                        Rating: <strong>{{$in['rating']}}</strong>
                    </td>
                </tr>
                    <tr>
                        <td>
                            Score: <strong>{{$in['score']}}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                          Name: <strong>{{$in['name']}}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> 
    <div class="col-lg-6 col-md-6 col-sm-6">
      <div class="user-profile-social-list">
          <table class="table small m-b-xs">
              <tbody>
                <tr>
                  <td>
                    Email: <strong>{{$in['email']}}</strong>
                  </td>
              </tr>
                <tr>
                  <td>
                      Training: <strong>{{$in['training']}}</strong>
                  </td>
              </tr>
                  <tr>
                      <td>
                          Balance: <strong>{{$in['balance']}}</strong>
                      </td>
                  </tr>
                  <tr>
                    <td>Date Certified: <strong>{{ \Carbon\Carbon::parse($in['registered'])->format('j F, Y') }}</strong></td>
                        {{-- Name: <strong>{{$in['registered']}}</strong> --}}
                  </tr>
                 
              </tbody>
          </table>
      </div>
  </div> 
    
</div>
@endforeach
@endif