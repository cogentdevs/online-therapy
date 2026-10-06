<?php

use App\Jobs\SendSubscriptionExpiryReminderJob;
use App\Mail\SubscriptionExpiryReminderMailToUser;
use App\Models\Currency;
use App\Models\SubscriptionExpiryReminder;
use App\Models\SubscriptionNotificationSetting;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-11 10:00:00');
    $this->currency = Currency::query()->create(['name' => 'PKR', 'symbol' => 'PKR']);
    $this->user = User::factory()->create();
});

afterEach(fn () => Carbon::setTestNow());

function reminderSettings(array $overrides = []): SubscriptionNotificationSetting
{
    return SubscriptionNotificationSetting::query()->create(array_merge([
        'first_reminder_days' => 7,
        'second_reminder_days' => null,
        'third_reminder_days' => null,
        'isActive' => true,
    ], $overrides));
}

function reminderSubscription(User $user, Currency $currency, array $overrides = [], array $modules = ['Articles']): UserSubscription
{
    $product = SubscriptionProduct::query()->create([
        'name' => $overrides['product_name'] ?? 'Reminder Pack',
        'product_for' => $overrides['product_for'] ?? SubscriptionProduct::FOR_PLAN,
        'price' => 200,
        'currency_id' => $currency->id,
        'duration' => 1,
        'duration_type' => 'month',
        'isActive' => true,
    ]);

    $subscription = UserSubscription::query()->create(array_merge([
        'user_id' => $user->id,
        'subscription_product_id' => $product->id,
        'product_for' => $product->product_for,
        'product_name' => $product->name,
        'currency_id' => $currency->id,
        'price' => 200,
        'discount' => 0,
        'total' => 200,
        'start_date' => today(),
        'end_date' => today()->addDays(7),
        'status' => 'active',
        'is_active' => true,
    ], $overrides));

    foreach ($modules as $moduleName) {
        $type = SubscriptionType::query()->firstOrCreate(
            ['name' => $moduleName],
            ['slug' => str($moduleName)->slug(), 'isActive' => true],
        );
        $subscription->subscriptionTypes()->attach($type);
    }

    return $subscription;
}

test('disabled or missing settings produce no reminders', function () {
    Mail::fake();
    Queue::fake();

    reminderSubscription($this->user, $this->currency);
    $this->artisan('subscriptions:send-expiry-reminders')
        ->expectsOutput('Subscription expiry reminders are disabled.')
        ->assertSuccessful();

    reminderSettings(['isActive' => false]);
    $this->artisan('subscriptions:send-expiry-reminders')
        ->expectsOutput('Subscription expiry reminders are disabled.')
        ->assertSuccessful();

    Mail::assertNothingSent();
    Queue::assertNothingPushed();
    expect(SubscriptionExpiryReminder::query()->count())->toBe(0);
});

test('only configured due thresholds send and successful tracking is recorded', function () {
    Mail::fake();
    Queue::fake();
    reminderSettings(['first_reminder_days' => 7, 'second_reminder_days' => null, 'third_reminder_days' => 1]);
    $sevenDay = reminderSubscription($this->user, $this->currency);
    $fiveDay = reminderSubscription($this->user, $this->currency, ['end_date' => today()->addDays(5)]);
    $oneDay = reminderSubscription($this->user, $this->currency, ['end_date' => today()->addDay()]);

    $this->artisan('subscriptions:send-expiry-reminders')
        ->expectsOutput('Configured reminder thresholds: 7, 1')
        ->expectsOutput('Eligible reminders: 2')
        ->expectsOutput('Queued: 2')
        ->expectsOutput('Skipped already sent/queued: 0')
        ->expectsOutput('Failed to dispatch: 0')
        ->expectsOutput('Database chunk size: 50')
        ->assertSuccessful();

    Queue::assertPushed(SendSubscriptionExpiryReminderJob::class, 2);
    Mail::assertNothingSent();
    expect(SubscriptionExpiryReminder::query()->count())->toBe(2)
        ->and($sevenDay->expiryReminders()->sole()->reminder_days)->toBe(7)
        ->and($sevenDay->expiryReminders()->sole()->status)->toBe(SubscriptionExpiryReminder::STATUS_PENDING)
        ->and($sevenDay->expiryReminders()->sole()->sent_at)->toBeNull()
        ->and($oneDay->expiryReminders()->sole()->reminder_days)->toBe(1)
        ->and($fiveDay->expiryReminders()->exists())->toBeFalse();
});

