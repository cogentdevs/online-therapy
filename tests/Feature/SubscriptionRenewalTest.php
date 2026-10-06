<?php

use App\Mail\SubscriptionActivatedMailToUser;
use App\Mail\SubscriptionPaymentReceivedMailToAdmin;
use App\Mail\SubscriptionPaymentReceivedMailToUser;
use App\Models\Currency;
use App\Models\PaymentAccount;
use App\Models\SubscriptionNotificationSetting;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\SubscriptionEntitlementService;
use App\Services\SubscriptionPaymentMailService;
use App\Services\SubscriptionPurchaseService;
use App\Services\SubscriptionRenewalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-10 12:00:00');
    Role::findOrCreate('user', 'web');
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true,
    ]);
    $this->type = SubscriptionType::query()->create([
        'name' => 'Articles', 'slug' => 'articles-renewal', 'isActive' => true,
    ]);
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    $this->paymentAccount = PaymentAccount::query()->create([
        'bank_name' => 'Meezan Bank',
        'account_title' => 'Digital Magazine',
        'account_no' => '00123456789',
        'is_active' => true,
    ]);
    config(['mail.admin_address' => 'subscriptions-admin@example.test']);
    Storage::fake('local');
});

afterEach(fn () => Carbon::setTestNow());

function renewalProduct(Currency $currency, SubscriptionType $type, string $name = 'Monthly Pack'): SubscriptionProduct
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

function renewalSettings(?int $first = 7, ?int $second = 5, ?int $third = 1, bool $active = true): void
{
    SubscriptionNotificationSetting::query()->create([
        'first_reminder_days' => $first,
        'second_reminder_days' => $second,
        'third_reminder_days' => $third,
        'isActive' => $active,
    ]);
}

function ownedRenewalSubscription(User $user, SubscriptionProduct $product, Carbon $start, Carbon $end): UserSubscription
{
    $subscription = $user->userSubscriptions()->create([
        'subscription_product_id' => $product->id,
        'product_for' => $product->product_for,
        'product_name' => $product->name,
        'currency_id' => $product->currency_id,
        'price' => $product->price,
        'discount' => 0,
        'total' => $product->price,
        'payment_method' => 'card',
        'start_date' => $start,
        'end_date' => $end,
        'status' => 'active',
        'is_active' => true,
    ]);
    $subscription->subscriptionTypes()->attach($product->subscriptionTypes()->pluck('subscription_types.id'));

    return $subscription;
}

function renewalPaymentPayload(PaymentAccount $paymentAccount, array $overrides = []): array
{
    return array_merge([
        'payment_account_id' => $paymentAccount->id,
        'payment_slip' => UploadedFile::fake()->image('renewal-slip.jpg'),
        'transaction_id' => 'RENEW-TXN-1',
    ], $overrides);
}

test('renewal window uses maximum configured day and remains continuous through expiry day', function () {
    renewalSettings(7, 5, 1);
    $product = renewalProduct($this->currency, $this->type);
    $service = app(SubscriptionRenewalService::class);

    expect($service->renewalWindowDays())->toBe(7);

    foreach ([7, 6, 5, 3, 1, 0] as $remainingDays) {
        UserSubscription::query()->delete();
        ownedRenewalSubscription($this->user, $product, today()->subMonth(), today()->addDays($remainingDays));
        expect($service->stateFor($this->user, $product)['state'])
            ->toBe(SubscriptionRenewalService::STATE_RENEWAL_AVAILABLE);
    }
});

test('same active product outside window is disabled and blocked by checkout get and post', function () {
    Mail::fake();
    renewalSettings();
    $product = renewalProduct($this->currency, $this->type);
    ownedRenewalSubscription($this->user, $product, today()->subMonth(), today()->addDays(20));

    $this->actingAs($this->user)->get(route('front.subscriptions'))
        ->assertOk()
        ->assertSee('فعال سبسکرپشن')
        ->assertSee('disabled', false);
    $this->get(route('front.subscriptions.checkout', $product))
        ->assertRedirect(route('front.subscriptions'));
    $this->post(route('front.subscriptions.checkout.store', $product), renewalPaymentPayload($this->paymentAccount))
        ->assertSessionHasErrors('subscription');

    expect($this->user->userSubscriptions()->count())->toBe(1);
    Mail::assertNothingSent();
});

