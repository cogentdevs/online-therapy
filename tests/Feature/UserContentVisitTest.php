<?php

use App\Models\Article;
use App\Models\Magazine;
use App\Models\User;
use App\Models\UserContentVisit;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow('2026-09-12 10:00:00');
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['is_active' => true]);
    $this->user->assignRole('user');
});

afterEach(fn () => Carbon::setTestNow());

function recentActivityArticle(bool $isFree = true, string $title = 'Recent Article'): Article
{
    return Article::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'publish_date' => '2026-09-01',
        'published_at' => '2026-09-01 10:00:00',
        'article' => 'Article body',
        'isFree' => $isFree,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]);
}

function recentActivityMagazine(bool $isFree = true, string $title = 'Recent Magazine'): Magazine
{
    return Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'publish_date' => '2026-09-01',
        'published_at' => '2026-09-01 10:00:00',
        'description' => 'Magazine body',
        'isFree' => $isFree,
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
    ]);
}

test('visit model resolves user article and magazine relationships', function () {
    $article = recentActivityArticle();
    $magazine = recentActivityMagazine();
    $articleVisit = $article->contentVisits()->create([
        'user_id' => $this->user->id,
        'last_visited_at' => now(),
    ]);
    $magazineVisit = $magazine->contentVisits()->create([
        'user_id' => $this->user->id,
        'last_visited_at' => now(),
    ]);

    expect($articleVisit->user->is($this->user))->toBeTrue()
        ->and($articleVisit->visitable->is($article))->toBeTrue()
        ->and($magazineVisit->visitable->is($magazine))->toBeTrue()
        ->and($this->user->contentVisits()->count())->toBe(2)
        ->and($article->contentVisits()->count())->toBe(1)
        ->and($magazine->contentVisits()->count())->toBe(1)
        ->and($articleVisit->last_visited_at)->toBeInstanceOf(Carbon::class);
});

test('authenticated article visits create once and update last visited time', function () {
    $article = recentActivityArticle();

    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'recent']))->assertOk();
    $firstVisitedAt = UserContentVisit::query()->sole()->last_visited_at;

    Carbon::setTestNow('2026-09-12 11:00:00');
    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'recent']))->assertOk();

    $visit = UserContentVisit::query()->sole();
    expect(UserContentVisit::query()->count())->toBe(1)
        ->and($visit->user_id)->toBe($this->user->id)
        ->and($visit->visitable_type)->toBe($article->getMorphClass())
        ->and($visit->visitable_id)->toBe($article->id)
        ->and($visit->last_visited_at->gt($firstVisitedAt))->toBeTrue();
});

test('authenticated magazine visits create once and update last visited time', function () {
    $magazine = recentActivityMagazine();

    $this->actingAs($this->user)->get(route('shumara-detail', [$magazine->id, 'recent']))->assertOk();
    Carbon::setTestNow('2026-09-12 11:00:00');
    $this->actingAs($this->user)->get(route('shumara-detail', [$magazine->id, 'recent']))->assertOk();

    $visit = UserContentVisit::query()->sole();
    expect(UserContentVisit::query()->count())->toBe(1)
        ->and($visit->visitable_type)->toBe($magazine->getMorphClass())
        ->and($visit->visitable_id)->toBe($magazine->id)
        ->and($visit->last_visited_at->equalTo(now()))->toBeTrue();
});

test('different users and content modules receive isolated visit rows', function () {
    $article = recentActivityArticle();
    $magazine = recentActivityMagazine();
    $otherUser = User::factory()->create(['is_active' => true]);
    $otherUser->assignRole('user');

    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'recent']))->assertOk();
    $this->actingAs($this->user)->get(route('shumara-detail', [$magazine->id, 'recent']))->assertOk();
    $this->actingAs($otherUser)->get(route('mazmoon-detail', [$article->id, 'recent']))->assertOk();

    expect(UserContentVisit::query()->count())->toBe(3)
        ->and($this->user->contentVisits()->count())->toBe(2)
        ->and($otherUser->contentVisits()->count())->toBe(1);
});

test('guest detail visits are not tracked', function () {
    $article = recentActivityArticle();
    $magazine = recentActivityMagazine();

    $this->get(route('mazmoon-detail', [$article->id, 'recent']))->assertOk();
    $this->get(route('shumara-detail', [$magazine->id, 'recent']))->assertOk();

    expect(UserContentVisit::query()->count())->toBe(0);
});

test('authenticated admin sessions are not treated as frontend user activity', function () {
    Role::findOrCreate('super-admin', 'web');
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole('super-admin');
    $article = recentActivityArticle();

    $this->actingAs($admin)->get(route('mazmoon-detail', [$article->id, 'recent']))->assertOk();

    expect(UserContentVisit::query()->count())->toBe(0);
});

test('paid content denied by existing gates is not tracked', function () {
    $article = recentActivityArticle(false);
    $magazine = recentActivityMagazine(false);

    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'paid']))
        ->assertRedirect(route('front.subscriptions'));
    $this->actingAs($this->user)->get(route('shumara-detail', [$magazine->id, 'paid']))
        ->assertRedirect(route('front.subscriptions'));

    expect(UserContentVisit::query()->count())->toBe(0);
});

test('database uniqueness rejects duplicate visits for one user and target', function () {
    $article = recentActivityArticle();
    $attributes = [
        'user_id' => $this->user->id,
        'visitable_type' => $article->getMorphClass(),
        'visitable_id' => $article->id,
        'last_visited_at' => now(),
    ];

    UserContentVisit::query()->create($attributes);

    expect(fn () => UserContentVisit::query()->create($attributes))->toThrow(QueryException::class);
});

test('tracking leaves the existing article visit counter setting unchanged', function () {
    $article = recentActivityArticle();
    $article->update(['show_visit_counter' => true]);

    $this->actingAs($this->user)->get(route('mazmoon-detail', [$article->id, 'recent']))->assertOk();

    expect($article->fresh()->show_visit_counter)->toBeTrue();
});
