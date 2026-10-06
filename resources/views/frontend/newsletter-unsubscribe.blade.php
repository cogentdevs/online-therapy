@extends('layouts.frontLayout.front-design')

@section('title', 'نیوز لیٹر سے ان سبسکرائب کریں')
@section('meta_description', 'ڈیجیٹل میگزین نیوز لیٹر کی رکنیت ختم کریں')

@section('content')
    <div class="newsletter-unsubscribe-page">
        <div class="container-fluid newsletter-unsubscribe-page__container">
            <nav class="front-taza-breadcrumb front-ui" aria-label="بریڈ کرمب">
                <a href="{{ route('frontend.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i> صفحہ اول</a>
                <span aria-hidden="true">/</span><span aria-current="page">نیوز لیٹر سے ان سبسکرائب کریں</span>
            </nav>

            <section class="newsletter-unsubscribe-card">
                <div class="newsletter-unsubscribe-card__icon"><i class="fa-regular fa-envelope-open" aria-hidden="true"></i></div>
                <h1>نیوز لیٹر سے ان سبسکرائب کریں</h1>

                @if ($subscriber->status === \App\Models\NewsletterSubscriber::STATUS_UNSUBSCRIBED)
                    <div class="alert alert-info mb-0">آپ پہلے ہی نیوز لیٹر سے ان سبسکرائب ہو چکے ہیں۔</div>
                @else
                    <p>اگر آپ مزید ہمارا نیوز لیٹر وصول نہیں کرنا چاہتے تو نیچے وجہ منتخب کریں اور ان سبسکرائب کی تصدیق کریں۔</p>
                    <div class="newsletter-unsubscribe-card__email" dir="ltr">{{ $subscriber->maskedEmail() }}</div>

                    <form method="POST" action="{{ route('front.newsletter.unsubscribe.store', $subscriber->unsubscribe_token) }}">
                        @csrf
                        <fieldset>
                            <legend>ان سبسکرائب کرنے کی وجہ</legend>
                            @foreach ($reasons as $value => $label)
                                <label class="newsletter-unsubscribe-reason">
                                    <input type="radio" name="reason" value="{{ $value }}" @checked(old('reason') === $value) required>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </fieldset>
                        @error('reason')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                        <div class="mt-3">
                            <label class="form-label" for="newsletter-other-reason">دیگر وجہ</label>
                            <textarea class="form-control @error('other_reason') is-invalid @enderror" id="newsletter-other-reason" name="other_reason" rows="3" maxlength="500">{{ old('other_reason') }}</textarea>
                            @error('other_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button class="newsletter-unsubscribe-card__submit" type="submit"><i class="fa-solid fa-ban" aria-hidden="true"></i> ان سبسکرائب کریں</button>
                    </form>
                @endif
            </section>
        </div>
    </div>
@endsection
