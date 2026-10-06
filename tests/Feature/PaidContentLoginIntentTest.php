<?php

use App\Mail\TwoFactorOtpMailToUser;
use App\Models\Article;
use App\Models\Currency;
use App\Models\Magazine;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create([
        'email' => 'paid-reader@example.com',
        'password' => 'password123',
        'is_active' => true,
    ]);
    $this->user->assignRole('user');
    $this->article = Article::query()->forceCreate([
        'language' => 'ur', 'title' => 'Paid Login Article', 'publish_date' => now()->toDateString(),
        'published_at' => now(), 'article' => 'Subscriber-only body', 'isFree' => false,
        'isActive' => true, 'status' => Article::STATUS_PUBLISHED,
    ]);
    $this->articleUrl = route('mazmoon-detail', [$this->article->id, 'paid-login-article']);
});

function grantPaidLoginArticleEntitlement(User $user): void
{
    $currency = Currency::query()->create(['name' => 'Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true]);
    $type = SubscriptionType::query()->create(['name' => 'Articles', 'slug' => 'articles', 'isActive' => true]);
    $product = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN, 'name' => 'Article Plan',
        'currency_id' => $currency->id, 'price' => 100, 'duration_value' => 1,
        'duration_unit' => 'month', 'isActive' => true,
    ]);
    $subscription = UserSubscription::query()->create([
        'user_id' => $user->id, 'subscription_product_id' => $product->id,
        'product_for' => $product->product_for, 'product_name' => $product->name,
        'currency_id' => $currency->id, 'price' => 100, 'discount' => 0, 'total' => 100,
        'start_date' => today(), 'end_date' => today()->addMonth(), 'status' => 'active', 'is_active' => true,
    ]);
    $subscription->subscriptionTypes()->attach($type);
}

test('direct paid article stores exact destination and home auto opens shared modal', function () {
    $this->get($this->articleUrl)
        ->assertRedirect(route('frontend.home'))
        ->assertSessionHas('front_paid_content_intended_url', $this->articleUrl)
        ->assertSessionHas('show_front_login_modal', true);

    $this->get(route('frontend.home'))->assertOk()
        ->assertSee('data-subscription-login-modal', false)
        ->assertSee('data-auto-open="true"', false)
        ->assertDontSee('Subscriber-only body');
});

test('ajax login returns exact paid article then chunk six decides entitlement', function (bool $entitled) {
    if ($entitled) {
        grantPaidLoginArticleEntitlement($this->user);
    }

    $this->get($this->articleUrl)->assertRedirect(route('frontend.home'));
    $this->postJson(route('front.login.store'), [
        'email' => $this->user->email,
        'password' => 'password123',
        'login_source' => 'homepage',
    ])->assertOk()->assertJsonPath('redirect', $this->articleUrl);

    $result = $this->get($this->articleUrl);
    $entitled
        ? $result->assertOk()->assertSee('Subscriber-only body')
        : $result->assertRedirect(route('front.subscriptions'))->assertSessionHas('warning');
})->with([true, false]);

test('magazine guest login preserves the exact protected endpoint', function (string $routeName) {
    $magazine = Magazine::query()->forceCreate([
        'language' => 'ur', 'title' => 'Paid Login Magazine', 'publish_date' => now()->toDateString(),
        'published_at' => now(), 'description' => 'Subscriber-only magazine', 'isFree' => false,
        'is_downloadable' => true, 'isActive' => true, 'status' => Magazine::STATUS_PUBLISHED,
    ]);
    $destination = route($routeName, [$magazine->id, 'paid-login-magazine']);

    $this->get($destination)->assertRedirect(route('frontend.home'))
        ->assertSessionHas('front_paid_content_intended_url', $destination);
    $this->postJson(route('front.login.store'), [
        'email' => $this->user->email,
        'password' => 'password123',
    ])->assertOk()->assertJsonPath('redirect', $destination);
})->with(['shumara-detail', 'shumara-detail.pdf', 'shumara-detail.download']);

test('free article creates no login intent', function () {
    $this->article->update(['isFree' => true]);

    $this->get($this->articleUrl)->assertOk();
    expect(session()->has('front_paid_content_intended_url'))->toBeFalse()
        ->and(session()->has('show_front_login_modal'))->toBeFalse();
});

test('modal cancel clears paid content intent only', function () {
    $this->withSession([
        'front_paid_content_intended_url' => $this->articleUrl,
        'front_subscription_product_id' => 42,
        'unrelated' => 'preserved',
    ])->deleteJson(route('front.paid-content.intent.clear'))->assertOk()->assertJson(['cleared' => true]);

    expect(session()->has('front_paid_content_intended_url'))->toBeFalse()
        ->and(session('front_subscription_product_id'))->toBe(42)
        ->and(session('unrelated'))->toBe('preserved');
});

test('subscription checkout intent takes precedence over paid content intent', function () {
    $currency = Currency::query()->create(['name' => 'Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true]);
    $type = SubscriptionType::query()->create(['name' => 'Magazine', 'slug' => 'magazine', 'isActive' => true]);
    $product = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_PLAN, 'name' => 'Priority Plan',
        'currency_id' => $currency->id, 'price' => 100, 'duration_value' => 1,
        'duration_unit' => 'month', 'isActive' => true,
    ]);
    $product->subscriptionTypes()->attach($type);

    $this->withSession([
        'front_subscription_product_id' => $product->id,
        'front_paid_content_intended_url' => $this->articleUrl,
    ])->postJson(route('front.login.store'), [
        'email' => $this->user->email,
        'password' => 'password123',
    ])->assertOk()->assertJsonPath('redirect', route('front.subscriptions.checkout', $product));
});

test('paid content destination survives existing two factor login', function () {
    Mail::fake();
    $this->user->twoFactorSetting()->create(['method' => 'email', 'is_enabled' => true, 'verified_at' => now()]);
    $this->get($this->articleUrl)->assertRedirect(route('frontend.home'));

    $this->postJson(route('front.login.store'), [
        'email' => $this->user->email,
        'password' => 'password123',
    ])->assertOk()->assertJsonPath('redirect', route('front.two-factor.challenge'));

    expect(session('front_two_factor_login.intended'))->toBe($this->articleUrl);
    Mail::assertSent(TwoFactorOtpMailToUser::class);
});

test('injected external paid content destination is rejected', function () {
    $this->withSession(['front_paid_content_intended_url' => 'https://evil-site.example/paid'])
        ->postJson(route('front.login.store'), [
            'email' => $this->user->email,
            'password' => 'password123',
        ])->assertOk()->assertJsonPath('redirect', route('front.account'));

    expect(session()->has('front_paid_content_intended_url'))->toBeFalse();
});
