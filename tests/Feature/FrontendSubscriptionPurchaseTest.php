<?php

use App\Mail\SubscriptionActivatedMailToUser;
use App\Mail\SubscriptionPaymentReceivedMailToAdmin;
use App\Mail\SubscriptionPaymentReceivedMailToUser;
use App\Models\Currency;
use App\Models\PaymentAccount;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\SubscriptionEntitlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-10 12:00:00');
    config(['mail.admin_address' => 'subscriptions-admin@example.test']);
    Role::findOrCreate('user', 'web');
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee',
        'code' => 'PKR',
        'symbol' => 'Rs',
        'isActive' => true,
    ]);
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    $this->paymentAccount = PaymentAccount::query()->create([
        'bank_name' => 'Meezan Bank',
        'account_title' => 'Digital Magazine',
        'account_no' => '00123456789',
        'is_active' => true,
    ]);
    Storage::fake('local');
});

afterEach(function () {
    Carbon::setTestNow();
});

function makePurchasableProduct(
    Currency $currency,
    string $productFor = SubscriptionProduct::FOR_PLAN,
    int $durationValue = 1,
    string $durationUnit = 'month',
    array $typeNames = ['Articles'],
): SubscriptionProduct {
    $product = SubscriptionProduct::query()->create([
        'product_for' => $productFor,
        'name' => $productFor === SubscriptionProduct::FOR_PLAN ? 'Monthly Reading Plan' : 'Gold Membership',
        'currency_id' => $currency->id,
        'price' => 1000,
        'duration_value' => $durationValue,
        'duration_unit' => $durationUnit,
        'discount_type' => 'percentage',
        'discount_value' => 20,
        'isActive' => true,
    ]);

    foreach ($typeNames as $typeName) {
        $type = SubscriptionType::query()->create([
            'name' => $typeName,
            'slug' => strtolower($typeName).'-'.fake()->unique()->numerify('###'),
            'isActive' => true,
        ]);
        $product->subscriptionTypes()->attach($type);
    }

    return $product;
}

function websitePaymentPayload(PaymentAccount $paymentAccount, array $overrides = []): array
{
    return array_merge([
        'payment_account_id' => $paymentAccount->id,
        'payment_slip' => UploadedFile::fake()->image('payment-slip.jpg'),
        'transaction_id' => 'TXN-12345',
    ], $overrides);
}

test('guest cannot submit subscription checkout', function () {
    $product = makePurchasableProduct($this->currency);

    $this->post(route('front.subscriptions.checkout.store', $product), [
        'payment_method' => 'card',
    ])->assertRedirect(route('front.login'));

    $this->assertDatabaseEmpty('user_subscriptions');
});

test('website payment submission stores a private pending purchase and sends review emails only', function () {
    Mail::fake();
    $product = makePurchasableProduct($this->currency);

    $response = $this->actingAs($this->user)->post(route('front.subscriptions.checkout.store', $product), websitePaymentPayload($this->paymentAccount, [
        'user_id' => User::factory()->create()->id,
        'price' => 1,
        'total' => 1,
    ]));

    $response->assertRedirect(route('front.account.subscriptions'))
        ->assertSessionHas('success');
    $this->assertDatabaseHas('user_subscriptions', [
        'user_id' => $this->user->id,
        'subscription_product_id' => $product->id,
        'product_for' => 'plan',
        'product_name' => 'Monthly Reading Plan',
        'currency_id' => $this->currency->id,
        'price' => 800,
        'discount' => 0,
        'total' => 800,
        'payment_method' => 'bank_transfer',
        'payment_account_id' => $this->paymentAccount->id,
        'transaction_id' => 'TXN-12345',
        'payment_status' => 'pending',
        'start_date' => null,
        'end_date' => null,
        'status' => 'pending',
        'is_active' => false,
    ]);
    $subscription = UserSubscription::query()->sole();
    expect($subscription->subscriptionTypes)->toHaveCount(1);

    expect($subscription->payment_submitted_at)->not->toBeNull()
        ->and($subscription->duration_value_snapshot)->toBe(1)
        ->and($subscription->duration_unit_snapshot)->toBe('month')
        ->and($subscription->order_no)->toBeNull()
        ->and($subscription->invoice_no)->toBeNull();
    Storage::disk('local')->assertExists($subscription->payment_slip);
    expect($subscription->payment_slip)->toStartWith('payment-slips/');
    Mail::assertSent(SubscriptionPaymentReceivedMailToUser::class, function (SubscriptionPaymentReceivedMailToUser $mail): bool {
        $html = $mail->render();

        return $mail->hasTo($this->user->email)
            && str_contains($html, 'pending admin verification')
            && str_contains($html, 'TXN-12345')
            && ! str_contains($html, 'payment-slips/');
    });
    Mail::assertSent(SubscriptionPaymentReceivedMailToAdmin::class, 'subscriptions-admin@example.test');
    Mail::assertSentCount(2);
    Mail::assertNotSent(SubscriptionActivatedMailToUser::class);
});

