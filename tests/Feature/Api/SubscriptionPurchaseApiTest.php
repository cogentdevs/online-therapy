<?php

use App\Mail\SubscriptionActivatedMailToUser;
use App\Mail\SubscriptionPurchasedMailToAdmin;
use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-24 12:00:00');
    config(['mail.admin_address' => 'subscriptions-admin@example.test']);
    Mail::fake();

    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee',
        'code' => 'PKR',
        'symbol' => 'Rs',
        'isActive' => true,
    ]);
    $this->user = User::factory()->create(['is_active' => true]);
});

afterEach(function () {
    Carbon::setTestNow();
});

function apiPurchaseProduct(Currency $currency, string $productFor = SubscriptionProduct::FOR_PLAN, array $modules = ['Articles']): SubscriptionProduct
{
    $product = SubscriptionProduct::query()->create([
        'product_for' => $productFor,
        'name' => $productFor === SubscriptionProduct::FOR_PLAN ? 'Mobile Plan' : 'Mobile Membership',
        'currency_id' => $currency->id,
        'price' => 1000,
        'duration_value' => 1,
        'duration_unit' => $productFor === SubscriptionProduct::FOR_PLAN ? 'month' : 'year',
        'discount_type' => 'percentage',
        'discount_value' => 20,
        'isActive' => true,
    ]);

    foreach ($modules as $moduleName) {
        $type = SubscriptionType::query()->create([
            'name' => $moduleName,
            'slug' => strtolower($moduleName).'-'.fake()->unique()->numerify('###'),
            'isActive' => true,
        ]);
        $product->subscriptionTypes()->attach($type);
    }

    return $product;
}

test('purchase endpoint requires sanctum authentication and validates its minimal contract', function () {
    $product = apiPurchaseProduct($this->currency);

    $this->postJson(route('api.me.subscriptions.purchase'), [
        'subscription_product_id' => $product->id,
        'payment_method' => 'card',
    ])->assertUnauthorized();

    Sanctum::actingAs($this->user);

    $this->postJson(route('api.me.subscriptions.purchase'), [
        'subscription_product_id' => 999999,
        'payment_method' => 'cash',
    ])->assertUnprocessable()->assertJsonValidationErrors(['subscription_product_id', 'payment_method']);

    $this->assertDatabaseEmpty('user_subscriptions');
    Mail::assertNothingSent();
});

test('authenticated user purchases plan with authoritative snapshots and existing mails', function () {
    $product = apiPurchaseProduct($this->currency, modules: ['Articles', 'Magazine']);
    $otherUser = User::factory()->create();
    Sanctum::actingAs($this->user);

    $response = $this->postJson(route('api.me.subscriptions.purchase'), [
        'subscription_product_id' => $product->id,
        'payment_method' => 'easypaisa',
        'user_id' => $otherUser->id,
        'product_for' => 'membership',
        'price' => 1,
        'discount' => 999,
        'total' => 0,
        'status' => 'expired',
        'is_active' => false,
        'start_date' => '2040-01-01',
        'end_date' => '2040-01-02',
        'module_ids' => [],
        'order_no' => 'ATTACK',
        'invoice_no' => 'ATTACK',
        'transaction_id' => 'ATTACK',
    ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.subscription.product_for', 'plan')
        ->assertJsonPath('data.subscription.price', '800.00')
        ->assertJsonPath('data.subscription.discount', '0.00')
        ->assertJsonPath('data.subscription.total', '800.00')
        ->assertJsonPath('data.subscription.payment_method', 'easypaisa')
        ->assertJsonPath('data.subscription.status', 'active')
        ->assertJsonCount(2, 'data.subscription.modules');

    $subscription = UserSubscription::query()->sole();
    expect($subscription->user_id)->toBe($this->user->id)
        ->and($subscription->product_name)->toBe('Mobile Plan')
        ->and($subscription->currency_id)->toBe($this->currency->id)
        ->and($subscription->start_date->toDateString())->toBe('2026-09-24')
        ->and($subscription->end_date->toDateString())->toBe('2026-10-24')
        ->and($subscription->order_no)->toBeNull()
        ->and($subscription->invoice_no)->toBeNull()
        ->and($subscription->transaction_id)->toBeNull()
        ->and($subscription->subscriptionTypes)->toHaveCount(2);

    Mail::assertSent(SubscriptionActivatedMailToUser::class, fn ($mail): bool => $mail->hasTo($this->user->email));
    Mail::assertSent(SubscriptionPurchasedMailToAdmin::class, fn ($mail): bool => $mail->hasTo('subscriptions-admin@example.test'));
    Mail::assertNothingQueued();
});

test('membership purchase preserves product type duration and module snapshots', function () {
    $product = apiPurchaseProduct($this->currency, SubscriptionProduct::FOR_MEMBERSHIP, ['Magazine', 'Audio']);
    Sanctum::actingAs($this->user);

    $this->postJson(route('api.me.subscriptions.purchase'), [
        'subscription_product_id' => $product->id,
        'payment_method' => 'jazzcash',
    ])->assertCreated()
        ->assertJsonPath('data.subscription.product_for', 'membership')
        ->assertJsonPath('data.subscription.end_date', '2027-09-24')
        ->assertJsonCount(2, 'data.subscription.modules');

    expect(UserSubscription::query()->sole()->subscriptionTypes)->toHaveCount(2);
});

test('unavailable product is rejected without partial writes or mail', function () {
    $product = apiPurchaseProduct($this->currency);
    $product->update(['isActive' => false]);
    Sanctum::actingAs($this->user);

    $this->postJson(route('api.me.subscriptions.purchase'), [
        'subscription_product_id' => $product->id,
        'payment_method' => 'card',
    ])->assertUnprocessable()->assertJsonValidationErrors('subscription');

    $this->assertDatabaseEmpty('user_subscriptions');
    $this->assertDatabaseEmpty('user_sub_types');
    Mail::assertNothingSent();
});

test('existing account read apis immediately expose an api purchase', function () {
    $product = apiPurchaseProduct($this->currency, modules: ['Articles']);
    Sanctum::actingAs($this->user);

    $subscriptionId = $this->postJson(route('api.me.subscriptions.purchase'), [
        'subscription_product_id' => $product->id,
        'payment_method' => 'card',
    ])->assertCreated()->json('data.subscription.id');

    $this->getJson(route('api.me.subscriptions.active'))
        ->assertOk()->assertJsonPath('data.has_active_subscription', true)
        ->assertJsonPath('data.active_subscriptions.0.id', $subscriptionId);
    $this->getJson(route('api.me.entitlements.index'))
        ->assertOk()->assertJsonPath('data.modules.0.name', 'Articles');
    $this->getJson(route('api.me.subscriptions.index'))
        ->assertOk()->assertJsonPath('data.subscriptions.0.id', $subscriptionId);
});
