@extends('layouts.frontLayout.front-design')

@section('title', 'تشہیر کیجئے')
@section('meta_description', 'ڈیجیٹل میگزین پر اپنی تشہیری درخواست جمع کروائیں')

@section('content')
    @php
        // Display translations only; Ad::PAGE_PLACEMENTS remains the source for valid keys and availability.
        $pageLabels = [
            'header' => 'ہیڈر',
            'home' => 'صفحہ اول',
            'taza_shumara' => 'تازہ شمارہ',
        ];
        $placeLabels = [
            'header_ad' => 'ہیڈر اشتہار — 728 × 90',
            'home_horizontal_large' => 'افقی اشتہار (بڑا) — 1020 × 150',
            'home_horizontal_small' => 'افقی اشتہار (چھوٹا) — 500 × 150',
            'taza_sidebar_1_normal' => 'دائیں سائیڈ بار (پہلا، عام) — 250 × 300',
            'taza_sidebar_1_tall' => 'دائیں سائیڈ بار (پہلا، لمبا) — 600 × 300',
            'taza_sidebar_2_normal' => 'دائیں سائیڈ بار (دوسرا، عام) — 250 × 300',
            'taza_sidebar_2_tall' => 'دائیں سائیڈ بار (دوسرا، لمبا) — 600 × 300',
        ];
    @endphp
    <div class="front-advertise" dir="rtl">
        <nav class="front-advertise__breadcrumb front-ui" aria-label="بریڈ کرمب">
            <div class="container-fluid front-advertise__container">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">›</span><span aria-current="page">تشہیر کیجئے</span>
            </div>
        </nav>

        <div class="container-fluid front-advertise__container">
            <header class="front-advertise__heading">
                <h1>تشہیر کیجئے</h1>
                <span class="front-advertise__heading-line" aria-hidden="true"></span>
                <p>اپنے برانڈ کو ہمارے قارئین تک پہنچائیں۔ تاریخیں اور تشہیری مقامات منتخب کر کے درخواست جمع کروائیں۔</p>
            </header>

            <div class="row g-4 align-items-start front-advertise__layout">
                <div class="col-12 col-lg-8">
                    <section class="front-advertise__card front-advertise__form-card" aria-labelledby="advertise-form-title">
                        <h2 id="advertise-form-title">تشہیر کے لیے درخواست کریں</h2>
                        <p class="front-advertise__intro">براہ کرم درج ذیل فارم مکمل کریں، ہماری ٹیم جلد آپ سے رابطہ کرے گی۔</p>

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">براہ کرم فارم میں درج غلطیاں درست کریں۔</div>
                        @endif

                        <form method="post" action="{{ route('front.advertise.store') }}" data-advertise-form
                            data-availability-url="{{ route('front.advertise.availability') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="advertise-name">نام <span>*</span></label>
                                    <input id="advertise-name" name="name" type="text" value="{{ old('name', $user->name) }}" maxlength="255" required
                                        class="form-control @error('name') is-invalid @enderror" placeholder="اپنا مکمل نام درج کریں">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="advertise-email">ای میل <span>*</span></label>
                                    <input id="advertise-email" name="email" type="email" dir="ltr" value="{{ old('email', $user->email) }}" maxlength="255" required
                                        class="form-control @error('email') is-invalid @enderror" placeholder="email@example.com">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="advertise-phone">فون نمبر <span>*</span></label>
                                    <input id="advertise-phone" name="phone" type="text" dir="ltr" value="{{ old('phone', $user->phone) }}" maxlength="255" required
                                        class="form-control @error('phone') is-invalid @enderror" placeholder="0300 1234567">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="advertise-company">کمپنی / برانڈ کا نام <span class="text-danger">*</span></label>
                                    <input id="advertise-company" name="company" type="text" value="{{ old('company') }}" maxlength="255"
                                        class="form-control @error('company') is-invalid @enderror" placeholder="کمپنی یا برانڈ کا نام درج کریں" required>
                                    @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="advertise-from">آغاز کی تاریخ <span>*</span></label>
                                    <input id="advertise-from" name="from_date" type="date" dir="ltr" min="{{ today()->toDateString() }}"
                                        value="{{ old('from_date') }}" required data-advertise-from
                                        class="form-control @error('from_date') is-invalid @enderror">
                                    @error('from_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="advertise-to">اختتام کی تاریخ <span>*</span></label>
                                    <input id="advertise-to" name="to_date" type="date" dir="ltr" min="{{ today()->toDateString() }}"
                                        value="{{ old('to_date') }}" required data-advertise-to
                                        class="form-control @error('to_date') is-invalid @enderror">
                                    @error('to_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <p class="front-advertise__duration" data-advertise-duration aria-live="polite">کل مدت: تاریخیں منتخب کریں</p>

                            <fieldset class="front-advertise__placements">
                                <legend>تشہیری مقامات <span>*</span></legend>
                                <p class="front-advertise__availability-message" data-advertise-message role="status" aria-live="polite">
                                    تشہیری مقامات دیکھنے کے لیے پہلے آغاز اور اختتام کی تاریخ منتخب کریں۔
                                </p>
                                @foreach ($pagePlacements as $pageName => $page)
                                    <div class="front-advertise__placement-group">
                                        <h3>{{ $pageLabels[$pageName] ?? $page['label'] }}</h3>
                                        @foreach ($page['places'] as $place => $label)
                                            <label class="front-advertise__placement" data-advertise-placement
                                                data-page-name="{{ $pageName }}" data-place="{{ $place }}">
                                                <input type="checkbox" name="placements[]" value="{{ $pageName }}:{{ $place }}"
                                                    @checked(in_array($pageName.':'.$place, (array) old('placements', []), true)) disabled>
                                                <span class="front-advertise__placement-label">{{ $placeLabels[$place] ?? $label }}</span>
                                                <span class="front-advertise__placement-status" data-advertise-placement-status>تاریخیں منتخب کریں</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endforeach
                            </fieldset>
                            @error('placements')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            @error('placements.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

                            <div class="front-advertise__details">
                                <label for="advertise-details">مزید تفصیلات</label>
                                <textarea id="advertise-details" name="details" rows="4" maxlength="10000"
                                    class="form-control @error('details') is-invalid @enderror" placeholder="اپنی تشہیر سے متعلق مزید تفصیلات یہاں درج کریں">{{ old('details') }}</textarea>
                                @error('details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="front-advertise__captcha">{!! app('captcha')->display() !!}</div>
                            @error('g-recaptcha-response')<div class="text-danger small">{{ $message }}</div>@enderror

                            <button type="submit" class="front-advertise__submit"><i class="fa-regular fa-paper-plane" aria-hidden="true"></i> درخواست بھیجیں</button>
                        </form>
                    </section>
                </div>

                <div class="col-12 col-lg-4">
                    <aside class="front-advertise__sidebar">
                        <section class="front-advertise__card front-advertise__preview-card">
                            <h2>دستیاب تشہیری مقامات</h2>
                            <p>ہماری ویب سائٹ پر درج ذیل مقامات پر تشہیر کے مواقع دستیاب ہیں:</p>
                            <div class="front-advertise__preview-list">
                                @foreach ($pagePlacements as $pageName => $page)
                                    @foreach ($page['places'] as $place => $label)
                                        <div class="front-advertise__preview" data-preview-place="{{ $place }}">
                                            <span class="front-advertise__preview-image" aria-hidden="true">
                                                <img src="{{ asset('images/frontend-images/ads/placement-preview.svg') }}" alt="" loading="lazy">
                                                <span class="front-advertise__preview-highlight"></span>
                                            </span>
                                            <span class="front-advertise__preview-copy">
                                                <strong>{{ $placeLabels[$place] ?? $label }}</strong>
                                                <small>{{ $pageLabels[$pageName] ?? $page['label'] }}</small>
                                            </span>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </section>
                        <section class="front-advertise__card front-advertise__sidebar-cta">
                            <h2>آئیں مل کر ترقی کریں</h2>
                            <p>اپنے برانڈ کے لیے مناسب تشہیر منتخب کریں۔ درخواست موصول ہونے کے بعد ہماری ٹیم آپ سے رابطہ کرے گی۔</p>
                            @if ($generalSetting?->email)
                                <a href="mailto:{{ $generalSetting->email }}"><i class="fa-regular fa-envelope" aria-hidden="true"></i> {{ $generalSetting->email }}</a>
                            @endif
                            @if ($generalSetting?->contact_1)
                                <a href="tel:{{ preg_replace('/[^+\d]/', '', $generalSetting->contact_1) }}"><i class="fa-solid fa-phone" aria-hidden="true"></i> <span dir="ltr">{{ $generalSetting->contact_1 }}</span></a>
                            @endif
                        </section>
                    </aside>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! app('captcha')->renderJs('ur') !!}
@endpush