test('membership snapshots all modules independently from later product changes', function () {
    Mail::fake();
    $product = makePurchasableProduct(
        $this->currency,
        SubscriptionProduct::FOR_MEMBERSHIP,
        1,
        'year',
        ['Magazine', 'Articles', 'Audio'],
    );
    $purchasedTypeIds = $product->subscriptionTypes()->pluck('subscription_types.id')->sort()->values()->all();

    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $product), websitePaymentPayload($this->paymentAccount))
        ->assertRedirect(route('front.account.subscriptions'));

    $subscription = UserSubscription::query()->sole();
    expect($subscription->product_for)->toBe('membership')
        ->and($subscription->end_date)->toBeNull()
        ->and($subscription->subscriptionTypes()->pluck('subscription_types.id')->sort()->values()->all())->toBe($purchasedTypeIds);

    $product->subscriptionTypes()->detach();
    expect($subscription->subscriptionTypes()->pluck('subscription_types.id')->sort()->values()->all())->toBe($purchasedTypeIds);
    Mail::assertSent(SubscriptionPaymentReceivedMailToUser::class, 1);
    Mail::assertSent(SubscriptionPaymentReceivedMailToAdmin::class, 1);
});

test('payment validation and unavailable products cannot create purchases or mails', function () {
    Mail::fake();
    $product = makePurchasableProduct($this->currency);

    $this->actingAs($this->user)->post(route('front.subscriptions.checkout.store', $product), [])
        ->assertSessionHasErrors(['payment_account_id', 'payment_slip']);

    $product->update(['isActive' => false]);
    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $product), websitePaymentPayload($this->paymentAccount))
        ->assertNotFound();

    $this->assertDatabaseEmpty('user_subscriptions');
    Mail::assertNothingSent();
});

test('product without active modules and unsupported duration are rejected atomically', function () {
    Mail::fake();
    $withoutModules = makePurchasableProduct($this->currency, SubscriptionProduct::FOR_MEMBERSHIP, typeNames: []);
    $unsupportedDuration = makePurchasableProduct($this->currency, durationUnit: 'quarter');

    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $withoutModules), websitePaymentPayload($this->paymentAccount))
        ->assertSessionHasErrors('subscription');
    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $unsupportedDuration), websitePaymentPayload($this->paymentAccount))
        ->assertSessionHasErrors('subscription');

    $this->assertDatabaseEmpty('user_subscriptions');
    $this->assertDatabaseEmpty('user_sub_types');
    Mail::assertNothingSent();
});

test('pending requests for different products create independent inactive history', function () {
    Mail::fake();
    $firstProduct = makePurchasableProduct($this->currency);
    $secondProduct = makePurchasableProduct($this->currency, SubscriptionProduct::FOR_MEMBERSHIP, typeNames: ['Magazine', 'Audio']);

    foreach ([$firstProduct, $secondProduct] as $product) {
        $this->actingAs($this->user)
            ->post(route('front.subscriptions.checkout.store', $product), websitePaymentPayload($this->paymentAccount))
            ->assertRedirect(route('front.account.subscriptions'));
    }

    expect(UserSubscription::query()->count())->toBe(2)
        ->and(UserSubscription::query()->where('is_active', false)->count())->toBe(2)
        ->and(UserSubscription::query()->oldest('id')->first()->subscriptionTypes)->toHaveCount(1)
        ->and(UserSubscription::query()->latest('id')->first()->subscriptionTypes)->toHaveCount(2);
    Mail::assertSent(SubscriptionPaymentReceivedMailToUser::class, 2);
    Mail::assertSent(SubscriptionPaymentReceivedMailToAdmin::class, 2);
});

test('duplicate pending payment is rejected before another slip is stored', function () {
    $product = makePurchasableProduct($this->currency);

    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $product), websitePaymentPayload($this->paymentAccount))
        ->assertRedirect(route('front.account.subscriptions'));
    $firstSlip = UserSubscription::query()->sole()->payment_slip;

    $this->from(route('front.subscriptions.checkout', $product))
        ->post(route('front.subscriptions.checkout.store', $product), websitePaymentPayload($this->paymentAccount))
        ->assertRedirect(route('front.subscriptions.checkout', $product))
        ->assertSessionHasErrors('subscription');

    $this->assertDatabaseCount('user_subscriptions', 1);
    $this->assertDatabaseCount('user_sub_types', 1);
    Storage::disk('local')->assertExists($firstSlip);
    expect(Storage::disk('local')->allFiles('payment-slips'))->toHaveCount(1);
});

test('pending payment grants no entitlement and exposes no invoice', function () {
    $product = makePurchasableProduct($this->currency);

    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $product), websitePaymentPayload($this->paymentAccount));
    $subscription = UserSubscription::query()->sole();

    expect(app(SubscriptionEntitlementService::class)->activeSubscriptions($this->user))->toBeEmpty();
    $this->get(route('front.account.subscriptions.invoice.view', $subscription))->assertNotFound();
    $this->get(route('front.account.subscriptions.invoice.download', $subscription))->assertNotFound();
});
