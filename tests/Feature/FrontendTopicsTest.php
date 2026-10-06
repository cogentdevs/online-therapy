<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Magazine;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function frontendTopic(string $name, bool $active = true, string $language = 'ur'): Category
{
    return Category::query()->create([
        'language' => $language,
        'name' => $name,
        'isActive' => $active,
    ]);
}

test('topics page shows active Urdu categories sixteen per page with working links and fallback icons', function () {
    $categories = collect(range(1, 18))->map(fn (int $number): Category => frontendTopic('موضوع '.$number));
    frontendTopic('غیر فعال موضوع', false);
    frontendTopic('English Topic', true, 'en');

    $response = $this->get(route('front.mozoaat'));

    $response->assertSuccessful()
        ->assertSee('موضوعات')
        ->assertSee('front-topic-list-card__icon')
        ->assertSee(route('mozu-detail', ['id' => $categories->first()->id, 'mozuName' => 'موضوع-1']), false)
        ->assertDontSee('غیر فعال موضوع')
        ->assertDontSee('English Topic');

    expect(substr_count($response->getContent(), 'class="front-topic-list-card"'))->toBe(16);

    $secondPage = $this->get(route('front.mozoaat', ['source' => 'footer', 'page' => 2]));
    $secondPage->assertSuccessful()->assertSee('source=footer', false);
    expect(substr_count($secondPage->getContent(), 'class="front-topic-list-card"'))->toBe(2);
});

test('topic content count includes only frontend visible magazines and articles', function () {
    $category = frontendTopic('شائع مواد');

    $publicMagazine = Magazine::query()->forceCreate([
        'language' => 'ur', 'title' => 'Public Magazine', 'isActive' => true,
        'status' => Magazine::STATUS_PUBLISHED, 'publish_date' => today(),
    ]);
    $draftMagazine = Magazine::query()->forceCreate([
        'language' => 'ur', 'title' => 'Draft Magazine', 'isActive' => true,
        'status' => Magazine::STATUS_DRAFT, 'publish_date' => today(),
    ]);
    $publicArticle = Article::query()->forceCreate([
        'language' => 'ur', 'title' => 'Public Article', 'article' => 'Body', 'isActive' => true,
        'status' => Article::STATUS_PUBLISHED, 'publish_date' => today(),
    ]);
    $inactiveArticle = Article::query()->forceCreate([
        'language' => 'ur', 'title' => 'Inactive Article', 'article' => 'Body', 'isActive' => false,
        'status' => Article::STATUS_PUBLISHED, 'publish_date' => today(),
    ]);

    $category->magazines()->attach([$publicMagazine->id, $draftMagazine->id]);
    $category->articles()->attach([$publicArticle->id, $inactiveArticle->id]);

    $this->get(route('front.mozoaat'))
        ->assertSuccessful()
        ->assertSee('2 مواد');
});

test('homepage navbar footer and popular topics section hide topic discovery links', function () {
    collect(range(1, 10))->each(fn (int $number): Category => frontendTopic('متحرک موضوع '.$number));

    $response = $this->get(route('frontend.home'));
    $response->assertSuccessful()
        ->assertDontSee('تمام موضوعات دیکھیں')
        ->assertDontSee(route('front.mozoaat'), false)
        ->assertDontSee('front-popular-topics__container')
        ->assertDontSee('متحرک موضوع 10');

    expect(substr_count($response->getContent(), 'class="front-topic-card"'))->toBe(0);

    preg_match('/<footer class="front-footer">(.*?)<\/footer>/s', $response->getContent(), $footerMatch);
    $footer = $footerMatch[1] ?? '';

    expect($footer)->not->toContain('متحرک موضوع 1')
        ->and($footer)->not->toContain('متحرک موضوع 5')
        ->and($footer)->not->toContain('ہفتہ وار میگزین')
        ->and($footer)->not->toContain('موضوعات')
        ->and($footer)->not->toContain('مضمون نگار')
        ->and($footer)->not->toContain('سوال پوچھیں')
        ->and($footer)->not->toContain('تشہیر کیجئے')
        ->and($footer)->not->toContain(route('front.disclaimer'))
        ->and($footer)->toContain('فوری لنکس')
        ->and($footer)->toContain('رابطہ کریں');
});

test('category URL resolves through the existing article filter flow', function () {
    $category = frontendTopic('کاروبار');

    $this->get(route('mozu-detail', ['id' => $category->id, 'mozuName' => 'کاروبار']))
        ->assertSuccessful()
        ->assertSee('کاروبار');
});
