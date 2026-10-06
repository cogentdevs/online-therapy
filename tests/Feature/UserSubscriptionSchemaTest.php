<?php

use App\Models\Currency;
use App\Models\PaymentAccount;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee',
        'code' => 'PKR',
        'symbol' => 'Rs',
        'isActive' => true,
    ]);
    $this->product = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_MEMBERSHIP,
        'name' => 'Reader Membership',
        'currency_id' => $this->currency->id,
        'price' => 1500,
        'duration_value' => 3,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
});

function createUserSubscriptionSnapshot(User $user, SubscriptionProduct $product, Currency $currency): UserSubscription
{
    return UserSubscription::query()->create([
        'user_id' => $user->id,
        'subscription_product_id' => $product->id,
        'product_for' => $product->product_for,
        'product_name' => $product->name,
        'currency_id' => $currency->id,
        'price' => '1500.00',
        'discount' => '0.00',
        'total' => '1500.00',
        'start_date' => '2026-09-10',
        'end_date' => '2026-12-10',
        'status' => 'active',
        'is_active' => true,
    ]);
}

test('user subscription snapshot tables contain the required columns', function () {
    expect(Schema::hasTable('user_subscriptions'))->toBeTrue()
        ->and(Schema::hasColumns('user_subscriptions', [
            'id', 'order_no', 'invoice_no', 'transaction_id', 'user_id', 'subscription_product_id', 'product_for', 'product_name',
            'currency_id', 'price', 'discount', 'total', 'payment_method', 'start_date',
            'payment_account_id', 'payment_slip', 'payment_status', 'payment_submitted_at',
            'reviewed_at', 'reviewed_by', 'rejection_reason', 'duration_value_snapshot',
            'duration_unit_snapshot', 'end_date', 'status', 'is_active', 'created_at', 'updated_at',
        ]))->toBeTrue()
        ->and(Schema::hasTable('user_sub_types'))->toBeTrue()
        ->and(Schema::hasColumns('user_sub_types', [
            'id', 'user_subscription_id', 'subscription_type_id', 'created_at', 'updated_at',
        ]))->toBeTrue();
});

test('invoice placeholder fields are nullable and remain unassigned during ordinary model creation', function () {
    $columns = collect(Schema::getColumns('user_subscriptions'))->keyBy('name');
    $subscription = createUserSubscriptionSnapshot($this->user, $this->product, $this->currency);

    expect($columns->get('order_no')['nullable'])->toBeTrue()
        ->and($columns->get('invoice_no')['nullable'])->toBeTrue()
        ->and($columns->get('transaction_id')['nullable'])->toBeTrue()
        ->and($subscription->order_no)->toBeNull()
        ->and($subscription->invoice_no)->toBeNull()
        ->and($subscription->transaction_id)->toBeNull();
});

test('user subscription exposes its purchase relationships and casts', function () {
    $subscription = createUserSubscriptionSnapshot($this->user, $this->product, $this->currency);

    expect($this->user->userSubscriptions->first()->is($subscription))->toBeTrue()
        ->and($subscription->user->is($this->user))->toBeTrue()
        ->and($subscription->subscriptionProduct->is($this->product))->toBeTrue()
        ->and($subscription->currency->is($this->currency))->toBeTrue()
        ->and($this->product->userSubscriptions->first()->is($subscription))->toBeTrue()
        ->and($subscription->price)->toBe('1500.00')
        ->and($subscription->is_active)->toBeTrue()
        ->and($subscription->start_date->toDateString())->toBe('2026-09-10');
});

