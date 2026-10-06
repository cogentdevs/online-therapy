<?php

namespace App\Services;

use App\Models\About;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\InfoPage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class PublicContentApiService
{
    /** @return Collection<int, About> */
    public function aboutSections(): Collection
    {
        return About::query()->where('language', config('content_language.code'))->where('is_active', true)->orderBy('id')
            ->get(['id', 'language', 'section_condition', 'title', 'description', 'title_2', 'description_2', 'image', 'image_2']);
    }

    /** @return Collection<int, FaqCategory> */
    public function faqCategories(): Collection
    {
        return FaqCategory::query()->where('language', $this->configuredLanguage())
            ->where('isActive', true)->orderBy('id')->get(['id', 'language', 'name', 'icon']);
    }

    /** @return array{category: FaqCategory|null, faqs: Collection<int, Faq>} */
    public function faqs(?int $categoryId): array
    {
        $categoryQuery = FaqCategory::query()->where('language', $this->configuredLanguage())->where('isActive', true);
        $category = $categoryId === null
            ? $categoryQuery->orderBy('id')->first(['id', 'language', 'name', 'icon'])
            : $categoryQuery->find($categoryId, ['id', 'language', 'name', 'icon']);

        if ($categoryId !== null && $category === null) {
            throw (new ModelNotFoundException)->setModel(FaqCategory::class, [$categoryId]);
        }

        $faqs = $category === null ? collect() : Faq::query()
            ->where('faq_category_id', $category->id)->where('language', $category->language)
            ->where('isActive', true)->orderBy('id')->get(['id', 'faq_category_id', 'question', 'answer']);

        return compact('category', 'faqs');
    }

    /** @return array{definition: array<string, mixed>, language: string, page: InfoPage|null} */
    public function legalPage(string $slug): array
    {
        $definition = config('info_pages.pages.'.$slug);

        if (! is_array($definition)) {
            throw (new ModelNotFoundException)->setModel(InfoPage::class, [$slug]);
        }

        $language = $this->configuredLanguage();
        $page = InfoPage::query()->forPage($definition['key'], $language)->first(['id', 'language', 'page', 'title', 'description']);

        return compact('definition', 'language', 'page');
    }

    public function configuredLanguage(): string
    {
        return config('content_language.code');
    }
}
