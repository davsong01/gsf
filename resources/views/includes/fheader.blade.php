<head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>GSF | @yield('title', "Gofamint Students' Fellowship")</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:title" content="GSF | @yield('ogtitle', "Gofamint Students' Fellowship")"> 
        <meta property="og:image" content="{{ asset('frontend/img/logo.png') }}"/> 

        <meta property="og:description" content="GSF | @yield('description', "Gofamint Students' Fellowship")"/>
        <meta property="og:site_name" content="GSF" />

        <!-- Favicon -->
        <link href="{{ asset('frontend/img/logo.png') }}" rel="icon">

        <!-- Google Font -->
        <!-- <link href="https://fonts.googleapis.com/css2?family=Lato&family=Oswald:wght@200;300;400&display=swap" rel="stylesheet"> -->
         <link href="{{ asset('frontend/css/opensans.css') }}" rel="stylesheet">

        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
        <link href="{{ asset('frontend/lib/animate/animate.min.css') }}" rel="stylesheet">
        <link href="{{ asset('frontend/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
        

         {{-- Select2 --}}
         <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

    </head>