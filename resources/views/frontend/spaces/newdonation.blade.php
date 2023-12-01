@extends('frontend.spaces.layouts.app')
@section('title', 'Make Donations')
@section('ogtitle', 'Make Donations')
@section('ogdescription')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
      blockquote {
        font-size: 1.27rem;
        background: #547d9b;
        border-radius: 10px;
        padding: 25px;
        font-style: italic;
      }
    </style>
@endsection
@section('content')
  <section class="section section-header bg-primary overlay-primary text-white pb-2" style="margin-top:0px" !important>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12 col-md-8 text-center">
            <h1 class="display-2 mb-4">Donate to GSF </h1>
          </div>
        </div>
      </div>
    </section>
    <section class="min-vh-80 d-flex align-items-center" style="margin-top:50px">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-12">
            <div class="text-center text-md-center mb-5 mt-md-0 text-white" style="margin-top:20px">
              <h1 class="mb-0 h3" style="color:black">Cheerful Donation</h1> <br>
              <blockquote>"For God so loved the world that he gave ..." (John 3:16)</blockquote>
              <p class="text-center text-md-center mb-5 mt-md-0" style="color:black">
                 God gave up his Son, Jesus Christ, who left behind the glorious riches of heaven, to come to earth. Jesus loved us with compassion and empathy. He willingly gave up his life. He loved the world so much that he died to give us eternal life.Is there any better way to learn how to be a voluntary and cheerful giver than to observe the way Jesus gave? Jesus never once complained about the sacrifices he made. <br>Our heavenly Father loves to bless his children with good gifts. Likewise, God desires to see his own nature duplicated in his children. Cheerful giving is God's grace revealed through us. Here is an opportunity to practise God's own nature by willingly giving to support God's work through the GOFAMINT Student's Fellowship
              </p>
            </div>
          </div>
          <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
            <div class="signin-inner mt-3 mt-lg-0 bg-white shadow-soft border rounded border-light p-4 p-lg-5 w-100 fmxw-900">
                @include('includes.alerts')
                <form action="{{ route('newalumni.save') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                    <div class="col-md-12">
                      <div class="mb-4">
                        <label for="name">Name</label>
                        <input class="form-control" required id="name" name="name" value="{{old('name')}}" placeholder="Enter name" type="text" aria-label="Name">
                      </div>
                      <div class="mb-4">
                        <label for="phone">Phone</label>
                        <input class="form-control" required id="phone" name="phone" value="{{old('phone')}}" placeholder="Enter phone" type="text" aria-label="phone">
                      </div>
                      <div class="mb-4">
                        <label for="phone">Email</label>
                        <input class="form-control" required id="email" name="email" value="{{old('email')}}" placeholder="Enter email" type="text" aria-label="phone">
                      </div>

                      <div class="mb-4">
                          <label for="campus">Campus (Optional)</label>
                          <select name="campus" id="campus" class="form-control select2" required aria-label="Campus" >
                            @foreach(\App\Models\Chapter::where('id','<>',86)->get() as $chapter)
                              <option value="{{$chapter->id}}" {{ old('campus') == $chapter->id ? 'selected' : '' }}>{{ $chapter->name }}</option>
                            @endforeach
                          </select>
                      </div> 
                      <div class="mb-4">
                          <label for="type">Donation Type</label>
                          <select name="type" id="type" class="form-control" required aria-label="type" >
                            <option value="" {{ old('type') == 'donation' ? 'selected' : '' }}>{{ $chapter->name }}</option>
                          </select>
                      </div>                    
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <button type="submit" class="btn btn-block btn-primary">Submit details</button>
                    </div>
                  </div>
                  
                </form>
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
@section('js')
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.select2').select2({
         width: 'resolve',
         theme: "bootstrap"
      });
  });
  </script>
    <script>
      $('#portfolio').change(function (e) {
        if(this.value == 'Exco'){
          $('#office-div').show();
          $('#office').attr('required', true);
        }else{
          $('#office-div').hide();
          $('#office').attr('required', false);
        }
      });
    </script>
@endsection