test('sent reminders are skipped on repeated command runs', function () {
    Queue::fake();
    reminderSettings();
    $subscription = reminderSubscription($this->user, $this->currency);
    SubscriptionExpiryReminder::query()->create([
        'user_subscription_id' => $subscription->id,
        'reminder_days' => 7,
        'status' => SubscriptionExpiryReminder::STATUS_SENT,
        'sent_at' => now(),
    ]);

    $this->artisan('subscriptions:send-expiry-reminders')
        ->expectsOutput('Eligible reminders: 1')
        ->expectsOutput('Queued: 0')
        ->expectsOutput('Skipped already sent/queued: 1')
        ->assertSuccessful();

    Queue::assertNothingPushed();
    expect($subscription->expiryReminders()->count())->toBe(1);
});

test('membership creates one reminder rather than one reminder per module', function () {
    Queue::fake();
    reminderSettings();
    $membership = reminderSubscription(
        $this->user,
        $this->currency,
        ['product_for' => SubscriptionProduct::FOR_MEMBERSHIP],
        ['Articles', 'Magazine'],
    );

    $this->artisan('subscriptions:send-expiry-reminders')->assertSuccessful();

    Queue::assertPushed(SendSubscriptionExpiryReminderJob::class, 1);
    expect($membership->expiryReminders()->count())->toBe(1)
        ->and($membership->subscriptionTypes()->count())->toBe(2);
});

test('expired inactive cancelled future null-date and unmatched subscriptions are excluded', function () {
    Queue::fake();
    reminderSettings();

    reminderSubscription($this->user, $this->currency, ['end_date' => today()->subDay()]);
    reminderSubscription($this->user, $this->currency, ['is_active' => false]);
    reminderSubscription($this->user, $this->currency, ['status' => 'cancelled']);
    reminderSubscription($this->user, $this->currency, ['start_date' => today()->addDay()]);
    reminderSubscription($this->user, $this->currency, ['end_date' => null]);
    reminderSubscription($this->user, $this->currency, ['end_date' => today()->addDays(5)]);

    $this->artisan('subscriptions:send-expiry-reminders')
        ->expectsOutput('Eligible reminders: 0')
        ->assertSuccessful();

    Queue::assertNothingPushed();
    expect(SubscriptionExpiryReminder::query()->count())->toBe(0);
});

test('different due subscriptions for one user are processed independently without changing snapshots', function () {
    Queue::fake();
    reminderSettings();
    $plan = reminderSubscription($this->user, $this->currency, [], ['Articles']);
    $membership = reminderSubscription($this->user, $this->currency, ['product_for' => SubscriptionProduct::FOR_MEMBERSHIP], ['Magazine', 'Audio']);

    $this->artisan('subscriptions:send-expiry-reminders')->assertSuccessful();

    Queue::assertPushed(SendSubscriptionExpiryReminderJob::class, 2);
    expect(SubscriptionExpiryReminder::query()->count())->toBe(2)
        ->and($plan->subscriptionTypes()->count())->toBe(1)
        ->and($membership->subscriptionTypes()->count())->toBe(2);
});

test('failed tracking is requeued with the same row while still due', function () {
    Queue::fake();
    reminderSettings();
    $subscription = reminderSubscription($this->user, $this->currency);
    $tracking = SubscriptionExpiryReminder::query()->create([
        'user_subscription_id' => $subscription->id,
        'reminder_days' => 7,
        'status' => SubscriptionExpiryReminder::STATUS_FAILED,
        'sent_at' => null,
    ]);

    $this->artisan('subscriptions:send-expiry-reminders')
        ->expectsOutput('Queued: 1')
        ->assertSuccessful();

    Queue::assertPushed(SendSubscriptionExpiryReminderJob::class, 1);
    expect($subscription->expiryReminders()->count())->toBe(1)
        ->and($tracking->fresh()->status)->toBe(SubscriptionExpiryReminder::STATUS_PENDING)
        ->and($tracking->sent_at)->toBeNull();
});

