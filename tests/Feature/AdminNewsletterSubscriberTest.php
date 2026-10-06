<?php

use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    config(['services.brevo_newsletter.api_key' => 'key', 'services.brevo_newsletter.list_id' => 77]);
});

function newsletterAdmin(array $permissions): User
{
    $role = Role::findOrCreate('newsletter-admin-'.str()->random(8), 'web');
    $role->syncPermissions(['admin.access', 'dashboard.view', ...$permissions]);
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

test('Newsletter Subscriber admin listing and sidebar obey dedicated permission', function () {
    $subscriber = NewsletterSubscriber::factory()->create(['brevo_sync_status' => NewsletterSubscriber::SYNC_FAILED]);
    $unauthorized = newsletterAdmin([]);
    $authorized = newsletterAdmin(['newsletter-subscribers.view']);

    $this->actingAs($unauthorized)->get(route('admin.newsletter-subscribers.index'))->assertForbidden();
    $this->actingAs($authorized)->get(route('admin.newsletter-subscribers.index'))
        ->assertSuccessful()
        ->assertSee($subscriber->email)
        ->assertSee('Newsletter Subscribers');
});

test('authorized re-sync uses dedicated list and preserves local subscriber on failure', function () {
    $subscriber = NewsletterSubscriber::factory()->create(['brevo_sync_status' => NewsletterSubscriber::SYNC_FAILED]);
    $admin = newsletterAdmin(['newsletter-subscribers.view', 'newsletter-subscribers.edit']);
    Http::fake([
        'api.brevo.com/v3/contacts/attributes' => Http::response(['attributes' => [['category' => 'normal', 'name' => 'DM_UNSUB_TOKEN', 'type' => 'text']]]),
        'api.brevo.com/v3/contacts' => Http::response(['message' => 'Unavailable'], 503),
    ]);

    $this->actingAs($admin)->post(route('admin.newsletter-subscribers.resync', $subscriber))->assertRedirect();

    expect($subscriber->fresh())->not->toBeNull()
        ->and($subscriber->fresh()->status)->toBe(NewsletterSubscriber::STATUS_SUBSCRIBED)
        ->and($subscriber->fresh()->brevo_sync_status)->toBe(NewsletterSubscriber::SYNC_FAILED);
    Http::assertSent(fn ($request): bool => $request['listIds'] === [77]);
});

test('Newsletter Subscriber module exposes no delete route', function () {
    expect(collect(app('router')->getRoutes())->contains(fn ($route): bool => str_starts_with((string) $route->getName(), 'admin.newsletter-subscribers.') && in_array('DELETE', $route->methods(), true)))->toBeFalse();
});

test('re-sync of unsubscribed subscriber removes only the Newsletter list', function () {
    $subscriber = NewsletterSubscriber::factory()->create([
        'status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED,
        'brevo_sync_status' => NewsletterSubscriber::SYNC_FAILED,
    ]);
    $admin = newsletterAdmin(['newsletter-subscribers.view', 'newsletter-subscribers.edit']);
    Http::fake(['api.brevo.com/v3/contacts/*' => Http::response(null, 204)]);

    $this->actingAs($admin)->post(route('admin.newsletter-subscribers.resync', $subscriber))->assertRedirect();

    Http::assertSent(fn ($request): bool => $request->method() === 'PUT'
        && $request['unlinkListIds'] === [77]
        && ! isset($request['listIds']));
    expect($subscriber->fresh()->status)->toBe(NewsletterSubscriber::STATUS_UNSUBSCRIBED)
        ->and($subscriber->fresh()->brevo_sync_status)->toBe(NewsletterSubscriber::SYNC_SYNCED);
});
