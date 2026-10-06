<?php

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use App\Mail\AdvertisingRequestReceivedMailToAdmin;
use App\Mail\AdvertisingRequestReceivedMailToUser;
use App\Models\Ad;
use App\Models\AdRequest;
use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo(now()->setDate(2026, 9, 22));
    config(['captcha.sitekey' => 'advertise-test-site-key', 'captcha.secret' => 'advertise-test-secret']);
    config(['mail.admin_address' => 'cogentdevs@gmail.com']);
    Role::findOrCreate('user', 'web');
    $this->advertiser = User::factory()->create([
        'name' => 'Ali Advertiser',
        'email' => 'ali@example.test',
        'phone' => '03001234567',
        'is_active' => true,
    ]);
    $this->advertiser->assignRole('user');
});

function advertisingPayload(array $overrides = []): array
{
    return array_replace([
        'name' => 'Snapshot Name',
        'email' => 'snapshot@example.test',
        'phone' => '03009998888',
        'company' => 'Example Brand',
        'from_date' => '2026-10-10',
        'to_date' => '2026-10-20',
        'placements' => ['home:home_horizontal_large'],
        'details' => 'Please contact us.',
        'g-recaptcha-response' => 'verified-google-token',
    ], $overrides);
}

test('guest is sent to login and the advertising URL remains intended', function () {
    $this->get(route('front.advertise'))
        ->assertRedirect(route('front.login'))
        ->assertSessionHas('url.intended', route('front.advertise'));
});

test('the page uses shared layout, prefills user fields, and links from navigation and home', function () {
    $this->actingAs($this->advertiser)->get(route('front.advertise'))
        ->assertSuccessful()
        ->assertViewIs('frontend.advertise')
        ->assertSee('تشہیر کیجئے')
        ->assertSee('value="Ali Advertiser"', false)
        ->assertSee('value="ali@example.test"', false)
        ->assertSee('value="03001234567"', false)
        ->assertSee('advertise-test-site-key', false)
        ->assertSee('دستیاب تشہیری مقامات')
        ->assertSee('placement-preview.svg', false)
        ->assertSee('ہیڈر اشتہار — 728 × 90')
        ->assertSee('افقی اشتہار (بڑا) — 1020 × 150')
        ->assertSee('دائیں سائیڈ بار (دوسرا، لمبا) — 600 × 300')
        ->assertDontSee('Header Advertisement')
        ->assertDontSee('ہمارے ساتھ تشہیر کیوں؟')
        ->assertSee('name="_token"', false)
        ->assertSee('front-footer', false)
        ->assertSee('aria-current="page"', false);

    $this->get(route('frontend.home'))->assertSee(route('front.advertise'), false);
});

test('availability and submission require a frontend account', function () {
    $this->getJson(route('front.advertise.availability', [
        'from_date' => '2026-10-10',
        'to_date' => '2026-10-20',
    ]))->assertRedirect(route('front.login'));

    $this->post(route('front.advertise.store'), advertisingPayload())->assertRedirect(route('front.login'));
});

test('availability rejects past, reversed, and missing dates', function (array $dates, string $field) {
    $this->actingAs($this->advertiser)
        ->getJson(route('front.advertise.availability', $dates))
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'past start' => [['from_date' => '2026-09-21', 'to_date' => '2026-10-20'], 'from_date'],
    'reversed' => [['from_date' => '2026-10-21', 'to_date' => '2026-10-20'], 'to_date'],
    'missing' => [['to_date' => '2026-10-20'], 'from_date'],
]);

test('availability calculates inclusive days and returns every canonical placement', function () {
    $response = $this->actingAs($this->advertiser)->getJson(route('front.advertise.availability', [
        'from_date' => '2026-10-05',
        'to_date' => '2026-10-05',
    ]))->assertSuccessful()->assertJsonPath('duration_days', 1);

    $expected = collect(Ad::PAGE_PLACEMENTS)->flatMap(fn (array $page, string $pageName): array => collect($page['places'])->map(fn (string $label, string $place): array => [
        'page_name' => $pageName,
        'place' => $place,
        'label' => $label,
    ])->all())->values();

    expect($response->json('placements'))->toHaveCount($expected->count());

    foreach ($expected as $slot) {
        $response->assertJsonFragment($slot);
    }

    $this->getJson(route('front.advertise.availability', [
        'from_date' => '2026-10-05',
        'to_date' => '2026-10-06',
    ]))->assertJsonPath('duration_days', 2);
});

