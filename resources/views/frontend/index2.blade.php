<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>WAACSP</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta property="og:url" content="{{ config('app.url').'/induction' }}"> --}}
    <meta property="og:title" content="GSF - 2021 Conference"> 
    <meta property="og:image" content="{{ asset('frontend/logo.png') }}"/>

    <meta property="og:description" content="WAACSP - Induction 2021"/>
    <meta property="og:site_name" content="WAACSP" />



    <link rel="icon" type="image/png" href="{{ asset('frontend/logo.png') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/bootstrap.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/font-awesome.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/animate.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/select2.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/main.css') }}">

</head>

<body>
    <div class="bg-img1 size1 flex-w flex-c-m p-t-55 p-b-55 p-l-15 p-r-15"
        style=" padding-top: 20px; padding-bottom:20px; background-image: url('{{ asset('frontend/01.jpeg') }}">
        <div class="wsize1 bor1 bg1 p-t-175 p-b-45 p-l-15 p-r-15 respon1" style="margin-bottom: 10px;">
            <div class="wrappic1">
                <img src="{{ asset('frontend/logo.png') }}" style="max-width: 25%;" alt="LOGO">
            </div>
            <p class="txt-center m1-txt1 p-t-33 p-b-68">
                WAACSP INDUCTION WEBSITE COMING SOON
            </p>
            <div class="wsize2 flex-w flex-c hsize1 cd100">
                <div class="flex-col-c-m size2 how-countdown">
                    <span class="l1-txt1 p-b-9 days"></span>
                    <span class="s1-txt1">Days</span>
                </div>
                <div class="flex-col-c-m size2 how-countdown">
                    <span class="l1-txt1 p-b-9 hours"></span>
                    <span class="s1-txt1">Hours</span>
                </div>
                <div class="flex-col-c-m size2 how-countdown">
                    <span class="l1-txt1 p-b-9 minutes"></span>
                    <span class="s1-txt1">Minutes</span>
                </div>
                <div class="flex-col-c-m size2 how-countdown">
                    <span class="l1-txt1 p-b-9 seconds"></span>
                    <span class="s1-txt1">Seconds</span>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript" async="" src="{{ asset('frontend/analytics.js') }}">
    </script>
    <script src="{{ asset('frontend/jquery-3.js') }}"></script>

    <script src="{{ asset('frontend/popper.js') }}"></script>
    <script src="{{ asset('frontend/bootstrap.js') }}"></script>

    <script src="{{ asset('frontend/select2.js') }}"></script>

    <script src="{{ asset('frontend/moment.js') }}"></script>
    <script src="{{ asset('frontend/moment-timezone.js') }}"></script>
    <script src="{{ asset('frontend/moment-timezone-with-data.js') }}"></script>
    <script src="{{ asset('frontend/countdowntime.js') }}"></script>
    <script>
        $('.cd100').countdown100({
            /*Set Endtime here*/
            /*Endtime must be > current time*/
            endtimeYear: 2020,
            endtimeMonth: 11,
            endtimeDate: 25,
            endtimeHours: 00,
            endtimeMinutes: 00,
            endtimeSeconds: 00,
            timeZone: ""
            // ex:  timeZone: "America/New_York"
            //go to " http://momentjs.com/timezone/ " to get timezone
        });

    </script>

    <script src="{{ asset('frontend/tilt.js') }}"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })

    </script>

    <script src="{{ asset('frontend/main.js') }}"></script>

    <script async="" src="{{ asset('frontend/js') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

    </script>

</body>

</html>
