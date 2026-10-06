<?php

use App\Http\Controllers\FrontController;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\HomeCard;
use App\Models\HomeCardTitlePosition;
use App\Models\HomePageSectionHeading;
use App\Models\Magazine;
use App\Models\SearchContent;
use App\Models\SiteVisit;
use App\Models\Slider;
use App\Models\TazaShumara;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

uses(LazilyRefreshDatabase::class);

function homepageContentVisit(Article|Magazine $content): SiteVisit
{
    return SiteVisit::query()->create([
        'visitor_id' => (string) Str::uuid(),
        'public_token_hash' => hash('sha256', Str::random(64)),
        'page_key' => $content instanceof Magazine ? 'shumara-detail' : 'mazmoon-detail',
        'route_name' => $content instanceof Magazine ? 'shumara-detail' : 'mazmoon-detail',
        'visitable_type' => $content->getMorphClass(),
        'visitable_id' => $content->id,
        'started_at' => now(),
    ]);
}

function homepageSearchClick(Article|Magazine $content, string $keyword = 'کاروبار'): SearchContent
{
    return SearchContent::query()->create([
        'content_type' => $content->getMorphClass(),
        'content_id' => $content->id,
        'search_keyword' => $keyword,
        'created_at' => now(),
    ]);
}

test('homepage shows top four searched magazines and articles below audio and above promotion', function () {
    $this->withoutVite();
    $magazines = collect(range(1, 5))->map(fn (int $rank): Magazine => Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Searched Magazine '.$rank,
        'issue_number' => 'MS-'.$rank,
        'publish_date' => '2026-08-'.str_pad((string) $rank, 2, '0', STR_PAD_LEFT),
        'published_at' => '2026-08-01 10:00:00',
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
    ]));
    $articles = collect(range(1, 5))->map(fn (int $rank): Article => Article::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Searched Article '.$rank,
        'publish_date' => '2026-08-'.str_pad((string) $rank, 2, '0', STR_PAD_LEFT),
        'published_at' => '2026-08-01 10:00:00',
        'article' => 'Body',
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
    ]));

    $magazines->each(function (Magazine $magazine, int $index): void {
        for ($click = 0; $click < 5 - $index; $click++) {
            homepageSearchClick($magazine);
        }
    });
    $articles->each(function (Article $article, int $index): void {
        for ($click = 0; $click < 5 - $index; $click++) {
            homepageSearchClick($article);
        }
    });
    $searchClickCount = SearchContent::query()->count();

    $response = $this->get(route('frontend.home'))->assertSuccessful();
    $html = $response->getContent();

    $response->assertSee('سب سے زیادہ تلاش کیے گئے')
        ->assertDontSee('id="most-searched-magazines-tab"', false)
        ->assertDontSee('id="most-searched-magazines" role="tabpanel"', false)
        ->assertSee('id="most-searched-articles-tab"', false)
        ->assertSee('id="most-searched-articles" role="tabpanel"', false)
        ->assertDontSee(route('shumara-detail', ['id' => $magazines[0]->id, 'slug' => 'searched-magazine-1']), false)
        ->assertSee(route('mazmoon-detail', ['id' => $articles[0]->id, 'slug' => 'searched-article-1']), false)
        ->assertDontSee('Searched Magazine 1')
        ->assertDontSee('Searched Magazine 5');

    expect(substr_count($html, 'data-most-searched-count'))->toBe(4)
        ->and(substr_count($html, 'data-ranked-content'))->toBe(2)
        ->and(strpos($html, 'front-audio-newsletter'))->toBeLessThan(strpos($html, 'front-most-searched'))
        ->and($html)->not->toContain('front-popular-topics')
        ->and(substr_count($html, 'fa-solid fa-magnifying-glass'))->toBeGreaterThanOrEqual(5)
        ->and($html)->not->toContain('/search-content/')
        ->and(SearchContent::query()->count())->toBe($searchClickCount);
});

