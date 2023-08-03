<div class="col-12 col-sm-10 col-md-4 col-lg-12 mb-4">
    <div class="card border-light mb-4 animate-up-5">
    <div class="row no-gutters align-items-center">
        <div class="col-12 col-lg-6 col-xl-5"><a href="{{ route('user.single', $alumni->slug) }}" style="padding:20px"><img
            src="{{ !is_null($alumni->passport) ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg') }}" alt="{{ $alumni->passport }}"
            alt="private office" class="card-img p-2 rounded-sm list-image"></a></div>
        <div class="col-12 col-lg-6 col-xl-7">
        <div class="card-body"><a href="{{ route('user.single', $alumni->slug) }}">
            <h4 class="h5">{{ $alumni->name }}</h4>
            </a>
            <div class="d-flex my-3"><span class="star fas fa-star text-warning"></span> <span
                class="star fas fa-star text-warning"></span> <span
                class="star fas fa-star text-warning"></span> <span
                class="star fas fa-star text-warning"></span> <span
                class="star fas fa-star text-warning"></span> <span
                class="badge badge-pill badge-primary ml-2">5.0</span></div>
            <ul class="list-group mb-3">
            <li class="list-group-item small p-0"><span class="fas fa-map-marker-alt mr-2"></span>New
                York, Manhattan, USA</li>
            <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2"></span>Old Street
                (2 mins walk)</li>
            </ul>
            <div class="d-flex justify-content-between">
            <div class="col pl-0"><span class="text-muted font-small d-block">Monthly</span> <span
                class="h6 text-dark font-weight-bold">500$</span></div>
            <div class="col"><span class="text-muted font-small d-block">People</span> <span
                class="h6 text-dark font-weight-bold">12</span></div>
            <div class="col pr-0"><span class="text-muted font-small d-block">Sq.Ft</span> <span
                class="h6 text-dark font-weight-bold">1200</span></div>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>