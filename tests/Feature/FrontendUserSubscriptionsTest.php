<?php

use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-10 12:00:00');
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    $this->otherUser = User::factory()->create(['is_active' => true]);
    $this->otherUser->assignRole('user');
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true,
    ]);
    $this->product = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_MEMBERSHIP,
        'name' => 'Current Membership',
        'currency_id' => $this->currency->id,
        'price' => 2000,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $this->magazine = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
    $this->articles = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
});

afterEach(fn () => Carbon::setTestNow());

function makeFrontendAccountSubscription(User $user, SubscriptionProduct $product, Currency $currency, array $overrides = []): UserSubscription
{
    return UserSubscription::query()->create(array_merge([
        'user_id' => $user->id,
        'subscription_product_id' => $product->id,
        'product_for' => 'membership',
        'product_name' => 'Old Membership Name',
        'currency_id' => $currency->id,
        'price' => 1000,
        'discount' => 0,
        'total' => 1000,
        'payment_method' => 'jazzcash',
        'start_date' => '2026-09-01',
        'end_date' => '2026-10-01',
        'status' => 'active',
        'is_active' => true,
    ], $overrides));
}

test('guest cannot access profile or subscription history', function () {
    $this->get(route('front.account.profile'))->assertRedirect(route('front.login'));
    $this->get(route('front.account.subscriptions'))->assertRedirect(route('front.login'));
});

test('dashboard shows deduplicated module access and compact typed package summaries', function () {
    $audio = SubscriptionType::query()->create(['name' => 'Audio', 'slug' => 'audio', 'isActive' => true]);
    $first = makeFrontendAccountSubscription($this->user, $this->product, $this->currency, [
        'product_for' => 'plan', 'product_name' => 'Article Monthly Pack',
    ]);
    $second = makeFrontendAccountSubscription($this->user, $this->product, $this->currency, [
        'product_for' => 'membership', 'product_name' => 'Quarterly Membership',
    ]);
    $third = makeFrontendAccountSubscription($this->user, $this->product, $this->currency, ['product_name' => 'Second Membership']);
    $fourth = makeFrontendAccountSubscription($this->user, $this->product, $this->currency, ['product_name' => 'Fourth Active Purchase']);
    $expired = makeFrontendAccountSubscription($this->user, $this->product, $this->currency, ['product_name' => 'Expired One', 'end_date' => '2026-09-09']);
    makeFrontendAccountSubscription($this->user, $this->product, $this->currency, ['product_name' => 'Future One', 'start_date' => '2026-09-11']);
    makeFrontendAccountSubscription($this->user, $this->product, $this->currency, ['product_name' => 'Inactive One', 'is_active' => false]);
    $first->subscriptionTypes()->attach($this->articles);
    $second->subscriptionTypes()->attach([$this->magazine->id, $this->articles->id]);
    $third->subscriptionTypes()->attach($audio);
    $fourth->subscriptionTypes()->attach($this->magazine);
    $expired->subscriptionTypes()->attach($this->magazine);

    $response = $this->actingAs($this->user)->get(route('front.account'))
        ->assertOk()
        ->assertSee('فعال سبسکرپشنز')
        ->assertSee('<strong>4</strong>', false)
        ->assertSee('فعال رسائی')
        ->assertSee('ہفتہ وار میگزین')
        ->assertSee('مضامین')
        ->assertSee('آڈیو')
        ->assertSee('front-account-active-more', false)
        ->assertDontSee('Fourth Active Purchase')
        ->assertDontSee('Expired One')
        ->assertDontSee('Future One')
        ->assertDontSee('Inactive One')
        ->assertSee('href="'.route('front.account.subscriptions').'"', false);

    expect(substr_count($response->getContent(), 'data-active-access-module="'.$this->articles->id.'"'))->toBe(1)
        ->and(substr_count($response->getContent(), 'data-active-access-module="'.$this->magazine->id.'"'))->toBe(1)
        ->and(substr_count($response->getContent(), 'data-active-package='))->toBe(0);
});

test('dashboard shows subscription empty state and separate browse action', function () {
    $this->actingAs($this->user)->get(route('front.account'))
        ->assertOk()
        ->assertSee('آپ کی کوئی فعال سبسکرپشن موجود نہیں ہے۔')
        ->assertSee('href="'.route('front.subscriptions').'"', false);
});

test('profile has no active subscription summary section', function () {
    makeFrontendAccountSubscription($this->user, $this->product, $this->currency, ['product_name' => 'Dashboard Only Subscription']);

    $this->actingAs($this->user)->get(route('front.account.profile'))
        ->assertOk()
        ->assertDontSee('فعال سبسکرپشنز')
        ->assertDontSee('Dashboard Only Subscription');
});

test('history shows every own purchase newest first with effective states and payment labels', function () {
    $older = makeFrontendAccountSubscription($this->user, $this->product, $this->currency, [
        'product_name' => 'Older Expired', 'end_date' => '2026-09-09', 'created_at' => '2026-08-01 10:00:00',
    ]);
    $newer = makeFrontendAccountSubscription($this->user, $this->product, $this->currency, [
        'product_name' => 'Newer Future', 'start_date' => '2026-09-11', 'created_at' => '2026-09-01 10:00:00',
    ]);
    $older->subscriptionTypes()->attach($this->magazine);
    $newer->subscriptionTypes()->attach($this->articles);
    makeFrontendAccountSubscription($this->otherUser, $this->product, $this->currency, ['product_name' => 'Other User Purchase']);

    $response = $this->actingAs($this->user)->get(route('front.account.subscriptions'))
        ->assertOk()
        ->assertViewIs('frontend.user-account.subscriptions')
        ->assertSee('user-subscription-history-table', false)
        ->assertSee('Older Expired')
        ->assertSee('Newer Future')
        ->assertSee('میعاد ختم')
        ->assertSee('طے شدہ')
        ->assertSee('JazzCash')
        ->assertDontSee('Other User Purchase');

    expect(strpos($response->getContent(), 'Newer Future'))->toBeLessThan(strpos($response->getContent(), 'Older Expired'));
});

test('history is paginated ten records per page', function () {
    foreach (range(1, 11) as $number) {
        makeFrontendAccountSubscription($this->user, $this->product, $this->currency, ['product_name' => 'Purchase '.$number]);
    }

    $this->actingAs($this->user)->get(route('front.account.subscriptions'))
        ->assertOk()
        ->assertViewHas('subscriptions', fn ($subscriptions): bool => $subscriptions->perPage() === 10
            && $subscriptions->count() === 10
            && $subscriptions->total() === 11);
});

test('dashboard and history remain based on immutable purchase snapshots', function () {
    $subscription = makeFrontendAccountSubscription($this->user, $this->product, $this->currency);
    $subscription->subscriptionTypes()->attach([$this->magazine->id, $this->articles->id]);
    $this->product->subscriptionTypes()->sync([$this->magazine->id]);
    $this->product->update(['name' => 'New Membership Name', 'price' => 2000]);

    $this->actingAs($this->user)->get(route('front.account'))
        ->assertOk()
        ->assertDontSee('New Membership Name');

    $this->actingAs($this->user)->get(route('front.account.subscriptions'))
        ->assertSee('Old Membership Name')
        ->assertDontSee('New Membership Name')
        ->assertSee('PKR 1,000')
        ->assertSee('ہفتہ وار میگزین')
        ->assertSee('مضامین');
});