test('homepage shows top four magazines and articles ordered by existing site visits', function () {
    $this->withoutVite();
    $magazines = collect(range(1, 5))->map(fn (int $rank): Magazine => Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Viewed Magazine '.$rank,
        'issue_number' => 'MV-'.$rank,
        'publish_date' => '2026-08-'.str_pad((string) $rank, 2, '0', STR_PAD_LEFT),
        'published_at' => '2026-08-01 10:00:00',
        'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED,
        'show_visit_counter' => false,
    ]));
    $articles = collect(range(1, 5))->map(fn (int $rank): Article => Article::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Viewed Article '.$rank,
        'publish_date' => '2026-08-'.str_pad((string) $rank, 2, '0', STR_PAD_LEFT),
        'published_at' => '2026-08-01 10:00:00',
        'article' => 'Body',
        'isActive' => true,
        'status' => Article::STATUS_PUBLISHED,
        'show_visit_counter' => false,
    ]));

    $magazines->each(function (Magazine $magazine, int $index): void {
        foreach (range(1, 5 - $index) as $unused) {
            homepageContentVisit($magazine);
        }
    });
    $articles->each(function (Article $article, int $index): void {
        foreach (range(1, 5 - $index) as $unused) {
            homepageContentVisit($article);
        }
    });
    $contentVisitCount = SiteVisit::query()->whereNotNull('visitable_type')->count();

    $response = $this->get(route('frontend.home'))->assertSuccessful();
    $html = $response->getContent();

    $response->assertSee('سب سے زیادہ دیکھے گئے')
        ->assertDontSee('id="most-viewed-magazines-tab"', false)
        ->assertDontSee('id="most-viewed-magazines" role="tabpanel"', false)
        ->assertSee('id="most-viewed-articles-tab"', false)
        ->assertSee('id="most-viewed-articles" role="tabpanel"', false)
        ->assertDontSee(route('shumara-detail', ['id' => $magazines[0]->id, 'slug' => 'viewed-magazine-1']), false)
        ->assertSee(route('mazmoon-detail', ['id' => $articles[0]->id, 'slug' => 'viewed-article-1']), false)
        ->assertDontSee('Viewed Magazine 1')
        ->assertDontSee('Viewed Magazine 5')
        ->assertDontSee('Viewed Article 5');

    expect(substr_count($html, 'data-most-viewed-count'))->toBe(4)
        ->and(strpos($html, 'Viewed Article 1'))->toBeLessThan(strpos($html, 'Viewed Article 2'))
        ->and(strpos($html, 'front-most-viewed'))->toBeLessThan(strpos($html, 'front-audio-newsletter'))
        ->and(SiteVisit::query()->whereNotNull('visitable_type')->count())->toBe($contentVisitCount);
});

test('the Urdu RTL homepage renders with isolated frontend assets', function () {
    $weeklyMagazine = Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'ہفتہ وار ڈیجیٹل میگزین',
        'cover_image' => 'images/frontend-images/magazine/latest-cover.svg',
        'publish_date' => '2026-04-01',
        'isActive' => true,
    ]);
    TazaShumara::query()->create([
        'language' => 'ur',
        'magazine_id' => $weeklyMagazine->id,
        'is_active' => true,
    ]);

    HomePageSectionHeading::query()->create([
        'language' => 'ur',
        'section_name' => 'categories',
        'short_title' => 'تازہ انتخاب',
        'main_title' => 'مقبول موضوعات',
    ]);
    HomePageSectionHeading::query()->create([
        'language' => 'ur',
        'section_name' => 'audio',
        'main_title' => 'آڈیو انٹرویوز',
    ]);

    HomeCard::query()->create([
        'language' => 'ur',
        'position' => 'below slider',
        'title' => 'Homepage card',
        'is_active' => true,
    ]);
    HomeCard::query()->create([
        'language' => 'ur',
        'position' => 'above footer',
        'title' => 'Homepage footer card',
        'is_active' => true,
    ]);

    foreach (['top', 'center', 'bottom'] as $position) {
        Slider::query()->create([
            'content_position' => $position,
            'main_heading' => ucfirst($position).' slider',
            'image' => 'images/backend-images/slider/'.$position.'.webp',
            'isActive' => true,
        ]);
    }

    $homeRoute = Route::getRoutes()->getByName('frontend.home');

    expect($homeRoute)
        ->not->toBeNull()
        ->and($homeRoute->uri())->toBe('/')
        ->and($homeRoute->getActionName())->toBe(FrontController::class.'@index');

    $response = $this->get(route('frontend.home'));

    $response->assertOk()
        ->assertSee('<html lang="ur" dir="rtl">', false)
        ->assertSee('nav-link active', false)
        ->assertDontSee('front-nav-parent-link active', false)
        ->assertSee('resources/sass/front/front.scss')
        ->assertSee('resources/js/front/front.js')
        ->assertSee('col-lg-8 order-1 order-lg-2')
        ->assertSee('col-lg-4 order-2 order-lg-1')
        ->assertSee('front-slider-content--top')
        ->assertSee('front-slider-content--center')
        ->assertSee('front-slider-content--bottom')
        ->assertSee('front-home-card__title--right')
        ->assertSee('data-bs-ride="carousel"', false)
        ->assertDontSee('data-front-mobile-submenu-toggle', false)
        ->assertDontSee('front-home-ads__container')
        ->assertDontSee('front-ad-slot--home', false)
        ->assertDontSee('728 × 90')
        ->assertDontSee('images/frontend-images/ads/header-ad.svg')
        ->assertDontSee('images/frontend-images/ads/horizontal-ad.svg')
        ->assertDontSee('front-editorial-magazine__container')
        ->assertDontSee('front-weekly-card__button')
        ->assertDontSee('front-magazine-promo')
        ->assertSee('front-audio-newsletter__container')
        ->assertDontSee('front-audio-interview-card__play')
        ->assertSee('front-newsletter__button')
        ->assertDontSee('front-audio-card')
        ->assertDontSee('front-home-promo__banner')
        ->assertDontSee('front-popular-topics__container')
        ->assertSee('front-home-banners-login__container')
        ->assertSee('front-home-login-card__button')
        ->assertDontSee('class="front-home-banner"', false)
        ->assertDontSee('مقبول موضوعات')
        ->assertDontSee('<h2 id="popular-topics-heading">مقبول موضوعات</h2>', false)
        ->assertDontSee('<span class="front-section-heading__eyebrow">تازہ انتخاب</span>', false)
        ->assertDontSee('ہفتہ وار ڈیجیٹل میگزین')
        ->assertDontSee('آڈیو انٹرویوز');

    expect(substr_count($response->getContent(), 'front-home-section-spacing'))->toBe(6)
        ->and($response->getContent())->toContain('<section class="front-hero"');
});

