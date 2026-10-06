<?php

use App\Models\Currency;
use App\Models\SubscriptionExpiryReminder;
use App\Models\SubscriptionNotificationSetting;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\UserSubType;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee',
        'code' => 'PKR',
        'symbol' => 'Rs',
        'isActive' => true,
    ]);
    $this->product = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => 'Article Plan',
        'currency_id' => $this->currency->id,
        'price' => 200,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
});

function trackedUserSubscription(User $user, SubscriptionProduct $product, Currency $currency): UserSubscription
{
    return UserSubscription::query()->create([
        'user_id' => $user->id,
        'subscription_product_id' => $product->id,
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'product_name' => $product->name,
        'currency_id' => $currency->id,
        'price' => 200,
        'discount' => 0,
        'total' => 200,
        'start_date' => today(),
        'end_date' => today()->addMonth(),
        'status' => 'active',
        'is_active' => true,
    ]);
}

test('tracking schema and model store reminder state for a user subscription', function () {
    expect(Schema::hasColumns('subscription_expiry_reminders', [
        'id',
        'user_subscription_id',
        'reminder_days',
        'sent_at',
        'status',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    $subscription = trackedUserSubscription($this->user, $this->product, $this->currency);
    $sentAt = Carbon::parse('2026-09-11 09:30:00');
    $reminder = $subscription->expiryReminders()->create([
        'reminder_days' => 7,
        'sent_at' => $sentAt,
        'status' => SubscriptionExpiryReminder::STATUS_SENT,
    ]);

    expect($reminder->reminder_days)->toBe(7)
        ->and($reminder->sent_at)->toBeInstanceOf(Carbon::class)
        ->and($reminder->sent_at->equalTo($sentAt))->toBeTrue()
        ->and($reminder->status)->toBe(SubscriptionExpiryReminder::STATUS_SENT)
        ->and($reminder->userSubscription->is($subscription))->toBeTrue()
        ->and($subscription->expiryReminders()->sole()->is($reminder))->toBeTrue();
});

test('pending is the default tracking status and sent at may remain null', function () {
    $subscription = trackedUserSubscription($this->user, $this->product, $this->currency);
    $reminder = $subscription->expiryReminders()->create(['reminder_days' => 5]);

    expect($reminder->status)->toBe(SubscriptionExpiryReminder::STATUS_PENDING)
        ->and($reminder->sent_at)->toBeNull();
});

test('one subscription may track different reminder thresholds', function () {
    $subscription = trackedUserSubscription($this->user, $this->product, $this->currency);

    foreach ([7, 5, 1] as $days) {
        $subscription->expiryReminders()->create(['reminder_days' => $days]);
    }

    expect($subscription->expiryReminders()->orderByDesc('reminder_days')->pluck('reminder_days')->all())
        ->toBe([7, 5, 1]);
});

test('database uniqueness rejects the same reminder threshold for one subscription', function () {
    $subscription = trackedUserSubscription($this->user, $this->product, $this->currency);
    $subscription->expiryReminders()->create(['reminder_days' => 7]);

    expect(fn () => $subscription->expiryReminders()->create(['reminder_days' => 7]))
        ->toThrow(QueryException::class);

    expect($subscription->expiryReminders()->count())->toBe(1);
});

test('different subscriptions may track the same reminder threshold', function () {
    $first = trackedUserSubscription($this->user, $this->product, $this->currency);
    $second = trackedUserSubscription($this->user, $this->product, $this->currency);

    $firstReminder = $first->expiryReminders()->create(['reminder_days' => 7]);
    $secondReminder = $second->expiryReminders()->create(['reminder_days' => 7]);

    expect($firstReminder->user_subscription_id)->not->toBe($secondReminder->user_subscription_id)
        ->and(SubscriptionExpiryReminder::query()->where('reminder_days', 7)->count())->toBe(2);
});

test('deleting a subscription cascades tracking without changing independent snapshots or settings', function () {
    $subscription = trackedUserSubscription($this->user, $this->product, $this->currency);
    $type = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    $subscription->subscriptionTypes()->attach($type);
    $otherSubscription = trackedUserSubscription($this->user, $this->product, $this->currency);
    $otherEntitlement = UserSubType::query()->create([
        'user_subscription_id' => $otherSubscription->id,
        'subscription_type_id' => $type->id,
    ]);
    $setting = SubscriptionNotificationSetting::query()->create([
        'id' => 1,
        'first_reminder_days' => 7,
        'isActive' => true,
    ]);
    $reminder = $subscription->expiryReminders()->create(['reminder_days' => 7]);

    $subscription->delete();

    $this->assertModelMissing($reminder);
    $this->assertModelExists($otherEntitlement);
    $this->assertModelExists($setting);
    expect($setting->fresh()->first_reminder_days)->toBe(7);
});