test('allowed renewal creates a separate pending request and preserves current access', function () {
    Mail::fake();
    renewalSettings();
    $product = renewalProduct($this->currency, $this->type);
    $current = ownedRenewalSubscription($this->user, $product, Carbon::parse('2026-08-11'), Carbon::parse('2026-09-11'));
    $originalStartDate = $current->start_date->toDateString();
    $originalEndDate = $current->end_date->toDateString();

    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $product), renewalPaymentPayload($this->paymentAccount))
        ->assertRedirect(route('front.account.subscriptions'));

    $renewal = $this->user->userSubscriptions()->latest('id')->firstOrFail();
    expect($renewal->id)->not->toBe($current->id)
        ->and($renewal->payment_status)->toBe(UserSubscription::PAYMENT_STATUS_PENDING)
        ->and($renewal->status)->toBe(UserSubscription::STATUS_PENDING)
        ->and($renewal->is_active)->toBeFalse()
        ->and($renewal->start_date)->toBeNull()
        ->and($renewal->end_date)->toBeNull()
        ->and($renewal->subscriptionTypes)->toHaveCount(1)
        ->and($current->fresh()->start_date->toDateString())->toBe($originalStartDate)
        ->and($current->fresh()->end_date->toDateString())->toBe($originalEndDate)
        ->and($current->fresh()->status)->toBe('active')
        ->and($current->fresh()->is_active)->toBeTrue()
        ->and(app(SubscriptionEntitlementService::class)->activeSubscriptions($this->user)->modelKeys())->toContain($current->id)
        ->not->toContain($renewal->id);
    Storage::disk('local')->assertExists($renewal->payment_slip);
    Mail::assertSent(SubscriptionPaymentReceivedMailToUser::class, 1);
    Mail::assertSent(SubscriptionPaymentReceivedMailToAdmin::class, 1);
    Mail::assertNotSent(SubscriptionActivatedMailToUser::class);
});

test('one future same-product renewal blocks checkout and a third purchase', function () {
    Mail::fake();
    renewalSettings();
    $product = renewalProduct($this->currency, $this->type);
    ownedRenewalSubscription($this->user, $product, today()->subMonth(), today()->addDays(3));
    ownedRenewalSubscription($this->user, $product, today()->addDays(4), today()->addMonth()->addDays(4));

    $this->actingAs($this->user)->get(route('front.subscriptions'))
        ->assertSee('تجدید ہو چکی ہے');
    $this->get(route('front.subscriptions.checkout', $product))
        ->assertRedirect(route('front.subscriptions'));
    $this->post(route('front.subscriptions.checkout.store', $product), renewalPaymentPayload($this->paymentAccount))
        ->assertSessionHasErrors('subscription');

    expect($this->user->userSubscriptions()->count())->toBe(2);
    Mail::assertNothingSent();
});

test('inactive reminder settings disable early renewal but expired product and different product remain purchasable', function () {
    renewalSettings(active: false);
    $product = renewalProduct($this->currency, $this->type);
    $otherProduct = renewalProduct($this->currency, $this->type, 'Different Pack');
    ownedRenewalSubscription($this->user, $product, today()->subMonth(), today()->addDay());
    $service = app(SubscriptionRenewalService::class);

    expect($service->stateFor($this->user, $product)['state'])->toBe(SubscriptionRenewalService::STATE_ACTIVE_LOCKED)
        ->and($service->stateFor($this->user, $otherProduct)['state'])->toBe(SubscriptionRenewalService::STATE_AVAILABLE);

    $this->user->userSubscriptions()->where('subscription_product_id', $product->id)->update([
        'end_date' => today()->subDay(),
    ]);

    expect($service->stateFor($this->user, $product)['state'])->toBe(SubscriptionRenewalService::STATE_AVAILABLE);
});

