<?php

use App\Models\Ad;
use App\Models\AdRequest;
use App\Models\Magazine;
use App\Models\TazaShumara;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function frontendAd(array $attributes = []): Ad
{
    return Ad::query()->forceCreate(array_merge([
        'language' => 'ur',
        'title' => 'Frontend campaign',
        'page_name' => 'header',
        'place' => 'header_ad',
        'ad_image' => 'images/backend-images/ads/campaign.webp',
        'ad_url' => 'https://advertiser.example/campaign',
        'isActive' => true,
    ], $attributes));
}

function frontendAdTazaShumara(): void
{
    $magazine = Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Ads Magazine',
        'publish_date' => '2026-09-04',
        'isActive' => true,
    ]);

    TazaShumara::query()->create([
        'language' => 'ur',
        'magazine_id' => $magazine->id,
        'show_title' => true,
        'show_short_description' => false,
        'is_active' => true,
    ]);
}

function frontendLinkedTazaAd(array $attributes = []): Ad
{
    $user = User::factory()->create();
    $request = AdRequest::query()->create([
        'user_id' => $user->id,
        'name' => 'Advertiser',
        'email' => 'advertiser@example.test',
        'phone' => '03001234567',
        'from_date' => '2026-09-23',
        'to_date' => '2026-10-10',
    ]);
    $placement = $request->placements()->create([
        'page_name' => 'taza_shumara',
        'place' => 'taza_sidebar_1_normal',
    ]);

    return frontendAd(array_merge([
        'page_name' => 'taza_shumara',
        'place' => 'taza_sidebar_1_normal',
        'ad_image' => 'images/backend-images/ads/linked-campaign.webp',
        'start_date' => '2026-09-23',
        'expiry_date' => '2026-10-10',
        'ad_request_id' => $request->id,
        'ad_request_placement_id' => $placement->id,
    ], $attributes));
}

test('eligible header image ad remains hidden from the frontend header', function () {
    $ad = frontendAd();

    $this->get(route('frontend.home'))
        ->assertSuccessful()
        ->assertDontSee(asset($ad->ad_image), false)
        ->assertDontSee(route('ads.click', $ad), false)
        ->assertDontSee($ad->ad_url, false);
});

test('ineligible header ads do not leave a header placeholder', function (array $attributes) {
    $this->travelTo(now()->setDate(2026, 9, 4));
    frontendAd($attributes);

    $this->get(route('frontend.home'))
        ->assertSuccessful()
        ->assertDontSee('728 × 90');
})->with([
    'inactive' => [['isActive' => false]],
    'future' => [['start_date' => '2026-09-05']],
    'expired' => [['expiry_date' => '2026-09-03']],
    'another language' => [['language' => 'en']],
    'empty draft' => [['ad_image' => null, 'google_ad_code' => null]],
]);

test('an eligible ad expiring today remains hidden from the frontend header', function () {
    $this->travelTo(now()->setDate(2026, 9, 4));
    $ad = frontendAd(['expiry_date' => '2026-09-04']);

    $this->get(route('frontend.home'))->assertDontSee(asset($ad->ad_image), false);
});

test('home placements remain configured but do not render on the homepage', function () {
    $large = frontendAd([
        'title' => 'Large home ad',
        'page_name' => 'home',
        'place' => 'home_horizontal_large',
        'ad_image' => 'images/backend-images/ads/home-large.webp',
    ]);
    $small = frontendAd([
        'title' => 'Small home ad',
        'page_name' => 'home',
        'place' => 'home_horizontal_small',
        'ad_image' => 'images/backend-images/ads/home-small.webp',
    ]);

    $html = $this->get(route('frontend.home'))->assertSuccessful()->getContent();

    expect($html)
        ->not->toContain(asset($large->ad_image))
        ->not->toContain(asset($small->ad_image))
        ->not->toContain('front-ad-slot--home');
});

test('each taza shumara ad renders only in its exact place', function () {
    frontendAdTazaShumara();
    $places = [
        'taza_sidebar_1_normal',
        'taza_sidebar_1_tall',
        'taza_sidebar_2_normal',
        'taza_sidebar_2_tall',
    ];

    foreach ($places as $place) {
        frontendAd([
            'page_name' => 'taza_shumara',
            'place' => $place,
            'ad_image' => 'images/backend-images/ads/'.$place.'.webp',
        ]);
    }

    $html = $this->get(route('taza.shumara'))->assertSuccessful()->getContent();

    expect($html)->toContain('front-taza-ad-slots')
        ->toContain('front-ad-placement')
        ->toContain('front-ad-placement--tall');

    foreach ($places as $place) {
        expect(substr_count($html, 'images/backend-images/ads/'.$place.'.webp'))->toBe(1);
    }
});

test('blank google code falls back to the taza shumara placeholder', function () {
    frontendAdTazaShumara();
    frontendAd([
        'page_name' => 'taza_shumara',
        'place' => 'taza_sidebar_1_normal',
        'ad_image' => null,
        'google_ad_code' => '   ',
    ]);

    $this->get(route('taza.shumara'))
        ->assertSuccessful()
        ->assertSee('front-ad-placeholder', false)
        ->assertSee('250 × 300');
});

test('missing ad image file falls back to the slot placeholder instead of rendering a broken image', function () {
    frontendAdTazaShumara();
    $ad = frontendLinkedTazaAd([
        'ad_image' => 'images/backend-images/ads/missing-campaign.png',
    ]);

    $this->travelTo('2026-09-24');
    $this->get(route('taza.shumara'))
        ->assertSuccessful()
        ->assertSee('front-ad-placeholder', false)
        ->assertSee('250 × 300')
        ->assertDontSee(asset($ad->ad_image), false);
});

