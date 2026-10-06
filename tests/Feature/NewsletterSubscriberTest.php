<?php

use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    config([
        'services.brevo_newsletter.api_key' => 'newsletter-api-key',
        'services.brevo_newsletter.list_id' => 77,
        'services.brevo_newsletter.webhook_token' => 'webhook-secret',
    ]);
});

test('guest subscription normalizes email and syncs only to the dedicated list', function () {
    Http::fake([
        'api.brevo.com/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'api.brevo.com/v3/contacts' => Http::response([], 201),
    ]);

    $this->postJson(route('front.newsletter.subscribe'), ['email' => '  Reader@Example.COM  '])
        ->assertSuccessful()
        ->assertJsonPath('success', true);

    $subscriber = NewsletterSubscriber::query()->sole();
    expect($subscriber->email)->toBe('reader@example.com')
        ->and($subscriber->status)->toBe(NewsletterSubscriber::STATUS_SUBSCRIBED)
        ->and($subscriber->unsubscribe_token)->toHaveLength(64)
        ->and($subscriber->subscribed_at)->not->toBeNull()
        ->and($subscriber->brevo_sync_status)->toBe(NewsletterSubscriber::SYNC_SYNCED);

    Http::assertSent(fn ($request): bool => $request->url() === 'https://api.brevo.com/v3/contacts'
        && $request['listIds'] === [77]
        && filled($request['attributes']['DM_UNSUB_TOKEN'] ?? null)
        && $request['updateEnabled'] === true);
});

test('duplicate and re-subscription reuse the same local record', function () {
    Http::fake([
        'api.brevo.com/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'api.brevo.com/v3/contacts' => Http::response([], 201),
    ]);
    $subscriber = NewsletterSubscriber::factory()->create([
        'email' => 'reader@example.com',
        'status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED,
        'unsubscribed_at' => now()->subDay(),
        'unsubscribe_reason' => 'Brevo newsletter unsubscribe',
    ]);

    $this->postJson(route('front.newsletter.subscribe'), ['email' => 'READER@example.com'])->assertSuccessful();
    $subscriber->refresh();

    expect(NewsletterSubscriber::query()->count())->toBe(1)
        ->and($subscriber->status)->toBe(NewsletterSubscriber::STATUS_SUBSCRIBED)
        ->and($subscriber->unsubscribed_at)->toBeNull()
        ->and($subscriber->unsubscribe_reason)->toBeNull();

    $this->postJson(route('front.newsletter.subscribe'), ['email' => 'reader@example.com'])
        ->assertSuccessful()
        ->assertJsonPath('message', 'آپ پہلے ہی نیوز لیٹر کو سبسکرائب کر چکے ہیں۔');
    expect(NewsletterSubscriber::query()->count())->toBe(1);
});

test('Brevo failure preserves local subscriber and stores a safe failure state', function () {
    Http::fake([
        'api.brevo.com/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'api.brevo.com/v3/contacts' => Http::response(['message' => 'Remote service unavailable'], 503),
    ]);

    $this->postJson(route('front.newsletter.subscribe'), ['email' => 'reader@example.com'])
        ->assertSuccessful()
        ->assertJsonPath('sync_pending', true);

    $subscriber = NewsletterSubscriber::query()->sole();
    expect($subscriber->status)->toBe(NewsletterSubscriber::STATUS_SUBSCRIBED)
        ->and($subscriber->brevo_sync_status)->toBe(NewsletterSubscriber::SYNC_FAILED)
        ->and($subscriber->brevo_error)->toBe('Remote service unavailable');
});

test('authorized matching marketing unsubscribe is idempotent and isolated from users', function () {
    $user = User::factory()->create(['email' => 'reader@example.com', 'is_active' => true]);
    $subscriber = NewsletterSubscriber::factory()->create(['email' => 'reader@example.com']);
    $payload = ['event' => 'unsubscribe', 'type' => 'marketing', 'email' => 'reader@example.com', 'list_id' => [77]];

    $this->withToken('webhook-secret')->postJson(route('webhooks.brevo.newsletter'), $payload)
        ->assertSuccessful()->assertJsonPath('processed', true);
    $firstUnsubscribedAt = $subscriber->fresh()->unsubscribed_at;
    $this->withToken('webhook-secret')->postJson(route('webhooks.brevo.newsletter'), $payload)
        ->assertSuccessful()->assertJsonPath('processed', true);

    expect($subscriber->fresh()->status)->toBe(NewsletterSubscriber::STATUS_UNSUBSCRIBED)
        ->and($subscriber->fresh()->unsubscribed_at->equalTo($firstUnsubscribedAt))->toBeTrue()
        ->and($user->fresh()->is_active)->toBeTrue();
});

test('webhook rejects unauthorized calls and ignores unrelated events lists and unknown contacts', function () {
    $subscriber = NewsletterSubscriber::factory()->create(['email' => 'reader@example.com']);

    $this->postJson(route('webhooks.brevo.newsletter'), [])->assertUnauthorized();
    $this->withToken('webhook-secret')->postJson(route('webhooks.brevo.newsletter'), [
        'event' => 'delivered', 'type' => 'transactional', 'email' => $subscriber->email, 'list_id' => [77],
    ])->assertJsonPath('processed', false);
    $this->withToken('webhook-secret')->postJson(route('webhooks.brevo.newsletter'), [
        'event' => 'unsubscribe', 'type' => 'marketing', 'email' => $subscriber->email, 'list_id' => [88],
    ])->assertJsonPath('processed', false);
    $this->withToken('webhook-secret')->postJson(route('webhooks.brevo.newsletter'), [
        'event' => 'unsubscribe', 'type' => 'marketing', 'email' => 'unknown@example.com', 'list_id' => [77],
    ])->assertJsonPath('processed', false);

    expect($subscriber->fresh()->status)->toBe(NewsletterSubscriber::STATUS_SUBSCRIBED)
        ->and(NewsletterSubscriber::query()->count())->toBe(1);
});
