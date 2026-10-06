@props(['ad' => null, 'size', 'tall' => false])

@php
    $hasImage = filled($ad?->ad_image) && \Illuminate\Support\Facades\File::isFile(public_path($ad->ad_image));
@endphp

@if (filled($ad?->google_ad_code))
    <div @class(['front-ad-placement', 'front-ad-placement--tall' => $tall, 'front-ad-content', 'front-ad-content--google'])>{!! $ad->google_ad_code !!}</div>
@elseif ($hasImage)
    <div @class(['front-ad-placement', 'front-ad-placement--tall' => $tall, 'front-ad-content'])>
        @if ($ad->isCurrentlyTrackable())
            <a class="front-ad-content__link" href="{{ route('ads.click', $ad) }}" target="_blank"
                rel="noopener noreferrer">
                <img class="front-ad-content__image" src="{{ asset($ad->ad_image) }}" alt="{{ $ad->title ?? 'Advertisement' }}">
            </a>
        @else
            <img class="front-ad-content__image" src="{{ asset($ad->ad_image) }}" alt="{{ $ad->title ?? 'Advertisement' }}">
        @endif
    </div>
@else
    <div @class(['front-ad-placement', 'front-ad-placement--tall' => $tall, 'front-ad-placeholder', 'front-ad-placeholder--tall' => $tall])>
        <span class="front-ad-placeholder__label">اشتہار</span>
        <span class="front-ad-placeholder__size front-ui">{{ $size }}</span>
    </div>
@endif
