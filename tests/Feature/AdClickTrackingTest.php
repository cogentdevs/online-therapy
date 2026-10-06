<?php

use App\Models\ActivityLog;
use App\Models\Ad;
use App\Models\AdClick;
use App\Models\Language;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

function directTrackingAd(array $attributes = []): Ad
{
    return Ad::query()->forceCreate([
        'title' => 'Direct campaign',
        'page_name' => 'home',
        'place' => 'home_horizontal_large',
        'ad_url' => 'https://advertiser.example/landing',
        'isActive' => true,
        ...$attributes,
    ]);
}

test('valid direct ad clicks create events increment atomically and redirect to stored URL', function () {
    $ad = directTrackingAd(['start_date' => today(), 'expiry_date' => today()]);

    $this->get(route('ads.click', ['ad' => $ad, 'url' => 'https://attacker.example']))
        ->assertRedirect('https://advertiser.example/landing');
    $this->get(route('ads.click', $ad))->assertRedirect('https://advertiser.example/landing');

    expect($ad->fresh()->click_count)->toBe(2)
        ->and(AdClick::query()->whereBelongsTo($ad)->count())->toBe(2)
        ->and(AdClick::query()->whereBelongsTo($ad)->whereNotNull('clicked_at')->count())->toBe(2);
});

test('missing or unsafe destinations are not counted', function (array $attributes) {
    $ad = directTrackingAd($attributes);

    $this->get(route('ads.click', $ad))->assertNotFound();

    expect($ad->fresh()->click_count)->toBe(0)
        ->and(AdClick::query()->whereBelongsTo($ad)->exists())->toBeFalse();
})->with([
    'missing URL' => [['ad_url' => null]],
    'javascript URL' => [['ad_url' => 'javascript:alert(1)']],
    'data URL' => [['ad_url' => 'data:text/html,bad']],
]);

test('inactive and date-ineligible ads are not counted', function (array $attributes) {
    $ad = directTrackingAd($attributes);

    $this->get(route('ads.click', $ad))->assertNotFound();

    expect($ad->fresh()->click_count)->toBe(0)
        ->and($ad->clicks()->exists())->toBeFalse();
})->with([
    'inactive' => [['isActive' => false]],
    'expired yesterday' => [['expiry_date' => today()->subDay()]],
    'starts tomorrow' => [['start_date' => today()->addDay()]],
]);

test('Google ads are excluded from custom click tracking', function () {
    $code = '<ins class="adsbygoogle"></ins>';
    $ad = directTrackingAd(['google_ad_code' => $code]);

    $this->get(route('ads.click', $ad))->assertNotFound();

    expect($ad->fresh()->click_count)->toBe(0)
        ->and($ad->google_ad_code)->toBe($code)
        ->and($ad->clicks()->exists())->toBeFalse();
});

test('public clicks do not create Admin activity logs', function () {
    $ad = directTrackingAd();
    $before = ActivityLog::query()->count();

    $this->get(route('ads.click', $ad))->assertRedirect();

    expect(ActivityLog::query()->count())->toBe($before);
});

test('Admin listing and detail show the summary count while forms cannot set it', function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    Language::query()->create(['name' => 'Urdu', 'code' => 'ur', 'is_active' => true]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $ad = directTrackingAd(['click_count' => 1245]);

    $this->actingAs($admin)->get(route('admin.ads.index'))->assertSuccessful()->assertSee('1,245');
    $this->actingAs($admin)->get(route('admin.ads.show', $ad))->assertSuccessful()->assertSee('Total Clicks')->assertSee('1,245');
    $this->actingAs($admin)->post(route('admin.ads.store'), [
        'title' => 'Cannot seed clicks',
        'page_name' => 'header',
        'place' => 'header_ad',
        'click_count' => 777,
    ])->assertSessionHasNoErrors();
    $this->actingAs($admin)->put(route('admin.ads.update', $ad), [
        'title' => $ad->title,
        'page_name' => $ad->page_name,
        'place' => $ad->place,
        'click_count' => 9999,
    ])->assertSessionHasNoErrors();

    expect($ad->fresh()->click_count)->toBe(1245)
        ->and(Ad::query()->where('title', 'Cannot seed clicks')->value('click_count'))->toBe(0);
});

test('unknown ads return not found', function () {
    $this->get('/ads/999999/click')->assertNotFound();
});
