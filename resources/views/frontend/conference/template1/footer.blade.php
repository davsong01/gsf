<section class="bg-200 pt-9 pb-0" style="padding-top: 0rem !important;">
  <div class="container">
    <div class="row">
      <hr class="opacity-25" />
      <div class="text-400 text-center">
        <p style="text-align:center">If you are having challenges registering, whatsapp: <b> {{ $setting->official_phone }}</b> for
            assistance/guidance<br>
            <i class="fa fa-envelope"></i> {{ $setting->official_email }}</p>
            <div style="text-align:center">
              <h5>Follow on social media</h5>
              @if(!is_null($setting->facebook_event_page))
              <a href="{{ $setting->facebook_event_page }}"><img class="socials" src="{{ asset('frontend/img/site/socials/event.png') }}" alt=""></a>
              @endif
              @if(!is_null($setting->facebook_page))
              <a href="{{ $setting->facebook_page }}" target="_blank"><img class="socials" src="{{ asset('frontend/img/site/socials/facebook.png') }}" alt=""></a>
              @endif
              @if(!is_null($setting->instagram))
              <a href="{{ $setting->instagram }}" target="_blank"><img class="socials" src="{{ asset('frontend/img/site/socials/instagram.png') }}" alt=""></a>
              @endif
            </div>
        <p style="margin-top: 11px;"> © {{ date('Y') }} <a class="text-900" href="{{ config('app.url') }}" target="_blank">{{ config('app.name') }}</a>
        </p>
      </div>
    </div>
  </div>
  <!-- end of .container-->
</section>