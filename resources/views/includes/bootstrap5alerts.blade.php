{{-- Success message --}}
@if(session()->get('message'))
<div class="alert alert-success alert-dismissible fade show w-100" role="alert">
    {{ session()->get('message') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Error message --}}
@if(session()->get('error'))
<div class="alert alert-warning alert-dismissible fade show w-100" role="alert">
    {{ session()->get('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Import failures --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"></button>
    </div>
@endif


{{-- Warning --}}
@if(session()->get('warning'))
<div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
    {{ session()->get('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Generic alert --}}
@if(session()->get('any'))
<div class="alert alert-warning alert-dismissible fade show w-100" role="alert">
    {{ session()->get('any') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
