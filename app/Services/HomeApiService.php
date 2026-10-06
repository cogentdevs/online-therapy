<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Banner;
use App\Models\HomeCard;
use App\Models\HomeCardTitlePosition;
use App\Models\HomePageSectionHeading;
use App\Models\Slider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class HomeApiService
{
    public const HEADING_SECTIONS = [
        'latest_articles',
        'below_slider_home_cards',
        'editorial',
        'weekly_magazine',
        'audio',
        'newsletter',
        'advertise_with_us',
        'categories',
        'above_footer_home_cards',
    ];

    public function __construct(private readonly ArticleApiService $articleApiService) {}

    private function language(): string
    {
        return config('content_language.code');
    }

    /** @return Collection<int, Slider> */
    public function sliders(): Collection
    {
        return Slider::query()
            ->where('isActive', true)
            ->orderBy('id')
            ->get([
                'id', 'top_heading', 'main_heading', 'bottom_text', 'button_label',
                'button_url', 'image', 'content_position',
            ]);
    }

    /** @return Collection<int, HomePageSectionHeading> */
    public function sectionHeadings(): Collection
    {
        return HomePageSectionHeading::query()
            ->where('language', $this->language())
            ->whereIn('section_name', self::HEADING_SECTIONS)
            ->orderBy('id')
            ->get(['id', 'section_name', 'content_position', 'short_title', 'main_title', 'short_detail']);
    }

    /** @return array{below_slider: Collection<int, HomeCard>, above_footer: Collection<int, HomeCard>, title_positions: Collection<string, string>} */
    public function cards(): array
    {
        $cards = HomeCard::query()
            ->where('is_active', true)
            ->whereIn('position', ['below slider', 'above footer'])
            ->orderBy('id')
            ->get(['id', 'position', 'title', 'description', 'image']);
        $titlePositions = HomeCardTitlePosition::query()
            ->where('language', $this->language())
            ->whereIn('card_position', ['below slider', 'above footer'])
            ->pluck('title_position', 'card_position');

        return [
            'below_slider' => $cards->where('position', 'below slider')->values(),
            'above_footer' => $cards->where('position', 'above footer')->values(),
            'title_positions' => $titlePositions,
        ];
    }

    /** @return Collection<int, Article> */
    public function latestArticles(): Collection
    {
        return $this->homeArticleQuery()
            ->where('articles.show_on_latest', true)
            ->orderByDesc('articles.publish_date')
            ->orderByDesc('articles.id')
            ->limit(3)
            ->get();
    }

    /** @return array{featured: ?Article, center: Collection<int, Article>} */
    public function editorial(): array
    {
        $featured = $this->homeArticleQuery()
            ->where('articles.show_on_editorial_featured', true)
            ->orderByDesc('articles.publish_date')
            ->orderByDesc('articles.id')
            ->first();
        $center = $this->homeArticleQuery()
            ->where('articles.show_on_editorial_center', true)
            ->orderByDesc('articles.publish_date')
            ->orderByDesc('articles.id')
            ->limit(3)
            ->get();

        return compact('featured', 'center');
    }

    public function banner(): ?Banner
    {
        return Banner::query()
            ->where('language', $this->language())
            ->where('type', 'side-by-side')
            ->where('isActive', true)
            ->latest('id')
            ->first(['id', 'type', 'position', 'image', 'image_2']);
    }

    private function homeArticleQuery(): Builder
    {
        return $this->articleApiService->eligibleQuery()
            ->withCount('siteVisits')
            ->with([
                'authors' => fn (Relation $query): Relation => $query->where('isActive', true)->orderBy('name')->select(['authors.id', 'name', 'picture']),
                'categories' => fn (Relation $query): Relation => $query->where('categories.language', $this->language())->where('categories.isActive', true)->orderBy('name')->select(['categories.id', 'name']),
                'tags' => fn (Relation $query): Relation => $query->where('tags.language', $this->language())->where('tags.isActive', true)->orderBy('name')->select(['tags.id', 'name']),
            ])
            ->select([
                'articles.id', 'articles.title', 'articles.issue_number', 'articles.short_description',
                'articles.image', 'articles.publish_date', 'articles.isFree', 'articles.free_until',
                'articles.show_visit_counter', 'articles.show_on_latest',
                'articles.show_on_editorial_center', 'articles.show_on_editorial_featured',
            ]);
    }
}
