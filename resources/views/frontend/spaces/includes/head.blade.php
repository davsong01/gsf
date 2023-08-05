<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>GSF | @yield('title', "Gofamint Students' Fellowship")</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">

    <meta property="og:title" content="GSF | @yield('ogtitle', "Gofamint Students' Fellowship")"> 
    <meta property="og:image" content="{{ asset('frontend/img/logo.png') }}"/> 
    <meta property="og:description" content="GSF | @yield('ogdescription', "GSF Directory")"/>
    <meta property="og:site_name" content="GSF" />
    <meta property="og:url" content="@yield('ogurl', url('/'))">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#ffffff">
    <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="{{ asset('frontend/img/logo.png') }}" rel="icon">

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css">
    <link rel="stylesheet" href="{{asset('gsfcom/fancybox/min.css')}}">
    <link rel="stylesheet" href="{{asset('gsfcom/jqvmap/min.css')}}">
    <link type="text/css" href="{{asset('gsfcom/css/style.css')}}" rel="stylesheet">
    <style>
        .counter{
            background: #0d1b48;
            color: white !important;
            padding: 7px;
            border-radius: 50%;
            margin: auto;
            width: 40px;
            display: inline-flex;
            height: 40px;
            padding-left: 11px;
            padding-top: 10px;
            font-size: small;
        }
        .counter:hover{
            color:white !important;
        }
        .thick-line {
            font-weight: bold;
            padding: 2px;
        }
        .shadow{
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        }
        .line {
            border: 0.1px solid #0d1b48;
        }
        .typeahead.dropdown-menu li {
            width: 100%;
        }
        .portfolio{
            color:blue
        }
        .hover-hide-text:hover{
            display:none;
        }
    </style>
    @yield('css')
</head>