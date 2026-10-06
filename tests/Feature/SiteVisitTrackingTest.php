<?php

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\SiteVisit;
use App\Models\User;
use App\Models\UserContentVisit;
use App\Services\SiteVisitService;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function analyticsRequest(string $routeName, ?User $user = null, string $ip = '203.0.113.10'): Request
{
    $route = Route::getRoutes()->getByName($routeName);
    $request = Request::create($route?->uri() === '/' ? '/' : '/analytics-test', 'GET', server: [
        'REMOTE_ADDR' => $ip,
        'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0) Chrome/140.0.0.0',
    ]);
    $request->setRouteResolver(fn () => $route);
    $request->setUserResolver(fn () => $user);

    return $request;
}

test('static guest and authenticated visits are stored independently', function () {
    $service = app(SiteVisitService::class);
    $user = User::factory()->create();

    $service->record(analyticsRequest('frontend.home'));
    $service->record(analyticsRequest('frontend.home', $user));

    expect(SiteVisit::query()->count())->toBe(2)
        ->and(SiteVisit::query()->whereNull('user_id')->count())->toBe(1)
        ->and(SiteVisit::query()->where('user_id', $user->id)->count())->toBe(1)
        ->and(SiteVisit::query()->whereNotNull('visitable_type')->count())->toBe(0);
});

test('dynamic targets and model relationships resolve', function () {
    $article = Article::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Analytics article',
        'publish_date' => today(),
        'article' => 'Body',
        'isFree' => true,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Analytics', 'isActive' => true]);
    $author = Author::query()->forceCreate(['name' => 'Analytics Author', 'isActive' => true]);
    $magazine = Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Analytics magazine',
        'publish_date' => today(),
        'description' => 'Body',
        'isFree' => true,
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
    ]);
    $service = app(SiteVisitService::class);

    $service->record(analyticsRequest('mazmoon-detail'), $article);
    $service->record(analyticsRequest('mozu-detail'), $category);
    $service->record(analyticsRequest('front.mazmoon-nigaar.detail'), $author);
    $service->record(analyticsRequest('shumara-detail'), $magazine);

    $articleVisit = SiteVisit::query()->where('page_key', 'mazmoon-detail')->firstOrFail();
    expect($articleVisit->visitable())->toBeInstanceOf(MorphTo::class)
        ->and($articleVisit->visitable->is($article))->toBeTrue()
        ->and($article->siteVisits()->count())->toBe(1)
        ->and($category->siteVisits()->count())->toBe(1)
        ->and($author->siteVisits()->count())->toBe(1)
        ->and($magazine->siteVisits()->count())->toBe(1);
});

test('every page load creates a separate analytics visit row', function () {
    $request = analyticsRequest('frontend.home');
    $service = app(SiteVisitService::class);

    $service->record($request);
    $service->record($request);

    expect(SiteVisit::query()->count())->toBe(2);
});

test('successful logged in article detail records site analytics and personal recent activity independently', function () {
    Role::findOrCreate('user', 'web');
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole('user');
    $article = Article::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Two independent systems',
        'publish_date' => today(),
        'published_at' => now(),
        'article' => 'Body',
        'isFree' => true,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]);

    $this->actingAs($user)
        ->get(route('mazmoon-detail', [$article->id, 'two-systems']))
        ->assertOk();

    expect(SiteVisit::query()->where('page_key', 'mazmoon-detail')->count())->toBe(1)
        ->and(UserContentVisit::query()->where('user_id', $user->id)->count())->toBe(1);
});

test('guest article detail records only site analytics', function () {
    $article = Article::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Guest analytics',
        'publish_date' => today(),
        'published_at' => now(),
        'article' => 'Body',
        'isFree' => true,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]);

    $this->get(route('mazmoon-detail', [$article->id, 'guest-analytics']))->assertOk();

    expect(SiteVisit::query()->whereNull('user_id')->count())->toBe(1)
        ->and(UserContentVisit::query()->count())->toBe(0);
});

test('non allowlisted and non get requests are ignored', function () {
    $service = app(SiteVisitService::class);
    $loginRequest = analyticsRequest('front.login');
    $postRequest = analyticsRequest('frontend.home');
    $postRequest->setMethod('POST');

    $service->record($loginRequest);
    $service->record($postRequest);

    expect(SiteVisit::query()->count())->toBe(0);
});

test('geoip failure leaves location nullable without preventing visit creation', function () {
    app()->instance('geoip', new class
    {
        public function getLocation(string $ip): never
        {
            throw new RuntimeException('Provider unavailable');
        }
    });

    app(SiteVisitService::class)->record(analyticsRequest('frontend.home', ip: '127.0.0.1'));

    $visit = SiteVisit::query()->sole();
    expect($visit->ip_address)->toBe('127.0.0.1')
        ->and($visit->country)->toBeNull()
        ->and($visit->city)->toBeNull();
});

test('deleting a user preserves analytics and nulls its user relation', function () {
    $user = User::factory()->create();
    app(SiteVisitService::class)->record(analyticsRequest('frontend.home', $user));

    $user->delete();

    expect(SiteVisit::query()->sole()->user_id)->toBeNull();
});

test('heartbeat requires the opaque token visitor cookie and matching user ownership', function () {
    $user = User::factory()->create();
    $visitorId = (string) Str::uuid();
    $token = Str::random(64);
    $visit = SiteVisit::query()->create([
        'user_id' => $user->id,
        'visitor_id' => $visitorId,
        'public_token_hash' => hash('sha256', $token),
        'page_key' => 'home',
        'started_at' => now(),
    ]);

    $this->actingAs($user)
        ->withUnencryptedCookie(SiteVisitService::VISITOR_COOKIE, $visitorId)
        ->postJson(route('front.site-visits.heartbeat'), [
            'token' => $token,
            'active_seconds' => 20,
        ])->assertOk();

    expect($visit->fresh()->duration_seconds)->toBe(20)
        ->and($visit->fresh()->last_activity_at)->not->toBeNull();

    $otherUser = User::factory()->create();
    $this->actingAs($otherUser)
        ->withUnencryptedCookie(SiteVisitService::VISITOR_COOKIE, $visitorId)
        ->postJson(route('front.site-visits.heartbeat'), [
            'token' => $token,
            'active_seconds' => 10,
        ])->assertNotFound();

    expect($visit->fresh()->duration_seconds)->toBe(20);
});

test('heartbeat rejects oversized duration increments', function () {
    $this->postJson(route('front.site-visits.heartbeat'), [
        'token' => Str::random(64),
        'active_seconds' => 999999999,
    ])->assertUnprocessable();
});
