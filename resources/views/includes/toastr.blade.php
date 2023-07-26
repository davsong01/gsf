<script>
    @if(Session::has('message'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
            toastr.success("{{ session('message') }}");
    @endif
  

    @if($errors->any())
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true,
        "preventDuplicates": false,
        "timeOut": "30000",
        // "showDuration": "300",
        // "hideDuration": "1000",
        // "positionClass": "toast-bottom-right",
        // "onclick": null,
        // "extendedTimeOut": "60000",
        // "showEasing": "swing",
        // "hideEasing": "linear",
        // "showMethod": "fadeIn",
        // "hideMethod": "fadeOut"
    }   
        toastr.error("@foreach($errors->all() as $error){{ $error}}</br>@endforeach");

    @endif

    @if(Session::has('info'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
        toastr.info("{{ session('info') }}");
    @endif
  
    @if(Session::has('warning'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
        toastr.warning("{{ session('warning') }}");
    @endif

    @if(Session::has('danger'))
    toastr.options =
    {
        "closeButton" : true,
        "progressBar" : true
    }
        toastr.danger("{{ session('danger') }}");
    @endif
</script>
   