test('homepage keeps side by side advertisement banners hidden while retaining login', function () {
    $banner = Banner::query()->create([
        'language' => 'ur',
        'type' => 'side-by-side',
        'position' => 'center',
        'image' => 'images/frontend-images/banners/home-banner-one.svg',
        'image_2' => 'images/frontend-images/banners/home-banner-two.svg',
        'isActive' => true,
    ]);

    $this->get(route('frontend.home'))
        ->assertSuccessful()
        ->assertDontSee('images/frontend-images/banners/home-banner-one.svg')
        ->assertDontSee('images/frontend-images/banners/home-banner-two.svg')
        ->assertDontSee('class="front-home-banner"', false)
        ->assertSee('front-home-login-card', false);

    $banner->delete();

    $response = $this->get(route('frontend.home'))->assertSuccessful();

    expect($response->getContent())->not->toContain('500 × 350')
        ->toContain('front-home-login-card');
});

test('homepage keeps the weekly magazine card hidden while retaining its data and routes', function () {
    $inactiveMagazine = Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Inactive Magazine',
        'cover_image' => 'images/backend-images/magazines/inactive.webp',
        'publish_date' => '2026-08-01',
        'isActive' => true,
    ]);
    TazaShumara::query()->create([
        'language' => 'ur',
        'magazine_id' => $inactiveMagazine->id,
        'is_active' => false,
    ]);

    $magazine = Magazine::query()->forceCreate([
        'language' => 'ur',
        'title' => 'Active Urdu Magazine',
        'issue_number' => 'HOME-2026-01',
        'cover_image' => 'images/backend-images/magazines/original.webp',
        'publish_date' => '2026-09-03',
        'isActive' => true,
    ]);
    $tazaShumara = TazaShumara::query()->create([
        'language' => 'ur',
        'magazine_id' => $magazine->id,
        'cover_image' => 'images/backend-images/taza-shumara/custom.webp',
        'show_title' => true,
        'is_active' => true,
    ]);

    $response = $this->get(route('frontend.home'));

    $response->assertSuccessful()
        ->assertDontSee('Active Urdu Magazine')
        ->assertDontSee('HOME-2026-01')
        ->assertDontSee('images/backend-images/magazines/original.webp')
        ->assertDontSee('front-weekly-card', false)
        ->assertDontSee('images/backend-images/taza-shumara/custom.webp')
        ->assertDontSee('Inactive Magazine')
        ->assertDontSee(route('taza.shumara'), false)
        ->assertDontSee(route('sabqa-shumare'), false)
        ->assertDontSee(route('shumara-detail', ['id' => $magazine->id, 'slug' => 'active-urdu-magazine']), false);

    expect(Route::has('taza.shumara'))->toBeTrue()
        ->and(Route::has('sabqa-shumare'))->toBeTrue()
        ->and(Route::has('shumara-detail'))->toBeTrue();

    $tazaShumara->update(['show_title' => false]);

    $this->get(route('frontend.home'))
        ->assertSuccessful()
        ->assertDontSee('images/backend-images/magazines/original.webp')
        ->assertDontSee('Active Urdu Magazine');
});

