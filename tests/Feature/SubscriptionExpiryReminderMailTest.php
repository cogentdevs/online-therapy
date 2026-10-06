<?php

use App\Mail\SubscriptionExpiryReminderMailToUser;
use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Mail;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee',
        'code' => 'PKR',
        'symbol' => 'Rs',
        'isActive' => true,
    ]);
    $this->user = User::factory()->create(['name' => 'Ali Khan']);
});

function expiryReminderSubscription(
    User $user,
    Currency $currency,
    string $productFor,
    string $productName,
    array $moduleNames,
): UserSubscription {
    $product = SubscriptionProduct::query()->create([
        'product_for' => $productFor,
        'name' => 'Current Product Name',
        'currency_id' => $currency->id,
        'price' => 200,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $subscription = UserSubscription::query()->create([
        'user_id' => $user->id,
        'subscription_product_id' => $product->id,
        'product_for' => $productFor,
        'product_name' => $productName,
        'currency_id' => $currency->id,
        'price' => 200,
        'discount' => 0,
        'total' => 200,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-18',
        'status' => 'active',
        'is_active' => true,
    ]);

    foreach ($moduleNames as $moduleName) {
        $type = SubscriptionType::query()->create([
            'name' => $moduleName,
            'slug' => strtolower($moduleName).'-'.fake()->unique()->numerify('###'),
            'isActive' => true,
        ]);
        $subscription->subscriptionTypes()->attach($type);
    }

    return $subscription->load(['user', 'subscriptionTypes']);
}

test('plan reminder renders snapshot identity module days expiry and renewal action', function () {
    $subscription = expiryReminderSubscription(
        $this->user,
        $this->currency,
        SubscriptionProduct::FOR_PLAN,
        'Monthly Pack',
        ['Articles'],
    );
    $mail = new SubscriptionExpiryReminderMailToUser($subscription, 7);

    $mail->assertHasSubject('آپ کی سبسکرپشن 7 دن میں ختم ہونے والی ہے')
        ->assertSeeInHtml('Monthly Pack')
        ->assertSeeInHtml('منصوبہ')
        ->assertSeeInHtml('مضامین')
        ->assertSeeInHtml('7 دن')
        ->assertSeeInHtml('2026-09-18')
        ->assertSeeInHtml('سبسکرپشن کی تجدید کریں')
        ->assertSeeInHtml(route('front.subscriptions'));
});

test('one membership reminder renders all purchased module snapshots', function () {
    $subscription = expiryReminderSubscription(
        $this->user,
        $this->currency,
        SubscriptionProduct::FOR_MEMBERSHIP,
        'Gold Membership',
        ['Articles', 'Magazine'],
    );

    (new SubscriptionExpiryReminderMailToUser($subscription, 5))
        ->assertSeeInHtml('Gold Membership')
        ->assertSeeInHtml('رکنیت')
        ->assertSeeInHtml('مضامین')
        ->assertSeeInHtml('ہفتہ وار میگزین')
        ->assertSeeInHtml('2026-09-18');
});

test('reminder modules remain authoritative after current product configuration changes', function () {
    $subscription = expiryReminderSubscription(
        $this->user,
        $this->currency,
        SubscriptionProduct::FOR_PLAN,
        'Purchased Article Plan',
        ['Articles'],
    );
    $currentMagazineType = SubscriptionType::query()->create([
        'name' => 'Magazine',
        'slug' => 'current-magazine',
        'isActive' => true,
    ]);
    $subscription->subscriptionProduct->subscriptionTypes()->sync([$currentMagazineType->id]);

    (new SubscriptionExpiryReminderMailToUser($subscription, 1))
        ->assertSeeInHtml('مضامین')
        ->assertDontSeeInHtml('ہفتہ وار میگزین');
});

test('reminder exposes Brevo mailer and uses its isolated sender without changing global sender', function () {
    config([
        'mail.from' => ['address' => 'gmail@example.com', 'name' => 'Gmail Sender'],
        'mail.brevo_from' => ['address' => 'notifications@example.com', 'name' => 'Digital Magazine'],
    ]);
    $subscription = expiryReminderSubscription(
        $this->user,
        $this->currency,
        SubscriptionProduct::FOR_PLAN,
        'Monthly Pack',
        ['Articles'],
    );
    $mail = new SubscriptionExpiryReminderMailToUser($subscription, 7);
    $sender = $mail->envelope()->from;

    expect(SubscriptionExpiryReminderMailToUser::MAILER)->toBe('brevo')
        ->and($sender)->toBeInstanceOf(Address::class)
        ->and($sender->address)->toBe('notifications@example.com')
        ->and($sender->name)->toBe('Digital Magazine')
        ->and(config('mail.from.address'))->toBe('gmail@example.com');

    Mail::fake();
    Mail::mailer(SubscriptionExpiryReminderMailToUser::MAILER)->to($this->user)->send($mail);
    Mail::assertSent(SubscriptionExpiryReminderMailToUser::class, 1);
    Mail::assertNothingQueued();
});

test('missing optional display values use safe neutral fallbacks', function () {
    $this->user->update(['name' => '']);
    $subscription = expiryReminderSubscription(
        $this->user,
        $this->currency,
        SubscriptionProduct::FOR_PLAN,
        '',
        [],
    );
    $subscription->update(['end_date' => null]);
    $subscription->refresh()->load(['user', 'subscriptionTypes']);

    (new SubscriptionExpiryReminderMailToUser($subscription, 3))
        ->assertSeeInHtml('محترم صارف')
        ->assertSeeInHtml('سبسکرپشن')
        ->assertSeeInHtml('کوئی ماڈیول درج نہیں')
        ->assertSeeInHtml('درج نہیں');
});
