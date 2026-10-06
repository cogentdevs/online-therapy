<?php

use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\SubscriptionEntitlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-10 12:00:00');
    $this->user = User::factory()->create();
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true,
    ]);
    $this->product = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_MEMBERSHIP,
        'name' => 'Entitlement Product',
        'currency_id' => $this->currency->id,
        'price' => 1000,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $this->articles = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    $this->magazine = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
    $this->audio = SubscriptionType::query()->create(['name' => 'Audio', 'slug' => 'audio', 'isActive' => true]);
    $this->service = app(SubscriptionEntitlementService::class);
});

afterEach(function () {
    Carbon::setTestNow();
});

function makeEntitlementSubscription(User $user, SubscriptionProduct $product, Currency $currency, array $overrides = []): UserSubscription
{
    return UserSubscription::query()->create(array_merge([
        'user_id' => $user->id,
        'subscription_product_id' => $product->id,
        'product_for' => $product->product_for,
        'product_name' => $product->name,
        'currency_id' => $currency->id,
        'price' => 1000,
        'discount' => 0,
        'total' => 1000,
        'status' => 'active',
        'is_active' => true,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-30',
    ], $overrides));
}

test('currently active scope applies flags statuses and inclusive date boundaries', function () {
    makeEntitlementSubscription($this->user, $this->product, $this->currency, ['product_name' => 'valid']);
    makeEntitlementSubscription($this->user, $this->product, $this->currency, ['product_name' => 'disabled', 'is_active' => false]);
    makeEntitlementSubscription($this->user, $this->product, $this->currency, ['product_name' => 'cancelled', 'status' => 'cancelled']);
    makeEntitlementSubscription($this->user, $this->product, $this->currency, ['product_name' => 'future', 'start_date' => '2026-09-11']);
    makeEntitlementSubscription($this->user, $this->product, $this->currency, ['product_name' => 'starts-today', 'start_date' => '2026-09-10']);
    makeEntitlementSubscription($this->user, $this->product, $this->currency, ['product_name' => 'expired', 'end_date' => '2026-09-09']);
    makeEntitlementSubscription($this->user, $this->product, $this->currency, ['product_name' => 'ends-today', 'end_date' => '2026-09-10']);
    makeEntitlementSubscription($this->user, $this->product, $this->currency, ['product_name' => 'null-start', 'start_date' => null]);
    makeEntitlementSubscription($this->user, $this->product, $this->currency, ['product_name' => 'null-end', 'end_date' => null]);
    makeEntitlementSubscription($this->user, $this->product, $this->currency, ['product_name' => 'no-bounds', 'start_date' => null, 'end_date' => null]);

    expect(UserSubscription::query()->currentlyActive()->pluck('product_name')->all())->toBe([
        'valid', 'starts-today', 'ends-today', 'null-start', 'null-end', 'no-bounds',
    ]);
});

test('active plan grants its snapshot module and denies other modules', function () {
    $subscription = makeEntitlementSubscription($this->user, $this->product, $this->currency);
    $subscription->subscriptionTypes()->attach($this->articles);

    expect($this->service->hasEntitlement($this->user, $this->articles))->toBeTrue()
        ->and($this->service->hasEntitlement($this->user, $this->articles->id))->toBeTrue()
        ->and($this->service->hasEntitlement($this->user, 'Article'))->toBeTrue()
        ->and($this->service->hasEntitlement($this->user, $this->magazine))->toBeFalse()
        ->and($this->service->subscriptionGrantingEntitlement($this->user, 'articles')?->is($subscription))->toBeTrue();
});

test('active membership grants multiple modules through the same resolver', function () {
    $subscription = makeEntitlementSubscription($this->user, $this->product, $this->currency);
    $subscription->subscriptionTypes()->attach([$this->magazine->id, $this->articles->id, $this->audio->id]);

    expect($this->service->activeEntitlementTypes($this->user)->pluck('id')->sort()->values()->all())
        ->toBe(collect([$this->magazine->id, $this->articles->id, $this->audio->id])->sort()->values()->all())
        ->and($this->service->hasEntitlement($this->user, 'Magazines'))->toBeTrue()
        ->and($this->service->hasEntitlement($this->user, 'Audio'))->toBeTrue();
});

test('expired future and inactive subscriptions never leak entitlements', function () {
    $expired = makeEntitlementSubscription($this->user, $this->product, $this->currency, ['end_date' => '2026-09-09']);
    $future = makeEntitlementSubscription($this->user, $this->product, $this->currency, ['start_date' => '2026-09-11']);
    $inactive = makeEntitlementSubscription($this->user, $this->product, $this->currency, ['is_active' => false]);
    $expired->subscriptionTypes()->attach([$this->magazine->id, $this->audio->id]);
    $future->subscriptionTypes()->attach($this->articles);
    $inactive->subscriptionTypes()->attach($this->magazine);

    expect($this->service->activeEntitlementTypes($this->user))->toBeEmpty()
        ->and($this->service->hasEntitlement($this->user, $this->magazine))->toBeFalse()
        ->and($this->service->hasEntitlement($this->user, $this->articles))->toBeFalse();
});

test('multiple active subscriptions merge and deduplicate module entitlements', function () {
    $first = makeEntitlementSubscription($this->user, $this->product, $this->currency);
    $second = makeEntitlementSubscription($this->user, $this->product, $this->currency);
    $first->subscriptionTypes()->attach([$this->articles->id, $this->magazine->id]);
    $second->subscriptionTypes()->attach([$this->articles->id, $this->audio->id]);

    $types = $this->service->activeEntitlementTypes($this->user);

    expect($types)->toHaveCount(3)
        ->and($types->where('id', $this->articles->id))->toHaveCount(1)
        ->and($types->pluck('id')->sort()->values()->all())
        ->toBe(collect([$this->articles->id, $this->magazine->id, $this->audio->id])->sort()->values()->all());
});

test('purchase snapshot remains authoritative after current product modules change', function () {
    $this->product->subscriptionTypes()->attach([$this->magazine->id, $this->articles->id]);
    $subscription = makeEntitlementSubscription($this->user, $this->product, $this->currency);
    $subscription->subscriptionTypes()->attach([$this->magazine->id, $this->articles->id]);

    $this->product->subscriptionTypes()->sync([$this->magazine->id]);

    expect($this->service->hasEntitlement($this->user, $this->articles))->toBeTrue()
        ->and($this->service->activeEntitlementTypes($this->user)->pluck('id')->sort()->values()->all())
        ->toBe(collect([$this->magazine->id, $this->articles->id])->sort()->values()->all());
});

test('active subscriptions eager load entitlements without per-subscription queries', function () {
    $first = makeEntitlementSubscription($this->user, $this->product, $this->currency);
    $second = makeEntitlementSubscription($this->user, $this->product, $this->currency);
    $first->subscriptionTypes()->attach($this->articles);
    $second->subscriptionTypes()->attach($this->magazine);
    DB::flushQueryLog();
    DB::enableQueryLog();

    $subscriptions = $this->service->activeSubscriptions($this->user);

    expect(DB::getQueryLog())->toHaveCount(3)
        ->and($subscriptions->every(fn (UserSubscription $subscription): bool => $subscription->relationLoaded('subscriptionTypes')))->toBeTrue()
        ->and($subscriptions->every(fn (UserSubscription $subscription): bool => $subscription->relationLoaded('currency')))->toBeTrue();
});
