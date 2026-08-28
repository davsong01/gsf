@extends('frontend.spaces.layouts.app')
@section('title', 'NEC')
@section('ogtitle', 'GSF National Leaders')

@section('css')
<style>
    .our-team {
        text-align: center;
        margin-bottom: 25px;
    }

    .our-team .pic {
        position: relative;
        width: 100px;      /* match avatar */
        height: 100px;     /* match avatar */
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
    }

    /* Avatar image fills the container */
    .our-team .pic img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    /* Overlay */
    .social_media_team {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: rgba(59, 61, 66, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    /* Show overlay on hover */
    .our-team:hover .social_media_team {
        opacity: 1;
        pointer-events: auto;
    }

    /* Social icons */
    .team_social {
        padding: 0;
        margin: 0;
        list-style: none;
        display: flex;
        gap: 6px;
    }

    .team_social > li > a {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #5d5d5d;
        color: #fff;
        border-radius: 50%;
        font-size: 14px;
        transition: all 0.25s ease;
    }

    .team_social > li > a:hover {
        background: #125;
        transform: scale(1.1);
    }

    /* Team info */
    .team-prof {
        margin-top: 10px;
        line-height: 1.0;
    }

    .team-prof h6 {
        margin-bottom: 3px;
        font-size: 14px;
        font-weight: 600;
    }

    .team-prof small {
        display: block;
        font-size: 11px;
        font-weight: 200;
        line-height: 1.2;
    }

    .team-prof small.office {
        color: #c33c54;
    }

    .team-prof small.tenure {
        color: #0a0a0a;
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
                      <li class="breadcrumb-item text-secondary"><a href="/">Home</a></li>
                      <li class="breadcrumb-item text-muted active" aria-current="page">NEC</li>
                  </ol>
              </nav>
              <h1 class="mb-5">GSF National Leaders</h1>
          </div>
      </div>
  </div>
</div>

<div class="container mt-4">
    <!-- Search Bar -->
    <div class="row mb-5">
        <div class="col-md-4 offset-md-4">
            <input type="text" id="nec-search" class="form-control" placeholder="Search by Name or Office">
        </div>
    </div>

    <div class="row" id="nec-cards">
        @foreach($nec as $n)
            @php
                $stakeholder = $n->stakeholder;
                $email  = $stakeholder?->email;
                $displayName = $stakeholder?->name ?? 'N/A';
                $office = $n->name ?? 'N/A';
                $tenure = $n->tenure ?? '';
            @endphp

            <div class="col-md-2 col-sm-6 nec-card">
                <div class="our-team">
                    <div class="pic">
                        {!! renderAvatar($stakeholder, 100) !!}
                        <div class="social_media_team">
                            <ul class="team_social">
                                @if($email)
                                    <li>
                                        <a target="_blank" href="mailto:{{ $email }}">
                                            <i class="fa fa-envelope"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="team-prof" style="line-height: 1.0 !important;margin-top: 10px;">
                        <h6 class="card-title mb-1">{{ $displayName }}</h6>
                        <small style="color: #c33c54;font-size: 11px;font-weight: 200;">{{ $office }}</small><br>
                        <small style="color: #0a0a0a;font-size: 11px;font-weight: 200;">{{ $tenure }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('nec-search');
    const cards = document.querySelectorAll('.nec-card');

    searchInput.addEventListener('input', function () {
        const term = this.value.toLowerCase();

        cards.forEach(card => {
            const name = card.querySelector('h6')?.textContent.toLowerCase() ?? '';
            const office = card.querySelector('small')?.textContent.toLowerCase() ?? '';
            if (name.includes(term) || office.includes(term)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
