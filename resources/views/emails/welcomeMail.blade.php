@php
    $settings = $data['settings'] ?? activeConferenceEdition();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Email</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f5f7fa;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .email-container {
            width: 100%;
            background: #f5f7fa;
            padding: 40px 0;
        }

        .email-card {
            max-width: 600px;
            background: #ffffff;
            margin: auto;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        h1, h2 {
            font-weight: 600;
            margin: 0 0 15px;
            color: #111;
        }

        p {
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .btn-primary {
            display: inline-block;
            background: #4a80f6;
            color: #ffffff !important;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 10px;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 30px 0;
        }

        .footer {
            text-align: center;
            font-size: 13px;
            color: #777;
            margin-top: 25px;
        }

        .footer a {
            color: #4a80f6;
            text-decoration: none;
            margin: 0 5px;
        }

        .social-links {
            text-align: center;
            margin: 20px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
            text-decoration: none;
        }

        .social-links svg {
            width: 32px;
            height: 32px;
            fill: #4a80f6;
            vertical-align: middle;
        }

        .banner-img {
            max-width: 100%;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>

<body>

    <div class="email-container">
        <div class="email-card">

            {{-- HEADER / LOGO --}}
            <div style="text-align:center; margin-bottom: 25px;">
                <img src="{{ asset($settings->conference_logo) }}" alt="Logo" style="max-width: 100px;">
            </div>

            {{-- BODY CONTENT --}}
            {!! $data['content'] !!}

            {{-- CONFERENCE BANNER AFTER CONTENT --}}
            @if(!empty($settings->banner))
                <div style="text-align:center;">
                    <img src="{{ asset($settings->banner) }}" alt="Conference Banner" class="banner-img">
                </div>
            @endif

            {{-- SOCIAL ICONS (SVG) --}}
            <div class="social-links">
                @if(!empty($settings->facebook_page))
                    <a href="{{ $settings->facebook_page }}" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24">
                            <path d="M22 12c0-5.522-4.478-10-10-10S2 6.478 2 12c0 5 3.657 9.128 8.438 9.878v-6.987H7.898v-2.891h2.54V9.845c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562v1.875h2.773l-.443 2.891h-2.33v6.987C18.343 21.128 22 17 22 12z"/>
                        </svg>
                    </a>
                @endif

                @if(!empty($settings->instagram))
                    <a href="{{ $settings->instagram }}" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.056 1.97.24 2.43.403a4.92 4.92 0 0 1 1.77 1.153 4.92 4.92 0 0 1 1.153 1.77c.163.46.347 1.26.403 2.43.058 1.266.07 1.645.07 4.85s-.012 3.584-.07 4.85c-.056 1.17-.24 1.97-.403 2.43a4.902 4.902 0 0 1-1.153 1.77 4.902 4.902 0 0 1-1.77 1.153c-.46.163-1.26.347-2.43.403-1.266.058-1.645.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.056-1.97-.24-2.43-.403a4.902 4.902 0 0 1-1.77-1.153 4.902 4.902 0 0 1-1.153-1.77c-.163-.46-.347-1.26-.403-2.43C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.056-1.17.24-1.97.403-2.43a4.92 4.92 0 0 1 1.153-1.77 4.92 4.92 0 0 1 1.77-1.153c.46-.163 1.26-.347 2.43-.403C8.416 2.175 8.796 2.163 12 2.163zm0-2.163C8.736 0 8.332.012 7.052.07c-1.29.059-2.176.27-2.947.57a6.92 6.92 0 0 0-2.52 1.645A6.92 6.92 0 0 0 .07 4.938c-.3.771-.511 1.657-.57 2.947C-.012 8.332 0 8.736 0 12s.012 3.668.07 4.948c.059 1.29.27 2.176.57 2.947a6.9 6.9 0 0 0 1.645 2.52 6.9 6.9 0 0 0 2.52 1.645c.771.3 1.657.511 2.947.57C8.332 23.988 8.736 24 12 24s3.668-.012 4.948-.07c1.29-.059 2.176-.27 2.947-.57a6.92 6.92 0 0 0 2.52-1.645 6.92 6.92 0 0 0 1.645-2.52c.3-.771.511-1.657.57-2.947.058-1.28.07-1.684.07-4.948s-.012-3.668-.07-4.948c-.059-1.29-.27-2.176-.57-2.947a6.92 6.92 0 0 0-1.645-2.52 6.92 6.92 0 0 0-2.52-1.645c-.771-.3-1.657-.511-2.947-.57C15.668.012 15.264 0 12 0z"/>
                            <circle cx="12" cy="12" r="3.6"/>
                        </svg>
                    </a>
                @endif

                @if(!empty($settings->telegram))
                    <a href="{{ $settings->telegram }}" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 0C5.372 0 0 5.372 0 12c0 6.627 5.372 12 12 12 6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12zm5.507 8.991l-1.853 8.738c-.14.59-.505.735-1.025.458l-2.83-2.086-1.367 1.315c-.152.152-.281.281-.575.281l.207-2.932 5.341-4.823c.232-.207-.05-.322-.36-.115l-6.604 4.159-2.84-.888c-.616-.19-.628-.616.128-.91l11.107-4.29c.518-.192.973.128.805.91z"/>
                        </svg>
                    </a>
                @endif
            </div>

            <div class="divider"></div>

            {{-- FOOTER --}}
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <p>
                    Need help?  
                    <a href="mailto:{{ $settings->official_email }}">Contact Support</a>
                </p>
            </div>

        </div>
    </div>

</body>

</html>