test('homepage section heading settings render independently with physical RTL alignment', function () {
    HomeCard::query()->create([
        'language' => 'ur',
        'position' => 'below slider',
        'title' => 'Below card remains visible',
        'is_active' => true,
    ]);
    HomeCard::query()->create([
        'language' => 'ur',
        'position' => 'above footer',
        'title' => 'Above card remains visible',
        'is_active' => true,
    ]);
    HomeCardTitlePosition::query()->create([
        'language' => 'ur',
        'card_position' => 'below slider',
        'title_position' => 'left',
    ]);
    HomeCardTitlePosition::query()->create([
        'language' => 'ur',
        'card_position' => 'above footer',
        'title_position' => 'center',
    ]);

    foreach ([
        ['latest_articles', null, 'Dynamic Latest', null, null],
        ['below_slider_home_cards', null, null, null, 'center'],
        ['editorial', null, 'Dynamic Editorial', null, 'left'],
        ['weekly_magazine', null, 'Dynamic Magazine', null, 'right'],
        ['audio', null, 'Dynamic Audio', null, 'center'],
        ['newsletter', null, null, 'Newsletter detail only', 'left'],
        ['advertise_with_us', null, 'Dynamic Promotion', 'Promotion detail', 'center'],
        ['categories', 'Categories eyebrow only', null, null, 'right'],
        ['above_footer_home_cards', 'Footer eyebrow', 'Footer heading', 'Footer detail', 'right'],
    ] as [$sectionName, $shortTitle, $mainTitle, $shortDetail, $contentPosition]) {
        HomePageSectionHeading::query()->create([
            'language' => 'ur',
            'section_name' => $sectionName,
            'short_title' => $shortTitle,
            'main_title' => $mainTitle,
            'short_detail' => $shortDetail,
            'content_position' => $contentPosition,
        ]);
    }

    $response = $this->get(route('frontend.home'));

    $response->assertSuccessful()
        ->assertSee('Dynamic Latest')
        ->assertDontSee('Dynamic Editorial')
        ->assertDontSee('Dynamic Magazine')
        ->assertDontSee('Dynamic Audio')
        ->assertSee('Newsletter detail only')
        ->assertDontSee('Dynamic Promotion')
        ->assertDontSee('Promotion detail')
        ->assertDontSee('front-home-promo__banner')
        ->assertDontSee('Categories eyebrow only')
        ->assertSee('Footer eyebrow')
        ->assertSee('Footer heading')
        ->assertSee('Footer detail')
        ->assertSee('Below card remains visible')
        ->assertSee('Above card remains visible')
        ->assertSee('front-home-heading--left')
        ->assertSee('front-home-heading--center')
        ->assertSee('front-home-heading--right')
        ->assertSee('front-home-card__title--left')
        ->assertSee('front-home-card__title--center')
        ->assertDontSee('id="cards-heading"', false)
        ->assertDontSee('id="popular-topics-heading"', false)
        ->assertDontSee('ہمارا نیوز لیٹر سبسکرائب کریں')
        ->assertDontSee('اپنے برانڈ کو ہزاروں قارئین تک پہنچائیں');
});

test('homepage renders only active Urdu categories with optional image and name content', function () {
    HomePageSectionHeading::query()->create([
        'language' => 'ur',
        'section_name' => 'categories',
        'short_title' => 'Dynamic category eyebrow',
        'main_title' => 'Dynamic category heading',
        'short_detail' => 'Dynamic category detail',
        'content_position' => 'right',
    ]);

    $category = Category::query()->create([
        'language' => 'ur',
        'name' => 'Image and name category',
        'image' => 'images/backend-images/categories/image-and-name.webp',
        'isActive' => true,
    ]);
    Category::query()->create([
        'language' => 'ur',
        'name' => null,
        'image' => 'images/backend-images/categories/image-only.webp',
        'isActive' => true,
    ]);
    Category::query()->create([
        'language' => 'ur',
        'name' => 'Name only category',
        'image' => null,
        'isActive' => true,
    ]);
    Category::query()->create([
        'language' => 'ur',
        'name' => null,
        'image' => null,
        'isActive' => true,
    ]);
    Category::query()->create([
        'language' => 'ur',
        'name' => 'Inactive category',
        'isActive' => false,
    ]);
    Category::query()->create([
        'language' => 'en',
        'name' => 'English category',
        'isActive' => true,
    ]);

    $response = $this->get(route('frontend.home'));

    $response->assertSuccessful()
        ->assertDontSee('Dynamic category eyebrow')
        ->assertDontSee('Dynamic category heading')
        ->assertDontSee('Dynamic category detail')
        ->assertDontSee('Image and name category')
        ->assertDontSee('images/backend-images/categories/image-and-name.webp')
        ->assertDontSee('images/backend-images/categories/image-only.webp')
        ->assertDontSee('Name only category')
        ->assertDontSee('/mozu/'.$category->id.'/image-and-name-category', false)
        ->assertDontSee('Inactive category')
        ->assertDontSee('English category')
        ->assertDontSee('src=""', false);

    $html = $response->getContent();

    expect(substr_count($html, 'class="front-topic-card"'))->toBe(0)
        ->and(substr_count($html, 'Image and name category'))->toBe(0)
        ->and(substr_count($html, 'images/backend-images/categories/image-only.webp'))->toBe(0)
        ->and(substr_count($html, 'Name only category'))->toBe(0);
});

