<?php

use App\Models\Article;
use App\Models\Currency;
use App\Models\Magazine;
use App\Models\SubscriptionProduct;
use App\Models\SubscriptionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create([
        'email' => 'return-reader@example.com', 'password' => 'password123', 'is_active' => true,
    ]);
    $this->user->assignRole('user');
    $this->currency = Currency::query()->create([
        'name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true,
    ]);
    $this->article = Article::query()->forceCreate([
        'language' => 'ur', 'title' => 'Return Article', 'publish_date' => today(), 'published_at' => now(),
        'article' => 'Return journey protected body', 'isFree' => false, 'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]);
    $this->articleUrl = route('mazmoon-detail', [$this->article->id, 'return-article']);
});

function returnFlowProduct(Currency $currency, array $moduleNames, string $productFor = SubscriptionProduct::FOR_PLAN): SubscriptionProduct
{
    $product = SubscriptionProduct::query()->create([
        'product_for' => $productFor,
        'name' => $productFor === SubscriptionProduct::FOR_MEMBERSHIP ? 'Return Membership' : 'Return Plan',
        'currency_id' => $currency->id, 'price' => 500, 'duration_value' => 1,
        'duration_unit' => 'month', 'isActive' => true,
    ]);

    foreach ($moduleNames as $index => $moduleName) {
        $type = SubscriptionType::query()->create([
            'name' => $moduleName, 'slug' => strtolower($moduleName).'-return-'.$index, 'isActive' => true,
        ]);
        $product->subscriptionTypes()->attach($type);
    }

    return $product;
}

test('article plan purchase returns to exact article and successful access consumes intent', function () {
    $product = returnFlowProduct($this->currency, ['Articles']);

    $this->actingAs($this->user)->get($this->articleUrl)
        ->assertRedirect(route('front.subscriptions'))
        ->assertSessionHas('front_subscription_return_url', $this->articleUrl);
    $this->get(route('front.subscriptions'))->assertOk();
    $this->get(route('front.subscriptions.checkout', $product))->assertOk();

    $this->post(route('front.subscriptions.checkout.store', $product), ['payment_method' => 'card'])
        ->assertRedirect($this->articleUrl);
    expect(session('front_subscription_return_url'))->toBe($this->articleUrl);

    $this->get($this->articleUrl)->assertOk()->assertSee('Return journey protected body');
    expect(session()->has('front_subscription_return_url'))->toBeFalse();
});

test('membership containing articles completes article return journey', function () {
    $membership = returnFlowProduct($this->currency, ['Magazine', 'Articles'], SubscriptionProduct::FOR_MEMBERSHIP);

    $this->actingAs($this->user)->get($this->articleUrl)->assertRedirect(route('front.subscriptions'));
    $this->post(route('front.subscriptions.checkout.store', $membership), ['payment_method' => 'easypaisa'])
        ->assertRedirect($this->articleUrl);
    $this->get($this->articleUrl)->assertOk();
});

test('wrong module purchase retries article and preserves journey after denial', function () {
    $magazinePlan = returnFlowProduct($this->currency, ['Magazine']);

    $this->actingAs($this->user)->get($this->articleUrl)->assertRedirect(route('front.subscriptions'));
    $this->post(route('front.subscriptions.checkout.store', $magazinePlan), ['payment_method' => 'jazzcash'])
        ->assertRedirect($this->articleUrl);
    $this->get($this->articleUrl)->assertRedirect(route('front.subscriptions'));
    expect(session('front_subscription_return_url'))->toBe($this->articleUrl);
});

