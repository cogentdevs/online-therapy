<?php

use App\Models\Ad;
use App\Models\AdRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo(now()->setDate(2026, 9, 22));
    Role::findOrCreate('user', 'web');
    $this->advertiser = User::factory()->create(['is_active' => true]);
    $this->advertiser->assignRole('user');
    $this->otherAdvertiser = User::factory()->create(['is_active' => true]);
    $this->otherAdvertiser->assignRole('user');
    Mail::fake();
});

function accountAdRequest(User $user, array $attributes = []): AdRequest
{
    return AdRequest::query()->create(array_merge([
        'user_id' => $user->id,
        'name' => 'Snapshot Name',
        'email' => 'snapshot@example.test',
        'phone' => '03001234567',
        'from_date' => '2026-10-05',
        'to_date' => '2026-10-06',
    ], $attributes));
}

test('guest cannot open the account advertising request pages', function () {
    $adRequest = accountAdRequest($this->advertiser);

    $this->get(route('front.account.advertising-requests.index'))->assertRedirect(route('front.login'));
    $this->get(route('front.account.advertising-requests.show', $adRequest))->assertRedirect(route('front.login'));
});

test('empty list uses the account layout and links to the advertising form', function () {
    $this->actingAs($this->advertiser)
        ->get(route('front.account.advertising-requests.index'))
        ->assertSuccessful()
        ->assertSee('آپ نے ابھی تک کوئی تشہیری درخواست جمع نہیں کرائی۔')
        ->assertSee(route('front.advertise'), false)
        ->assertSee('میری تشہیری درخواستیں')
        ->assertSee('aria-current="page"', false);

    Mail::assertNothingOutgoing();
});

test('list shows only own requests newest first with status and inclusive duration', function () {
    $older = accountAdRequest($this->advertiser);
    $older->forceFill(['created_at' => now()->subDay()])->save();
    $newer = accountAdRequest($this->advertiser, ['from_date' => '2026-10-10', 'to_date' => '2026-10-20']);
    $other = accountAdRequest($this->otherAdvertiser);
    $newer->forceFill(['status' => 'quote_sent'])->save();
    $newer->placements()->create(['page_name' => 'header', 'place' => 'header_ad']);

    $this->actingAs($this->advertiser)
        ->get(route('front.account.advertising-requests.index'))
        ->assertSuccessful()
        ->assertSeeInOrder([$newer->request_no, $older->request_no])
        ->assertDontSee($other->request_no)
        ->assertSee('کوٹیشن ارسال کر دی گئی')
        ->assertSee('11 دن')
        ->assertSee('مقامات: 1');

    Mail::assertNothingOutgoing();
});

test('list paginates own requests ten at a time', function () {
    for ($index = 0; $index < 11; $index++) {
        accountAdRequest($this->advertiser);
    }

    $this->actingAs($this->advertiser)
        ->get(route('front.account.advertising-requests.index'))
        ->assertSuccessful()
        ->assertViewHas('adRequests', fn ($requests): bool => $requests->count() === 10 && $requests->total() === 11);
});

test('detail is owner-only and shows persisted snapshots and every placement', function () {
    $own = accountAdRequest($this->advertiser, ['company' => null, 'details' => null]);
    $other = accountAdRequest($this->otherAdvertiser);
    $own->placements()->create(['page_name' => 'header', 'place' => 'header_ad']);
    $own->placements()->create(['page_name' => 'home', 'place' => 'home_horizontal_small']);

    $this->actingAs($this->advertiser)
        ->get(route('front.account.advertising-requests.show', $own))
        ->assertSuccessful()
        ->assertSee($own->request_no)
        ->assertSee('Snapshot Name')
        ->assertSee('snapshot@example.test')
        ->assertSee('03001234567')
        ->assertSee(Ad::PAGE_PLACEMENTS['header']['places']['header_ad'])
        ->assertSee(Ad::PAGE_PLACEMENTS['home']['places']['home_horizontal_small'])
        ->assertSee('2 دن');

    $this->get(route('front.account.advertising-requests.show', $other))->assertNotFound();
    $this->get('/account/advertising-requests/999999')->assertNotFound();
    Mail::assertNothingOutgoing();
});