test('homepage renders eligible article placements by publish date with safe linked content', function () {
    $this->withoutVite();
    $originalPublicPath = public_path();
    $temporaryPublicPath = storage_path('framework/testing/home-article-public-'.Str::uuid());
    $imageDirectory = $temporaryPublicPath.'/images/backend-images/articles';
    File::ensureDirectoryExists($imageDirectory);
    UploadedFile::fake()->image('homepage.webp', 700, 350)->move($imageDirectory, 'homepage.webp');
    app()->usePublicPath($temporaryPublicPath);

    try {
        $category = Category::query()->create([
            'language' => 'ur',
            'name' => 'Dynamic Article Category',
            'isActive' => true,
        ]);

        $createArticle = static function (array $attributes) use ($category): Article {
            $article = Article::query()->forceCreate(array_merge([
                'language' => 'ur',
                'title' => 'Homepage Article',
                'publish_date' => '2026-01-01',
                'image' => 'images/backend-images/articles/homepage.webp',
                'isActive' => true,
                'status' => Article::STATUS_PUBLISHED,
            ], $attributes));
            $article->categories()->attach($category);

            return $article;
        };

        $oldLatest = $createArticle(['title' => 'Old Latest Article', 'publish_date' => '2026-01-01', 'show_on_latest' => true]);
        $thirdLatest = $createArticle(['title' => 'Third Latest Article', 'publish_date' => '2026-02-01', 'show_on_latest' => true]);
        $secondLatest = $createArticle(['title' => 'Second Latest Article', 'publish_date' => '2026-03-01', 'show_on_latest' => true]);
        $newestLatest = $createArticle(['title' => 'Newest Latest Article', 'publish_date' => '2026-04-01', 'show_on_latest' => true]);
        $createArticle(['title' => 'Inactive Latest Article', 'show_on_latest' => true, 'isActive' => false]);
        $createArticle(['title' => 'Draft Latest Article', 'show_on_latest' => true, 'status' => Article::STATUS_DRAFT]);
        $createArticle(['title' => 'English Latest Article', 'language' => 'en', 'show_on_latest' => true]);

        $oldCenter = $createArticle(['title' => 'Old Center Article', 'publish_date' => '2026-01-05', 'show_on_editorial_center' => true]);
        $thirdCenter = $createArticle(['title' => 'Third Center Article', 'publish_date' => '2026-02-05', 'image' => null, 'show_on_editorial_center' => true]);
        $secondCenter = $createArticle(['title' => 'Second Center Article', 'publish_date' => '2026-03-05', 'show_on_editorial_center' => true]);
        $newestCenter = $createArticle(['title' => 'Newest Center Article', 'publish_date' => '2026-04-05', 'show_on_editorial_center' => true]);

        $olderFeatured = $createArticle(['title' => 'Older Featured Article', 'publish_date' => '2026-04-10', 'show_on_editorial_featured' => true]);
        $featured = $createArticle([
            'title' => 'Featured Article Title',
            'publish_date' => '2026-05-10',
            'image' => null,
            'short_description' => str_repeat('Featured description for two line visual clamping. ', 5),
            'show_on_editorial_featured' => true,
        ]);

        $response = $this->get(route('frontend.home'));

        $response->assertSuccessful()
            ->assertSee('Newest Latest Article')
            ->assertSee('Second Latest Article')
            ->assertSee('Third Latest Article')
            ->assertDontSee('Old Latest Article')
            ->assertDontSee('Inactive Latest Article')
            ->assertDontSee('Draft Latest Article')
            ->assertDontSee('English Latest Article')
            ->assertSee('Dynamic Article Category')
            ->assertSee('front-article-title-clamp')
            ->assertDontSee('front-article-description-clamp')
            ->assertDontSee('front-editorial-magazine__container')
            ->assertSee(route('mazameen'), false)
            ->assertSee(route('mazmoon-detail', ['id' => $newestLatest->id, 'slug' => 'newest-latest-article']), false)
            ->assertSee('images/backend-images/articles/homepage.webp')
            ->assertDontSee('src=""', false);

        $html = $response->getContent();

        expect(strpos($html, 'Newest Latest Article'))->toBeLessThan(strpos($html, 'Second Latest Article'))
            ->and(strpos($html, 'Second Latest Article'))->toBeLessThan(strpos($html, 'Third Latest Article'))
            ->and($oldLatest->exists)->toBeTrue()
            ->and($oldCenter->exists)->toBeTrue()
            ->and($olderFeatured->exists)->toBeTrue();
    } finally {
        app()->usePublicPath($originalPublicPath);
        File::deleteDirectory($temporaryPublicPath);
    }
});

test('homepage article placement areas render safely when no articles are selected', function () {
    $this->get(route('frontend.home'))
        ->assertSuccessful()
        ->assertDontSee('front-latest-item')
        ->assertDontSee('front-editorial-item')
        ->assertDontSee('front-editorial-main');
});

