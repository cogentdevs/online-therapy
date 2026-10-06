@extends('layouts.frontLayout.front-design')

@section('title', 'About')
@section('meta_description', 'About Digital Magazine')

@section('content')
    <div class="front-taaruf">
        <div class="container-fluid front-taaruf__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">تعارف</span>
            </nav>

            <header class="front-taaruf__page-heading">
                <h1>تعارف</h1>
                <span aria-hidden="true"></span>
            </header>

            @if ($aboutSections->isNotEmpty())
                <div class="front-taaruf__sections">
                    @foreach ($aboutSections as $section)
                        @switch($section->section_condition)
                            @case(1)
                                <section class="front-taaruf__section front-taaruf__section--1 front-taaruf__section--text">
                                    @if (filled($section->title))
                                        <h2 class="front-taaruf__heading">{{ $section->title }}</h2>
                                    @endif
                                    @if (filled($section->description))
                                        <div class="front-taaruf__html front-taaruf__html--centered">{!! $section->description !!}</div>
                                    @endif
                                </section>
                            @break

                            @case(2)
                                @if ($section->image_available)
                                    <section class="front-taaruf__section front-taaruf__section--2 front-taaruf__section--media-only">
                                        <img class="front-taaruf__image" src="{{ asset($section->image) }}"
                                            alt="{{ filled($section->title) ? $section->title : 'تعارف' }}">
                                    </section>
                                @endif
                            @break

                            @case(3)
                                <section class="front-taaruf__section front-taaruf__section--3">
                                    <div class="row g-4 align-items-center">
                                        <div class="{{ $section->image_available ? 'col-12 col-lg-7' : 'col-12' }} front-taaruf__content">
                                            @if (filled($section->title))<h2 class="front-taaruf__heading">{{ $section->title }}</h2>@endif
                                            @if (filled($section->description))<div class="front-taaruf__html">{!! $section->description !!}</div>@endif
                                        </div>
                                        @if ($section->image_available)
                                            <div class="col-12 col-lg-5 front-taaruf__media">
                                                <img class="front-taaruf__image" src="{{ asset($section->image) }}" alt="{{ $section->title ?: 'تعارف' }}">
                                            </div>
                                        @endif
                                    </div>
                                </section>
                            @break

                            @case(4)
                                <section class="front-taaruf__section front-taaruf__section--4">
                                    <div class="row g-4 align-items-center">
                                        @if ($section->image_available)
                                            <div class="col-12 col-lg-5 front-taaruf__media">
                                                <img class="front-taaruf__image" src="{{ asset($section->image) }}" alt="{{ $section->title ?: 'تعارف' }}">
                                            </div>
                                        @endif
                                        <div class="{{ $section->image_available ? 'col-12 col-lg-7' : 'col-12' }} front-taaruf__content">
                                            @if (filled($section->title))<h2 class="front-taaruf__heading">{{ $section->title }}</h2>@endif
                                            @if (filled($section->description))<div class="front-taaruf__html">{!! $section->description !!}</div>@endif
                                        </div>
                                    </div>
                                </section>
                            @break

                            @case(5)
                                @php($hasBothTextColumns = filled($section->description) && filled($section->description_2))
                                <section class="front-taaruf__section front-taaruf__section--5 front-taaruf__two-column">
                                    <div class="row g-4">
                                        @if (filled($section->description) || filled($section->title))
                                            <div class="{{ $hasBothTextColumns ? 'col-12 col-lg-6' : 'col-12' }} front-taaruf__content">
                                                @if (filled($section->title))<h2 class="front-taaruf__heading">{{ $section->title }}</h2>@endif
                                                @if (filled($section->description))<div class="front-taaruf__html">{!! $section->description !!}</div>@endif
                                            </div>
                                        @endif
                                        @if (filled($section->description_2) || filled($section->title_2))
                                            <div class="{{ $hasBothTextColumns ? 'col-12 col-lg-6' : 'col-12' }} front-taaruf__content">
                                                @if (filled($section->title_2))<h2 class="front-taaruf__heading">{{ $section->title_2 }}</h2>@endif
                                                @if (filled($section->description_2))<div class="front-taaruf__html">{!! $section->description_2 !!}</div>@endif
                                            </div>
                                        @endif
                                    </div>
                                </section>
                            @break

                            @case(6)
                                @if ($section->image_available || $section->image_2_available)
                                    <section class="front-taaruf__section front-taaruf__section--6 front-taaruf__two-column">
                                        <div class="row g-4">
                                            @if ($section->image_available)
                                                <div class="{{ $section->image_2_available ? 'col-12 col-lg-6' : 'col-12' }} front-taaruf__media">
                                                    <img class="front-taaruf__image" src="{{ asset($section->image) }}" alt="تعارف تصویر">
                                                </div>
                                            @endif
                                            @if ($section->image_2_available)
                                                <div class="{{ $section->image_available ? 'col-12 col-lg-6' : 'col-12' }} front-taaruf__media">
                                                    <img class="front-taaruf__image" src="{{ asset($section->image_2) }}" alt="تعارف تصویر 2">
                                                </div>
                                            @endif
                                        </div>
                                    </section>
                                @endif
                            @break
                        @endswitch
                    @endforeach
                </div>
            @else
                <div class="front-taaruf__empty">تعارف کی معلومات دستیاب نہیں۔</div>
            @endif
        </div>
    </div>
@endsection
