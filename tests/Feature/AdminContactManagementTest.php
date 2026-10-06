<?php

use App\Models\Contact;
use App\Models\User;
use App\Services\Authorization\PermissionSyncService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    app(PermissionSyncService::class)->sync();
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

function contactAdmin(array $permissions): User
{
    $role = Role::findOrCreate('contact-admin-'.Str::random(8), 'web');
    $role->syncPermissions(['admin.access', 'dashboard.view', ...$permissions]);
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole($role);

    return $admin;
}

function contactMessage(array $attributes = []): Contact
{
    $contact = Contact::query()->create(array_merge([
        'name' => 'Contact Sender',
        'phone' => '03001234567',
        'email' => 'sender@example.com',
        'subject' => 'Magazine enquiry',
        'message' => "First line\nSecond line with full details.",
    ], $attributes));

    if (array_key_exists('is_read', $attributes)) {
        $contact->is_read = (bool) $attributes['is_read'];
        $contact->save();
    }

    return $contact;
}

test('Contact permissions are registered with only view and delete actions', function () {
    expect(config('admin_modules.modules.contacts'))->toBe([
        'label' => 'Contact Messages',
        'group' => 'Administration',
        'actions' => ['view' => 'View', 'delete' => 'Delete'],
    ]);

    foreach (['contacts.view', 'contacts.delete'] as $permission) {
        $this->assertDatabaseHas('permissions', ['name' => $permission, 'guard_name' => 'web']);
    }
});

test('Super Admin and authorized Admin can access Contact list while unauthorized Admin is denied', function () {
    $this->actingAs($this->superAdmin)->get(route('admin.contacts.index'))->assertSuccessful();
    $this->actingAs(contactAdmin(['contacts.view']))->get(route('admin.contacts.index'))->assertSuccessful();
    $this->actingAs(contactAdmin([]))->get(route('admin.contacts.index'))->assertForbidden();
    $this->actingAs(contactAdmin([]))->getJson(route('admin.contacts.data'))->assertForbidden();
});

test('Opening an unread Contact displays the full escaped message and marks it read', function () {
    $contact = contactMessage(['message' => "Complete <script>alert('x')</script>\nSecond line"]);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.contacts.show', $contact))
        ->assertSuccessful()
        ->assertSee('Complete &lt;script&gt;alert', false)
        ->assertDontSee('<script>', false)
        ->assertSee('Second line');

    expect($contact->refresh()->is_read)->toBeTrue();
});

test('An already-read Contact remains readable', function () {
    $contact = contactMessage(['is_read' => true]);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.contacts.show', $contact))
        ->assertSuccessful()
        ->assertSee($contact->message);

    expect($contact->refresh()->is_read)->toBeTrue();
});

test('Delete permission is enforced independently and removes only the selected Contact', function () {
    $selected = contactMessage(['email' => 'selected@example.com']);
    $remaining = contactMessage(['email' => 'remaining@example.com']);
    $viewer = contactAdmin(['contacts.view']);
    $deleter = contactAdmin(['contacts.delete']);

    $this->actingAs($viewer)->delete(route('admin.contacts.destroy', $selected))->assertForbidden();
    $this->actingAs($deleter)->delete(route('admin.contacts.destroy', $selected))->assertRedirect(route('admin.contacts.index'));

    $this->assertModelMissing($selected);
    $this->assertModelExists($remaining);
});

test('Contact DataTable returns newest first with status and permission-aware actions', function () {
    $older = contactMessage(['subject' => 'Older message', 'is_read' => true]);
    $older->forceFill(['created_at' => now()->subDay()])->saveQuietly();
    $newer = contactMessage(['subject' => 'Newest message']);

    $response = $this->actingAs(contactAdmin(['contacts.view', 'contacts.delete']))->getJson(route('admin.contacts.data', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
        'order' => [['column' => 5, 'dir' => 'desc']],
    ]));

    $response->assertSuccessful()
        ->assertJsonPath('recordsTotal', 2)
        ->assertJsonPath('data.0.subject', $newer->subject)
        ->assertJsonPath('data.0.is_read', false)
        ->assertJsonPath('data.1.subject', $older->subject)
        ->assertJsonPath('data.1.is_read', true)
        ->assertJsonPath('data.0.delete_url', route('admin.contacts.destroy', $newer));

    $viewOnlyResponse = $this->actingAs(contactAdmin(['contacts.view']))->getJson(route('admin.contacts.data'));
    $viewOnlyResponse->assertSuccessful()->assertJsonPath('data.0.delete_url', null);
});

test('Sidebar and dashboard Contact card follow effective permission and show unread count', function () {
    contactMessage();
    contactMessage(['is_read' => true]);

    $authorized = $this->actingAs(contactAdmin(['contacts.view']))->get(route('admin.dashboard'));
    $authorized->assertSuccessful()
        ->assertSee(route('admin.contacts.index'), false)
        ->assertSee('data-dashboard-module="contacts"', false)
        ->assertSee('data-dashboard-unread="1"', false);

    $unauthorized = $this->actingAs(contactAdmin([]))->get(route('admin.dashboard'));
    $unauthorized->assertSuccessful()
        ->assertDontSee(route('admin.contacts.index'), false)
        ->assertDontSee('data-dashboard-module="contacts"', false);

    $this->actingAs($this->superAdmin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('data-dashboard-module="contacts"', false);
});
