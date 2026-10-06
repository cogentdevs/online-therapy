<?php

use App\Models\Ad;
use App\Models\AdRequest;
use App\Models\AdRequestPlacement;
use App\Models\User;
use App\Services\AdPlacementAvailabilityService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function advertisingRequest(array $attributes = []): AdRequest
{
    return AdRequest::query()->create([
        'user_id' => User::factory()->create()->id,
        'name' => 'Advertiser',
        'email' => 'advertiser@example.com',
        'phone' => '03001234567',
        'from_date' => '2026-10-10',
        'to_date' => '2026-10-20',
        ...$attributes,
    ]);
}

function advertisingPlacement(AdRequest $request, string $status = 'pending', string $place = 'header_ad', string $page = 'header'): AdRequestPlacement
{
    $placement = $request->placements()->create(['page_name' => $page, 'place' => $place]);
    $placement->forceFill(['status' => $status])->save();

    return $placement;
}

function actualAdvertisingAd(array $attributes = []): Ad
{
    return Ad::query()->create([
        'title' => 'Existing campaign',
        'page_name' => 'header',
        'place' => 'header_ad',
        'start_date' => '2026-10-10',
        'expiry_date' => '2026-10-20',
        'isActive' => true,
        ...$attributes,
    ]);
}

test('an advertising request belongs to a user and has multiple placements', function () {
    $request = advertisingRequest();
    advertisingPlacement($request);
    advertisingPlacement($request, 'pending', 'home_horizontal_large', 'home');

    expect($request->user)->toBeInstanceOf(User::class)
        ->and($request->user->adRequests()->count())->toBe(1)
        ->and($request->placements()->count())->toBe(2)
        ->and($request->placements->first()->adRequest->is($request))->toBeTrue();
});

test('the same placement cannot be selected twice within one request', function () {
    $request = advertisingRequest();
    advertisingPlacement($request);

    expect(fn () => advertisingPlacement($request))->toThrow(QueryException::class);
});

test('request numbers derive from unique primary keys and cannot be edited', function () {
    $first = advertisingRequest();
    $second = advertisingRequest();

    expect($first->request_no)->toBe('ADREQ-'.str_pad((string) $first->id, 6, '0', STR_PAD_LEFT))
        ->and($second->request_no)->toBe('ADREQ-'.str_pad((string) $second->id, 6, '0', STR_PAD_LEFT))
        ->and($first->request_no)->not->toBe($second->request_no)
        ->and($first->fresh()->request_no)->toBe($first->request_no)
        ->and($first->status)->toBe('pending');

    expect(fn () => $first->forceFill(['request_no' => 'ADREQ-999999'])->save())->toThrow(LogicException::class);
});

test('placement keys are validated against the existing Ad placements', function () {
    $service = app(AdPlacementAvailabilityService::class);

    expect($service->isValidPlacement('header', 'header_ad'))->toBeTrue()
        ->and($service->isValidPlacement('home', 'home_horizontal_large'))->toBeTrue()
        ->and($service->isValidPlacement('header', 'home_horizontal_large'))->toBeFalse();

    expect(fn () => $service->isAvailable('header', 'home_horizontal_large', '2026-10-10', '2026-10-20'))
        ->toThrow(InvalidArgumentException::class);
});

test('invalid or reversed requested dates are rejected', function () {
    $service = app(AdPlacementAvailabilityService::class);

    expect(fn () => $service->isAvailable('header', 'header_ad', '2026-10-21', '2026-10-20'))
        ->toThrow(InvalidArgumentException::class);
    expect(fn () => $service->isAvailable('header', 'header_ad', '2026-02-30', '2026-10-20'))
        ->toThrow(InvalidArgumentException::class);
});

test('actual ads block overlapping and boundary dates but not later dates', function () {
    actualAdvertisingAd();
    $service = app(AdPlacementAvailabilityService::class);

    expect($service->isAvailable('header', 'header_ad', '2026-10-15', '2026-10-25'))->toBeFalse()
        ->and($service->isAvailable('header', 'header_ad', '2026-10-20', '2026-10-25'))->toBeFalse()
        ->and($service->isAvailable('header', 'header_ad', '2026-10-21', '2026-10-25'))->toBeTrue();
});

test('paused ads and open-ended ad dates remain booking conflicts', function () {
    actualAdvertisingAd(['isActive' => false, 'start_date' => null, 'expiry_date' => null]);
    $service = app(AdPlacementAvailabilityService::class);

    expect($service->isAvailable('header', 'header_ad', '2027-01-01', '2027-01-02'))->toBeFalse();
});

test('pending, quoted, conflicted, cancelled and published placements do not reserve a slot', function (string $status) {
    advertisingPlacement(advertisingRequest(), $status);

    expect(app(AdPlacementAvailabilityService::class)->isAvailable('header', 'header_ad', '2026-10-10', '2026-10-20'))
        ->toBeTrue();
})->with(['pending', 'quoted', 'conflicted', 'cancelled', 'published']);

test('multiple pending inquiries may request the same dates and place', function () {
    advertisingPlacement(advertisingRequest());
    advertisingPlacement(advertisingRequest());

    expect(app(AdPlacementAvailabilityService::class)->isAvailable('header', 'header_ad', '2026-10-10', '2026-10-20'))
        ->toBeTrue();
});

test('confirmed placement reserves its own page and place inclusively', function () {
    advertisingPlacement(advertisingRequest(), 'confirmed');
    $service = app(AdPlacementAvailabilityService::class);

    expect($service->isAvailable('header', 'header_ad', '2026-10-20', '2026-10-25'))->toBeFalse()
        ->and($service->isAvailable('header', 'header_ad', '2026-10-21', '2026-10-25'))->toBeTrue()
        ->and($service->isAvailable('home', 'home_horizontal_large', '2026-10-10', '2026-10-20'))->toBeTrue();
});

test('a different place on the same page is independent', function () {
    advertisingPlacement(advertisingRequest(), 'confirmed', 'home_horizontal_large', 'home');

    expect(app(AdPlacementAvailabilityService::class)->isAvailable('home', 'home_horizontal_small', '2026-10-10', '2026-10-20'))
        ->toBeTrue();
});

test('cancelled or rejected parent requests do not reserve slots', function (string $status) {
    $request = advertisingRequest();
    advertisingPlacement($request, 'confirmed');
    $request->forceFill(['status' => $status])->save();

    expect(app(AdPlacementAvailabilityService::class)->isAvailable('header', 'header_ad', '2026-10-10', '2026-10-20'))
        ->toBeTrue();
})->with(['cancelled', 'rejected']);

test('a reservation can be excluded by request or placement without excluding other requests', function () {
    $request = advertisingRequest();
    $placement = advertisingPlacement($request, 'confirmed');
    $service = app(AdPlacementAvailabilityService::class);

    expect($service->isAvailable('header', 'header_ad', '2026-10-10', '2026-10-20'))->toBeFalse()
        ->and($service->isAvailable('header', 'header_ad', '2026-10-10', '2026-10-20', excludeRequestId: $request->id))->toBeTrue()
        ->and($service->isAvailable('header', 'header_ad', '2026-10-10', '2026-10-20', excludePlacementId: $placement->id))->toBeTrue();

    advertisingPlacement(advertisingRequest(), 'confirmed');

    expect($service->isAvailable('header', 'header_ad', '2026-10-10', '2026-10-20', excludeRequestId: $request->id))->toBeFalse();
});
