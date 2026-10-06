<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Magazine;
use App\Models\UserContentVisit;
use Illuminate\Support\Str;

class UserContentVisitPresentationService
{
    /** @param iterable<int, UserContentVisit> $visits */
    public function prepare(iterable $visits): void
    {
        foreach ($visits as $visit) {
            $visitable = $visit->visitable;

            $visit->setAttribute('frontend_module_label', match (true) {
                $visitable instanceof Article => 'مضمون',
                $visitable instanceof Magazine => 'شمارہ',
                default => 'مواد',
            });
            $visit->setAttribute(
                'frontend_title',
                $visitable instanceof Article || $visitable instanceof Magazine
                    ? ($visitable->title ?: 'بلا عنوان')
                    : 'مواد دستیاب نہیں ہے'
            );
            $visit->setAttribute('frontend_url', match (true) {
                $visitable instanceof Article => route('mazmoon-detail', [
                    'id' => $visitable->getKey(),
                    'slug' => Str::slug($visitable->title ?? '') ?: 'article-'.$visitable->getKey(),
                ]),
                $visitable instanceof Magazine => route('shumara-detail', [
                    'id' => $visitable->getKey(),
                    'slug' => Str::slug($visitable->title ?? '') ?: 'magazine-'.$visitable->getKey(),
                ]),
                default => null,
            });
            $visit->setAttribute(
                'frontend_last_visited_at',
                $visit->last_visited_at === null
                    ? null
                    : $visit->last_visited_at->format('d M Y, h:i A').' PKT (UTC+5)'
            );
        }
    }
}
