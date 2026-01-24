@extends('frontend.spaces.layouts.app')
@section('title', 'Members')

@section('css')
<style>
    /* Rounded member images */
    .list-image {
        border-radius: 50% !important;
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 2px solid #0d6efd;
        margin: auto;
        display: block;
    }

    .card-member {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .card-member:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .member-name {
        font-weight: 600;
        margin-bottom: 0;
    }

    .member-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        display: inline-block;
        margin-right: 5px;
    }

    .bg-student { background-color: #0d6efd; color: #fff; }
    .bg-alumni { background-color: #dc3545; color: #fff; }

    .typeahead.dropdown-menu {
        width: 100%;
        left: 0 !important;
        max-height: 200px;
        overflow-y: auto;
    }

    .search-card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: box-shadow 0.3s ease;
    }

    .search-card:hover {
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    .section-header {
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .overlay-primary::before {
        content: '';
        position: absolute;
        inset: 0;
        background: #26408b !important;
        z-index: 1;
    }
    .section-header .container { position: relative; z-index: 2; }
</style>
@endsection

@section('content')
<div class="section section-header section-image bg-tertiary overlay-primary text-white overflow-hidden pb-6"
    data-background="../assets/img/new-york-hero.jpg">
    <div class="container z-2 text-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-transparent justify-content-center mb-3">
                <li class="breadcrumb-item text-secondary"><a href="/">Home</a></li>
                <li class="breadcrumb-item text-muted active" aria-current="page">Members</li>
            </ol>
        </nav>
        <h1 class="mb-4">Find a GSF Member</h1>
        <p class="lead">Search for members by name or school</p>
    </div>
</div>

<div class="section section-lg pt-6">
    <div class="container" id="spaces-container">

        <!-- Search Form -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card p-3 p-md-4 search-card">
                    <form autocomplete="off" class="row g-3" method="GET" action="{{ route('member.search') }}">
                        <div class="col-12 col-lg-5">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input id="name" name="name" type="text" class="form-control" placeholder="Type a name">
                            </div>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fas fa-university"></i></span>
                                <input id="school" name="school" type="text" class="form-control" placeholder="Type school name or leave empty">
                            </div>
                        </div>
                        <div class="col-12 col-lg-2">
                            <button class="btn btn-primary btn-lg w-100" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Members Count -->
        <div class="row mb-7">
            <div class="col-12 text-end">
                <h5>Total Members: <span class="text-primary">{{$alumnis->count()}}</span></h5>
            </div>
        </div>

        <!-- Member Cards -->
        <div class="row g-4">
            @foreach($alumnis as $user)
                @include('frontend.spaces.includes.user_block')
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                {{ $alumnis->links() }}
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script type="text/javascript">
    $('#name').typeahead({
        source: function (query, process) {
            return $.get("{{ route('alumni.suggestions') }}", { query: query }, function (data) { return process(data); });
        }
    });

    $('#school').typeahead({
        source: function (query, process) {
            return $.get("{{ route('campus.suggestions') }}", { query: query }, function (data) { return process(data); });
        }
    });
</script>
@endsection
