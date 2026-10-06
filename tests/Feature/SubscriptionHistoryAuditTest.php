<?php

use App\Models\ActivityLog;
use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\ActivityLogService;
use App\Services\FrontendAccountDataTableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true,
    ]);
    $this->product = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => 'History Plan',
        'currency_id' => $this->currency->id,
        'price' => 1000,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
});

function historyAuditSubscription(User $user, SubscriptionProduct $product, array $attributes = []): UserSubscription
{
    return $user->userSubscriptions()->create(array_merge([
        'subscription_product_id' => $product->id,
        'product_for' => $product->product_for,
        'product_name' => $product->name,
        'currency_id' => $product->currency_id,
        'price' => 1000,
        'discount' => 0,
        'total' => 1000,
        'payment_method' => 'bank_transfer',
        'payment_status' => UserSubscription::PAYMENT_STATUS_PENDING,
        'payment_submitted_at' => now(),
        'transaction_id' => 'PRIVATE-TXN-123',
        'status' => UserSubscription::STATUS_PENDING,
        'is_active' => false,
    ], $attributes));
}

test('subscription history exposes lifecycle details and only state valid actions', function () {
    $pending = historyAuditSubscription($this->user, $this->product);
    $rejected = historyAuditSubscription($this->user, $this->product, [
        'payment_status' => UserSubscription::PAYMENT_STATUS_REJECTED,
        'status' => UserSubscription::STATUS_REJECTED,
        'rejection_reason' => 'Reference could not be verified.',
        'reviewed_at' => now(),
    ]);
    $approved = historyAuditSubscription($this->user, $this->product, [
        'payment_status' => UserSubscription::PAYMENT_STATUS_APPROVED,
        'status' => UserSubscription::STATUS_ACTIVE,
        'is_active' => true,
        'start_date' => today(),
        'end_date' => today()->addMonth(),
        'invoice_no' => 'INV-100',
        'order_no' => 'ORD-100',
        'reviewed_at' => now(),
    ]);

    $result = app(FrontendAccountDataTableService::class)->subscriptions(
        $this->user,
        Request::create('/account/subscriptions', 'GET', ['length' => 10]),
    );
    $rows = collect($result['data'])->keyBy('status');

    expect($rows['pending'])
        ->payment_status->toBe('pending')
        ->invoice_no->toBeNull()
        ->invoice_view_url->toBeNull()
        ->resubmit_url->toBeNull()
        ->transaction_id->toBe('PRIVATE-TXN-123')
        ->and($rows['rejected'])
        ->payment_status->toBe('rejected')
        ->rejection_reason->toBe('Reference could not be verified.')
        ->resubmit_url->toBe(route('front.account.subscriptions.resubmit', $rejected))
        ->invoice_no->toBeNull()
        ->and($rows['active'])
        ->payment_status->toBe('approved')
        ->invoice_no->toBe('INV-100')
        ->order_no->toBe('ORD-100')
        ->invoice_view_url->toBe(route('front.account.subscriptions.invoice.view', $approved));

    expect($pending->fresh()->status)->toBe(UserSubscription::STATUS_PENDING);
});

test('frontend subscription audit events retain safe metadata only', function () {
    $subscription = historyAuditSubscription($this->user, $this->product, [
        'payment_slip' => 'payment-slips/private-slip.jpg',
    ]);
    $this->actingAs($this->user);

    app(ActivityLogService::class)->logUserLifecycle(
        $this->user,
        'payment_submitted',
        $subscription,
        'Subscription payment submitted for review.',
        null,
        [
            'user_id' => $this->user->id,
            'subscription_product_id' => $this->product->id,
            'payment_status' => UserSubscription::PAYMENT_STATUS_PENDING,
        ],
    );

    $log = ActivityLog::query()->sole();
    $serializedLog = json_encode($log->only(['description', 'old_values', 'new_values']));

    expect($log->module)->toBe('user_subscriptions')
        ->and($log->action)->toBe('payment_submitted')
        ->and($log->user_id)->toBe($this->user->id)
        ->and($log->subject_id)->toBe($subscription->id)
        ->and($serializedLog)->not->toContain('payment-slips/private-slip.jpg')
        ->and($serializedLog)->not->toContain('PRIVATE-TXN-123');

    $otherUser = User::factory()->create();
    app(ActivityLogService::class)->logUserLifecycle(
        $otherUser,
        'payment_resubmitted',
        $subscription,
        'Ignored mismatched actor.',
    );

    expect(ActivityLog::query()->count())->toBe(1);
});

test('user subscription permission registry retains the review action contract', function () {
    expect(config('admin_modules.modules.user-subscriptions.actions'))->toBe([
        'view' => 'View',
        'approve' => 'Approve Payment',
        'reject' => 'Reject Payment',
    ]);
});
