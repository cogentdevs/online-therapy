<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create([
        'name' => 'Ali Raza',
        'email' => 'ali@example.com',
        'phone' => '03001234567',
        'password' => 'unchanged-password',
        'profile_image' => 'user-avatar.png',
        'is_active' => true,
    ]);
    $this->user->assignRole('user');
    $this->createdProfileImages = [];
});

afterEach(function () {
    foreach ($this->createdProfileImages as $filename) {
        if ($filename !== 'user-avatar.png') {
            File::delete(public_path('images/frontend-images/users/'.$filename));
        }
    }
});

test('guest is redirected from profile page and update', function (string $method) {
    $response = $method === 'get'
        ? $this->get(route('front.account.profile'))
        : $this->patch(route('front.account.profile.update'));
    $response->assertRedirect(route('front.login'));
})->with(['get', 'patch']);

test('active frontend user sees own details and active profile sidebar', function () {
    $this->actingAs($this->user)->get(route('front.account.profile'))
        ->assertOk()->assertViewIs('frontend.user-account.profile')
        ->assertSee($this->user->name)->assertSee($this->user->email)->assertSee($this->user->phone)
        ->assertSee('name="phone"', false)
        ->assertSee('href="'.route('front.account.profile').'"', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('action="'.route('front.account.profile.update').'"', false)
        ->assertDontSee('class="front-account-image-remove"', false);
});

test('remove option only appears when a real custom profile image exists', function () {
    $customFilename = 'visible-custom-profile.png';
    File::put(public_path('images/frontend-images/users/'.$customFilename), 'custom');
    $this->createdProfileImages[] = $customFilename;
    $this->user->update(['profile_image' => $customFilename]);

    $this->actingAs($this->user)->get(route('front.account.profile'))
        ->assertOk()->assertSee('class="front-account-image-remove"', false);

    $this->user->update(['profile_image' => 'missing-custom-profile.png']);
    $this->actingAs($this->user)->get(route('front.account.profile'))
        ->assertOk()->assertDontSee('class="front-account-image-remove"', false);
});

test('inactive and non frontend users cannot access profile', function (string $kind) {
    if ($kind === 'inactive') {
        $this->user->update(['is_active' => false]);
        $this->actingAs($this->user)->get(route('front.account.profile'))->assertRedirect(route('front.login'));
        $this->assertGuest('web');

        return;
    }
    $this->user->syncRoles(Role::findOrCreate('super-admin', 'web'));
    $this->actingAs($this->user)->get(route('front.account.profile'))->assertForbidden();
})->with(['inactive', 'admin']);

test('name email and phone update while security fields remain unchanged', function () {
    $password = $this->user->password;
    $roles = $this->user->getRoleNames()->all();
    $this->actingAs($this->user)->patch(route('front.account.profile.update'), [
        'name' => 'Updated Reader',
        'email' => 'updated@example.com',
        'phone' => '03123456789',
    ])->assertRedirect(route('front.account.profile'))->assertSessionHas('success');

    $freshUser = $this->user->fresh();
    expect($freshUser->name)->toBe('Updated Reader')
        ->and($freshUser->email)->toBe('updated@example.com')
        ->and($freshUser->phone)->toBe('03123456789')
        ->and($freshUser->password)->toBe($password)
        ->and($freshUser->is_active)->toBeTrue()
        ->and($freshUser->getRoleNames()->all())->toBe($roles);
});

test('email must be unique except for current user', function () {
    User::factory()->create(['email' => 'taken@example.com']);
    $this->actingAs($this->user)->patch(route('front.account.profile.update'), [
        'name' => $this->user->name, 'email' => 'taken@example.com', 'phone' => $this->user->phone,
    ])->assertSessionHasErrors('email');
    $this->actingAs($this->user)->patch(route('front.account.profile.update'), [
        'name' => $this->user->name, 'email' => $this->user->email, 'phone' => $this->user->phone,
    ])->assertSessionHasNoErrors();
});

test('valid image stores safe filename and dashboard displays it', function () {
    $this->actingAs($this->user)->patch(route('front.account.profile.update'), [
        'name' => $this->user->name,
        'email' => $this->user->email,
        'phone' => $this->user->phone,
        'profile_image' => UploadedFile::fake()->image('unsafe original.png', 200, 200),
    ])->assertSessionHasNoErrors();

    $filename = $this->user->fresh()->profile_image;
    $this->createdProfileImages[] = $filename;
    expect($filename)->toMatch('/^user-\d+-[A-Za-z0-9]{20}\.png$/')
        ->and(str_contains($filename, '/'))->toBeFalse()
        ->and(File::exists(public_path('images/frontend-images/users/'.$filename)))->toBeTrue();
    $this->actingAs($this->user)->get(route('front.account'))->assertSee('images/frontend-images/users/'.$filename, false);
});

test('replacing custom image deletes old image and preserves default', function () {
    $oldFilename = 'old-custom-profile.png';
    $oldPath = public_path('images/frontend-images/users/'.$oldFilename);
    File::put($oldPath, 'old image');
    $this->createdProfileImages[] = $oldFilename;
    $this->user->update(['profile_image' => $oldFilename]);

    $this->actingAs($this->user)->patch(route('front.account.profile.update'), [
        'name' => $this->user->name, 'email' => $this->user->email, 'phone' => $this->user->phone,
        'profile_image' => UploadedFile::fake()->image('replacement.webp'),
    ])->assertSessionHasNoErrors();
    $newFilename = $this->user->fresh()->profile_image;
    $this->createdProfileImages[] = $newFilename;

    expect(File::exists($oldPath))->toBeFalse()
        ->and(File::exists(public_path('images/frontend-images/users/'.$newFilename)))->toBeTrue()
        ->and(File::exists(public_path('images/frontend-images/users/user-avatar.png')))->toBeTrue();
});

test('removing custom image deletes it and restores protected default', function () {
    $customFilename = 'remove-this-profile.png';
    File::put(public_path('images/frontend-images/users/'.$customFilename), 'custom');
    $this->createdProfileImages[] = $customFilename;
    $this->user->update(['profile_image' => $customFilename]);

    $this->actingAs($this->user)->patch(route('front.account.profile.update'), [
        'name' => $this->user->name, 'email' => $this->user->email, 'phone' => $this->user->phone, 'remove_profile_image' => '1',
    ])->assertSessionHasNoErrors();

    expect($this->user->fresh()->profile_image)->toBe('user-avatar.png')
        ->and(File::exists(public_path('images/frontend-images/users/'.$customFilename)))->toBeFalse()
        ->and(File::exists(public_path('images/frontend-images/users/user-avatar.png')))->toBeTrue();
});

test('null and missing profile images fall back to default avatar', function (string $value) {
    $this->user->update(['profile_image' => $value === 'null' ? null : 'missing-profile.png']);
    $this->actingAs($this->user)->get(route('front.account'))
        ->assertSee('images/frontend-images/users/user-avatar.png', false);
})->with(['null', 'missing']);

test('invalid upload is rejected without changing the profile', function () {
    $this->actingAs($this->user)->patch(route('front.account.profile.update'), [
        'name' => 'Changed', 'email' => 'changed@example.com', 'phone' => $this->user->phone,
        'profile_image' => UploadedFile::fake()->create('document.pdf', 20, 'application/pdf'),
    ])->assertSessionHasErrors('profile_image');

    expect($this->user->fresh()->name)->toBe('Ali Raza')
        ->and(Hash::check('unchanged-password', $this->user->fresh()->password))->toBeTrue();
});

test('phone is required and limited to the existing maximum length', function () {
    $this->actingAs($this->user)->patch(route('front.account.profile.update'), [
        'name' => $this->user->name, 'email' => $this->user->email, 'phone' => '',
    ])->assertSessionHasErrors('phone');
    $this->actingAs($this->user)->patch(route('front.account.profile.update'), [
        'name' => $this->user->name, 'email' => $this->user->email, 'phone' => str_repeat('1', 256),
    ])->assertSessionHasErrors('phone');
});