test('queued job sends through brevo and marks its tracking row sent', function () {
    $subscription = reminderSubscription($this->user, $this->currency);
    $tracking = SubscriptionExpiryReminder::query()->create([
        'user_subscription_id' => $subscription->id,
        'reminder_days' => 7,
        'status' => SubscriptionExpiryReminder::STATUS_PENDING,
    ]);
    $mailer = Mockery::mock(Mailer::class);
    $pendingMail = Mockery::mock(PendingMail::class);

    Mail::shouldReceive('mailer')->once()->with(SubscriptionExpiryReminderMailToUser::MAILER)->andReturn($mailer);
    $mailer->shouldReceive('to')->once()->with($this->user->email)->andReturn($pendingMail);
    $pendingMail->shouldReceive('send')->once()->with(Mockery::type(SubscriptionExpiryReminderMailToUser::class));

    (new SendSubscriptionExpiryReminderJob($tracking->id))->handle();

    expect($tracking->fresh()->status)->toBe(SubscriptionExpiryReminder::STATUS_SENT)
        ->and($tracking->fresh()->sent_at)->not->toBeNull();
});

test('temporary job failure remains retryable and final failure updates tracking', function () {
    $subscription = reminderSubscription($this->user, $this->currency);
    $tracking = SubscriptionExpiryReminder::query()->create([
        'user_subscription_id' => $subscription->id,
        'reminder_days' => 7,
        'status' => SubscriptionExpiryReminder::STATUS_PENDING,
    ]);
    $mailer = Mockery::mock(Mailer::class);
    $pendingMail = Mockery::mock(PendingMail::class);
    $exception = new RuntimeException('Simulated transport failure');

    Mail::shouldReceive('mailer')->once()->andReturn($mailer);
    $mailer->shouldReceive('to')->once()->andReturn($pendingMail);
    $pendingMail->shouldReceive('send')->once()->andThrow($exception);

    expect(fn () => (new SendSubscriptionExpiryReminderJob($tracking->id))->handle())
        ->toThrow(RuntimeException::class);
    expect($tracking->fresh()->status)->toBe(SubscriptionExpiryReminder::STATUS_PENDING);

    (new SendSubscriptionExpiryReminderJob($tracking->id))->failed($exception);

    expect($tracking->fresh()->status)->toBe(SubscriptionExpiryReminder::STATUS_FAILED)
        ->and($tracking->fresh()->sent_at)->toBeNull();
});

test('database chunking queues every eligible subscription', function (int $subscriptionCount) {
    Queue::fake();
    reminderSettings();
    $product = SubscriptionProduct::query()->create([
        'name' => 'Chunk Pack',
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'price' => 200,
        'currency_id' => $this->currency->id,
        'duration' => 1,
        'duration_type' => 'month',
        'isActive' => true,
    ]);

    foreach (range(1, $subscriptionCount) as $index) {
        UserSubscription::query()->create([
            'user_id' => $this->user->id,
            'subscription_product_id' => $product->id,
            'product_for' => SubscriptionProduct::FOR_PLAN,
            'product_name' => "Chunk Pack {$index}",
            'currency_id' => $this->currency->id,
            'price' => 200,
            'discount' => 0,
            'total' => 200,
            'start_date' => today(),
            'end_date' => today()->addDays(7),
            'status' => 'active',
            'is_active' => true,
        ]);
    }

    $this->artisan('subscriptions:send-expiry-reminders')
        ->expectsOutput("Eligible reminders: {$subscriptionCount}")
        ->expectsOutput("Queued: {$subscriptionCount}")
        ->expectsOutput('Database chunk size: 50')
        ->assertSuccessful();

    Queue::assertPushed(SendSubscriptionExpiryReminderJob::class, $subscriptionCount);
    expect(SubscriptionExpiryReminder::query()->count())->toBe($subscriptionCount);
})->with([23, 50, 127]);

test('an existing pending reminder is not dispatched again', function () {
    Queue::fake();
    reminderSettings();
    $subscription = reminderSubscription($this->user, $this->currency);
    SubscriptionExpiryReminder::query()->create([
        'user_subscription_id' => $subscription->id,
        'reminder_days' => 7,
        'status' => SubscriptionExpiryReminder::STATUS_PENDING,
    ]);

    $this->artisan('subscriptions:send-expiry-reminders')
        ->expectsOutput('Queued: 0')
        ->expectsOutput('Skipped already sent/queued: 1')
        ->assertSuccessful();

    Queue::assertNothingPushed();
    expect($subscription->expiryReminders()->count())->toBe(1);
});
