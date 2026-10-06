<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HomeArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::query()
            ->where('language', config('content_language.code'))
            ->orderBy('title')
            ->get([
                'id',
                'title',
                'publish_date',
                'show_on_latest',
                'show_on_editorial_center',
                'show_on_editorial_featured',
            ]);

        return view('admin.home-article.home-article', [
            'articles' => $articles,
            'latestArticleIds' => $articles->where('show_on_latest', true)->modelKeys(),
            'editorialCenterArticleIds' => $articles->where('show_on_editorial_center', true)->modelKeys(),
            'editorialFeaturedArticleId' => $articles->firstWhere('show_on_editorial_featured', true)?->id,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $articleExists = Rule::exists((new Article)->getTable(), 'id');
        $validated = $request->validate([
            'latest_article_ids' => ['nullable', 'array', 'max:3'],
            'latest_article_ids.*' => ['integer', 'distinct', $articleExists],
            'editorial_center_article_ids' => ['nullable', 'array', 'max:3'],
            'editorial_center_article_ids.*' => ['integer', 'distinct', $articleExists],
            'editorial_featured_article_id' => ['nullable', 'integer', $articleExists],
        ]);

        $latestArticleIds = array_map('intval', $validated['latest_article_ids'] ?? []);
        $editorialCenterArticleIds = array_map('intval', $validated['editorial_center_article_ids'] ?? []);
        $editorialFeaturedArticleId = isset($validated['editorial_featured_article_id'])
            ? (int) $validated['editorial_featured_article_id']
            : null;

        DB::transaction(function () use ($latestArticleIds, $editorialCenterArticleIds, $editorialFeaturedArticleId): void {
            Article::query()->where('show_on_latest', true)->update(['show_on_latest' => false]);

            if ($latestArticleIds !== []) {
                Article::query()->whereKey($latestArticleIds)->update(['show_on_latest' => true]);
            }

            Article::query()->where('show_on_editorial_center', true)->update(['show_on_editorial_center' => false]);

            if ($editorialCenterArticleIds !== []) {
                Article::query()->whereKey($editorialCenterArticleIds)->update(['show_on_editorial_center' => true]);
            }

            Article::query()->where('show_on_editorial_featured', true)->update([
                'show_on_editorial_featured' => false,
            ]);

            if ($editorialFeaturedArticleId !== null) {
                Article::query()->whereKey($editorialFeaturedArticleId)->update([
                    'show_on_editorial_featured' => true,
                ]);
            }
        });

        return redirect()
            ->route('admin.home-article.index')
            ->with('status', 'Home Article settings updated successfully.');
    }
}
