<?php

use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\SubscriptionEntitlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
    Carbon::setTestNow('2026-09-10 12:00:00');
    $this->user = User::factory()->create();
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true,
    ]);
    $this->articles = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    $this->magazine = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
    $this->entitlements = app(SubscriptionEntitlementService::class);
});

afterEach(fn () => Carbon::setTestNow());

function expiringSubscription(
    User $user,
    Currency $currency,
    array $types,
    array $overrides = [],
): UserSubscription {
    $product = SubscriptionProduct::query()->create([
        'product_for' => count($types) > 1 ? SubscriptionProduct::FOR_MEMBERSHIP : SubscriptionProduct::FOR_PLAN,
        'name' => 'Expiry Product '.fake()->unique()->numerify('###'),
        'currency_id' => $currency->id, 'price' => 500, 'duration_value' => 1,
        'duration_unit' => 'month', 'isActive' => true,
    ]);
    $subscription = UserSubscription::query()->create(array_merge([
        'user_id' => $user->id, 'subscription_product_id' => $product->id,
        'product_for' => $product->product_for, 'product_name' => $product->name,
        'currency_id' => $currency->id, 'price' => 500, 'discount' => 0, 'total' => 500,
        'status' => 'active', 'is_active' => true, 'start_date' => '2026-08-01', 'end_date' => '2026-09-09',
    ], $overrides));
    $subscription->subscriptionTypes()->attach(collect($types)->pluck('id'));

    return $subscription;
}

test('expired plan is deactivated while its entitlement snapshot and history remain', function () {
    $plan = expiringSubscription($this->user, $this->currency, [$this->articles]);

    expect($plan->is_active)->toBeTrue();
    $this->artisan('subscriptions:expire')
        ->expectsOutput('Expired subscriptions deactivated: 1')
        ->assertSuccessful();

    expect($plan->refresh()->is_active)->toBeFalse()
        ->and($plan->status)->toBe('expired')
        ->and($plan->subscriptionTypes()->pluck('subscription_types.id')->all())->toBe([$this->articles->id])
        ->and(UserSubscription::query()->whereKey($plan->id)->exists())->toBeTrue()
        ->and($this->entitlements->hasEntitlement($this->user, 'Articles'))->toBeFalse();
});

test('expired membership disables all its modules without deleting snapshots', function () {
    $membership = expiringSubscription($this->user, $this->currency, [$this->articles, $this->magazine]);

    $this->artisan('subscriptions:expire')->assertSuccessful();

    expect($membership->refresh()->is_active)->toBeFalse()
        ->and($membership->status)->toBe('expired')
        ->and($membership->subscriptionTypes()->count())->toBe(2)
        ->and($this->entitlements->hasEntitlement($this->user, 'Articles'))->toBeFalse()
        ->and($this->entitlements->hasEntitlement($this->user, 'Magazine'))->toBeFalse();
    $this->assertDatabaseCount('user_sub_types', 2);
});

test('overlapping active subscriptions continue granting their own modules', function () {
    $expiredMembership = expiringSubscription($this->user, $this->currency, [$this->articles, $this->magazine]);
    $activeArticlePlan = expiringSubscription($this->user, $this->currency, [$this->articles], [
        'product_name' => 'Still Active Article Plan', 'end_date' => '2026-10-10',
    ]);

    $this->artisan('subscriptions:expire')->expectsOutput('Expired subscriptions deactivated: 1')->assertSuccessful();

    expect($expiredMembership->refresh()->is_active)->toBeFalse()
        ->and($activeArticlePlan->refresh()->is_active)->toBeTrue()
        ->and($this->entitlements->hasEntitlement($this->user, 'Articles'))->toBeTrue()
        ->and($this->entitlements->hasEntitlement($this->user, 'Magazine'))->toBeFalse();
});

test('only passed end dates expire under inclusive date semantics', function () {
    $endsToday = expiringSubscription($this->user, $this->currency, [$this->articles], ['end_date' => '2026-09-10']);
    $future = expiringSubscription($this->user, $this->currency, [$this->articles], [
        'start_date' => '2026-09-20', 'end_date' => '2026-10-20',
    ]);
    $noEnd = expiringSubscription($this->user, $this->currency, [$this->articles], ['end_date' => null]);
    $cancelled = expiringSubscription($this->user, $this->currency, [$this->articles], [
        'status' => 'cancelled', 'is_active' => false, 'end_date' => '2026-09-01',
    ]);

    $this->artisan('subscriptions:expire')->expectsOutput('Expired subscriptions deactivated: 0')->assertSuccessful();
    expect($endsToday->refresh()->is_active)->toBeTrue()
        ->and($future->refresh()->is_active)->toBeTrue()
        ->and($noEnd->refresh()->is_active)->toBeTrue()
        ->and($cancelled->refresh()->status)->toBe('cancelled');

    Carbon::setTestNow('2026-09-11 00:01:00');
    $this->artisan('subscriptions:expire')->expectsOutput('Expired subscriptions deactivated: 1')->assertSuccessful();
    expect($endsToday->refresh()->status)->toBe('expired')
        ->and($future->refresh()->is_active)->toBeTrue()
        ->and($noEnd->refresh()->is_active)->toBeTrue()
        ->and($cancelled->refresh()->status)->toBe('cancelled');
});

test('command is idempotent and leaves a second active membership unchanged', function () {
    $expired = expiringSubscription($this->user, $this->currency, [$this->articles, $this->magazine]);
    $active = expiringSubscription($this->user, $this->currency, [$this->articles, $this->magazine], ['end_date' => '2026-12-31']);

    $this->artisan('subscriptions:expire')->expectsOutput('Expired subscriptions deactivated: 1')->assertSuccessful();
    $this->artisan('subscriptions:expire')->expectsOutput('Expired subscriptions deactivated: 0')->assertSuccessful();

    expect($expired->refresh()->status)->toBe('expired')
        ->and($active->refresh()->status)->toBe('active')
        ->and($this->entitlements->activeSubscriptions($this->user)->modelKeys())->toBe([$active->id])
        ->and($this->entitlements->hasEntitlement($this->user, 'Magazine'))->toBeTrue();
    $this->assertDatabaseCount('user_subscriptions', 2);
    $this->assertDatabaseCount('user_sub_types', 4);
});