test('actual ads and confirmed requests are unavailable while pending requests are selectable', function () {
    Ad::query()->create([
        'title' => 'Booked header',
        'page_name' => 'header',
        'place' => 'header_ad',
        'start_date' => '2026-10-10',
        'expiry_date' => '2026-10-20',
    ]);

    $confirmed = AdRequest::query()->create([
        'user_id' => $this->advertiser->id,
        'name' => 'Ali',
        'email' => 'ali@example.test',
        'phone' => '03001234567',
        'from_date' => '2026-10-10',
        'to_date' => '2026-10-20',
    ]);
    $confirmedPlacement = $confirmed->placements()->create(['page_name' => 'home', 'place' => 'home_horizontal_large']);
    $confirmedPlacement->forceFill(['status' => 'confirmed'])->save();

    $pending = AdRequest::query()->create([
        'user_id' => $this->advertiser->id,
        'name' => 'Ali',
        'email' => 'ali@example.test',
        'phone' => '03001234567',
        'from_date' => '2026-10-10',
        'to_date' => '2026-10-20',
    ]);
    $pending->placements()->create(['page_name' => 'home', 'place' => 'home_horizontal_small']);

    $placements = $this->actingAs($this->advertiser)->getJson(route('front.advertise.availability', [
        'from_date' => '2026-10-10',
        'to_date' => '2026-10-20',
    ]))->assertSuccessful()->json('placements');

    $byKey = collect($placements)->keyBy(fn (array $slot): string => $slot['page_name'].':'.$slot['place']);
    expect($byKey['header:header_ad']['available'])->toBeFalse()
        ->and($byKey['home:home_horizontal_large']['available'])->toBeFalse()
        ->and($byKey['home:home_horizontal_small']['available'])->toBeTrue();
});

test('invalid or duplicate placements and an empty selection are rejected', function (array $placements) {
    NoCaptcha::shouldReceive('verifyResponse')->zeroOrMoreTimes()->andReturn(true);

    $this->actingAs($this->advertiser)->post(route('front.advertise.store'), advertisingPayload([
        'placements' => $placements,
    ]))->assertSessionHasErrors('placements');

    $this->assertDatabaseEmpty('ad_requests');
})->with([
    'none' => [[]],
    'wrong pair' => [['header:home_horizontal_large']],
    'duplicate' => [['header:header_ad', 'header:header_ad']],
]);

test('submission validates dates and the existing Google captcha rule', function () {
    $this->actingAs($this->advertiser)->post(route('front.advertise.store'), advertisingPayload([
        'from_date' => '2026-09-21',
        'g-recaptcha-response' => '',
    ]))->assertSessionHasErrors(['from_date', 'g-recaptcha-response']);

    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(false);
    $this->post(route('front.advertise.store'), advertisingPayload())
        ->assertSessionHasErrors('g-recaptcha-response');

    $this->assertDatabaseEmpty('ad_requests');
});

test('submission requires a company or brand name', function (bool $omitCompany) {
    NoCaptcha::shouldReceive('verifyResponse')->zeroOrMoreTimes()->andReturn(true);

    $payload = advertisingPayload(['company' => '']);

    if ($omitCompany) {
        unset($payload['company']);
    }

    $this->actingAs($this->advertiser)
        ->post(route('front.advertise.store'), $payload)
        ->assertSessionHasErrors('company');

    $this->assertDatabaseEmpty('ad_requests');
})->with([
    'missing' => [true],
    'blank' => [false],
]);

test('stale availability rejects all selected placements without a partial request', function () {
    Mail::fake();
    Ad::query()->create([
        'title' => 'Booked',
        'page_name' => 'header',
        'place' => 'header_ad',
        'start_date' => '2026-10-10',
        'expiry_date' => '2026-10-20',
    ]);
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

    $this->actingAs($this->advertiser)->post(route('front.advertise.store'), advertisingPayload([
        'placements' => ['home:home_horizontal_large', 'header:header_ad'],
    ]))->assertSessionHasErrors('placements');

    $this->assertDatabaseEmpty('ad_requests');
    $this->assertDatabaseEmpty('ad_request_placements');
    Mail::assertNothingOutgoing();
});

test('valid submission stores snapshots and multiple pending placements then redirects to an owned success GET', function () {
    Mail::fake();
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);
    $otherUser = User::factory()->create();

    $response = $this->actingAs($this->advertiser)->post(route('front.advertise.store'), advertisingPayload([
        'placements' => ['home:home_horizontal_large', 'taza_shumara:taza_sidebar_1_normal'],
        'user_id' => $otherUser->id,
    ]));

    $this->assertDatabaseCount('ad_requests', 1);
    $request = AdRequest::query()->sole();
    $response->assertRedirect(route('front.advertise.success', $request));

    expect($request->user_id)->toBe($this->advertiser->id)
        ->and($request->name)->toBe('Snapshot Name')
        ->and($request->email)->toBe('snapshot@example.test')
        ->and($request->phone)->toBe('03009998888')
        ->and($request->company)->toBe('Example Brand')
        ->and($request->from_date->toDateString())->toBe('2026-10-10')
        ->and($request->to_date->toDateString())->toBe('2026-10-20')
        ->and($request->status)->toBe('pending')
        ->and($request->request_no)->toBe('ADREQ-'.str_pad((string) $request->id, 6, '0', STR_PAD_LEFT))
        ->and($request->placements()->count())->toBe(2)
        ->and($request->placements()->where('status', 'pending')->count())->toBe(2);

    $this->get(route('front.advertise.success', $request))->assertSuccessful()->assertSee($request->request_no);
    $this->get(route('front.advertise.success', $request))->assertSuccessful();
    $this->assertDatabaseCount('ad_requests', 1);
    Mail::assertSent(AdvertisingRequestReceivedMailToUser::class, 1);
    Mail::assertSent(AdvertisingRequestReceivedMailToAdmin::class, 1);
    Mail::assertSentCount(2);

    $this->actingAs($otherUser)->get(route('front.advertise.success', $request))->assertForbidden();
});

