<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'language',
    'magazine_id',
    'title',
    'issue_number',
    'publish_date',
    'short_description',
    'image',
    'article',
    'isFree',
    'free_until',
    'isFeatured',
    'show_on_latest',
    'show_on_editorial_center',
    'show_on_editorial_featured',
    'show_visit_counter',
    'isActive',
    'status',
    'scheduled_at',
    'published_at',
])]
class Article extends Model
{
    use HasAuditOwnership;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_SCHEDULED = 'scheduled';

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function magazine(): BelongsTo
    {
        return $this->belongsTo(Magazine::class);
    }

    public function ownerAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_admin_id');
    }

    /** @var array<string, mixed> */
    protected $attributes = [
        'isFree' => false,
        'isFeatured' => false,
        'show_on_latest' => false,
        'show_on_editorial_center' => false,
        'show_on_editorial_featured' => false,
        'show_visit_counter' => false,
        'isActive' => true,
        'status' => self::STATUS_DRAFT,
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_category_maps')->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tags::class, 'article_tag_maps', 'article_id', 'tag_id')->withTimestamps();
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'article_author_maps')->withTimestamps();
    }

    public function relatedArticles(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'related_articles',
            'article_id',
            'related_article_id',
        )->withTimestamps();
    }

    public function tazaShumaraPlacements(): HasMany
    {
        return $this->hasMany(TazaShumaraArticle::class);
    }

    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function contentVisits(): MorphMany
    {
        return $this->morphMany(UserContentVisit::class, 'visitable');
    }

    public function siteVisits(): MorphMany
    {
        return $this->morphMany(SiteVisit::class, 'visitable');
    }

    public function searchContents(): MorphMany
    {
        return $this->morphMany(SearchContent::class, 'content', 'content_type', 'content_id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'publish_date' => 'date',
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
            'isFree' => 'boolean',
            'free_until' => 'date',
            'isFeatured' => 'boolean',
            'show_on_latest' => 'boolean',
            'show_on_editorial_center' => 'boolean',
            'show_on_editorial_featured' => 'boolean',
            'show_visit_counter' => 'boolean',
            'isActive' => 'boolean',
        ];
    }
}
