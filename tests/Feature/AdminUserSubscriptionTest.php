<?php

use App\Mail\SubscriptionActivatedMailToUser;
use App\Mail\SubscriptionPaymentRejectedMailToUser;
use App\Models\ActivityLog;
use App\Models\Currency;
use App\Models\PaymentAccount;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\Authorization\PermissionSyncService;
use App\Services\SubscriptionEntitlementService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->customer = User::factory()->create(['name' => 'Subscription Customer', 'email' => 'customer@example.test', 'phone' => '03001234567']);
    $this->currency = Currency::query()->create(['name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true]);
    $this->entitlement = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    $this->paymentAccount = PaymentAccount::query()->create([
        'bank_name' => 'Meezan Bank',
        'account_title' => 'Digital Magazine',
        'account_no' => '00123456789',
        'iban' => 'PK00MEZN0000000000000000',
        'branch_code' => '0123',
        'is_active' => true,
    ]);
    Storage::fake('local');
});

function userSubscriptionAdmin(array $permissions): User
{
    $role = Role::findOrCreate('user-subscription-admin-'.str()->random(8), 'web');
    $role->syncPermissions(array_values(array_unique(['admin.access', 'dashboard.view', ...$permissions])));
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function purchasedSubscription(string $productFor, array $attributes = []): UserSubscription
{
    $product = SubscriptionProduct::query()->create([
        'product_for' => $productFor,
        'name' => $productFor === SubscriptionProduct::FOR_PLAN ? 'Monthly Plan' : 'Premium Membership',
        'currency_id' => test()->currency->id,
        'price' => 500,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $subscription = UserSubscription::query()->forceCreate(array_merge([
        'user_id' => test()->customer->id,
        'subscription_product_id' => $product->id,
        'product_for' => $productFor,
        'product_name' => $product->name,
        'currency_id' => test()->currency->id,
        'price' => 500,
        'discount' => 0,
        'total' => 500,
        'payment_method' => 'easypaisa',
        'start_date' => today(),
        'end_date' => today()->addMonth(),
        'status' => 'active',
        'is_active' => true,
    ], $attributes));
    $subscription->subscriptionTypes()->attach(test()->entitlement);

    return $subscription;
}

function pendingPaymentSubscription(array $attributes = []): UserSubscription
{
    $slipPath = UploadedFile::fake()->image('payment-slip.jpg')->storeAs('payment-slips', 'payment-slip.jpg', 'local');

    return purchasedSubscription(SubscriptionProduct::FOR_PLAN, array_merge([
        'payment_method' => 'bank_transfer',
        'payment_account_id' => test()->paymentAccount->id,
        'payment_slip' => $slipPath,
        'payment_status' => UserSubscription::PAYMENT_STATUS_PENDING,
        'payment_submitted_at' => now(),
        'duration_value_snapshot' => 1,
        'duration_unit_snapshot' => 'month',
        'start_date' => null,
        'end_date' => null,
        'status' => UserSubscription::STATUS_PENDING,
        'is_active' => false,
    ], $attributes));
}

test('authorized admin can list plan and membership purchases in the read only DataTable', function () {
    $olderPlan = purchasedSubscription(SubscriptionProduct::FOR_PLAN, ['created_at' => now()->subDay()]);
    $newerMembership = purchasedSubscription(SubscriptionProduct::FOR_MEMBERSHIP, ['product_name' => 'Stored Membership Snapshot', 'payment_method' => 'card', 'created_at' => now()]);
    $admin = userSubscriptionAdmin(['user-subscriptions.view']);

    $response = $this->actingAs($admin)->get(route('admin.user-subscriptions.index'))
        ->assertSuccessful()
        ->assertSee('data-admin-datatable', false)
        ->assertSee('data-admin-datatable-filters="user-subscriptions-table"', false)
        ->assertSee('Subscription Customer')->assertSee('customer@example.test')
        ->assertSee('Plan')->assertSee('Membership')->assertSee('Stored Membership Snapshot')
        ->assertSee('Rs 500')->assertSee('Easypaisa')->assertSee('Master / Visa Card')
        ->assertSee(route('admin.user-subscriptions.show', $olderPlan), false)
        ->assertSee(route('admin.user-subscriptions.show', $newerMembership), false);

    expect(strpos($response->getContent(), 'Stored Membership Snapshot'))->toBeLessThan(strpos($response->getContent(), 'Monthly Plan'));
});

test('admin without view permission cannot access list or detail and cannot see sidebar link', function () {
    $subscription = purchasedSubscription(SubscriptionProduct::FOR_PLAN);
    $admin = userSubscriptionAdmin([]);

    $this->actingAs($admin)->get(route('admin.user-subscriptions.index'))->assertForbidden();
    $this->get(route('admin.user-subscriptions.show', $subscription))->assertForbidden();
    $this->get(route('admin.dashboard'))->assertSuccessful()->assertDontSee(route('admin.user-subscriptions.index'), false);
});

test('authorized admin can inspect user snapshot billing entitlements and null references', function () {
    $subscription = purchasedSubscription(SubscriptionProduct::FOR_PLAN, ['order_no' => null, 'invoice_no' => null, 'transaction_id' => null]);

    $this->actingAs($this->superAdmin)->get(route('admin.user-subscriptions.show', $subscription))
        ->assertSuccessful()->assertSee('User Information')->assertSee('Subscription Customer')
        ->assertSee('customer@example.test')->assertSee('03001234567')->assertSee('Subscription Information')
        ->assertSee('Monthly Plan')->assertSee('Articles')->assertSee('Billing / Payment Information')
        ->assertSee('Pakistani Rupee (PKR)')->assertSee('Rs 500.00')->assertSee('Dates / Status')->assertSee('—');
});

test('detail safely handles missing optional relationships dates and entitlements', function () {
    $subscription = purchasedSubscription(SubscriptionProduct::FOR_MEMBERSHIP, ['currency_id' => null, 'payment_method' => null, 'start_date' => null, 'end_date' => null]);
    $subscription->subscriptionTypes()->detach();

    $this->actingAs($this->superAdmin)->get(route('admin.user-subscriptions.show', $subscription))
        ->assertSuccessful()->assertSee('Premium Membership')->assertSee('Membership')->assertSee('—');
});

test('future renewals and expired subscriptions use the existing effective status rules', function () {
    $future = purchasedSubscription(SubscriptionProduct::FOR_PLAN, ['start_date' => today()->addDay(), 'end_date' => today()->addMonth(), 'status' => 'active', 'is_active' => true]);
    $expired = purchasedSubscription(SubscriptionProduct::FOR_MEMBERSHIP, ['start_date' => today()->subMonth(), 'end_date' => today()->subDay(), 'status' => 'active', 'is_active' => true]);

    $this->actingAs($this->superAdmin)->get(route('admin.user-subscriptions.show', $future))->assertSuccessful()->assertSee('Scheduled');
    $this->get(route('admin.user-subscriptions.show', $expired))->assertSuccessful()->assertSee('Expired');
});

test('admin user subscription routes expose explicit listing review and slip actions', function () {
    $routes = collect(app('router')->getRoutes())->filter(fn ($route): bool => str_starts_with((string) $route->getName(), 'admin.user-subscriptions.'));

    expect($routes->pluck('action.as')->sort()->values()->all())->toBe([
        'admin.user-subscriptions.approve',
        'admin.user-subscriptions.index',
        'admin.user-subscriptions.payment-slip.download',
        'admin.user-subscriptions.payment-slip.view',
        'admin.user-subscriptions.reject',
        'admin.user-subscriptions.show',
    ]);
});

test('authorized admin can approve a pending payment exactly once and grant its stored entitlements', function () {
    Mail::fake();
    Carbon::setTestNow('2026-10-03 14:00:00');
    $subscription = pendingPaymentSubscription();
    $admin = userSubscriptionAdmin(['user-subscriptions.view', 'user-subscriptions.approve']);

    $this->actingAs($admin)
        ->post(route('admin.user-subscriptions.approve', $subscription))
        ->assertRedirect(route('admin.user-subscriptions.show', $subscription))
        ->assertSessionHas('status');

    $subscription->refresh();
    expect($subscription->payment_status)->toBe(UserSubscription::PAYMENT_STATUS_APPROVED)
        ->and($subscription->status)->toBe(UserSubscription::STATUS_ACTIVE)
        ->and($subscription->is_active)->toBeTrue()
        ->and($subscription->start_date->toDateString())->toBe('2026-10-03')
        ->and($subscription->end_date->toDateString())->toBe('2026-11-03')
        ->and($subscription->reviewed_by)->toBe($admin->id)
        ->and($subscription->reviewed_at)->not->toBeNull()
        ->and($subscription->invoice_no)->toBe(20260001)
        ->and($subscription->order_no)->toBe(20261001)
        ->and($subscription->subscriptionTypes)->toHaveCount(1)
        ->and($subscription->hasFinalInvoice())->toBeTrue()
        ->and(app(SubscriptionEntitlementService::class)->hasEntitlement($this->customer, $this->entitlement))->toBeTrue();

    $this->post(route('admin.user-subscriptions.approve', $subscription))
        ->assertSessionHasErrors('subscription');
    expect(ActivityLog::query()->where('module', 'user_subscriptions')->where('action', 'payment_approved')->count())->toBe(1);
    Mail::assertSent(SubscriptionActivatedMailToUser::class, $this->customer->email);
    Mail::assertSentCount(1);
    Carbon::setTestNow();
});

test('authorized admin can reject pending payment with a required reason and no entitlement', function () {
    Mail::fake();
    $subscription = pendingPaymentSubscription();
    $admin = userSubscriptionAdmin(['user-subscriptions.view', 'user-subscriptions.reject']);

    $this->actingAs($admin)
        ->post(route('admin.user-subscriptions.reject', $subscription), [])
        ->assertSessionHasErrors('rejection_reason');

    $this->post(route('admin.user-subscriptions.reject', $subscription), [
        'rejection_reason' => 'The submitted receipt could not be verified.',
    ])->assertRedirect(route('admin.user-subscriptions.show', $subscription));

    $subscription->refresh();
    expect($subscription->payment_status)->toBe(UserSubscription::PAYMENT_STATUS_REJECTED)
        ->and($subscription->status)->toBe(UserSubscription::STATUS_REJECTED)
        ->and($subscription->is_active)->toBeFalse()
        ->and($subscription->start_date)->toBeNull()
        ->and($subscription->end_date)->toBeNull()
        ->and($subscription->reviewed_by)->toBe($admin->id)
        ->and($subscription->rejection_reason)->toBe('The submitted receipt could not be verified.')
        ->and($subscription->invoice_no)->toBeNull()
        ->and($subscription->order_no)->toBeNull()
        ->and(app(SubscriptionEntitlementService::class)->hasEntitlement($this->customer, $this->entitlement))->toBeFalse();

    $this->post(route('admin.user-subscriptions.reject', $subscription), [
        'rejection_reason' => 'Try to reject again.',
    ])->assertSessionHasErrors('subscription');
    expect(ActivityLog::query()->where('module', 'user_subscriptions')->where('action', 'payment_rejected')->count())->toBe(1);
    Mail::assertSent(SubscriptionPaymentRejectedMailToUser::class, function (SubscriptionPaymentRejectedMailToUser $mail): bool {
        $html = $mail->render();

        return $mail->hasTo($this->customer->email)
            && str_contains($html, 'The submitted receipt could not be verified.')
            && str_contains($html, 'no invoice has been issued')
            && ! str_contains($html, 'payment-slips/');
    });
    Mail::assertSentCount(1);
});

test('mail delivery failure does not roll back an approved subscription', function () {
    $subscription = pendingPaymentSubscription();
    $admin = userSubscriptionAdmin(['user-subscriptions.view', 'user-subscriptions.approve']);
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP unavailable'));

    $this->actingAs($admin)
        ->post(route('admin.user-subscriptions.approve', $subscription))
        ->assertRedirect(route('admin.user-subscriptions.show', $subscription));

    $subscription->refresh();
    expect($subscription->payment_status)->toBe(UserSubscription::PAYMENT_STATUS_APPROVED)
        ->and($subscription->status)->toBe(UserSubscription::STATUS_ACTIVE)
        ->and($subscription->is_active)->toBeTrue()
        ->and($subscription->invoice_no)->not->toBeNull()
        ->and($subscription->order_no)->not->toBeNull();
});

test('review and private slip actions enforce their permissions', function () {
    $subscription = pendingPaymentSubscription();
    $admin = userSubscriptionAdmin([]);

    $this->actingAs($admin)->post(route('admin.user-subscriptions.approve', $subscription))->assertForbidden();
    $this->post(route('admin.user-subscriptions.reject', $subscription), ['rejection_reason' => 'Denied'])->assertForbidden();
    $this->get(route('admin.user-subscriptions.payment-slip.view', $subscription))->assertForbidden();
    $this->get(route('admin.user-subscriptions.payment-slip.download', $subscription))->assertForbidden();
});

test('authorized admin can stream the correct private slip but cannot access an unexpected private path', function () {
    $subscription = pendingPaymentSubscription();
    $admin = userSubscriptionAdmin(['user-subscriptions.view']);

    $this->actingAs($admin)
        ->get(route('admin.user-subscriptions.payment-slip.view', $subscription))
        ->assertSuccessful()
        ->assertHeader('content-type', 'image/jpeg')
        ->assertHeader('x-content-type-options', 'nosniff');
    $downloadResponse = $this->get(route('admin.user-subscriptions.payment-slip.download', $subscription))
        ->assertSuccessful();
    expect($downloadResponse->headers->get('content-disposition'))
        ->toContain('attachment;')
        ->toContain('payment-slip-'.$subscription->id.'.jpg');

    Storage::disk('local')->put('other/private.jpg', 'not a payment slip');
    $subscription->update(['payment_slip' => 'other/private.jpg']);
    $this->get(route('admin.user-subscriptions.payment-slip.view', $subscription))->assertNotFound();
});
