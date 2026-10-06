<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(LazilyRefreshDatabase::class);

test('frontend register page renders its prepared fields and navigation', function () {
    $this->withoutVite();

    $response = $this->get(route('front.register'));

    $response->assertSuccessful()
        ->assertSee('رجسٹریشن')
        ->assertSee('name="first_name"', false)
        ->assertSee('name="last_name"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="phone"', false)
        ->assertSee('name="password"', false)
        ->assertSee('name="password_confirmation"', false)
        ->assertSee('g-recaptcha', false)
        ->assertSee('aria-controls="register-password"', false)
        ->assertSee('aria-controls="register-password-confirmation"', false)
        ->assertSee(route('front.login'), false);
});

test('frontend login page renders prepared controls and cross links', function () {
    $this->withoutVite();

    $response = $this->get(route('front.login'));

    $response->assertSuccessful()
        ->assertSee('لاگ ان کریں')
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false)
        ->assertSee('name="remember"', false)
        ->assertSee('aria-controls="login-password"', false)
        ->assertSee('پاس ورڈ بھول گئے؟')
        ->assertSee(route('front.register'), false);
});

test('frontend auth exposes its existing view and submission routes', function () {
    expect(Route::getRoutes()->match(request()->create('/register', 'GET'))->getName())->toBe('front.register')
        ->and(Route::getRoutes()->match(request()->create('/login', 'GET'))->getName())->toBe('front.login');

    expect(Route::getRoutes()->match(request()->create('/register', 'POST'))->getName())->toBe('front.register.store')
        ->and(Route::getRoutes()->match(request()->create('/login', 'POST'))->getName())->toBe('front.login.store');
});