test('current language ad wins over a newer nullable language fallback', function () {
    $urdu = frontendAd(['ad_image' => 'images/backend-images/ads/urdu.webp']);
    frontendAd(['language' => null, 'ad_image' => 'images/backend-images/ads/global.webp']);

    $this->get(route('frontend.home'))
        ->assertSee(asset($urdu->ad_image), false)
        ->assertDontSee('images/backend-images/ads/global.webp', false);
});

test('nullable language ad is used when current language has no ad', function () {
    $ad = frontendAd(['language' => null]);

    $this->get(route('frontend.home'))->assertSee(asset($ad->ad_image), false);
});

test('image ad without a url renders without click tracking', function () {
    $ad = frontendAd(['ad_url' => null]);

    $html = $this->get(route('frontend.home'))->assertSuccessful()->getContent();

    expect($html)->toContain(asset($ad->ad_image))
        ->not->toContain(route('ads.click', $ad));
});

test('google ad code renders raw without custom click tracking', function () {
    $ad = frontendAd([
        'ad_image' => 'images/backend-images/ads/ignored.webp',
        'google_ad_code' => '<ins data-testid="google-slot">Google slot</ins>',
    ]);

    $response = $this->get(route('frontend.home'));

    $response->assertSuccessful()
        ->assertSee('<ins data-testid="google-slot">Google slot</ins>', false)
        ->assertDontSee(route('ads.click', $ad), false)
        ->assertDontSee($ad->ad_image, false);
    expect($ad->fresh()->click_count)->toBe(0);
});

test('newest eligible displayable ad is selected deterministically', function () {
    frontendAd(['ad_image' => 'images/backend-images/ads/older.webp']);
    $newest = frontendAd(['ad_image' => 'images/backend-images/ads/newest.webp']);

    $this->get(route('frontend.home'))
        ->assertSee(asset($newest->ad_image), false)
        ->assertDontSee('images/backend-images/ads/older.webp', false);
});

test('linked Taza Ad is scheduled before its start then renders through its inclusive expiry date', function () {
    frontendAdTazaShumara();
    $ad = frontendLinkedTazaAd();

    $this->travelTo(now()->setDate(2026, 9, 22));
    $this->get(route('taza.shumara'))->assertSuccessful()
        ->assertSee('front-ad-placeholder', false)
        ->assertDontSee(asset($ad->ad_image), false);

    foreach (['2026-09-23', '2026-09-30', '2026-10-10'] as $date) {
        $this->travelTo($date);
        $this->get(route('taza.shumara'))->assertSuccessful()
            ->assertSee(asset($ad->ad_image), false)
            ->assertSee(route('ads.click', $ad), false);
    }

    $this->travelTo('2026-10-11');
    $this->get(route('taza.shumara'))->assertSuccessful()
        ->assertSee('front-ad-placeholder', false)
        ->assertDontSee(asset($ad->ad_image), false);
});

test('linked and manual Ads share exact slot newest-first selection without linkage filtering', function () {
    frontendAdTazaShumara();
    $this->travelTo('2026-09-24');
    $manual = frontendAd([
        'page_name' => 'taza_shumara',
        'place' => 'taza_sidebar_1_normal',
        'ad_image' => 'images/backend-images/ads/manual-campaign.webp',
    ]);
    $linked = frontendLinkedTazaAd();

    $this->get(route('taza.shumara'))->assertSuccessful()
        ->assertSee(asset($linked->ad_image), false)
        ->assertDontSee(asset($manual->ad_image), false);

    $linked->isActive = false;
    $linked->save();
    $this->get(route('taza.shumara'))->assertSuccessful()
        ->assertSee(asset($manual->ad_image), false)
        ->assertDontSee(asset($linked->ad_image), false);
});

test('linked Ad still obeys language exact slot and displayable media requirements', function (array $attributes) {
    frontendAdTazaShumara();
    $this->travelTo('2026-09-24');
    $ad = frontendLinkedTazaAd($attributes);

    $response = $this->get(route('taza.shumara'))->assertSuccessful()
        ->assertSee('front-ad-placeholder', false);

    if (($attributes['place'] ?? null) === 'taza_sidebar_2_normal') {
        expect(strpos($response->getContent(), '250 × 300'))
            ->toBeLessThan(strpos($response->getContent(), asset($ad->ad_image)));
    } else {
        $response->assertDontSee($ad->ad_image ?? 'linked-campaign.webp', false);
    }
})->with([
    'wrong language' => [['language' => 'en']],
    'wrong place' => [['place' => 'taza_sidebar_2_normal']],
    'no media' => [['ad_image' => null, 'google_ad_code' => null]],
]);

test('linked nullable-language image and Google code follow existing fallback and rendering rules', function () {
    frontendAdTazaShumara();
    $this->travelTo('2026-09-24');
    $fallback = frontendLinkedTazaAd(['language' => null]);
    $this->get(route('taza.shumara'))->assertSuccessful()->assertSee(asset($fallback->ad_image), false);

    $fallback->google_ad_code = '<ins data-testid="linked-google">Google slot</ins>';
    $fallback->save();
    $this->get(route('taza.shumara'))->assertSuccessful()
        ->assertSee('<ins data-testid="linked-google">Google slot</ins>', false)
        ->assertDontSee(route('ads.click', $fallback), false);
});
