<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Magazine;
use Illuminate\Support\Str;

class BookmarkPresentationService
{
    /** @param iterable<int, Bookmark> $bookmarks */
    public function prepare(iterable $bookmarks): void
    {
        foreach ($bookmarks as $bookmark) {
            $bookmarkable = $bookmark->bookmarkable;

            $bookmark->setAttribute('frontend_module_label', match (true) {
                $bookmarkable instanceof Article => 'مضمون',
                $bookmarkable instanceof Magazine => 'شمارہ',
                default => 'مواد',
            });
            $bookmark->setAttribute(
                'frontend_title',
                $bookmarkable instanceof Article || $bookmarkable instanceof Magazine
                    ? ($bookmarkable->title ?: 'بلا عنوان')
                    : 'مواد دستیاب نہیں ہے'
            );
            $bookmark->setAttribute('frontend_url', match (true) {
                $bookmarkable instanceof Article => route('mazmoon-detail', [
                    'id' => $bookmarkable->getKey(),
                    'slug' => Str::slug($bookmarkable->title ?? '') ?: 'article-'.$bookmarkable->getKey(),
                ]),
                $bookmarkable instanceof Magazine => route('shumara-detail', [
                    'id' => $bookmarkable->getKey(),
                    'slug' => Str::slug($bookmarkable->title ?? '') ?: 'magazine-'.$bookmarkable->getKey(),
                    ...($bookmark->pdf_page >= 1 ? ['page' => $bookmark->pdf_page] : []),
                ]),
                default => null,
            });
        }
    }
}