test('an article selected in multiple homepage placements keeps its image in every placement', function () {
    $this->withoutVite();
    $originalPublicPath = public_path();
    $temporaryPublicPath = storage_path('framework/testing/shared-home-article-public-'.Str::uuid());
    $imageDirectory = $temporaryPublicPath.'/images/backend-images/articles';
    File::ensureDirectoryExists($imageDirectory);
    UploadedFile::fake()->image('shared.webp', 700, 350)->move($imageDirectory, 'shared.webp');
    app()->usePublicPath($temporaryPublicPath);

    try {
        Article::query()->forceCreate([
            'language' => 'ur',
            'title' => 'Shared Placement Article',
            'publish_date' => '2026-09-02',
            'image' => 'images/backend-images/articles/shared.webp',
            'isActive' => true,
            'status' => Article::STATUS_PUBLISHED,
            'show_on_latest' => true,
            'show_on_editorial_center' => true,
            'show_on_editorial_featured' => true,
        ]);

        $html = $this->get(route('frontend.home'))
            ->assertSuccessful()
            ->assertDontSee('images/backend-images/articles/placeholder.png')
            ->getContent();

        expect(substr_count($html, 'images/backend-images/articles/shared.webp'))->toBe(2);
    } finally {
        app()->usePublicPath($originalPublicPath);
        File::deleteDirectory($temporaryPublicPath);
    }
});

test('the frontend uses the globally shared website identity settings', function () {
    $generalSetting = new GeneralSetting([
        'app_name' => 'Urdu Digest',
        'logo' => 'images/backend-images/logo/frontend-logo.webp',
        'footer_logo' => 'images/backend-images/logo/frontend-footer-logo.webp',
        'favicon' => 'images/backend-images/logo/frontend-favicon.webp',
        'email' => 'hello@example.test',
        'contact_1' => '+92 300 1234567',
        'contact_2' => '+92 321 7654321',
        'address' => 'Lahore, Pakistan',
        'facebook' => 'https://facebook.com/urdu-digest',
        'instagram' => 'https://instagram.com/urdu-digest',
        'youtube' => 'https://youtube.com/@urdu-digest',
        'linkedin' => 'https://linkedin.com/company/urdu-digest',
        'tiktok' => 'https://tiktok.com/@urdu-digest',
        'x' => 'https://x.com/urdu-digest',
        'footer_text' => 'Trusted Urdu journalism.',
        'app_section_heading' => 'Download our app',
        'app_section_text' => 'Read anywhere, anytime.',
        'play_store_icon' => 'images/backend-images/logo/play-store.webp',
        'play_store_link' => 'https://play.google.com/store/apps/details?id=urdu.digest',
        'app_store_icon' => 'images/backend-images/logo/app-store.webp',
        'app_store_link' => 'https://apps.apple.com/app/urdu-digest/id123',
    ]);

    $html = view('frontend.index', [
        'aboveFooterCards' => collect(),
        'aboveFooterTitlePosition' => 'right',
        'generalSetting' => $generalSetting,
        'belowSliderCards' => collect(),
        'belowSliderTitlePosition' => 'right',
        'sliders' => collect(),
    ])->render();

    expect($html)
        ->toContain('<title>صفحہ اول | Urdu Digest</title>')
        ->toContain('images/backend-images/logo/frontend-logo.webp')
        ->toContain('images/backend-images/logo/frontend-footer-logo.webp')
        ->toContain('images/backend-images/logo/frontend-favicon.webp')
        ->toContain('hello@example.test')
        ->toContain('+92 300 1234567')
        ->toContain('+92 321 7654321')
        ->toContain('Lahore, Pakistan')
        ->toContain('https://facebook.com/urdu-digest')
        ->toContain('fa-facebook-f')
        ->toContain('fa-x-twitter')
        ->toContain('fa-instagram')
        ->toContain('fa-youtube')
        ->toContain('https://linkedin.com/company/urdu-digest')
        ->toContain('fa-linkedin-in')
        ->toContain('fa-tiktok')
        ->toContain('Trusted Urdu journalism.')
        ->not->toContain('Download our app')
        ->not->toContain('Read anywhere, anytime.')
        ->not->toContain('images/backend-images/logo/play-store.webp')
        ->not->toContain('https://play.google.com/store/apps/details?id=urdu.digest')
        ->not->toContain('images/backend-images/logo/app-store.webp')
        ->not->toContain('https://apps.apple.com/app/urdu-digest/id123');
});

test('frontend social and app settings render independently without empty links', function () {
    $generalSetting = new GeneralSetting([
        'linkedin' => 'https://linkedin.com/company/only-profile',
        'play_store_icon' => 'images/backend-images/logo/play-store-only.webp',
        'app_store_icon' => 'images/backend-images/logo/app-store-only.webp',
        'app_store_link' => 'https://apps.apple.com/app/example/id456',
    ]);

    $html = view('frontend.index', [
        'aboveFooterCards' => collect(),
        'aboveFooterTitlePosition' => 'right',
        'generalSetting' => $generalSetting,
        'belowSliderCards' => collect(),
        'belowSliderTitlePosition' => 'right',
        'sliders' => collect(),
    ])->render();

    expect(substr_count($html, 'https://linkedin.com/company/only-profile'))->toBe(2)
        ->and(substr_count($html, 'fa-linkedin-in'))->toBe(2)
        ->and($html)->not->toContain('fa-facebook-f')
        ->and($html)->not->toContain('fa-instagram')
        ->and($html)->not->toContain('fa-youtube')
        ->and($html)->not->toContain('fa-tiktok')
        ->and($html)->not->toContain('fa-x-twitter')
        ->and($html)->not->toContain('images/backend-images/logo/play-store-only.webp')
        ->and($html)->not->toContain('images/backend-images/logo/app-store-only.webp')
        ->and($html)->not->toContain('https://apps.apple.com/app/example/id456')
        ->and($html)->not->toContain('href=""')
        ->and($html)->not->toContain('src=""');
});

