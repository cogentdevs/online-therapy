<?php

use App\Mail\WelcomeMailToUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Role::findOrCreate('user', 'web');
    $this->token = Str::random(64);
    $this->pendingUser = User::factory()->create([
        'is_active' => false,
        'activation_token' => hash('sha256', $this->token),
        'activation_token_expires_at' => now()->addDay(),
    ]);
    $this->pendingUser->assignRole('user');
    $this->activationUrl = route('front.account.activation', ['token' => $this->token]);
});

test('valid activation GET is read only and displays the confirmation form', function () {
    $this->get($this->activationUrl)->assertOk()
        ->assertViewIs('frontend.user-activate-account')
        ->assertViewHas('state', 'pending')
        ->assertSee('اکاؤنٹ ایکٹیویٹ کریں')
        ->assertSee('name="_token"', false)
        ->assertHeader('Referrer-Policy', 'no-referrer');
    expect($this->pendingUser->fresh()->is_active)->toBeFalse();
    Mail::assertNothingOutgoing();
    $this->assertGuest();
});

test('activation clears credentials sends welcome once and redirects with login flash', function () {
    $this->post($this->activationUrl)->assertRedirect(route('front.login'))
        ->assertSessionHas('success', 'آپ کا اکاؤنٹ کامیابی سے ایکٹیویٹ ہو گیا ہے۔ اب آپ اپنے ای میل اور پاس ورڈ کے ذریعے لاگ ان کر سکتے ہیں۔');
    $user = $this->pendingUser->fresh();
    expect($user->is_active)->toBeTrue()
        ->and($user->activation_token)->toBeNull()
        ->and($user->activation_token_expires_at)->toBeNull()
        ->and($user->consumed_activation_token_hash)->toBe(hash('sha256', $this->token))
        ->and($user->toArray())->not->toHaveKey('consumed_activation_token_hash');
    Mail::assertSent(WelcomeMailToUser::class, fn ($mail) => $mail->hasTo($user->email));
    Mail::assertSentCount(1);
    $this->get(route('front.login'))->assertOk()->assertSee('آپ کا اکاؤنٹ کامیابی سے ایکٹیویٹ ہو گیا ہے۔');
    $this->assertGuest();

    $updatedAt = $user->updated_at;
    $this->travel(2)->days();
    $this->get($this->activationUrl)->assertOk()->assertViewHas('state', 'active')
        ->assertSee('آپ کا اکاؤنٹ پہلے ہی ایکٹیویٹ ہے۔')
        ->assertSee('لاگ ان کریں')->assertDontSee('type="submit"', false);
    $this->post($this->activationUrl)->assertOk()->assertViewHas('state', 'active');
    expect($user->fresh()->updated_at->equalTo($updatedAt))->toBeTrue();
    Mail::assertSentCount(1);
    $this->assertGuest();
});

test('already active account never sends welcome', function () {
    $this->pendingUser->update(['is_active' => true]);
    $this->get($this->activationUrl)->assertOk()->assertViewHas('state', 'active');
    $this->post($this->activationUrl)->assertOk()->assertViewHas('state', 'active');
    Mail::assertNothingOutgoing();
});

test('expired or missing expiry prevents activation', function (string $expiry) {
    $this->pendingUser->activation_token_expires_at = match ($expiry) {
        'past' => now()->subSecond(),
        'now' => now(),
        default => null,
    };
    $this->pendingUser->save();
    $this->get($this->activationUrl)->assertOk()->assertViewHas('state', 'expired')
        ->assertSee('یہ اکاؤنٹ ایکٹیویشن لنک ایکسپائر ہو چکا ہے۔')
        ->assertDontSee('type="submit"', false);
    $this->post($this->activationUrl)->assertOk()->assertViewHas('state', 'expired');
    expect($this->pendingUser->fresh()->is_active)->toBeFalse();
    Mail::assertNothingOutgoing();
})->with(['past', 'now', 'missing']);

test('invalid tokens receive a generic state without account details', function (mixed $token) {
    $url = route('front.account.activation', ['token' => $token]);
    foreach (['get', 'post'] as $method) {
        $this->{$method}($url)->assertOk()->assertViewHas('state', 'invalid')
            ->assertSee('یہ اکاؤنٹ ایکٹیویشن لنک درست نہیں ہے۔')
            ->assertDontSee($this->pendingUser->email)->assertDontSee($this->pendingUser->name)
            ->assertDontSee('type="submit"', false);
    }
    expect($this->pendingUser->fresh()->is_active)->toBeFalse();
    Mail::assertNothingOutgoing();
})->with([null, '', 'invalid', str_repeat('z', 64), ['nested']]);

test('POST checks expiry again after a valid GET', function () {
    $this->get($this->activationUrl)->assertViewHas('state', 'pending');
    $this->travel(2)->days();
    $this->post($this->activationUrl)->assertViewHas('state', 'expired');
    expect($this->pendingUser->fresh()->is_active)->toBeFalse();
    Mail::assertNothingOutgoing();
});

test('a consumed token cannot reactivate a subsequently disabled account', function () {
    $this->post($this->activationUrl)->assertRedirect(route('front.login'));
    $this->pendingUser->refresh()->update(['is_active' => false]);
    $this->post($this->activationUrl)->assertViewHas('state', 'invalid');
    expect($this->pendingUser->fresh()->is_active)->toBeFalse();
    Mail::assertSentCount(1);
});

test('activation requires the existing frontend user role', function () {
    $this->pendingUser->syncRoles([]);
    $this->post($this->activationUrl)->assertViewHas('state', 'invalid');
    expect($this->pendingUser->fresh()->is_active)->toBeFalse();
    Mail::assertNothingOutgoing();
});

test('welcome uses the shared mail layout and excludes credentials', function () {
    $mail = new WelcomeMailToUser($this->pendingUser);
    $mail->assertSeeInHtml($this->pendingUser->name)
        ->assertSeeInHtml('Digital Magazine')
        ->assertSeeInHtml(route('front.login'))
        ->assertSeeInHtml('لاگ ان کریں')
        ->assertDontSeeInHtml($this->pendingUser->password)
        ->assertDontSeeInHtml($this->token)
        ->assertDontSeeInHtml($this->pendingUser->activation_token);
});

test('stored hash and malformed array cannot be used as activation tokens', function () {
    foreach ([$this->pendingUser->activation_token, ['nested' => $this->token]] as $token) {
        $this->post(route('front.account.activation.store', ['token' => $token]))
            ->assertOk()->assertViewHas('state', 'invalid');
    }
    expect($this->pendingUser->fresh()->is_active)->toBeFalse();
    Mail::assertNothingOutgoing();
});

test('activation POST requires CSRF outside the test bypass', function () {
    $this->app->instance('env', 'local');
    $this->post($this->activationUrl)->assertStatus(419);
    expect($this->pendingUser->fresh()->is_active)->toBeFalse();
    Mail::assertNothingOutgoing();
});
