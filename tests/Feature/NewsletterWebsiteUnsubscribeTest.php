<?php

use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    config([
        'services.brevo_newsletter.api_key' => 'newsletter-key',
        'services.brevo_newsletter.list_id' => 77,
        'services.brevo_newsletter.base_url' => 'https://api.brevo.test/v3',
    ]);
});

test('new subscribers receive unique opaque tokens synchronized to Brevo personalization attribute', function () {
    Http::preventStrayRequests();
    Http::fake([
        'https://api.brevo.test/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'https://api.brevo.test/v3/contacts' => Http::response([], 201),
    ]);

    $this->postJson(route('front.newsletter.subscribe'), ['email' => 'first@example.com'])->assertSuccessful();
    $this->postJson(route('front.newsletter.subscribe'), ['email' => 'second@example.com'])->assertSuccessful();
    [$first, $second] = NewsletterSubscriber::query()->orderBy('id')->get();

    expect($first->unsubscribe_token)->toHaveLength(64)
        ->not->toBe($second->unsubscribe_token)
        ->and($first->unsubscribe_token)->not->toContain($first->email);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.brevo.test/v3/contacts'
        && filled($request['attributes']['DM_UNSUB_TOKEN'] ?? null)
        && $request['listIds'] === [77]);
});

test('missing Brevo personalization attribute is provisioned idempotently before contact sync', function () {
    Http::fakeSequence()
        ->push(['attributes' => []], 200)
        ->push([], 201)
        ->push([], 201);

    $this->postJson(route('front.newsletter.subscribe'), ['email' => 'attribute@example.com'])->assertSuccessful();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api.brevo.test/v3/contacts/attributes/normal/DM_UNSUB_TOKEN'
        && $request['type'] === 'text');
});

test('production website unsubscribe route contains opaque token without email or subscriber id', function () {
    $subscriber = NewsletterSubscriber::factory()->create(['email' => 'safe@example.com']);
    $url = route('front.newsletter.unsubscribe.show', $subscriber->unsubscribe_token);

    expect($url)->toContain($subscriber->unsubscribe_token)
        ->not->toContain($subscriber->email)
        ->not->toEndWith('/'.$subscriber->id);
});

test('public GET safely displays masked recipient and never mutates or calls Brevo', function () {
    Http::preventStrayRequests();
    $subscriber = NewsletterSubscriber::factory()->create(['email' => 'reader@example.com']);
    $original = $subscriber->getRawOriginal();

    $this->get(route('front.newsletter.unsubscribe.show', $subscriber->unsubscribe_token))
        ->assertSuccessful()
        ->assertSee('r*****@example.com')
        ->assertDontSee($subscriber->email);

    expect($subscriber->fresh()->status)->toBe($original['status'])
        ->and($subscriber->fresh()->unsubscribed_at)->toBeNull();
    Http::assertNothingSent();
});

test('invalid token fails safely without disclosing subscriber details', function () {
    NewsletterSubscriber::factory()->create(['email' => 'private@example.com']);

    $this->get(route('front.newsletter.unsubscribe.show', str_repeat('x', 64)))
        ->assertNotFound()
        ->assertDontSee('private@example.com');
});

test('POST is local first and removes only the dedicated Brevo list association', function () {
    Http::preventStrayRequests();
    Http::fake(['https://api.brevo.test/v3/contacts/*' => Http::response(null, 204)]);
    $user = User::factory()->create(['email' => 'reader@example.com']);
    $subscriber = NewsletterSubscriber::factory()->create(['email' => 'reader@example.com']);

    $this->post(route('front.newsletter.unsubscribe.store', $subscriber->unsubscribe_token), ['reason' => 'not_now'])
        ->assertRedirect(route('front.newsletter.unsubscribe.success'));

    $subscriber->refresh();
    expect($subscriber->status)->toBe(NewsletterSubscriber::STATUS_UNSUBSCRIBED)
        ->and($subscriber->unsubscribed_at)->not->toBeNull()
        ->and($subscriber->unsubscribe_reason)->toBe('میں فی الحال نیوز لیٹر وصول نہیں کرنا چاہتا')
        ->and($subscriber->brevo_sync_status)->toBe(NewsletterSubscriber::SYNC_SYNCED)
        ->and($user->fresh())->not->toBeNull();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PUT'
        && str_contains($request->url(), rawurlencode($subscriber->email))
        && $request['unlinkListIds'] === [77]
        && ! array_key_exists('emailBlacklisted', $request->data()));
});

test('Brevo removal failure preserves local unsubscribe and stores sanitized failure', function () {
    Http::fake(['https://api.brevo.test/v3/contacts/*' => Http::response(['message' => 'Removal unavailable'], 503)]);
    $subscriber = NewsletterSubscriber::factory()->create();

    $this->post(route('front.newsletter.unsubscribe.store', $subscriber->unsubscribe_token), [
        'reason' => 'other',
        'other_reason' => 'ذاتی ترجیح',
    ])->assertRedirect(route('front.newsletter.unsubscribe.success'));

    expect($subscriber->fresh()->status)->toBe(NewsletterSubscriber::STATUS_UNSUBSCRIBED)
        ->and($subscriber->fresh()->unsubscribe_reason)->toBe('ذاتی ترجیح')
        ->and($subscriber->fresh()->brevo_sync_status)->toBe(NewsletterSubscriber::SYNC_FAILED)
        ->and($subscriber->fresh()->brevo_error)->toBe('Removal unavailable');
});

test('already unsubscribed operation and success GET are idempotent', function () {
    Http::preventStrayRequests();
    $unsubscribedAt = now()->subDay();
    $subscriber = NewsletterSubscriber::factory()->create([
        'status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED,
        'unsubscribed_at' => $unsubscribedAt,
        'unsubscribe_reason' => 'Existing reason',
        'brevo_sync_status' => NewsletterSubscriber::SYNC_SYNCED,
    ]);

    $this->post(route('front.newsletter.unsubscribe.store', $subscriber->unsubscribe_token), ['reason' => 'too_many'])->assertRedirect();
    $this->get(route('front.newsletter.unsubscribe.success'))->assertSuccessful();

    expect($subscriber->fresh()->unsubscribed_at->equalTo($unsubscribedAt))->toBeTrue()
        ->and($subscriber->fresh()->unsubscribe_reason)->toBe('Existing reason');
    Http::assertNothingSent();
});

test('re-subscribe retains token and adds contact back to dedicated list', function () {
    Http::fake([
        'https://api.brevo.test/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'https://api.brevo.test/v3/contacts' => Http::response([], 201),
    ]);
    $subscriber = NewsletterSubscriber::factory()->create([
        'status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED,
        'unsubscribed_at' => now(),
        'brevo_sync_status' => NewsletterSubscriber::SYNC_SYNCED,
    ]);
    $token = $subscriber->unsubscribe_token;

    $this->postJson(route('front.newsletter.subscribe'), ['email' => $subscriber->email])->assertSuccessful();

    expect($subscriber->fresh()->unsubscribe_token)->toBe($token)
        ->and($subscriber->fresh()->status)->toBe(NewsletterSubscriber::STATUS_SUBSCRIBED);
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.brevo.test/v3/contacts'
        && $request['attributes']['DM_UNSUB_TOKEN'] === $token
        && $request['listIds'] === [77]);
});
