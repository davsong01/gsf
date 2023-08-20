@extends('frontend.spaces.layouts.app')
@section('title', 'NEC')

@section('css')
<style>
    .our-team{
      text-align: center;
    }
    .our-team .pic{
        position: relative;
    }
    .our-team .pic img{
        width: 100%;
        height: auto;
        border-radius: 50%;
    }
    .social_media_team{
        background: rgba(59, 61, 66, 0.6);
        border-radius: 50%;
        transform: scale(0);
        transition: all 0.35s ease-in-out 0s;
        visibility: hidden;
        position: absolute;
        top:0;
        left:0;
        width: 100%;
        height: 100%;
        text-align: center;
    }
    .our-team:hover .social_media_team{
        transform: scale(1);
        visibility: visible;
    }
    .team_social{
        padding: 0;
        list-style: none;
        margin-bottom: 0;
        position: relative;
        top:43%;
        left:0;
    }
    .team_social > li{
        display: inline-block;
    }
    .team_social > li > a{
        width: 35px;
        height: 35px;
        display: block;
        background: #5d5d5d;
        line-height: 35px;
        color:#fff;
        transition: all 0.35s ease-in-out 0s;
        border-radius: 3px;
        font-size: 15px;
    }
    .team_social > li > a:hover{
        background: #31aab5;
    }
    .post-title > a{
        color:#fff;
        font-size: 16px;
        font-style: normal;
        font-weight: 700;
        line-height: 18px;
        text-transform: capitalize;
    }
    .post-title > a:after{
        content:"|";
        color: #31aab5;
        display: inline-block;
        padding: 0 5px 0 10px;
    }
    .post-title small{
        color:#999;
        font-size: 12px;
    }
    .description{
        color:#fff;
    }
    .read{
        font-size: 13px;
        font-style: italic;
        font-weight: 400;
        color:#31aab5;
    }
    .read:hover{
        color:#fff;
    }
    @media screen and (max-width: 990px){
        .our-team{
            margin-bottom: 30px;
        }
    }
</style>
@endsection
@section('content')
  <div class="section section-header section-image bg-tertiary overlay-primary text-white overflow-hidden pb-6"
    data-background="../assets/img/new-york-hero.jpg">
    <div class="container z-2">
      <div class="row justify-content-center pt-3">
        <div class="col-12 text-center">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-transparent justify-content-center mb-4">
              <li class="breadcrumb-item text-secondary"><a href="/">Home</a>
              </li>
              <li class="breadcrumb-item text-muted active" aria-current="page">NEC</li>
            </ol>
          </nav>
          <h1 class="mb-5">GSF National Executive Council<span class="font-weight-bolder"></span></h1>
        </div>
      </div>
    </div>
  </div>
  <div class="container" style="margin-top:20px">
    <div class="row">
      @foreach($nec as $n)
        <div class="col-md-2 col-sm-6" style="margin-bottom: 40px;">
            <div class="our-team">
                <div class="pic">
                    <img src="{{ !is_null($n->passport) ? asset($n->passport) : asset('frontend/passports/avatar.jpg') }}" alt="">
                    <div class="social_media_team">
                        {{-- <ul class="team_social">
                            <li><a href="#"><i class="fa fa-envelope"></i></a></li>
                            <li><a href="#"><i class="fab fa-google-plus"></i></a></li>
                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-facebook"></i></a></li>
                        </ul> --}}
                    </div>
                </div>
                <div class="team-prof">
                    <h6 class="card-title mb-2">{{ $n->name }}</h3>
                     <small style="color: #c33c54;; font-size:100%">{{ $n->office }}</small>
                </div>
            </div>
        </div>
      @endforeach
    </div>
</div>
  <div class="section section-lg pt-6">
    <div id="spaces-container" class="container">
      
{{--       
        {{ $nec->links()}} --}}
      </div>
    </div>
  </div>
@endsection
@section('js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
  <script type="text/javascript">
    var path = "{{ route('alumni.suggestions') }}";
    $('#name').typeahead({
        source: function (query, process) {
            return $.get(path, {
                query: query
            }, function (data) {
                return process(data);
            });
        }
    });

    var path2 = "{{ route('campus.suggestions') }}";
    $('#school').typeahead({
        source: function (query, process) {
            return $.get(path2, {
                query: query
            }, function (data) {
                return process(data);
            });
        }
    });
</script>
@endsection