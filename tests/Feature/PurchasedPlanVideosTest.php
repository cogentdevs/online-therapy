<?php

use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Carbon::setTestNow('2026-10-06 12:00:00');
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
    $this->otherUser = User::factory()->create(['is_active' => true]);
    $this->otherUser->assignRole('user');
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true,
    ]);
    $this->type = SubscriptionType::query()->create(['name' => 'Video', 'slug' => 'video', 'isActive' => true]);
    $this->plan = purchasedPlan('Monthly Video Pack', $this->currency, $this->type);
});

afterEach(fn (): ?Carbon => Carbon::setTestNow());

function purchasedPlan(string $name, Currency $currency, SubscriptionType $type, string $productFor = SubscriptionProduct::FOR_PLAN): SubscriptionProduct
{
    $product = SubscriptionProduct::query()->create([
        'product_for' => $productFor,
        'name' => $name,
        'currency_id' => $currency->id,
        'price' => 300,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $product->subscriptionTypes()->attach($type);

    return $product;
}

function purchasedPlanSubscription(User $user, SubscriptionProduct $product, Currency $currency, array $overrides = []): UserSubscription
{
    return UserSubscription::query()->create(array_replace([
        'user_id' => $user->id,
        'subscription_product_id' => $product->id,
        'product_for' => $product->product_for,
        'product_name' => $product->name,
        'currency_id' => $currency->id,
        'price' => 300,
        'discount' => 0,
        'total' => 300,
        'payment_method' => 'bank_transfer',
        'payment_status' => UserSubscription::PAYMENT_STATUS_APPROVED,
        'start_date' => today()->subDay(),
        'end_date' => today()->addMonth(),
        'status' => UserSubscription::STATUS_ACTIVE,
        'is_active' => true,
    ], $overrides));
}

function purchasedPlanVideo(string $title, bool $active = true, string $status = Video::STATUS_PUBLISHED): Video
{
    return Video::query()->create([
        'title' => $title,
        'video_link' => 'https://videos.example.test/'.str()->slug($title),
        'short_description' => $title.' description',
        'is_active' => $active,
        'status' => $status,
    ]);
}

test('subscription history shows View Videos only for a currently active plan', function (): void {
    $active = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'order_no' => 20261001, 'invoice_no' => 20260001,
    ]);
    $pending = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'status' => UserSubscription::STATUS_PENDING, 'payment_status' => UserSubscription::PAYMENT_STATUS_PENDING,
        'is_active' => false, 'start_date' => null, 'end_date' => null,
    ]);
    $rejected = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'status' => UserSubscription::STATUS_REJECTED, 'payment_status' => UserSubscription::PAYMENT_STATUS_REJECTED,
        'is_active' => false, 'start_date' => null, 'end_date' => null,
    ]);
    $expired = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'status' => UserSubscription::STATUS_EXPIRED, 'is_active' => false, 'end_date' => today()->subDay(),
    ]);
    $future = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'start_date' => today()->addDay(), 'end_date' => today()->addMonths(2),
    ]);
    $membership = purchasedPlan('Video Membership', $this->currency, $this->type, SubscriptionProduct::FOR_MEMBERSHIP);
    $membershipSubscription = purchasedPlanSubscription($this->user, $membership, $this->currency);

    $this->actingAs($this->user)->get(route('front.account.subscriptions'))
        ->assertSuccessful()
        ->assertSee(route('front.account.subscriptions.videos', $active), false)
        ->assertDontSee(route('front.account.subscriptions.videos', $pending), false)
        ->assertDontSee(route('front.account.subscriptions.videos', $rejected), false)
        ->assertDontSee(route('front.account.subscriptions.videos', $expired), false)
        ->assertDontSee(route('front.account.subscriptions.videos', $future), false)
        ->assertDontSee(route('front.account.subscriptions.videos', $membershipSubscription), false)
        ->assertSee(route('front.account.subscriptions.invoice.view', $active), false)
        ->assertSee(route('front.account.subscriptions.resubmit', $rejected), false);

    $this->actingAs($this->user)->getJson(route('front.account.subscriptions', ['datatable' => 1]))
        ->assertSuccessful()
        ->assertJsonFragment(['videos_url' => route('front.account.subscriptions.videos', $active)])
        ->assertJsonFragment(['videos_url' => null]);
});

test('a user without a purchased subscription cannot discover or access a plan videos page', function (): void {
    $otherSubscription = purchasedPlanSubscription($this->otherUser, $this->plan, $this->currency);

    $this->actingAs($this->user)->get(route('front.account.subscriptions'))
        ->assertSuccessful()
        ->assertDontSee('Monthly Video Pack')
        ->assertDontSee(route('front.account.subscriptions.videos', $otherSubscription), false);
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos', $otherSubscription))->assertNotFound();
});