test('magazine detail viewer and download keep their exact return endpoints', function (string $routeName) {
    $magazine = Magazine::query()->forceCreate([
        'language' => 'ur', 'title' => 'Return Magazine', 'publish_date' => today(), 'published_at' => now(),
        'description' => 'Protected magazine', 'isFree' => false, 'is_downloadable' => true,
        'isActive' => true, 'status' => Magazine::STATUS_PUBLISHED,
    ]);
    $destination = route($routeName, [$magazine->id, 'return-magazine']);
    $product = returnFlowProduct($this->currency, ['Magazine']);

    $this->actingAs($this->user)->get($destination)->assertRedirect(route('front.subscriptions'))
        ->assertSessionHas('front_subscription_return_url', $destination);
    $this->post(route('front.subscriptions.checkout.store', $product), ['payment_method' => 'card'])
        ->assertRedirect($destination);

    if ($routeName === 'shumara-detail') {
        $this->get($destination)->assertOk();
        expect(session()->has('front_subscription_return_url'))->toBeFalse();
    }
})->with(['shumara-detail', 'shumara-detail.pdf', 'shumara-detail.download']);

test('failed checkout keeps return intent and creates no purchase', function () {
    $product = returnFlowProduct($this->currency, ['Articles']);

    $this->actingAs($this->user)->get($this->articleUrl)->assertRedirect(route('front.subscriptions'));
    $this->from(route('front.subscriptions.checkout', $product))
        ->post(route('front.subscriptions.checkout.store', $product), ['payment_method' => 'cash'])
        ->assertRedirect(route('front.subscriptions.checkout', $product))
        ->assertSessionHasErrors('payment_method');

    expect(session('front_subscription_return_url'))->toBe($this->articleUrl);
    $this->assertDatabaseEmpty('user_subscriptions');
});

test('manual purchase retains existing checkout success redirect', function () {
    $product = returnFlowProduct($this->currency, ['Articles']);

    $this->actingAs($this->user)
        ->post(route('front.subscriptions.checkout.store', $product), ['payment_method' => 'card'])
        ->assertRedirect(route('front.subscriptions.checkout', $product))
        ->assertSessionHas('success');
});

test('external purchase return destination is rejected after successful purchase', function () {
    $product = returnFlowProduct($this->currency, ['Articles']);

    $this->actingAs($this->user)
        ->withSession(['front_subscription_return_url' => 'https://evil.example/paid'])
        ->post(route('front.subscriptions.checkout.store', $product), ['payment_method' => 'card'])
        ->assertRedirect(route('front.subscriptions.checkout', $product));

    expect(session()->has('front_subscription_return_url'))->toBeFalse();
});

test('free and already entitled content do not create purchase return intent', function () {
    $this->article->update(['isFree' => true]);
    $this->actingAs($this->user)->get($this->articleUrl)->assertOk();
    expect(session()->has('front_subscription_return_url'))->toBeFalse();

    $this->article->update(['isFree' => false]);
    $product = returnFlowProduct($this->currency, ['Articles']);
    $this->post(route('front.subscriptions.checkout.store', $product), ['payment_method' => 'card']);
    $this->get($this->articleUrl)->assertOk();
    expect(session()->has('front_subscription_return_url'))->toBeFalse();
});

test('guest login and purchase preserve the article across both transitions', function () {
    $product = returnFlowProduct($this->currency, ['Articles']);

    $this->get($this->articleUrl)->assertRedirect(route('frontend.home'));
    $this->postJson(route('front.login.store'), [
        'email' => $this->user->email, 'password' => 'password123',
    ])->assertOk()->assertJsonPath('redirect', $this->articleUrl);
    $this->get($this->articleUrl)->assertRedirect(route('front.subscriptions'));
    $this->post(route('front.subscriptions.checkout.store', $product), ['payment_method' => 'card'])
        ->assertRedirect($this->articleUrl);
    $this->get($this->articleUrl)->assertOk();
});

test('logout invalidates the pending purchase return session', function () {
    $this->actingAs($this->user)->withSession(['front_subscription_return_url' => $this->articleUrl]);

    $this->post(route('front.logout'))->assertRedirect(route('frontend.home'));
    expect(session()->has('front_subscription_return_url'))->toBeFalse();
});
