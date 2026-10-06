<?php

use App\Jobs\SendSubscriptionExpiredNotificationJob;
use App\Mail\SubscriptionExpiredMailToUser;
use App\Models\Currency;
use App\Models\SubscriptionExpiryNotification;
use App\Models\SubscriptionExpiryReminder;
use App\Models\SubscriptionNotificationSetting;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-12 10:00:00');
    Queue::fake();
    $this->user = User::factory()->create(['name' => 'Ali Khan']);
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true,
    ]);
});

afterEach(fn () => Carbon::setTestNow());

function expiredNotificationSubscription(
    User $user,
    Currency $currency,
    string $productFor = SubscriptionProduct::FOR_PLAN,
    array $modules = ['Articles'],
    array $overrides = [],
): UserSubscription {
    $product = SubscriptionProduct::query()->create([
        'product_for' => $productFor,
        'name' => 'Current Product Name',
        'currency_id' => $currency->id,
        'price' => 500,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $subscription = $user->userSubscriptions()->create(array_merge([
        'subscription_product_id' => $product->id,
        'product_for' => $productFor,
        'product_name' => $productFor === SubscriptionProduct::FOR_PLAN ? 'Purchased Plan' : 'Purchased Membership',
        'currency_id' => $currency->id,
        'price' => 500,
        'discount' => 0,
        'total' => 500,
        'payment_method' => 'card',
        'start_date' => '2026-08-01',
        'end_date' => '2026-09-11',
        'status' => 'active',
        'is_active' => true,
    ], $overrides));

    foreach ($modules as $moduleName) {
        $type = SubscriptionType::query()->create([
            'name' => $moduleName,
            'slug' => str($moduleName)->slug().'-'.fake()->unique()->numerify('###'),
            'isActive' => true,
        ]);
        $subscription->subscriptionTypes()->attach($type);
    }

    return $subscription;
}

test('expiry transition reserves one pending notification and queues after commit', function () {
    $subscription = expiredNotificationSubscription($this->user, $this->currency);

    $this->artisan('subscriptions:expire')
        ->expectsOutput('Expired subscriptions deactivated: 1')
        ->expectsOutput('Expiry notifications queued: 1')
        ->assertSuccessful();

    $notification = $subscription->expiryNotification()->sole();
    expect($subscription->fresh()->status)->toBe('expired')
        ->and($subscription->fresh()->is_active)->toBeFalse()
        ->and($notification->status)->toBe(SubscriptionExpiryNotification::STATUS_PENDING)
        ->and($notification->sent_at)->toBeNull();
    Queue::assertPushed(SendSubscriptionExpiredNotificationJob::class, function ($job) use ($notification, $subscription): bool {
        return $job->subscriptionExpiryNotificationId === $notification->id
            && $job->queue === SendSubscriptionExpiredNotificationJob::QUEUE
            && $subscription->fresh()->status === 'expired';
    });
});

test('inclusive boundary and non-qualifying subscriptions remain unchanged and unqueued', function () {
    $today = expiredNotificationSubscription($this->user, $this->currency, overrides: ['end_date' => today()]);
    $nullEnd = expiredNotificationSubscription($this->user, $this->currency, overrides: ['end_date' => null]);
    $inactive = expiredNotificationSubscription($this->user, $this->currency, overrides: ['is_active' => false]);

    $this->artisan('subscriptions:expire')->expectsOutput('Expired subscriptions deactivated: 0')->assertSuccessful();

    expect($today->fresh()->status)->toBe('active')
        ->and($nullEnd->fresh()->status)->toBe('active')
        ->and($inactive->fresh()->is_active)->toBeFalse()
        ->and(SubscriptionExpiryNotification::query()->count())->toBe(0);
    Queue::assertNothingPushed();
});

test('notification uniqueness and command idempotency prevent duplicate delivery jobs', function () {
    $subscription = expiredNotificationSubscription($this->user, $this->currency);
    $this->artisan('subscriptions:expire')->assertSuccessful();
    $this->artisan('subscriptions:expire')->assertSuccessful();

    expect($subscription->expiryNotification()->count())->toBe(1);
    Queue::assertPushed(SendSubscriptionExpiredNotificationJob::class, 1);

    expect(fn () => SubscriptionExpiryNotification::query()->create([
        'user_subscription_id' => $subscription->id,
    ]))->toThrow(QueryException::class);
});

test('successful job sends one Brevo mail and marks notification sent while sent rerun no-ops', function () {
    $subscription = expiredNotificationSubscription($this->user, $this->currency, overrides: [
        'status' => 'expired', 'is_active' => false,
    ])->load(['user', 'subscriptionTypes']);
    $notification = $subscription->expiryNotification()->create();
    $mailer = Mockery::mock(Mailer::class);
    $pendingMail = Mockery::mock(PendingMail::class);

    Mail::shouldReceive('mailer')->once()->with(SubscriptionExpiredMailToUser::MAILER)->andReturn($mailer);
    $mailer->shouldReceive('to')->once()->with($this->user->email)->andReturn($pendingMail);
    $pendingMail->shouldReceive('send')->once()->with(Mockery::type(SubscriptionExpiredMailToUser::class));

    $job = new SendSubscriptionExpiredNotificationJob($notification->id);
    $job->handle();
    $job->handle();

    expect($notification->fresh()->status)->toBe(SubscriptionExpiryNotification::STATUS_SENT)
        ->and($notification->fresh()->sent_at)->not->toBeNull()
        ->and($subscription->fresh()->status)->toBe('expired')
        ->and($subscription->fresh()->is_active)->toBeFalse();
});

test('temporary exception remains retryable and final failure marks only notification failed', function () {
    $subscription = expiredNotificationSubscription($this->user, $this->currency, overrides: [
        'status' => 'expired', 'is_active' => false,
    ]);
    $notification = $subscription->expiryNotification()->create();
    $exception = new RuntimeException('Simulated Brevo failure');
    $mailer = Mockery::mock(Mailer::class);
    $pendingMail = Mockery::mock(PendingMail::class);

    Mail::shouldReceive('mailer')->once()->andReturn($mailer);
    $mailer->shouldReceive('to')->once()->andReturn($pendingMail);
    $pendingMail->shouldReceive('send')->once()->andThrow($exception);

    expect(fn () => (new SendSubscriptionExpiredNotificationJob($notification->id))->handle())
        ->toThrow(RuntimeException::class);
    expect($notification->fresh()->status)->toBe(SubscriptionExpiryNotification::STATUS_PENDING);

    (new SendSubscriptionExpiredNotificationJob($notification->id))->failed($exception);

    expect($notification->fresh()->status)->toBe(SubscriptionExpiryNotification::STATUS_FAILED)
        ->and($notification->fresh()->sent_at)->toBeNull()
        ->and($subscription->fresh()->status)->toBe('expired')
        ->and($subscription->fresh()->is_active)->toBeFalse();
});

test('plan and membership expiry mails use purchased snapshot modules and shared layout', function () {
    $plan = expiredNotificationSubscription($this->user, $this->currency)->load(['user', 'subscriptionTypes']);
    $membership = expiredNotificationSubscription(
        $this->user,
        $this->currency,
        SubscriptionProduct::FOR_MEMBERSHIP,
        ['Articles', 'Magazine', 'Audio'],
    )->load(['user', 'subscriptionTypes']);

    $planMail = new SubscriptionExpiredMailToUser($plan);
    $membershipMail = new SubscriptionExpiredMailToUser($membership);

    $planMail->assertSeeInHtml('Purchased Plan')
        ->assertSeeInHtml('منصوبہ')
        ->assertSeeInHtml('مضامین')
        ->assertSeeInHtml('دوبارہ سبسکرائب کریں');
    $membershipMail->assertSeeInHtml('Purchased Membership')
        ->assertSeeInHtml('رکنیت')
        ->assertSeeInHtml('مضامین')
        ->assertSeeInHtml('ہفتہ وار میگزین')
        ->assertSeeInHtml('آڈیو')
        ->assertSeeInHtml(route('front.subscriptions'));

    expect($membership->expiryNotification()->count())->toBe(0);
});

test('current product changes and disabled reminder settings do not affect actual expiry notification', function () {
    SubscriptionNotificationSetting::query()->create(['isActive' => false]);
    $subscription = expiredNotificationSubscription($this->user, $this->currency, modules: ['Articles']);
    $currentMagazine = SubscriptionType::query()->create([
        'name' => 'Magazine', 'slug' => 'current-magazine-expiry', 'isActive' => true,
    ]);
    $subscription->subscriptionProduct->subscriptionTypes()->sync([$currentMagazine->id]);

    $this->artisan('subscriptions:expire')->assertSuccessful();

    $mail = new SubscriptionExpiredMailToUser($subscription->fresh()->load(['user', 'subscriptionTypes']));
    $mail->assertSeeInHtml('مضامین')->assertDontSeeInHtml('ہفتہ وار میگزین');
    expect(SubscriptionExpiryNotification::query()->count())->toBe(1)
        ->and(SubscriptionExpiryReminder::query()->count())->toBe(0);
    Queue::assertPushed(SendSubscriptionExpiredNotificationJob::class, 1);
});
