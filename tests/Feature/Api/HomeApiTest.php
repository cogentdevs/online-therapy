<?php

use App\Models\Article;
use App\Models\Banner;
use App\Models\HomeCard;
use App\Models\HomeCardTitlePosition;
use App\Models\HomePageSectionHeading;
use App\Models\Slider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function homeApiArticle(string $title, string $date, array $attributes = []): Article
{
    return Article::query()->forceCreate([
        'language' => 'ur',
        'title' => $title,
        'article' => '<p>Protected article body</p>',
        'short_description' => 'Public summary',
        'publish_date' => $date,
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
        ...$attributes,
    ]);
}

test('sliders endpoint preserves current homepage eligibility order content and positions', function () {
    $first = Slider::query()->create([
        'language' => 'ur', 'top_heading' => 'Top', 'main_heading' => 'First',
        'bottom_text' => 'Description', 'button_label' => 'Read',
        'button_url' => 'https://example.test/read', 'image' => 'images/slider.webp',
        'content_position' => 'top', 'isActive' => true,
    ]);
    Slider::query()->create(['language' => 'ur', 'main_heading' => 'Inactive', 'isActive' => false]);
    $second = Slider::query()->create(['language' => 'en', 'main_heading' => 'Second', 'isActive' => true]);

    $this->getJson('/api/home/sliders')
        ->assertOk()
        ->assertJsonCount(2, 'data.sliders')
        ->assertJsonPath('data.sliders.0.id', $first->id)
        ->assertJsonPath('data.sliders.0.image_url', asset('images/slider.webp'))
        ->assertJsonPath('data.sliders.0.content_position', 'top')
        ->assertJsonPath('data.sliders.0.button_url', 'https://example.test/read')
        ->assertJsonPath('data.sliders.1.id', $second->id)
        ->assertJsonMissing(['created_by' => null]);
});

test('section headings endpoint returns only current Urdu homepage heading records keyed by section', function () {
    HomePageSectionHeading::query()->create([
        'language' => 'ur', 'section_name' => 'latest_articles', 'content_position' => 'right',
        'short_title' => 'Latest', 'main_title' => 'Latest Articles', 'short_detail' => 'Current writing',
    ]);
    HomePageSectionHeading::query()->create([
        'language' => 'en', 'section_name' => 'editorial', 'main_title' => 'English Editorial',
    ]);
    HomePageSectionHeading::query()->create([
        'language' => 'ur', 'section_name' => 'not_rendered', 'main_title' => 'Unused',
    ]);

    $this->getJson('/api/home/section-headings')
        ->assertOk()
        ->assertJsonPath('data.headings.latest_articles.main_title', 'Latest Articles')
        ->assertJsonPath('data.headings.latest_articles.content_position', 'right')
        ->assertJsonMissingPath('data.headings.editorial')
        ->assertJsonMissing(['main_title' => 'Unused']);
});

test('home cards are grouped in website order and include group title positions', function () {
    $existingImage = 'images/frontend-images/banners/home-banner-one.svg';
    HomeCard::query()->create([
        'language' => 'ur', 'position' => 'below slider', 'title' => 'Below',
        'description' => 'Below description', 'image' => $existingImage, 'is_active' => true,
    ]);
    HomeCard::query()->create([
        'language' => 'en', 'position' => 'above footer', 'title' => 'Above', 'is_active' => true,
    ]);
    HomeCard::query()->create([
        'language' => 'ur', 'position' => 'below slider', 'title' => 'Inactive', 'is_active' => false,
    ]);
    HomeCardTitlePosition::query()->create(['language' => 'ur', 'card_position' => 'below slider', 'title_position' => 'left']);
    HomeCardTitlePosition::query()->create(['language' => 'ur', 'card_position' => 'above footer', 'title_position' => 'invalid']);

    $this->getJson('/api/home/cards')
        ->assertOk()
        ->assertJsonCount(1, 'data.below_slider')
        ->assertJsonCount(1, 'data.above_footer')
        ->assertJsonPath('data.below_slider.0.title_position', 'left')
        ->assertJsonPath('data.below_slider.0.image_url', asset($existingImage))
        ->assertJsonPath('data.above_footer.0.title_position', 'right')
        ->assertJsonPath('data.title_positions.above_footer', 'right')
        ->assertJsonMissing(['title' => 'Inactive']);
});