test('guest CTA remains available and server rechecks eligibility after login', function () {
    renewalSettings();
    $product = renewalProduct($this->currency, $this->type);
    ownedRenewalSubscription($this->user, $product, today()->subMonth(), today()->addDays(20));

    $this->get(route('front.subscriptions'))
        ->assertOk()
        ->assertSee('data-subscription-guest-cta', false);

    $this->actingAs($this->user)->get(route('front.subscriptions.checkout', $product))
        ->assertRedirect(route('front.subscriptions'));
});

test('renewal approval preserves paid days and schedules future entitlement from its duration snapshot', function () {
    Mail::fake();
    renewalSettings();
    $product = renewalProduct($this->currency, $this->type);
    $current = ownedRenewalSubscription($this->user, $product, Carbon::parse('2026-08-11'), Carbon::parse('2026-09-11'));

    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $product), renewalPaymentPayload($this->paymentAccount));
    $renewal = $this->user->userSubscriptions()->latest('id')->firstOrFail();
    $reviewer = User::factory()->create();
    $approved = app(SubscriptionPurchaseService::class)->approvePayment($renewal, $reviewer);
    app(SubscriptionPaymentMailService::class)->sendApproved($approved);

    expect($approved->start_date->toDateString())->toBe('2026-09-12')
        ->and($approved->end_date->toDateString())->toBe('2026-10-12')
        ->and($approved->duration_value_snapshot)->toBe(1)
        ->and($current->fresh()->start_date->toDateString())->toBe('2026-08-11')
        ->and($current->fresh()->end_date->toDateString())->toBe('2026-09-11')
        ->and($approved->hasFinalInvoice())->toBeTrue()
        ->and(app(SubscriptionEntitlementService::class)->activeSubscriptions($this->user)->modelKeys())->toContain($current->id)
        ->not->toContain($approved->id);
    Mail::assertSent(SubscriptionActivatedMailToUser::class, 1);
});

test('renewal approved after the old period expires starts on approval date', function () {
    Mail::fake();
    renewalSettings();
    $product = renewalProduct($this->currency, $this->type);
    ownedRenewalSubscription($this->user, $product, Carbon::parse('2026-08-01'), Carbon::parse('2026-09-09'));

    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $product), renewalPaymentPayload($this->paymentAccount));
    $renewal = $this->user->userSubscriptions()->latest('id')->firstOrFail();
    $renewal->update(['duration_value_snapshot' => 2, 'duration_unit_snapshot' => 'week']);
    $approved = app(SubscriptionPurchaseService::class)->approvePayment($renewal, User::factory()->create());

    expect($approved->start_date->toDateString())->toBe('2026-09-10')
        ->and($approved->end_date->toDateString())->toBe('2026-09-24');
});

