<div class="col-12 col-md-6 col-lg-4">
    <div class="card border-light mb-4 animate-up-5"><a href="{{ route('campus.single', $chapter->id) }}"
        class="position-relative"><img style="height: 240px;" src="{{ asset($chapter->banner ?? 'gsfcom/images/image-office.jpg') }}"
        class="card-img-top rounded-xl p-2" alt="{{ $chapter->banner }}"></a>
    <div class="card-body" style="height: 250px;"><a href="{{ route('campus.single', $chapter->id) }}">
        <h4 class="h5">{{$chapter->name}}</h4>
        </a>
        <div class="d-flex my-2">
        <span class="badge badge-pill badge-primary">{{ ($chapter->users_count > 0) ? $chapter->users_count .'+ Members' : ''}}</span></div>
        <ul class="list-group mb-3">
        <li class="list-group-item small p-0"><span
            class="fas fa-map-marker-alt mr-2"></span>{{ $chapter->field->name }} <span class="thick-line">|</span> {{ $chapter->zone->name }} 
        </li>
        @if($chapter->address)
        <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>{{$chapter->address}}</li>
        @else 
        <li class="list-group-item small p-0"> &nbsp;  </li>

        @endif
        @if($chapter->email)
        <li class="list-group-item small p-0"><span class="fas fa-envelope mr-2"></span>{{ $chapter->email }}</li>
        @else 
        <li class="list-group-item small p-0"> &nbsp; </span></li>
        @endif
        </ul>
    </div>
    </div>
</div>