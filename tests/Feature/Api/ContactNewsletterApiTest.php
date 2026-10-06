<?php

use App\Mail\ContactMailToAdmin;
use App\Models\Contact;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'services.brevo_newsletter.api_key' => 'newsletter-api-key',
        'services.brevo_newsletter.list_id' => 77,
    ]);
});

test('public contact api stores validated message without browser captcha and sends existing admin mail', function () {
    Mail::fake();

    $this->postJson(route('api.contact.store'), [
        'name' => 'Mobile Reader',
        'phone' => '03001234567',
        'email' => 'reader@example.test',
        'subject' => 'Mobile contact',
        'message' => 'Please send more information.',
        'is_read' => true,
    ])->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['contact_id']]);

    $contact = Contact::query()->sole();
    expect($contact->is_read)->toBeFalse();
    Mail::assertSent(ContactMailToAdmin::class, fn ($mail): bool => $mail->contact->is($contact));
});

test('contact api returns json validation errors', function () {
    $this->postJson(route('api.contact.store'), [
        'email' => 'invalid-email',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'phone', 'email', 'subject', 'message']);

    $this->assertDatabaseEmpty('contacts');
});

test('public newsletter api normalizes email and reuses existing subscription service', function () {
    Http::fake([
        'api.brevo.com/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'api.brevo.com/v3/contacts' => Http::response([], 201),
    ]);

    $this->postJson(route('api.newsletter.subscribe'), ['email' => ' Reader@Example.COM '])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('already_subscribed', false)
        ->assertJsonPath('sync_pending', false);

    expect(NewsletterSubscriber::query()->sole()->email)->toBe('reader@example.com');

    $this->postJson(route('api.newsletter.subscribe'), ['email' => 'reader@example.com'])
        ->assertOk()->assertJsonPath('already_subscribed', true);
    expect(NewsletterSubscriber::query()->count())->toBe(1);
});

test('newsletter api rejects an invalid email and has no duplicate api route', function () {
    $this->postJson(route('api.newsletter.subscribe'), ['email' => 'invalid'])
        ->assertUnprocessable()->assertJsonValidationErrors('email');

    expect(collect(app('router')->getRoutes()->getRoutes())
        ->filter(fn ($route): bool => $route->uri() === 'api/newsletter/subscribe')
        ->count())->toBe(1);
});