test('owner can replace rejected evidence without changing commercial snapshots', function () {
    Mail::fake();
    $product = renewalProduct($this->currency, $this->type);
    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $product), renewalPaymentPayload($this->paymentAccount));
    $rejected = $this->user->userSubscriptions()->sole();
    $oldSlip = $rejected->payment_slip;
    $originalSnapshots = $rejected->only([
        'product_name', 'currency_id', 'price', 'discount', 'total', 'duration_value_snapshot', 'duration_unit_snapshot',
    ]);
    $rejected->update([
        'payment_status' => UserSubscription::PAYMENT_STATUS_REJECTED,
        'status' => UserSubscription::STATUS_REJECTED,
        'reviewed_at' => now(),
        'reviewed_by' => User::factory()->create()->id,
        'rejection_reason' => 'Reference could not be verified.',
    ]);
    $product->update(['name' => 'Changed Product', 'price' => 9999, 'duration_value' => 2, 'duration_unit' => 'year']);

    $this->actingAs($this->user)
        ->get(route('front.account.subscriptions'))
        ->assertSuccessful()
        ->assertSee('Reference could not be verified.')
        ->assertSee(route('front.account.subscriptions.resubmit', $rejected), false);
    $this->actingAs($this->user)
        ->get(route('front.account.subscriptions.resubmit', $rejected))
        ->assertSuccessful()
        ->assertSee('Reference could not be verified.')
        ->assertSee(route('front.account.subscriptions.resubmit.store', $rejected), false);
    $this->post(route('front.account.subscriptions.resubmit.store', $rejected), renewalPaymentPayload($this->paymentAccount, [
        'payment_slip' => UploadedFile::fake()->image('replacement.jpg'),
        'transaction_id' => 'CORRECTED-REF',
    ]))->assertRedirect(route('front.account.subscriptions'));

    $rejected->refresh();
    expect($rejected->only(array_keys($originalSnapshots)))->toBe($originalSnapshots)
        ->and($rejected->payment_status)->toBe(UserSubscription::PAYMENT_STATUS_PENDING)
        ->and($rejected->status)->toBe(UserSubscription::STATUS_PENDING)
        ->and($rejected->is_active)->toBeFalse()
        ->and($rejected->transaction_id)->toBe('CORRECTED-REF')
        ->and($rejected->reviewed_at)->toBeNull()
        ->and($rejected->reviewed_by)->toBeNull()
        ->and($rejected->rejection_reason)->toBeNull()
        ->and($rejected->invoice_no)->toBeNull()
        ->and($rejected->subscriptionTypes)->toHaveCount(1);
    Storage::disk('local')->assertMissing($oldSlip);
    Storage::disk('local')->assertExists($rejected->payment_slip);
    Mail::assertSent(SubscriptionPaymentReceivedMailToUser::class, 2);
    Mail::assertSent(SubscriptionPaymentReceivedMailToAdmin::class, 2);
});

test('resubmission is owner scoped and failed validation preserves rejected state and old slip', function () {
    Mail::fake();
    $product = renewalProduct($this->currency, $this->type);
    $oldSlip = UploadedFile::fake()->image('old-slip.jpg')->store('payment-slips', 'local');
    $rejected = $this->user->userSubscriptions()->create([
        'subscription_product_id' => $product->id,
        'product_for' => $product->product_for,
        'product_name' => $product->name,
        'currency_id' => $this->currency->id,
        'price' => 500,
        'discount' => 0,
        'total' => 500,
        'payment_method' => 'bank_transfer',
        'payment_account_id' => $this->paymentAccount->id,
        'payment_slip' => $oldSlip,
        'payment_status' => UserSubscription::PAYMENT_STATUS_REJECTED,
        'payment_submitted_at' => now(),
        'duration_value_snapshot' => 1,
        'duration_unit_snapshot' => 'month',
        'status' => UserSubscription::STATUS_REJECTED,
        'is_active' => false,
        'rejection_reason' => 'Rejected evidence.',
    ]);
    $otherUser = User::factory()->create(['is_active' => true]);
    $otherUser->assignRole('user');
    $this->paymentAccount->update(['is_active' => false]);

    $this->actingAs($otherUser)->get(route('front.account.subscriptions.resubmit', $rejected))->assertNotFound();
    $this->actingAs($this->user)
        ->post(route('front.account.subscriptions.resubmit.store', $rejected), renewalPaymentPayload($this->paymentAccount))
        ->assertSessionHasErrors('payment_account_id');

    $rejected->refresh();
    expect($rejected->status)->toBe(UserSubscription::STATUS_REJECTED)
        ->and($rejected->payment_status)->toBe(UserSubscription::PAYMENT_STATUS_REJECTED)
        ->and($rejected->payment_slip)->toBe($oldSlip)
        ->and($rejected->rejection_reason)->toBe('Rejected evidence.');
    Storage::disk('local')->assertExists($oldSlip);
    expect(Storage::disk('local')->allFiles('payment-slips'))->toHaveCount(1);
    Mail::assertNothingSent();
});