test('new request emails use persisted snapshots canonical labels and inclusive duration', function () {
    Mail::fake();
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

    $this->actingAs($this->advertiser)->post(route('front.advertise.store'), advertisingPayload([
        'from_date' => '2026-10-05',
        'to_date' => '2026-10-06',
        'placements' => ['header:header_ad', 'home:home_horizontal_small'],
        'company' => 'Example Brand',
        'details' => null,
    ]))->assertRedirect();

    $adRequest = AdRequest::query()->with('placements')->sole();
    expect($adRequest->durationDays())->toBe(2);

    Mail::assertSent(AdvertisingRequestReceivedMailToUser::class, function (AdvertisingRequestReceivedMailToUser $mail) use ($adRequest): bool {
        $html = $mail->render();

        return $mail->hasTo('snapshot@example.test')
            && $mail->adRequest->is($adRequest)
            && str_contains($mail->envelope()->subject, $adRequest->request_no)
            && str_contains($html, $adRequest->request_no)
            && str_contains($html, Ad::PAGE_PLACEMENTS['header']['places']['header_ad'])
            && str_contains($html, Ad::PAGE_PLACEMENTS['home']['places']['home_horizontal_small'])
            && str_contains($html, '2 دن')
            && ! str_contains($html, 'home_horizontal_small');
    });

    Mail::assertSent(AdvertisingRequestReceivedMailToAdmin::class, function (AdvertisingRequestReceivedMailToAdmin $mail) use ($adRequest): bool {
        $html = $mail->render();

        return $mail->hasTo('admin@example.test')
            && $mail->adRequest->is($adRequest)
            && str_contains($mail->envelope()->subject, $adRequest->request_no)
            && str_contains($html, 'snapshot@example.test')
            && str_contains($html, '03009998888')
            && str_contains($html, $adRequest->request_no);
    });

    $this->get(route('front.advertise.success', $adRequest))->assertSuccessful();
    Mail::assertSentCount(2);
});

test('configured general settings email is the admin fallback when no admin mail address is set', function () {
    config(['mail.admin_address' => null]);
    GeneralSetting::query()->create(['email' => 'cogentdevs@gmail.com']);
    Mail::fake();
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

    $this->actingAs($this->advertiser)->post(route('front.advertise.store'), advertisingPayload())->assertRedirect();

    Mail::assertSent(AdvertisingRequestReceivedMailToAdmin::class, fn (AdvertisingRequestReceivedMailToAdmin $mail): bool => $mail->hasTo('cogentdevs@gmail.com'));
});

test('invalid submission sends neither new-request email', function () {
    Mail::fake();
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

    $this->actingAs($this->advertiser)->post(route('front.advertise.store'), advertisingPayload([
        'placements' => [],
    ]))->assertSessionHasErrors('placements');

    $this->assertDatabaseEmpty('ad_requests');
    Mail::assertNothingOutgoing();
});

test('a user mail failure preserves the request and still attempts the admin email', function () {
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);
    Mail::shouldReceive('to')->once()->with('snapshot@example.test')->andThrow(new RuntimeException('User SMTP failure'));
    $adminPendingMail = Mockery::mock(PendingMail::class);
    $adminPendingMail->shouldReceive('send')->once()->with(Mockery::type(AdvertisingRequestReceivedMailToAdmin::class));
    Mail::shouldReceive('to')->once()->with('admin@example.test')->andReturn($adminPendingMail);

    $this->actingAs($this->advertiser)->post(route('front.advertise.store'), advertisingPayload())->assertRedirect();

    $this->assertDatabaseCount('ad_requests', 1);
    $this->assertDatabaseCount('ad_request_placements', 1);
});

test('an admin mail failure preserves the request after attempting the user email', function () {
    NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);
    $userPendingMail = Mockery::mock(PendingMail::class);
    $userPendingMail->shouldReceive('send')->once()->with(Mockery::type(AdvertisingRequestReceivedMailToUser::class));
    Mail::shouldReceive('to')->once()->with('snapshot@example.test')->andReturn($userPendingMail);
    Mail::shouldReceive('to')->once()->with('admin@example.test')->andThrow(new RuntimeException('Admin SMTP failure'));

    $this->actingAs($this->advertiser)->post(route('front.advertise.store'), advertisingPayload())->assertRedirect();

    $this->assertDatabaseCount('ad_requests', 1);
    $this->assertDatabaseCount('ad_request_placements', 1);
});