test('detail shows each persisted placement status and confirmed without an Ad', function () {
    $request = accountAdRequest($this->advertiser);
    $slots = [
        ['header', 'header_ad', 'pending', 'زیرِ جائزہ'],
        ['home', 'home_horizontal_large', 'quoted', 'کوٹیشن ارسال شدہ'],
        ['home', 'home_horizontal_small', 'confirmed', 'تصدیق شدہ'],
        ['taza_shumara', 'taza_sidebar_1_normal', 'conflicted', 'منتخب مدت میں دستیاب نہیں'],
        ['taza_shumara', 'taza_sidebar_1_tall', 'published', 'شائع شدہ'],
        ['taza_shumara', 'taza_sidebar_2_normal', 'cancelled', 'منسوخ'],
    ];

    foreach ($slots as [$page, $place, $status]) {
        $slot = $request->placements()->create(['page_name' => $page, 'place' => $place]);
        $slot->forceFill(['status' => $status])->save();
    }

    $response = $this->actingAs($this->advertiser)->get(route('front.account.advertising-requests.show', $request))->assertSuccessful();

    foreach ($slots as [$page, $place, $status, $label]) {
        $response->assertSee(Ad::PAGE_PLACEMENTS[$page]['places'][$place])->assertSee($label);
    }

    $response->assertSee('مقام کی تصدیق ہو چکی ہے، اشتہار ابھی شائع نہیں ہوا۔')
        ->assertDontSee('کل کلکس:');
});

test('linked image Ad shows its title dates active state and existing tracked click count', function () {
    $request = accountAdRequest($this->advertiser);
    $slot = $request->placements()->create(['page_name' => 'header', 'place' => 'header_ad']);
    $ad = Ad::query()->create([
        'title' => 'My Image Ad', 'page_name' => 'header', 'place' => 'header_ad',
        'ad_image' => 'images/backend-images/ads/example.png', 'ad_url' => 'https://example.test',
        'isActive' => true, 'start_date' => '2026-09-20', 'expiry_date' => '2026-09-30',
        'ad_request_id' => $request->id, 'ad_request_placement_id' => $slot->id,
    ]);
    $ad->click_count = 7;
    $ad->save();

    $this->actingAs($this->advertiser)
        ->get(route('front.account.advertising-requests.show', $request))
        ->assertSuccessful()
        ->assertSee('My Image Ad')
        ->assertSee('فعال')
        ->assertSee('20 Sep 2026')
        ->assertSee('30 Sep 2026')
        ->assertSee('کل کلکس: 7');

    Mail::assertNothingOutgoing();
});

test('linked Ad status distinguishes scheduled expired and inactive', function (array $attributes, string $label) {
    $request = accountAdRequest($this->advertiser);
    $slot = $request->placements()->create(['page_name' => 'header', 'place' => 'header_ad']);
    Ad::query()->create(array_merge([
        'title' => 'Status Ad', 'page_name' => 'header', 'place' => 'header_ad',
        'ad_request_id' => $request->id, 'ad_request_placement_id' => $slot->id,
        'isActive' => true,
    ], $attributes));

    $this->actingAs($this->advertiser)
        ->get(route('front.account.advertising-requests.show', $request))
        ->assertSuccessful()
        ->assertSee('Status Ad')
        ->assertSee($label);
})->with([
    'scheduled' => [['start_date' => '2026-09-23', 'expiry_date' => '2026-10-10'], 'آئندہ شائع ہوگا'],
    'expired' => [['start_date' => '2026-09-01', 'expiry_date' => '2026-09-21'], 'مدت ختم ہو گئی'],
    'inactive' => [['isActive' => false], 'غیر فعال'],
]);

test('Google code and image without a valid destination do not show click counts', function (array $adAttributes) {
    $request = accountAdRequest($this->advertiser);
    $slot = $request->placements()->create(['page_name' => 'header', 'place' => 'header_ad']);
    $ad = Ad::query()->create(array_merge([
        'title' => 'Untracked Ad', 'page_name' => 'header', 'place' => 'header_ad',
        'ad_request_id' => $request->id, 'ad_request_placement_id' => $slot->id,
        'isActive' => true,
    ], $adAttributes));
    $ad->click_count = 99;
    $ad->save();

    $this->actingAs($this->advertiser)
        ->get(route('front.account.advertising-requests.show', $request))
        ->assertSuccessful()
        ->assertSee('اس اشتہار کے لیے داخلی کلک ٹریکنگ دستیاب نہیں ہے۔')
        ->assertDontSee('کل کلکس: 99');
})->with([
    'google code' => [['google_ad_code' => '<div>Google ad</div>', 'ad_url' => 'https://example.test']],
    'image without URL' => [['ad_image' => 'images/backend-images/ads/example.png']],
    'image with invalid URL' => [['ad_image' => 'images/backend-images/ads/example.png', 'ad_url' => 'javascript:alert(1)']],
]);

test('account advertising routes expose GET only', function () {
    expect(collect(app('router')->getRoutes()->getRoutes())
        ->filter(fn ($route): bool => str_starts_with((string) $route->getName(), 'front.account.advertising-requests.'))
        ->every(fn ($route): bool => $route->methods() === ['GET', 'HEAD']))->toBeTrue();
});
