<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceEnglishContentLanguage
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isContentMutationRoute($request)) {
            $request->merge(['language' => config('content_language.code')]);
        }

        return $next($request);
    }

    private function isContentMutationRoute(Request $request): bool
    {
        return in_array($request->route()?->getName(), [
            'admin.about.store', 'admin.ads.store', 'admin.article.store', 'admin.banner.store',
            'admin.categories.store', 'admin.consultancy.store', 'admin.faq.store', 'admin.faq-categories.store',
            'admin.home-cards.store', 'admin.home-sections.store', 'admin.magazine.store',
            'admin.meta-tags.store', 'admin.slider.store', 'admin.tags.store', 'admin.taza-shumara.store',
            'admin.about.update', 'admin.ads.update', 'admin.article.update', 'admin.banner.update',
            'admin.categories.update', 'admin.consultancy.update', 'admin.faq.update', 'admin.faq-categories.update',
            'admin.home-cards.update', 'admin.home-sections.update', 'admin.info-pages.update', 'admin.meta-tags.update',
            'admin.magazine.update', 'admin.slider.update', 'admin.tags.update', 'admin.taza-shumara.update',
        ], true);
    }
}
