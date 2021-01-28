@if(session()->get('message'))
<div class="alert alert-success" role="alert" style="width: 100%;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Success!</strong> {{ session()->get('message')}}
      </div>
@endif

@if(session()->get('error'))
<div class="alert alert-warning" role="alert" style="width: 100%;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Whoops!</strong> {{ session()->get('error')}} </strong>
      </div>
@endif


@if(session()->get('warning'))
<div class="alert alert-danger" role="alert" style="width: 100%;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong style="color:black">Hey!</strong> <span style="color:black">{{ session()->get('warning')}}</span>
</div>
@endif

@if(session()->get('any'))
<div class="alert alert-warning" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong>Whoops!</strong> {{ session()->get('any')}}
</div>
@endif