test('homepage renders only active sliders with safe dynamic content and positions', function () {
    Slider::query()->create([
        'top_heading' => 'Top label',
        'main_heading' => 'First active slider',
        'bottom_text' => 'First description',
        'button_label' => 'Read first',
        'button_url' => 'https://example.test/first',
        'image' => 'images/backend-images/slider/first.webp',
        'content_position' => 'top',
        'isActive' => true,
    ]);
    Slider::query()->create([
        'main_heading' => 'Inactive slider',
        'image' => 'images/backend-images/slider/inactive.webp',
        'isActive' => false,
    ]);
    Slider::query()->create([
        'button_label' => 'Missing URL',
        'bottom_text' => 'Bottom positioned text',
        'image' => 'images/backend-images/slider/second.webp',
        'content_position' => 'bottom',
        'isActive' => true,
    ]);
    Slider::query()->create([
        'main_heading' => 'Null position fallback',
        'button_url' => 'https://example.test/no-label',
        'image' => 'images/backend-images/slider/third.webp',
        'content_position' => null,
        'isActive' => true,
    ]);
    Slider::query()->create([
        'main_heading' => 'Invalid position fallback',
        'content_position' => 'left',
        'isActive' => true,
    ]);

    $response = $this->get(route('frontend.home'));

    $response->assertSuccessful();
    $html = $response->getContent();

    expect($html)
        ->toContain('images/backend-images/slider/first.webp')
        ->toContain('images/backend-images/slider/second.webp')
        ->toContain('images/backend-images/slider/third.webp')
        ->not->toContain('images/backend-images/slider/inactive.webp')
        ->toContain('Top label')
        ->toContain('First active slider')
        ->toContain('First description')
        ->toContain('Read first')
        ->not->toContain('Missing URL')
        ->toContain('front-slider-content--top')
        ->toContain('front-slider-content--bottom')
        ->toContain('front-slider-content--center')
        ->and(substr_count($html, 'class="carousel-item active"'))->toBe(1)
        ->and(substr_count($html, 'data-bs-slide-to='))->toBe(4)
        ->and(strpos($html, 'First active slider'))->toBeLessThan(strpos($html, 'Invalid position fallback'));
});

test('homepage loads safely when no active sliders exist', function () {
    Slider::query()->create([
        'main_heading' => 'Inactive only',
        'isActive' => false,
    ]);

    $this->get(route('frontend.home'))
        ->assertSuccessful()
        ->assertDontSee('frontHeroCarousel')
        ->assertDontSee('front-home-cards__container')
        ->assertDontSee('above-footer-cards-heading')
        ->assertDontSee('id="latest-heading"', false)
        ->assertDontSee('id="editorial-heading"', false)
        ->assertDontSee('id="magazine-heading"', false)
        ->assertDontSee('id="audio-heading"', false)
        ->assertDontSee('id="newsletter-heading"', false)
        ->assertDontSee('id="home-promo-heading"', false)
        ->assertDontSee('id="popular-topics-heading"', false)
        ->assertDontSee('front-editorial-main')
        ->assertDontSee('front-audio-interview-card')
        ->assertSee('front-newsletter')
        ->assertDontSee('front-popular-topics__container');
});

test('homepage keeps the above footer cards section hidden when no active cards match that position', function () {
    HomeCard::query()->create([
        'language' => 'ur',
        'position' => 'below slider',
        'title' => 'Visible below slider card',
        'is_active' => true,
    ]);
    HomeCard::query()->create([
        'language' => 'ur',
        'position' => 'above footer',
        'title' => 'Inactive above footer card',
        'is_active' => false,
    ]);

    $this->get(route('frontend.home'))
        ->assertSuccessful()
        ->assertSee('Visible below slider card')
        ->assertDontSee('Inactive above footer card')
        ->assertDontSee('above-footer-cards-heading');
});

test('homepage applies separate safe physical alignment classes to Home Card titles', function () {
    HomeCard::query()->create([
        'language' => 'ur',
        'position' => 'below slider',
        'title' => 'Below slider alignment card',
        'is_active' => true,
    ]);
    HomeCard::query()->create([
        'language' => 'ur',
        'position' => 'above footer',
        'title' => 'Above footer alignment card',
        'is_active' => true,
    ]);
    HomeCardTitlePosition::query()->create([
        'language' => 'ur',
        'card_position' => 'below slider',
        'title_position' => 'left',
    ]);
    HomeCardTitlePosition::query()->create([
        'language' => 'ur',
        'card_position' => 'above footer',
        'title_position' => 'center',
    ]);

    $this->get(route('frontend.home'))
        ->assertSuccessful()
        ->assertSee('front-card__title front-home-card__title--left', false)
        ->assertSee('front-card__title front-home-card__title--center', false)
        ->assertDontSee('front-section-heading front-home-card__title--', false);
});