test('payment review fields remain nullable and expose payment account and reviewer relationships', function () {
    $paymentAccount = PaymentAccount::query()->create([
        'bank_name' => 'Meezan Bank',
        'account_title' => 'Digital Magazine',
        'account_no' => '001234567890',
    ]);
    $reviewer = User::factory()->create();
    $subscription = createUserSubscriptionSnapshot($this->user, $this->product, $this->currency);

    expect($subscription->payment_account_id)->toBeNull()
        ->and($subscription->payment_slip)->toBeNull()
        ->and($subscription->payment_status)->toBeNull()
        ->and($subscription->payment_submitted_at)->toBeNull()
        ->and($subscription->reviewed_at)->toBeNull()
        ->and($subscription->reviewed_by)->toBeNull()
        ->and($subscription->rejection_reason)->toBeNull()
        ->and($subscription->duration_value_snapshot)->toBeNull()
        ->and($subscription->duration_unit_snapshot)->toBeNull();

    $subscription->update([
        'payment_account_id' => $paymentAccount->id,
        'payment_slip' => 'private/payment-slips/example.pdf',
        'payment_status' => UserSubscription::PAYMENT_STATUS_PENDING,
        'payment_submitted_at' => '2026-10-03 10:00:00',
        'reviewed_at' => '2026-10-03 11:00:00',
        'reviewed_by' => $reviewer->id,
        'duration_value_snapshot' => 3,
        'duration_unit_snapshot' => 'month',
        'status' => UserSubscription::STATUS_PENDING,
        'is_active' => false,
        'start_date' => null,
        'end_date' => null,
    ]);
    $subscription->refresh();

    expect($subscription->paymentAccount->is($paymentAccount))->toBeTrue()
        ->and($paymentAccount->userSubscriptions->first()->is($subscription))->toBeTrue()
        ->and($subscription->reviewer->is($reviewer))->toBeTrue()
        ->and($subscription->payment_submitted_at->toDateTimeString())->toBe('2026-10-03 10:00:00')
        ->and($subscription->reviewed_at->toDateTimeString())->toBe('2026-10-03 11:00:00')
        ->and($subscription->duration_value_snapshot)->toBe(3)
        ->and($subscription->toArray())->not->toHaveKey('payment_slip');
});

test('pending and rejected subscriptions cannot qualify for active entitlement scope', function (string $status) {
    $subscription = createUserSubscriptionSnapshot($this->user, $this->product, $this->currency);
    $subscription->update(['status' => $status, 'is_active' => false]);

    expect(UserSubscription::query()->currentlyActive()->whereKey($subscription->getKey())->exists())->toBeFalse();
})->with([
    UserSubscription::STATUS_PENDING,
    UserSubscription::STATUS_REJECTED,
]);

test('one purchase supports one or multiple entitlement snapshots', function () {
    $subscription = createUserSubscriptionSnapshot($this->user, $this->product, $this->currency);
    $magazine = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
    $articles = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    $audio = SubscriptionType::query()->create(['name' => 'Audio', 'slug' => 'audio', 'isActive' => true]);

    $subscription->subscriptionTypes()->attach($magazine);
    expect($subscription->subscriptionTypes()->pluck('subscription_types.id')->all())->toBe([$magazine->id]);

    $subscription->subscriptionTypes()->attach([$articles->id, $audio->id]);
    expect($subscription->subscriptionTypes()->count())->toBe(3)
        ->and($audio->userSubscriptions->first()->is($subscription))->toBeTrue();
});

test('duplicate entitlement snapshots are prevented', function () {
    $subscription = createUserSubscriptionSnapshot($this->user, $this->product, $this->currency);
    $type = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    $subscription->subscriptionTypes()->attach($type);

    expect(fn () => $subscription->subscriptionTypes()->attach($type))->toThrow(QueryException::class);
});

test('deleting a user subscription cascades only its entitlement snapshots', function () {
    $subscription = createUserSubscriptionSnapshot($this->user, $this->product, $this->currency);
    $type = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
    $this->product->subscriptionTypes()->attach($type);
    $subscription->subscriptionTypes()->attach($type);

    $subscription->delete();

    $this->assertDatabaseEmpty('user_sub_types');
    $this->assertDatabaseHas('subscription_product_types', [
        'subscription_product_id' => $this->product->id,
        'subscription_type_id' => $type->id,
    ]);
});
