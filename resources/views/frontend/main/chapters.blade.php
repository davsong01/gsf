@extends('frontend.main.mainlayout')
@section('title', 'Chapters')
@section('content')
<div class="page-bread mb20">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>GSF Community - Chapters</h3>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>
<div class="container mb0">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center center-heading mb40">
            <h2>GSF Chapters</h2>
            <p>
                List most recent places are submitted by our users. It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.
            </p>
                    <div class="form-group">
                        <input type="text" name="product_name" id="product_name" class="form-control input-lg"
                               placeholder="Start typing to search for a GSF chapter"/>
                        <div id="productList">
                        </div>
                    </div>
                    {{ csrf_field() }}
        </div>
    </div><!--/row-->
</div>

<div class="">
    <div class="container">
        <div class="row">
            @foreach($chapters as $chapter)
            <div class="col-sm-6 col-md-3 mb30">
                <div class="card-overlay">
                    <img src="{{ asset($chapter->banner ?? 'main/images/chapters/coming-soon.png') }}" style="width:100%;height:170px" class="img-responsive" alt="{{ $chapter->banner }}">
                    <div class="card-hover">
                        <div class="card-content">
                            <h3><a href="{{ route('campus.single', $chapter->id) }}">{{ $chapter->name }}</a></h3>
                            <a class="label view-campus-details label-primary" href="{{ route('campus.single', $chapter->id) }}">View details</a>
                        </div><!--/card-content-->
                    </div>
                </div>
            </div> 
            @endforeach
        </div>
    </div>
</div>  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $('#product_name').keyup(function () {
            var data = $(this).val();
            if (data != '') {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('campus.autocomplete') }}",
                    method: "POST",
                    data: {data: data, _token: _token},
                    success: function (data) {
                        $('#productList').fadeIn();
                        $('#productList').html(data);
                    }
                });
            }
        });
        $(document).on('click', 'li', function () {
            $('#product_name').val($(this).text());
            $('#productList').fadeOut();
        });
    });
</script>
@endsection
