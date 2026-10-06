@extends('layouts.frontLayout.front-design')

@section('title', 'Courses')
@section('meta_description', 'Explore currently available courses')

@section('content')
    <section class="consultancies-page">
        <div class="container-fluid consultancies-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="Breadcrumb">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
                <span aria-hidden="true">/</span><span aria-current="page">Courses</span>
            </nav>
            <header class="consultancies-page__heading"><h1>Courses</h1><p>Explore our currently available courses.</p></header>
            @forelse ($courses as $course)
                @if ($loop->first)<div class="row g-4">@endif
                <div class="col-12 col-md-6 col-xl-4">@include('frontend.courses._card')</div>
                @if ($loop->last)</div>@endif
            @empty
                <div class="consultancies-page__empty">No courses are currently available.</div>
            @endforelse
            @if ($courses->hasPages())
                <nav class="front-sabqa-pagination front-ui" aria-label="Course pages">{{ $courses->links('pagination::bootstrap-5') }}</nav>
            @endif
        </div>
    </section>
@endsection
