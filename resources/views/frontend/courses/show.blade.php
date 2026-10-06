@extends('layouts.frontLayout.front-design')

@section('title', $course->title)
@section('meta_description', $course->short_description)

@section('content')
    <section class="consultancy-detail">
        <div class="container-fluid consultancy-detail__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="Breadcrumb">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
                <span aria-hidden="true">/</span><a href="{{ route('front.courses.index') }}">Courses</a>
                <span aria-hidden="true">/</span><span aria-current="page">{{ $course->title }}</span>
            </nav>
            <article class="consultancy-detail__article">
                @if (filled($course->image))<img class="consultancy-detail__image" src="{{ asset($course->image) }}" alt="{{ $course->title }}">@endif
                <div class="consultancy-detail__content">
                    <h1>{{ $course->title }}</h1>
                    @if (filled($course->short_description))<p class="consultancy-detail__summary">{{ $course->short_description }}</p>@endif
                    <dl class="consultancy-detail__details front-ui"><div><dt>Duration</dt><dd>{{ $course->formattedDuration() }}</dd></div></dl>
                    @if (filled($course->description))<div class="consultancy-detail__body">{!! $course->description !!}</div>@endif
                </div>
            </article>
            @if ($course->relatedCourses->isNotEmpty())
                <section class="consultancy-related" aria-labelledby="related-courses-heading">
                    <div class="front-section-heading"><h2 id="related-courses-heading">Related Courses</h2><span class="front-section-heading__line" aria-hidden="true"></span></div>
                    <div class="row g-4">
                        @foreach ($course->relatedCourses as $relatedCourse)
                            <div class="col-12 col-md-6 col-xl-4">@include('frontend.courses._card', ['course' => $relatedCourse])</div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
@endsection
