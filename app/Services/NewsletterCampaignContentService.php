<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Magazine;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignContent;
use Illuminate\Support\Collection;

class NewsletterCampaignContentService
{
    public function eligibleArticles(): Collection
    {
        return Article::query()->where('isActive', true)->where('status', Article::STATUS_PUBLISHED)->latest('publish_date')->get(['id', 'title', 'issue_number', 'publish_date', 'short_description', 'language']);
    }

    public function eligibleMagazines(): Collection
    {
        return Magazine::query()->where('isActive', true)->where('status', Magazine::STATUS_PUBLISHED)->latest('publish_date')->get(['id', 'title', 'issue_number', 'publish_date', 'description', 'language']);
    }

    public function hydrate(NewsletterCampaign $campaign): NewsletterCampaign
    {
        $campaign->load('contents');
        $articles = Article::query()->whereIn('id', $campaign->contents->where('content_type', 'article')->pluck('content_id'))->get()->keyBy('id');
        $magazines = Magazine::query()->whereIn('id', $campaign->contents->where('content_type', 'magazine')->pluck('content_id'))->get()->keyBy('id');
        $campaign->contents->each(fn (NewsletterCampaignContent $item) => $item->setAttribute('resolved_content', $item->content_type === 'article' ? $articles->get($item->content_id) : $magazines->get($item->content_id)));

        return $campaign;
    }
}
