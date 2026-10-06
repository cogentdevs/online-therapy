<?php

use App\Models\SubscriptionNotificationSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->superAdmin = User::factory()->create(['is_active' => true]);
    $this->superAdmin->assignRole(Role::findOrCreate('super-admin', 'web'));
});

function reminderSettingsPayload(array $overrides = []): array
{
    return array_merge([
        'first_reminder_days' => 7,
        'second_reminder_days' => 5,
        'third_reminder_days' => 1,
        'isActive' => 1,
    ], $overrides);
}

test('admin can open reminder settings before the singleton exists', function () {
    $this->actingAs($this->superAdmin)
        ->get(route('admin.subscription-notification-settings.edit'))
        ->assertSuccessful()
        ->assertSee('Subscription Reminder Settings')
        ->assertSee('First Reminder Days')
        ->assertSee('Reminder Settings');

    expect(SubscriptionNotificationSetting::query()->count())->toBe(0);
});

test('admin can save three reminder days and update the same singleton', function () {
    $this->actingAs($this->superAdmin)
        ->put(route('admin.subscription-notification-settings.update'), reminderSettingsPayload())
        ->assertRedirect(route('admin.subscription-notification-settings.edit'))
        ->assertSessionHas('status');

    $setting = SubscriptionNotificationSetting::query()->sole();

    expect($setting->getKey())->toBe(1)
        ->and($setting->first_reminder_days)->toBe(7)
        ->and($setting->second_reminder_days)->toBe(5)
        ->and($setting->third_reminder_days)->toBe(1)
        ->and($setting->isActive)->toBeTrue();

    $this->actingAs($this->superAdmin)
        ->put(route('admin.subscription-notification-settings.update'), reminderSettingsPayload([
            'first_reminder_days' => 14,
            'second_reminder_days' => null,
            'third_reminder_days' => null,
            'isActive' => 0,
        ]))
        ->assertSessionHasNoErrors();

    expect(SubscriptionNotificationSetting::query()->count())->toBe(1)
        ->and($setting->fresh()->first_reminder_days)->toBe(14)
        ->and($setting->fresh()->second_reminder_days)->toBeNull()
        ->and($setting->fresh()->third_reminder_days)->toBeNull()
        ->and($setting->fresh()->isActive)->toBeFalse();
});

test('optional reminder combinations can be saved', function (array $days) {
    $this->actingAs($this->superAdmin)
        ->put(route('admin.subscription-notification-settings.update'), reminderSettingsPayload($days))
        ->assertSessionHasNoErrors();

    $setting = SubscriptionNotificationSetting::query()->sole();

    expect($setting->first_reminder_days)->toBe($days['first_reminder_days'])
        ->and($setting->second_reminder_days)->toBe($days['second_reminder_days'])
        ->and($setting->third_reminder_days)->toBe($days['third_reminder_days']);
})->with([
    'one reminder' => [[
        'first_reminder_days' => 7,
        'second_reminder_days' => null,
        'third_reminder_days' => null,
    ]],
    'two reminders' => [[
        'first_reminder_days' => 7,
        'second_reminder_days' => null,
        'third_reminder_days' => 1,
    ]],
    'no reminders' => [[
        'first_reminder_days' => null,
        'second_reminder_days' => null,
        'third_reminder_days' => null,
    ]],
]);

test('blank and zero reminder values are normalized to null', function () {
    $this->actingAs($this->superAdmin)
        ->put(route('admin.subscription-notification-settings.update'), reminderSettingsPayload([
            'first_reminder_days' => '0',
            'second_reminder_days' => '',
            'third_reminder_days' => 1,
        ]))
        ->assertSessionHasNoErrors();

    $setting = SubscriptionNotificationSetting::query()->sole();

    expect($setting->first_reminder_days)->toBeNull()
        ->and($setting->second_reminder_days)->toBeNull()
        ->and($setting->third_reminder_days)->toBe(1);
});

test('negative and non-integer reminder days are rejected', function (string $field, mixed $value) {
    $this->actingAs($this->superAdmin)
        ->put(route('admin.subscription-notification-settings.update'), reminderSettingsPayload([$field => $value]))
        ->assertSessionHasErrors($field);

    expect(SubscriptionNotificationSetting::query()->count())->toBe(0);
})->with([
    'negative' => ['first_reminder_days', -1],
    'decimal' => ['second_reminder_days', 2.5],
    'text' => ['third_reminder_days', 'soon'],
]);

test('duplicate positive reminder days are rejected while disabled values may repeat', function () {
    $this->actingAs($this->superAdmin)
        ->put(route('admin.subscription-notification-settings.update'), reminderSettingsPayload([
            'first_reminder_days' => 7,
            'second_reminder_days' => 7,
        ]))
        ->assertSessionHasErrors('reminder_days');

    expect(SubscriptionNotificationSetting::query()->count())->toBe(0);

    $this->actingAs($this->superAdmin)
        ->put(route('admin.subscription-notification-settings.update'), reminderSettingsPayload([
            'first_reminder_days' => 7,
            'second_reminder_days' => 0,
            'third_reminder_days' => 0,
        ]))
        ->assertSessionHasNoErrors();

    expect(SubscriptionNotificationSetting::query()->sole()->first_reminder_days)->toBe(7);
});

test('admin reminder settings routes reject guests and frontend users', function () {
    $this->get(route('admin.subscription-notification-settings.edit'))
        ->assertRedirect(route('admin.login'));
    $this->put(route('admin.subscription-notification-settings.update'), reminderSettingsPayload())
        ->assertRedirect(route('admin.login'));

    $frontendUser = User::factory()->create(['is_active' => true]);
    $frontendUser->assignRole(Role::findOrCreate('user', 'web'));

    $this->actingAs($frontendUser)
        ->get(route('admin.subscription-notification-settings.edit'))
        ->assertForbidden();
    $this->actingAs($frontendUser)
        ->put(route('admin.subscription-notification-settings.update'), reminderSettingsPayload())
        ->assertForbidden();
});

test('reminder settings module uses view and edit permissions', function () {
    expect(config('admin_modules.modules.subscription-reminder-settings'))->toBe([
        'label' => 'Subscription Reminder Settings',
        'group' => 'Subscriptions',
        'actions' => ['view' => 'View', 'edit' => 'Edit'],
    ]);
});