test('latest articles use shared eligibility current flag ordering and limit without bodies', function () {
    homeApiArticle('Old', '2026-01-01', ['show_on_latest' => true]);
    $third = homeApiArticle('Third', '2026-02-01', ['show_on_latest' => true]);
    $second = homeApiArticle('Second', '2026-03-01', ['show_on_latest' => true]);
    $first = homeApiArticle('First', '2026-04-01', ['show_on_latest' => true, 'isFree' => false]);
    homeApiArticle('Draft', '2026-05-01', ['show_on_latest' => true, 'status' => Article::STATUS_DRAFT]);
    homeApiArticle('Inactive', '2026-05-02', ['show_on_latest' => true, 'isActive' => false]);
    homeApiArticle('Future', '2099-01-01', ['show_on_latest' => true]);
    homeApiArticle('Not selected', '2026-06-01', ['show_on_latest' => false]);

    $this->getJson('/api/home/latest-articles')
        ->assertOk()
        ->assertJsonCount(3, 'data.articles')
        ->assertJsonPath('data.articles.0.id', $first->id)
        ->assertJsonPath('data.articles.1.id', $second->id)
        ->assertJsonPath('data.articles.2.id', $third->id)
        ->assertJsonPath('data.articles.0.detail_url', route('api.articles.show', $first->id))
        ->assertJsonMissingPath('data.articles.0.content')
        ->assertJsonMissing(['article' => '<p>Protected article body</p>']);
});

test('editorial returns one newest featured and three newest center eligible summaries', function () {
    homeApiArticle('Older featured', '2026-01-01', ['show_on_editorial_featured' => true]);
    $featured = homeApiArticle('Featured', '2026-04-01', ['show_on_editorial_featured' => true]);
    homeApiArticle('Old center', '2026-01-01', ['show_on_editorial_center' => true]);
    $centerThree = homeApiArticle('Center three', '2026-02-01', ['show_on_editorial_center' => true]);
    $centerTwo = homeApiArticle('Center two', '2026-03-01', ['show_on_editorial_center' => true]);
    $centerOne = homeApiArticle('Center one', '2026-04-01', ['show_on_editorial_center' => true]);
    homeApiArticle('Draft center', '2026-05-01', ['show_on_editorial_center' => true, 'status' => Article::STATUS_DRAFT]);

    $this->getJson('/api/home/editorial')
        ->assertOk()
        ->assertJsonPath('data.featured.id', $featured->id)
        ->assertJsonCount(3, 'data.center')
        ->assertJsonPath('data.center.0.id', $centerOne->id)
        ->assertJsonPath('data.center.1.id', $centerTwo->id)
        ->assertJsonPath('data.center.2.id', $centerThree->id)
        ->assertJsonMissingPath('data.featured.content');
});

test('banners endpoint returns only latest active Urdu homepage side by side banner and never ads', function () {
    $firstImage = 'images/frontend-images/banners/home-banner-one.svg';
    $secondImage = 'images/frontend-images/banners/home-banner-two.svg';
    Banner::query()->create(['language' => 'ur', 'type' => 'side-by-side', 'image' => $firstImage, 'isActive' => true]);
    $latest = Banner::query()->create([
        'language' => 'ur', 'type' => 'side-by-side', 'position' => 'center',
        'image' => $firstImage, 'image_2' => $secondImage, 'isActive' => true,
    ]);
    Banner::query()->create(['language' => 'ur', 'type' => 'full', 'position' => 'category-detail-top-full', 'isActive' => true]);
    Banner::query()->create(['language' => 'en', 'type' => 'side-by-side', 'isActive' => true]);

    $this->getJson('/api/home/banners')
        ->assertOk()
        ->assertJsonCount(1, 'data.banners')
        ->assertJsonPath('data.banners.0.id', $latest->id)
        ->assertJsonPath('data.banners.0.type', 'side-by-side')
        ->assertJsonPath('data.banners.0.image_url', asset($firstImage))
        ->assertJsonPath('data.banners.0.image_2_url', asset($secondImage))
        ->assertJsonMissing(['created_by' => null]);
});

test('all home endpoints return independent safe empty states', function () {
    $this->getJson('/api/home/sliders')->assertOk()->assertJsonCount(0, 'data.sliders');
    $this->getJson('/api/home/section-headings')->assertOk()->assertJsonPath('data.headings', []);
    $this->getJson('/api/home/cards')->assertOk()
        ->assertJsonCount(0, 'data.below_slider')->assertJsonCount(0, 'data.above_footer');
    $this->getJson('/api/home/latest-articles')->assertOk()->assertJsonCount(0, 'data.articles');
    $this->getJson('/api/home/editorial')->assertOk()
        ->assertJsonPath('data.featured', null)->assertJsonCount(0, 'data.center');
    $this->getJson('/api/home/banners')->assertOk()->assertJsonCount(0, 'data.banners');
    $this->getJson('/api/home')->assertNotFound();
    $this->getJson('/api/v1/home/sliders')->assertNotFound();
    $this->getJson('/api/api/home/sliders')->assertNotFound();
});