test('an owner sees only active published videos assigned to the subscribed plan', function (): void {
    $subscription = purchasedPlanSubscription($this->user, $this->plan, $this->currency);
    $published = purchasedPlanVideo('Published Plan Video');
    $draft = purchasedPlanVideo('Draft Plan Video', true, Video::STATUS_DRAFT);
    $inactive = purchasedPlanVideo('Inactive Plan Video', false);
    $otherPlan = purchasedPlan('Other Video Pack', $this->currency, $this->type);
    $otherPlanVideo = purchasedPlanVideo('Other Plan Video');
    $this->plan->videos()->attach([$published->id, $draft->id, $inactive->id]);
    $otherPlan->videos()->attach($otherPlanVideo);

    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos', $subscription))
        ->assertSuccessful()
        ->assertSee('Monthly Video Pack')
        ->assertSee('Published Plan Video')
        ->assertDontSee('Draft Plan Video')
        ->assertDontSee('Inactive Plan Video')
        ->assertDontSee('Other Plan Video')
        ->assertSee(route('front.account.subscriptions.videos.watch', [$subscription, $published]), false)
        ->assertDontSee($published->video_link, false);
});

test('the plan videos screen renders an empty state when there are no eligible videos', function (): void {
    $subscription = purchasedPlanSubscription($this->user, $this->plan, $this->currency);
    $this->plan->videos()->attach(purchasedPlanVideo('Draft Only Video', true, Video::STATUS_DRAFT));

    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos', $subscription))
        ->assertSuccessful()
        ->assertSee('No videos are currently available for this plan.');
});

test('live plan video assignments are available immediately and removed assignments are blocked', function (): void {
    $subscription = purchasedPlanSubscription($this->user, $this->plan, $this->currency);
    $firstVideo = purchasedPlanVideo('First Live Video');
    $secondVideo = purchasedPlanVideo('Second Live Video');
    $this->plan->videos()->attach($firstVideo);

    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos', $subscription))
        ->assertSuccessful()
        ->assertSee('First Live Video')
        ->assertDontSee('Second Live Video');

    $this->plan->videos()->attach($secondVideo);
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos', $subscription))
        ->assertSee('First Live Video')
        ->assertSee('Second Live Video');

    $this->plan->videos()->detach($firstVideo);
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$subscription, $firstVideo]))
        ->assertNotFound();
});

test('only the current renewal record grants plan video access', function (): void {
    $video = purchasedPlanVideo('Renewal Video');
    $this->plan->videos()->attach($video);
    $expired = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'status' => UserSubscription::STATUS_EXPIRED, 'is_active' => false, 'end_date' => today()->subDay(),
    ]);
    $active = purchasedPlanSubscription($this->user, $this->plan, $this->currency);
    $future = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'start_date' => today()->addDay(), 'end_date' => today()->addMonths(2),
    ]);

    $this->actingAs($this->user)->get(route('front.account.subscriptions'))
        ->assertSee(route('front.account.subscriptions.videos', $active), false)
        ->assertDontSee(route('front.account.subscriptions.videos', $expired), false)
        ->assertDontSee(route('front.account.subscriptions.videos', $future), false);
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$active, $video]))
        ->assertRedirect($video->video_link);
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$expired, $video]))
        ->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$future, $video]))
        ->assertNotFound();
});

test('plan video routes enforce ownership, active entitlement, plan membership, and video eligibility', function (): void {
    $active = purchasedPlanSubscription($this->user, $this->plan, $this->currency);
    $published = purchasedPlanVideo('Watchable Video');
    $draft = purchasedPlanVideo('Blocked Draft', true, Video::STATUS_DRAFT);
    $inactive = purchasedPlanVideo('Blocked Inactive', false);
    $this->plan->videos()->attach([$published->id, $draft->id, $inactive->id]);
    $otherPlan = purchasedPlan('Other Pack', $this->currency, $this->type);
    $otherPlanVideo = purchasedPlanVideo('Other Pack Video');
    $otherPlan->videos()->attach($otherPlanVideo);
    $otherSubscription = purchasedPlanSubscription($this->otherUser, $this->plan, $this->currency);
    $expired = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'status' => UserSubscription::STATUS_EXPIRED, 'is_active' => false, 'end_date' => today()->subDay(),
    ]);
    $future = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'start_date' => today()->addDay(), 'end_date' => today()->addMonths(2),
    ]);
    $pending = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'status' => UserSubscription::STATUS_PENDING, 'payment_status' => UserSubscription::PAYMENT_STATUS_PENDING,
        'is_active' => false, 'start_date' => null, 'end_date' => null,
    ]);
    $rejected = purchasedPlanSubscription($this->user, $this->plan, $this->currency, [
        'status' => UserSubscription::STATUS_REJECTED, 'payment_status' => UserSubscription::PAYMENT_STATUS_REJECTED,
        'is_active' => false, 'start_date' => null, 'end_date' => null,
    ]);
    $membership = purchasedPlan('Video Membership', $this->currency, $this->type, SubscriptionProduct::FOR_MEMBERSHIP);
    $membershipSubscription = purchasedPlanSubscription($this->user, $membership, $this->currency);

    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$active, $published]))
        ->assertRedirect($published->video_link);
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos', $otherSubscription))->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$otherSubscription, $published]))->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$active, $otherPlanVideo]))->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$active, $draft]))->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$active, $inactive]))->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$expired, $published]))->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$future, $published]))->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$pending, $published]))->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos.watch', [$rejected, $published]))->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.videos', $membershipSubscription))->assertNotFound();
});