test('homepage falls back to physical right for an invalid Home Card title position', function () {
    HomeCard::query()->create([
        'language' => 'ur',
        'position' => 'below slider',
        'title' => 'Fallback alignment card',
        'is_active' => true,
    ]);
    HomeCardTitlePosition::query()->create([
        'language' => 'ur',
        'card_position' => 'below slider',
        'title_position' => 'invalid',
    ]);

    $this->get(route('frontend.home'))
        ->assertSuccessful()
        ->assertSee('front-card__title front-home-card__title--right', false);
});

test('homepage renders active home cards in their positions with partial content and responsive image fitting', function () {
    $this->withoutVite();
    $originalPublicPath = public_path();
    $temporaryPublicPath = storage_path('framework/testing/home-card-public-'.Str::uuid());
    $imageDirectory = $temporaryPublicPath.'/images/backend-images/home-cards';
    File::ensureDirectoryExists($imageDirectory);
    app()->usePublicPath($temporaryPublicPath);

    try {
        UploadedFile::fake()->image('small.png', 120, 100)->move($imageDirectory, 'small.png');
        UploadedFile::fake()->image('large.png', 700, 350)->move($imageDirectory, 'large.png');

        HomeCard::query()->create([
            'language' => 'ur',
            'position' => 'below slider',
            'image' => 'images/backend-images/home-cards/small.png',
            'is_active' => true,
        ]);
        HomeCard::query()->create([
            'language' => 'ur',
            'position' => 'below slider',
            'title' => 'Title only card',
            'is_active' => true,
        ]);
        HomeCard::query()->create([
            'language' => 'ur',
            'position' => 'below slider',
            'description' => 'A short description only card.',
            'is_active' => true,
        ]);
        HomeCard::query()->create([
            'language' => 'ur',
            'position' => 'below slider',
            'title' => 'Large image card',
            'description' => str_repeat('Long home card description for visual line clamping. ', 10),
            'image' => 'images/backend-images/home-cards/large.png',
            'is_active' => true,
        ]);
        HomeCard::query()->create([
            'language' => 'ur',
            'position' => 'below slider',
            'title' => 'Missing image file card',
            'image' => 'images/backend-images/home-cards/missing.png',
            'is_active' => true,
        ]);
        HomeCard::query()->create([
            'language' => 'ur',
            'position' => 'below slider',
            'title' => 'Inactive card',
            'is_active' => false,
        ]);
        HomeCard::query()->create([
            'language' => 'ur',
            'position' => 'above footer',
            'title' => 'Above footer icon card',
            'image' => 'images/backend-images/home-cards/small.png',
            'is_active' => true,
        ]);
        HomeCard::query()->create([
            'language' => 'ur',
            'position' => 'above footer',
            'title' => 'Above footer large card',
            'description' => str_repeat('Above footer long description for the shared clamp. ', 10),
            'image' => 'images/backend-images/home-cards/large.png',
            'is_active' => true,
        ]);
        HomeCard::query()->create([
            'language' => 'ur',
            'position' => 'above footer',
            'title' => 'Inactive above footer card',
            'is_active' => false,
        ]);

        $response = $this->get(route('frontend.home'));

        $response->assertSuccessful()
            ->assertSee('front-home-cards__container')
            ->assertSee('Title only card')
            ->assertSee('A short description only card.')
            ->assertSee('Large image card')
            ->assertSee('images/backend-images/home-cards/small.png')
            ->assertSee('images/backend-images/home-cards/large.png')
            ->assertSee('front-home-card__image--contain')
            ->assertSee('front-home-card__image--cover')
            ->assertSee('front-home-card__description')
            ->assertDontSee('images/backend-images/home-cards/missing.png')
            ->assertDontSee('Inactive card')
            ->assertSee('Above footer icon card')
            ->assertSee('Above footer large card')
            ->assertDontSee('Inactive above footer card')
            ->assertDontSee('src=""', false);

        $html = $response->getContent();

        expect(substr_count($html, 'Title only card'))->toBe(1)
            ->and(substr_count($html, 'Above footer icon card'))->toBe(2)
            ->and(substr_count($html, 'front-home-card__image--contain'))->toBe(2)
            ->and(substr_count($html, 'front-home-card__image--cover'))->toBe(2)
            ->and($html)->not->toContain('front-popular-topics');
    } finally {
        app()->usePublicPath($originalPublicPath);
        File::deleteDirectory($temporaryPublicPath);
    }
});
