<?php

namespace App\Services;

use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\MetaTag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class FrontendSharedDataService
{
    public function generalSetting(): ?GeneralSetting
    {
        try {
            return Schema::hasTable((new GeneralSetting)->getTable())
                ? GeneralSetting::query()->with('defaultLanguage:id,name,code,is_default,is_active')->first()
                : null;
        } catch (Throwable) {
            return null;
        }
    }

    /** @return Collection<int, Language> */
    public function activeLanguages(): Collection
    {
        try {
            return Schema::hasTable((new Language)->getTable())
                ? Language::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code', 'is_default', 'is_active'])
                : collect();
        } catch (Throwable) {
            return collect();
        }
    }

    /** @return Collection<int, Category> */
    public function navigationCategories(): Collection
    {
        try {
            if (! Schema::hasTable((new Category)->getTable())) {
                return collect();
            }

            $categories = Category::query()
                ->where('language', config('content_language.code'))
                ->where('isActive', true)
                ->whereNotNull('name')
                ->where('name', '!=', '')
                ->orderBy('id')
                ->get(['id', 'language', 'name']);

            $categorySlugs = collect();

            if (Schema::hasTable((new MetaTag)->getTable())) {
                $categorySlugs = MetaTag::query()
                    ->where('table_name', (new Category)->getTable())
                    ->where('language', config('content_language.code'))
                    ->whereIn('table_id', $categories->modelKeys())
                    ->pluck('slug_url', 'table_id');
            }

            $categories->each(function (Category $category) use ($categorySlugs): void {
                $slug = $categorySlugs->get($category->id)
                    ?: Str::of($category->name)
                        ->squish()
                        ->replaceMatches('/[^\pL\pN]+/u', '-')
                        ->trim('-')
                        ->lower()
                        ->toString();
                $slug = $slug ?: 'mozu-'.$category->id;

                $category->setAttribute('navigation_slug', $slug);
                $category->setAttribute('navigation_url', route('mozu-detail', [
                    'id' => $category->id,
                    'mozuName' => $slug,
                ]));
            });

            return $categories;
        } catch (Throwable) {
            return collect();
        }
    }
}
