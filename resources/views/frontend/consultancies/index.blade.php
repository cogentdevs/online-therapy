@extends('layouts.frontLayout.front-design')

@section('title', 'Consultancies')
@section('meta_description', 'Explore currently available consultancies')

@section('content')
    <section class="consultancies-page">
        <div class="container-fluid consultancies-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="Breadcrumb">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> Home</a>
                <span aria-hidden="true">/</span><span aria-current="page">Consultancies</span>
            </nav>
            <header class="consultancies-page__heading">
                <h1>Consultancies</h1>
                <p>Explore expert consultancy services tailored to your needs.</p>
            </header>
            @forelse ($consultancies as $consultancy)
                @if ($loop->first)<div class="row g-4">@endif
                <div class="col-12 col-md-6 col-xl-4">@include('frontend.consultancies._card')</div>
                @if ($loop->last)</div>@endif
            @empty
                <div class="consultancies-page__empty">No consultancies are currently available.</div>
            @endforelse
            @if ($consultancies->hasPages())
                <nav class="front-sabqa-pagination front-ui" aria-label="Consultancy pages">{{ $consultancies->links('pagination::bootstrap-5') }}</nav>
            @endif
        </div>
    </section>
@endsection
