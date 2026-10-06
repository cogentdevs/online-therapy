@extends('layouts.frontLayout.front-design')

@section('title', 'سوال پوچھیں')
@section('meta_description', 'اپنا سوال ڈیجیٹل میگزین کی ماہر ٹیم کو بھیجیں')

@section('content')
    <div class="front-ask-question" dir="rtl">
        <nav class="front-ask-question__breadcrumb front-ui" aria-label="بریڈ کرمب">
            <div class="container-fluid front-ask-question__container">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">›</span><span aria-current="page">سوال پوچھیں</span>
            </div>
        </nav>

        <div class="container-fluid front-ask-question__container">
            <header class="front-ask-question__heading">
                <h1>سوال پوچھیں</h1>
                <span class="front-ask-question__heading-line" aria-hidden="true"></span>
                <h2>آپ کے سوالات ہمارے لیے اہم ہیں</h2>
                <p>شریعہ، کاروبار، معیشت یا دیگر متعلقہ موضوعات پر اپنے سوالات ہمارے ساتھ شیئر کریں۔ ہماری ماہر ٹیم آپ کے سوال کا جائزہ لے کر رہنمائی فراہم کرے گی۔</p>
            </header>

            <div class="row g-4 align-items-start front-ask-question__layout">
                <div class="col-12 col-lg-8">
                    <section class="front-ask-question__card front-ask-question__form-card" aria-labelledby="ask-question-form-title">
                        <h2 id="ask-question-form-title">اپنا سوال بھیجیں</h2>
                        <p class="front-ask-question__intro">براہ کرم نیچے دیے گئے فارم کو مکمل کریں۔ تمام فلڈز ضروری ہیں۔</p>

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">براہ کرم فارم میں درج غلطیاں درست کریں۔</div>
                        @endif

                        <form method="post" action="{{ route('front.ask-question.store') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="ask-name">نام <span>*</span></label>
                                    <div class="front-ask-question__input-wrap">
                                        <input id="ask-name" name="name" type="text" value="{{ old('name', $user->name) }}" maxlength="255" required class="form-control @error('name') is-invalid @enderror" placeholder="آپ کا نام درج کریں">
                                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                                    </div>
                                    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="ask-email">ای میل <span>*</span></label>
                                    <div class="front-ask-question__input-wrap">
                                        <input id="ask-email" name="email" type="email" dir="ltr" value="{{ old('email', $user->email) }}" maxlength="255" required class="form-control @error('email') is-invalid @enderror" placeholder="آپ کی ای میل درج کریں">
                                        <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                                    </div>
                                    @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="ask-phone">فون کا نمبر <span>*</span></label>
                                    <div class="front-ask-question__input-wrap">
                                        <input id="ask-phone" name="phone" type="text" dir="ltr" value="{{ old('phone', $user->phone) }}" maxlength="255" required class="form-control @error('phone') is-invalid @enderror" placeholder="0300 1234567">
                                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                                    </div>
                                    @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="ask-subject">سوال کا عنوان <span>*</span></label>
                                    <div class="front-ask-question__input-wrap">
                                        <input id="ask-subject" name="subject" type="text" value="{{ old('subject') }}" maxlength="255" required class="form-control @error('subject') is-invalid @enderror" placeholder="آپ کے سوال کا مختصر عنوان درج کریں">
                                        <i class="fa-regular fa-file-lines" aria-hidden="true"></i>
                                    </div>
                                    @error('subject')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label for="ask-sawal">اپنا سوال <span>*</span></label>
                                    <textarea id="ask-sawal" name="sawal" rows="8" maxlength="2000" required data-ask-question-text class="form-control @error('sawal') is-invalid @enderror" placeholder="اپنا مکمل سوال یہاں درج کریں...">{{ old('sawal') }}</textarea>
                                    <div class="front-ask-question__counter" data-ask-question-count aria-live="polite">0 / 2000</div>
                                    @error('sawal')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="front-ask-question__captcha">{!! app('captcha')->display() !!}</div>
                            @error('g-recaptcha-response')<div class="text-danger small">{{ $message }}</div>@enderror
                            <button type="submit" class="front-ask-question__submit"><i class="fa-regular fa-paper-plane" aria-hidden="true"></i> سوال ارسال کریں</button>
                        </form>
                    </section>
                </div>

                <div class="col-12 col-lg-4">
                    <aside class="front-ask-question__sidebar">
                        <section class="front-ask-question__card front-ask-question__info">
                            <div class="front-ask-question__illustration" aria-hidden="true"><i class="fa-solid fa-comment-dots"></i><span>؟</span></div>
                            <h2>سوال پوچھنے کے بارے میں</h2>
                            <p>آپ کسی بھی شریعہ، کاروبار، معیشت یا عمومی رہنمائی سے متعلق سوال پوچھ سکتے ہیں۔ ہماری ٹیم مناسب وقت میں جواب فراہم کرے گی۔</p>
                            <ul>
                                <li><i class="fa-solid fa-users" aria-hidden="true"></i><span><strong>ماہرین کی رہنمائی</strong><small>مستند اور قابل اعتماد جوابات</small></span></li>
                                <li><i class="fa-regular fa-comments" aria-hidden="true"></i><span><strong>مفت سوال و جواب</strong><small>رہنمائی کے لیے کوئی فیس نہیں</small></span></li>
                                <li><i class="fa-solid fa-lock" aria-hidden="true"></i><span><strong>رازداری کا احترام</strong><small>آپ کی معلومات محفوظ ہیں</small></span></li>
                                <li><i class="fa-regular fa-file-lines" aria-hidden="true"></i><span><strong>معیاری مواد</strong><small>علم پر مبنی رہنمائی</small></span></li>
                            </ul>
                        </section>
                        <section class="front-ask-question__card front-ask-question__contact">
                            <h2>ہم سے رابطہ کریں</h2>
                            @if ($generalSetting?->contact_1)
                                <a href="tel:{{ preg_replace('/[^+\d]/', '', $generalSetting->contact_1) }}"><i class="fa-solid fa-phone" aria-hidden="true"></i><span dir="ltr">{{ $generalSetting->contact_1 }}</span></a>
                            @endif
                            @if ($generalSetting?->email)
                                <a href="mailto:{{ $generalSetting->email }}"><i class="fa-regular fa-envelope" aria-hidden="true"></i><span>{{ $generalSetting->email }}</span></a>
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
