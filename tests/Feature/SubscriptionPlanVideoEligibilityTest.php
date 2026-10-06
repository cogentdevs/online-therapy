<?php

use App\Models\Currency;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->admin = User::factory()->create(['is_active' => true]);
    $this->admin->assignRole(Role::findOrCreate('super-admin', 'web'));
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee',
        'code' => 'PKR',
        'symbol' => 'Rs',
        'isActive' => true,
    ]);
    $this->type = SubscriptionType::query()->create([
        'name' => 'Video',
        'slug' => 'video',
        'isActive' => true,
    ]);
});

function subscriptionPlanVideoPayload(Currency $currency, SubscriptionType $type, array $overrides = []): array
{
    return array_merge([
        'name' => 'Video Subscription Plan',
        'subscription_type_id' => $type->id,
        'currency_id' => $currency->id,
        'price' => 100,
        'duration_value' => 1,
        'duration_unit' => 'month',
    ], $overrides);
}

function subscriptionPlanVideo(string $title, bool $isActive, string $status): Video
{
    return Video::query()->create([
        'title' => $title,
        'video_link' => 'https://example.com/'.str($title)->slug(),
        'is_active' => $isActive,
        'status' => $status,
    ]);
}

function subscriptionPlanWithVideos(Currency $currency, SubscriptionType $type, array $videoIds): SubscriptionProduct
{
    $plan = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN,
        'name' => 'Existing Video Plan',
        'currency_id' => $currency->id,
        'price' => 100,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    $plan->subscriptionTypes()->attach($type);
    $plan->videos()->attach($videoIds);

    return $plan;
}

test('plan create lists only active published Videos', function () {
    subscriptionPlanVideo('Active Published Video', true, Video::STATUS_PUBLISHED);
    subscriptionPlanVideo('Active Draft Video', true, Video::STATUS_DRAFT);
    subscriptionPlanVideo('Inactive Published Video', false, Video::STATUS_PUBLISHED);
    subscriptionPlanVideo('Inactive Draft Video', false, Video::STATUS_DRAFT);

    $this->actingAs($this->admin)
        ->get(route('admin.subscription-plan.create'))
        ->assertSuccessful()
        ->assertSee('Active Published Video')
        ->assertDontSee('Active Draft Video')
        ->assertDontSee('Inactive Published Video')
        ->assertDontSee('Inactive Draft Video');
});

test('plan creation accepts only active published Video assignments', function () {
    $publishedVideo = subscriptionPlanVideo('Published Video', true, Video::STATUS_PUBLISHED);
    $draftVideo = subscriptionPlanVideo('Draft Video', true, Video::STATUS_DRAFT);
    $inactiveVideo = subscriptionPlanVideo('Inactive Video', false, Video::STATUS_PUBLISHED);

    $this->actingAs($this->admin)
        ->post(route('admin.subscription-plan.store'), subscriptionPlanVideoPayload($this->currency, $this->type, [
            'video_ids' => [$publishedVideo->id],
        ]))
        ->assertRedirect(route('admin.subscription-plan.index'));

    $plan = SubscriptionProduct::query()->where('name', 'Video Subscription Plan')->sole();

    expect($plan->videos()->pluck('videos.id')->all())->toBe([$publishedVideo->id]);

    foreach ([$draftVideo, $inactiveVideo] as $ineligibleVideo) {
        $this->post(route('admin.subscription-plan.store'), subscriptionPlanVideoPayload($this->currency, $this->type, [
            'name' => 'Rejected Plan '.$ineligibleVideo->id,
            'video_ids' => [$ineligibleVideo->id],
        ]))->assertSessionHasErrors('video_ids.0');
    }
});

test('plan edit retains assigned draft and inactive Videos while rejecting new ineligible assignments', function () {
    $draftVideo = subscriptionPlanVideo('Existing Draft Video', true, Video::STATUS_DRAFT);
    $inactiveVideo = subscriptionPlanVideo('Existing Inactive Video', false, Video::STATUS_PUBLISHED);
    $newDraftVideo = subscriptionPlanVideo('New Draft Video', true, Video::STATUS_DRAFT);
    $plan = subscriptionPlanWithVideos($this->currency, $this->type, [$draftVideo->id, $inactiveVideo->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.subscription-plan.edit', ['id' => $plan->id]))
        ->assertSuccessful()
        ->assertSee('Existing Draft Video')
        ->assertSee('Existing Inactive Video')
        ->assertSee('Existing: Draft')
        ->assertSee('Existing: Inactive');

    $this->post(route('admin.subscription-plan.update', ['id' => $plan->id]), subscriptionPlanVideoPayload($this->currency, $this->type, [
        'name' => 'Updated Existing Video Plan',
        'price' => 125,
        'isActive' => '1',
        'video_ids' => [$draftVideo->id, $inactiveVideo->id],
    ]))->assertRedirect(route('admin.subscription-plan.index'));

    expect($plan->refresh()->videos()->pluck('videos.id')->sort()->values()->all())
        ->toBe(collect([$draftVideo->id, $inactiveVideo->id])->sort()->values()->all());

    $this->post(route('admin.subscription-plan.update', ['id' => $plan->id]), subscriptionPlanVideoPayload($this->currency, $this->type, [
        'name' => 'Updated Existing Video Plan',
        'isActive' => '1',
        'video_ids' => [$draftVideo->id, $inactiveVideo->id, $newDraftVideo->id],
    ]))->assertSessionHasErrors('video_ids');

    expect($plan->refresh()->videos()->whereKey($newDraftVideo->id)->exists())->toBeFalse();
});

test('an active Video becomes selectable after publication without changing plan assignments', function () {
    $video = subscriptionPlanVideo('Publish Me', true, Video::STATUS_DRAFT);
    $plan = subscriptionPlanWithVideos($this->currency, $this->type, [$video->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.subscription-plan.create'))
        ->assertDontSee('Publish Me');

    $video->update(['status' => Video::STATUS_PUBLISHED]);

    $this->get(route('admin.subscription-plan.create'))
        ->assertSuccessful()
        ->assertSee('Publish Me');

    expect($plan->videos()->whereKey($video)->exists())->toBeTrue();
});
