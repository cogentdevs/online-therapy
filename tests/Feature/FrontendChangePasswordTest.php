<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create([
        'password' => 'current-password',
        'remember_token' => 'old-remember-token',
        'is_active' => true,
    ]);
    $this->user->assignRole('user');
});

test('guest is redirected from change password page and submission', function (string $method) {
    $response = $method === 'get'
        ? $this->get(route('front.account.change-password'))
        : $this->post(route('front.account.change-password.update'));

    $response->assertRedirect(route('front.login'));
})->with(['get', 'post']);

test('active exact frontend user sees page shared sidebar and correct active item', function () {
    $this->actingAs($this->user)->get(route('front.account.change-password'))
        ->assertOk()
        ->assertViewIs('frontend.user-account.change-password')
        ->assertSee('action="'.route('front.account.change-password.update').'"', false)
        ->assertSee('href="'.route('front.account.change-password').'"', false)
        ->assertSee('data-front-change-password-form', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('action="'.route('front.logout').'"', false);
});

test('inactive and non frontend users cannot access change password', function (string $kind) {
    if ($kind === 'inactive') {
        $this->user->update(['is_active' => false]);
        $this->actingAs($this->user)->get(route('front.account.change-password'))
            ->assertRedirect(route('front.login'));
        $this->assertGuest('web');

        return;
    }

    $this->user->syncRoles(Role::findOrCreate('super-admin', 'web'));
    $this->actingAs($this->user)->get(route('front.account.change-password'))->assertForbidden();
})->with(['inactive', 'admin']);

test('correct current password changes hash rotates remember token and keeps session', function () {
    $oldRememberToken = $this->user->remember_token;
    $this->actingAs($this->user)->post(route('front.account.change-password.update'), [
        'current_password' => 'current-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertRedirect(route('front.account.change-password'))
        ->assertSessionHas('success', 'آپ کا پاس ورڈ کامیابی سے تبدیل ہو گیا ہے۔');

    $this->assertAuthenticatedAs($this->user, 'web');
    $freshUser = $this->user->fresh();
    expect(Hash::check('new-password', $freshUser->password))->toBeTrue()
        ->and(Hash::check('current-password', $freshUser->password))->toBeFalse()
        ->and($freshUser->remember_token)->not->toBe($oldRememberToken);
});

test('wrong current password does not change password', function () {
    $this->actingAs($this->user)->post(route('front.account.change-password.update'), [
        'current_password' => 'incorrect-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertSessionHasErrors(['current_password' => 'موجودہ پاس ورڈ درست نہیں ہے۔']);

    expect(Hash::check('current-password', $this->user->fresh()->password))->toBeTrue();
});

test('new password minimum confirmation and difference rules are enforced', function (array $data, string $message) {
    $this->actingAs($this->user)->post(route('front.account.change-password.update'), $data)
        ->assertSessionHasErrors(['password' => $message]);

    expect(Hash::check('current-password', $this->user->fresh()->password))->toBeTrue();
})->with([
    'minimum' => [[
        'current_password' => 'current-password',
        'password' => 'short',
        'password_confirmation' => 'short',
    ], 'نیا پاس ورڈ کم از کم 8 حروف پر مشتمل ہونا چاہیے۔'],
    'confirmation' => [[
        'current_password' => 'current-password',
        'password' => 'new-password',
        'password_confirmation' => 'different-password',
    ], 'نئے پاس ورڈ کی تصدیق مطابقت نہیں رکھتی۔'],
    'different' => [[
        'current_password' => 'current-password',
        'password' => 'current-password',
        'password_confirmation' => 'current-password',
    ], 'نیا پاس ورڈ موجودہ پاس ورڈ سے مختلف ہونا چاہیے۔'],
]);

test('old login fails and new login succeeds after changing password', function () {
    $this->actingAs($this->user)->post(route('front.account.change-password.update'), [
        'current_password' => 'current-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);
    auth('web')->logout();

    $this->post(route('front.login.store'), ['email' => $this->user->email, 'password' => 'current-password'])
        ->assertSessionHasErrors('email');
    $this->post(route('front.login.store'), ['email' => $this->user->email, 'password' => 'new-password'])
        ->assertRedirect(route('front.account'));
    $this->assertAuthenticatedAs($this->user, 'web');
});
