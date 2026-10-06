<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['content_type', 'content_id', 'sort_order'])]
class NewsletterCampaignContent extends Model
{
    public const TYPE_ARTICLE = 'article';

    public const TYPE_MAGAZINE = 'magazine';

    public const TYPES = [self::TYPE_ARTICLE, self::TYPE_MAGAZINE];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NewsletterCampaign::class, 'newsletter_campaign_id');
    }

    public function displayTitle(): string
    {
        return (string) data_get($this, 'resolved_content.title', 'Unavailable content');
    }

    public function description(): string
    {
        return (string) ($this->content_type === self::TYPE_ARTICLE ? data_get($this, 'resolved_content.short_description') : data_get($this, 'resolved_content.description'));
    }

    public function truncatedDescription(): string
    {
        return Str::limit(strip_tags($this->description()), 240);
    }

    public function frontendUrl(): ?string
    {
        $content = data_get($this, 'resolved_content');
        if (! $content) {
            return null;
        } $slug = Str::slug($content->title ?? '');

        return $this->content_type === self::TYPE_ARTICLE ? route('mazmoon-detail', ['id' => $content->id, 'slug' => $slug ?: 'article-'.$content->id]) : route('shumara-detail', ['id' => $content->id, 'slug' => $slug ?: 'magazine-'.$content->id]);
    }
}
