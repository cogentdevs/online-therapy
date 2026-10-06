@extends('layouts.frontLayout.front-design')

@section('title', $consultancy->title)
@section('meta_description', $consultancy->short_description)

@section('content')
    <section class="consultancy-detail">
        <div class="container-fluid consultancy-detail__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="Breadcrumb">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.consultancies.index') }}">Consultancies</a>
                <span aria-hidden="true">/</span><span aria-current="page">{{ $consultancy->title }}</span>
            </nav>
            <article class="consultancy-detail__article">
                @if (filled($consultancy->image))
                    <img class="consultancy-detail__image" src="{{ asset($consultancy->image) }}" alt="{{ $consultancy->title }}">
                @endif
                <div class="consultancy-detail__content">
                    <h1>{{ $consultancy->title }}</h1>
                    @if (filled($consultancy->short_description))<p class="consultancy-detail__summary">{{ $consultancy->short_description }}</p>@endif
                    <dl class="consultancy-detail__details front-ui">
                        <div><dt>Duration</dt><dd>{{ $consultancy->formattedDuration() }}</dd></div>
                        <div><dt>Medium</dt><dd>{{ $consultancy->formattedMedium() }}</dd></div>
                    </dl>
                    @if (filled($consultancy->description))<div class="consultancy-detail__body">{!! $consultancy->description !!}</div>@endif
                </div>
            </article>
            @if ($consultancy->relatedConsultancies->isNotEmpty())
                <section class="consultancy-related" aria-labelledby="related-consultancies-heading">
                    <div class="front-section-heading"><h2 id="related-consultancies-heading">Related Consultancies</h2><span class="front-section-heading__line" aria-hidden="true"></span></div>
                    <div class="row g-4">
                        @foreach ($consultancy->relatedConsultancies as $relatedConsultancy)
                            <div class="col-12 col-md-6 col-xl-4">@include('frontend.consultancies._card', ['consultancy' => $relatedConsultancy])</div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
@endsection
