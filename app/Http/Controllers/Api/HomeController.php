<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Author;
use App\Models\Banner;
use App\Models\Category;
use App\Models\HomeCard;
use App\Models\HomePageSectionHeading;
use App\Models\Slider;
use App\Models\Tags;
use App\Services\HomeApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function __construct(private readonly HomeApiService $homeApiService) {}

    public function sliders(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => [
            'sliders' => $this->homeApiService->sliders()->map(fn (Slider $slider): array => [
                'id' => $slider->id,
                'top_heading' => $slider->top_heading,
                'main_heading' => $slider->main_heading,
                'description' => $slider->bottom_text,
                'image_url' => $this->assetUrl($slider->image),
                'button_text' => $slider->button_label,
                'button_url' => $slider->button_url,
                'content_position' => $slider->content_position,
            ])->values(),
        ]]);
    }

    public function sectionHeadings(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => [
            'headings' => $this->homeApiService->sectionHeadings()
                ->mapWithKeys(fn (HomePageSectionHeading $heading): array => [$heading->section_name => [
                    'short_title' => $heading->short_title,
                    'main_title' => $heading->main_title,
                    'short_detail' => $heading->short_detail,
                    'content_position' => $heading->content_position,
                ]]),
        ]]);
    }

    public function cards(): JsonResponse
    {
        $cards = $this->homeApiService->cards();
        $resolvePosition = static fn (mixed $position): string => in_array($position, ['center', 'left', 'right'], true)
            ? $position
            : 'right';

        return response()->json(['success' => true, 'data' => [
            'below_slider' => $cards['below_slider']->map(fn (HomeCard $card): array => $this->cardData(
                $card,
                $resolvePosition($cards['title_positions']->get('below slider')),
            ))->values(),
            'above_footer' => $cards['above_footer']->map(fn (HomeCard $card): array => $this->cardData(
                $card,
                $resolvePosition($cards['title_positions']->get('above footer')),
            ))->values(),
            'title_positions' => [
                'below_slider' => $resolvePosition($cards['title_positions']->get('below slider')),
                'above_footer' => $resolvePosition($cards['title_positions']->get('above footer')),
            ],
        ]]);
    }

    public function latestArticles(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => [
            'articles' => $this->homeApiService->latestArticles()
                ->map(fn (Article $article): array => $this->articleData($article))->values(),
        ]]);
    }

    public function editorial(): JsonResponse
    {
        $editorial = $this->homeApiService->editorial();

        return response()->json(['success' => true, 'data' => [
            'featured' => $editorial['featured'] ? $this->articleData($editorial['featured']) : null,
            'center' => $editorial['center']->map(fn (Article $article): array => $this->articleData($article))->values(),
        ]]);
    }

    public function banners(): JsonResponse
    {
        $banner = $this->homeApiService->banner();

        return response()->json(['success' => true, 'data' => [
            'banners' => $banner ? [$this->bannerData($banner)] : [],
        ]]);
    }

    /** @return array<string, mixed> */
    private function cardData(HomeCard $card, string $titlePosition): array
    {
        return [
            'id' => $card->id,
            'title' => $card->title,
            'description' => $card->description,
            'image_url' => $this->existingAssetUrl($card->image),
            'position' => $card->position,
            'title_position' => $titlePosition,
        ];
    }

    /** @return array<string, mixed> */
    private function articleData(Article $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'issue_number' => $article->issue_number,
            'short_description' => $article->short_description,
            'image_url' => $this->assetUrl($article->image),
            'published_date' => $article->publish_date?->toDateString(),
            'is_free' => (bool) $article->isFree,
            'free_until' => $article->free_until?->toDateString(),
            'view_count' => $article->show_visit_counter ? (int) $article->site_visits_count : null,
            'authors' => $article->authors->map(fn (Author $author): array => [
                'id' => $author->id,
                'name' => $author->name,
                'picture_url' => $this->assetUrl($author->picture),
            ])->values(),
            'categories' => $article->categories->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'detail_url' => route('api.categories.show', $category->id),
            ])->values(),
            'tags' => $article->tags->map(fn (Tags $tag): array => ['id' => $tag->id, 'name' => $tag->name])->values(),
            'detail_url' => route('api.articles.show', $article->id),
        ];
    }

    /** @return array<string, mixed> */
    private function bannerData(Banner $banner): array
    {
        return [
            'id' => $banner->id,
            'type' => $banner->type,
            'position' => $banner->position,
            'image_url' => $this->existingAssetUrl($banner->image),
            'image_2_url' => $this->existingAssetUrl($banner->image_2),
        ];
    }

    private function existingAssetUrl(?string $path): ?string
    {
        return filled($path) && File::isFile(public_path($path)) ? $this->assetUrl($path) : null;
    }

    private function assetUrl(?string $path): ?string
    {
        return filled($path) ? asset(ltrim(str_replace('\\', '/', $path), '/')) : null;
    }
}
