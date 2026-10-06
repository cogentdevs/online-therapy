<?php

use App\Models\Currency;
use App\Models\PaymentAccount;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('user', 'web');
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true,
    ]);
    $this->type = SubscriptionType::query()->create([
        'name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true,
    ]);
    $this->product = createFrontendCheckoutProduct($this->currency, $this->type, 'Monthly Plan');
    $this->paymentAccount = PaymentAccount::query()->create([
        'bank_name' => 'Meezan Bank',
        'account_title' => 'Digital Magazine',
        'iban' => 'PK00MEZN0000000000000000',
        'account_no' => '00123456789',
        'branch_code' => '0123',
        'is_active' => true,
    ]);
    $this->user = User::factory()->create([
        'email' => 'checkout-reader@example.com', 'password' => 'password123', 'is_active' => true,
    ]);
    $this->user->assignRole('user');
});

function createFrontendCheckoutProduct(Currency $currency, SubscriptionType $type, string $name): SubscriptionProduct
{
    $product = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => $name,
        'currency_id' => $currency->id,
        'price' => 500,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $product->subscriptionTypes()->attach($type);

    return $product;
}

test('guest card carries the real product intent target and login modal', function () {
    $this->get(route('front.subscriptions'))
        ->assertOk()
        ->assertSee('data-product-id="'.$this->product->id.'"', false)
        ->assertSee('data-intent-url="'.route('front.subscriptions.intent', $this->product).'"', false)
        ->assertSee('data-subscription-login-modal', false)
        ->assertSee('action="'.route('front.login.store').'"', false);
});

test('checkout requires login and active frontend product', function () {
    $checkoutUrl = route('front.subscriptions.checkout', $this->product);
    $this->get($checkoutUrl)->assertRedirect(route('front.login'));

    $this->actingAs($this->user)->get($checkoutUrl)
        ->assertOk()
        ->assertViewIs('frontend.subscription-checkout')
        ->assertSee('Monthly Plan')
        ->assertSee('PKR 500', false)
        ->assertSee('ہفتہ وار میگزین')
        ->assertSee('name="payment_account_id"', false)
        ->assertSee('Meezan Bank')
        ->assertSee('00123456789')
        ->assertSee('name="payment_slip"', false)
        ->assertSee('name="transaction_id"', false)
        ->assertSee('enctype="multipart/form-data"', false)
        ->assertSee('data-checkout-action disabled', false)
        ->assertSee('action="'.route('front.subscriptions.checkout.store', $this->product).'"', false)
        ->assertSee('کوپن کوڈ درج کریں');

    $this->product->update(['isActive' => false]);
    $this->get($checkoutUrl)->assertNotFound();
    $this->get('/subscriptions/999999/checkout')->assertNotFound();
});

test('checkout renders only active payment accounts without creating purchase records', function () {
    PaymentAccount::query()->create([
        'bank_name' => 'Hidden Bank',
        'account_title' => 'Inactive Account',
        'account_no' => '999999',
        'is_active' => false,
    ]);

    $response = $this->actingAs($this->user)->get(route('front.subscriptions.checkout', $this->product));

    $response->assertOk()
        ->assertSee('Meezan Bank')
        ->assertDontSee('Hidden Bank');
    expect(substr_count($response->getContent(), 'name="payment_account_id"'))->toBe(1)
        ->and($response->getContent())->not->toContain('name="payment_account_id" checked');

    $this->assertDatabaseCount('subscription_products', 1);
    $this->assertDatabaseCount('subscription_product_types', 1);
    $this->assertDatabaseEmpty('user_subscriptions');
    expect(Schema::hasTable('payments'))->toBeFalse();
});

test('checkout submission requires an active account and a safe payment slip', function () {
    $inactiveAccount = PaymentAccount::query()->create([
        'bank_name' => 'Inactive Bank',
        'account_title' => 'Inactive Account',
        'account_no' => '999999',
        'is_active' => false,
    ]);

    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $this->product), [
            'payment_account_id' => $inactiveAccount->id,
            'payment_slip' => UploadedFile::fake()->image('slip.jpg'),
        ])
        ->assertSessionHasErrors('payment_account_id');

    $this->post(route('front.subscriptions.checkout.store', $this->product), [
        'payment_account_id' => $this->paymentAccount->id,
        'payment_slip' => UploadedFile::fake()->create('slip.txt', 10, 'text/plain'),
    ])->assertSessionHasErrors('payment_slip');

    $this->assertDatabaseEmpty('user_subscriptions');
});

test('subscription intent validates products and replaces an earlier selection', function () {
    $secondProduct = createFrontendCheckoutProduct($this->currency, $this->type, 'Yearly Plan');

    $this->postJson(route('front.subscriptions.intent', $this->product))
        ->assertOk()
        ->assertJsonPath('checkout_url', route('front.subscriptions.checkout', $this->product));
    expect(session('front_subscription_product_id'))->toBe($this->product->id);

    $this->postJson(route('front.subscriptions.intent', $secondProduct))->assertOk();
    expect(session('front_subscription_product_id'))->toBe($secondProduct->id);

    $secondProduct->update(['isActive' => false]);
    $this->postJson(route('front.subscriptions.intent', $secondProduct))->assertNotFound();
});

test('modal login reuses frontend authentication and returns the selected checkout target', function () {
    $this->postJson(route('front.subscriptions.intent', $this->product))->assertOk();

    $this->postJson(route('front.login.store'), [
        'email' => $this->user->email,
        'password' => 'password123',
        'remember' => true,
    ])->assertOk()->assertJsonPath('redirect', route('front.subscriptions.checkout', $this->product));

    $this->assertAuthenticatedAs($this->user, 'web');
    expect(session()->has('front_subscription_product_id'))->toBeFalse();
});

test('invalid modal credentials keep the user unauthenticated without a checkout redirect', function () {
    $this->postJson(route('front.subscriptions.intent', $this->product))->assertOk();

    $this->postJson(route('front.login.store'), [
        'email' => $this->user->email,
        'password' => 'incorrect-password',
    ])->assertUnprocessable()->assertJsonMissingPath('redirect')->assertJsonValidationErrors('email');

    $this->assertGuest('web');
    expect(session('front_subscription_product_id'))->toBe($this->product->id);
});

test('two factor modal login preserves selected checkout through the existing challenge flow', function () {
    Mail::fake();
    $this->user->twoFactorSetting()->create([
        'method' => 'email', 'is_enabled' => true, 'verified_at' => now(),
    ]);
    $this->postJson(route('front.subscriptions.intent', $this->product))->assertOk();

    $this->postJson(route('front.login.store'), [
        'email' => $this->user->email,
        'password' => 'password123',
    ])->assertOk()
        ->assertJsonPath('redirect', route('front.two-factor.challenge'))
        ->assertJsonPath('two_factor_required', true);

    $this->assertGuest('web');
    expect(session('front_two_factor_login.intended'))->toBe(route('front.subscriptions.checkout', $this->product));
});

test('closing the modal can clear only the subscription intent', function () {
    $this->withSession([
        'front_subscription_product_id' => $this->product->id,
        'unrelated' => 'preserved',
    ])->deleteJson(route('front.subscriptions.intent.clear'))->assertOk()->assertJson(['cleared' => true]);

    expect(session()->has('front_subscription_product_id'))->toBeFalse()
        ->and(session('unrelated'))->toBe('preserved');
});
