<?php

use App\Http\Controllers\FrontController;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Magazine;
use App\Models\Tags;
use App\Models\TazaShumara;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(LazilyRefreshDatabase::class);

function frontendTazaMagazine(string $title, array $attributes = []): Magazine
{
    return Magazine::query()->forceCreate(array_merge([
        'language' => 'ur',
        'title' => $title,
        'issue_number' => 'TZ-2026-01',
        'publish_date' => '2026-09-03',
        'cover_image' => 'images/backend-images/magazines/'.$title.'.webp',
        'description' => $title.' description',
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
    ], $attributes));
}

function frontendTazaArticle(string $title): Article
{
    return Article::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'publish_date' => '2026-09-02',
        'short_description' => $title.' description',
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]);
}

test('taza shumara route renders active configuration relationships placements ad placeholders and related Magazines', function () {
    $this->withoutVite();

    $route = Route::getRoutes()->getByName('taza.shumara');
    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('taza-shumara')
        ->and($route->getActionName())->toBe(FrontController::class.'@tazaShumara');

    $magazine = frontendTazaMagazine('Main Magazine');
    $relatedMagazine = frontendTazaMagazine('Related Magazine');
    $magazine->relatedMagazines()->attach($relatedMagazine);
    $author = Author::query()->forceCreate(['name' => 'Magazine Author', 'picture' => 'images/author.webp', 'isActive' => true]);
    $category = Category::query()->forceCreate(['language' => 'ur', 'name' => 'Magazine Topic', 'isActive' => true]);
    $tag = Tags::query()->forceCreate(['language' => 'ur', 'name' => 'Magazine Tag', 'isActive' => true]);
    $magazine->authors()->attach($author);
    $magazine->categories()->attach($category);
    $magazine->tags()->attach($tag);

    $tazaShumara = TazaShumara::query()->create([
        'language' => 'ur',
        'magazine_id' => $magazine->id,
        'cover_image' => 'images/backend-images/taza-shumara/custom.webp',
        'show_title' => true,
        'show_short_description' => true,
        'is_active' => true,
    ]);
    $top = frontendTazaArticle('Top Full');
    $center = frontendTazaArticle('Center Half');
    $bottom = frontendTazaArticle('Bottom Third');
    $tazaShumara->articlePlacements()->createMany([
        ['article_id' => $bottom->id, 'display_width' => 'third', 'position' => 'bottom', 'sort_order' => 1],
        ['article_id' => $center->id, 'display_width' => 'half', 'position' => 'center', 'sort_order' => 1],
        ['article_id' => $top->id, 'display_width' => 'full', 'position' => 'top', 'sort_order' => 1],
    ]);
    $response = $this->get(route('taza.shumara'));
    $response->assertSuccessful()
        ->assertSee('Main Magazine')->assertSee('Main Magazine description')->assertSee('03 Sep 2026')->assertSee('TZ-2026-01')
        ->assertSee('images/backend-images/taza-shumara/custom.webp')
        ->assertSee($magazine->cover_image)
        ->assertSee('Magazine Author')->assertSee('Magazine Topic')->assertSee('Magazine Tag')
        ->assertSee(route('front.mazmoon-nigaar.detail', ['id' => $author->id, 'slug' => $author->name]), false)
        ->assertSee(route('mozu-detail', ['id' => $category->id, 'mozuName' => $category->name]), false)
        ->assertSee(route('sabqa-shumare', ['tag' => $tag->id]), false)
        ->assertSee('Top Full')->assertSee('Center Half')->assertSee('Bottom Third')
        ->assertSee('col-lg-6 order-1 order-lg-2', false)
        ->assertSee('col-12', false)->assertSee('col-md-6', false)->assertSee('col-md-4', false)
        ->assertSee('front-taza-compact-card--half', false)
        ->assertSee('front-taza-ads')->assertSee('front-ad-placeholder--tall')->assertSee('250 × 300')->assertSee('600 × 300')->assertSee('Related Magazine')
        ->assertSee('front-taza-related-magazine-card', false)
        ->assertSee('front-weekly-card__cover', false)
        ->assertSee('front-weekly-card__button', false)
        ->assertSee('width="300" height="500"', false)
        ->assertSee('/articles/'.$top->id.'/top-full', false)
        ->assertSee(route('shumara-detail', ['id' => $magazine->id, 'slug' => 'main-magazine']), false)
        ->assertSee(route('shumara-detail', ['id' => $relatedMagazine->id, 'slug' => 'related-magazine']), false);

    $html = $response->getContent();
    expect(strpos($html, 'Top Full'))->toBeLessThan(strpos($html, 'Center Half'))
        ->and(strpos($html, 'Center Half'))->toBeLessThan(strpos($html, 'Bottom Third'))
        ->and(substr_count($html, 'images/backend-images/taza-shumara/custom.webp'))->toBe(1)
        ->and(substr_count($html, $magazine->cover_image))->toBe(1);
});

test('taza shumara hides optional data blocks while retaining ad placeholders', function () {
    $magazine = frontendTazaMagazine('Original Cover Magazine', [
        'cover_image' => 'images/backend-images/magazines/original.webp',
    ]);
    TazaShumara::query()->create([
        'language' => 'ur',
        'magazine_id' => $magazine->id,
        'cover_image' => null,
        'show_title' => false,
        'show_short_description' => false,
        'is_active' => true,
    ]);

    $this->get(route('taza.shumara'))
        ->assertSuccessful()
        ->assertSee('col-lg-6 order-1 order-lg-2', false)
        ->assertSee($magazine->cover_image)
        ->assertDontSee('front-taza-magazine__cover')
        ->assertDontSee('Original Cover Magazine')
        ->assertDontSee('Original Cover Magazine description')
        ->assertDontSee('front-taza-authors')
        ->assertDontSee('front-taza-topic-list')
        ->assertDontSee('front-taza-tags')
        ->assertSee('front-taza-ads')
        ->assertDontSee('related-magazines-heading');
});

test('taza shumara handles a missing active record safely', function () {
    $this->get(route('taza.shumara'))
        ->assertSuccessful()
        ->assertSee('فی الحال کوئی فعال تازہ شمارہ دستیاب نہیں')
        ->assertDontSee('front-taza-layout');
});

test('frontend navbar links to the taza shumara page', function () {
    $this->withoutVite();
    $response = $this->get(route('taza.shumara'))->assertSuccessful();

    $response
        ->assertSee('front-nav-parent-link active', false)
        ->assertSee('dropdown-item active', false)
        ->assertDontSee('nav-link active" href="'.route('frontend.home'), false);
});

test('taza shumara page uses the full available width', function () {
    $styles = file_get_contents(resource_path('sass/front/_taza-shumara.scss'));
    expect($styles)->toContain('.front-taza-shumara__container { max-width: none;')->toContain('width: 100%;');
});
