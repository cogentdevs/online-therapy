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
    'title',
    'issue_number',
    'publish_date',
    'cover_image',
    'scheduled_at',
    'published_at',
    'description',
    'isFree',
    'free_until',
    'isFeatured',
    'show_visit_counter',
    'is_downloadable',
    'isActive',
    'status',
])]
class Magazine extends Model
{
    use HasAuditOwnership;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_SCHEDULED = 'scheduled';

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function ownerAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_admin_id');
    }

    /** @var array<string, mixed> */
    protected $attributes = [
        'isFree' => false,
        'isFeatured' => false,
        'show_visit_counter' => false,
        'is_downloadable' => false,
        'isActive' => true,
        'status' => self::STATUS_DRAFT,
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'magazine_category_maps')->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tags::class, 'magazine_tag_maps', 'magazine_id', 'tag_id')->withTimestamps();
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'magazine_author_maps')->withTimestamps();
    }

    public function relatedMagazines(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'related_magazines',
            'magazine_id',
            'related_magazine_id',
        )->withTimestamps();
    }

    public function storageLocations(): HasMany
    {
        return $this->hasMany(MediaStorageLocation::class, 'media_id')
            ->where('media_type', MediaStorageLocation::MEDIA_MAGAZINE);
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
            'show_visit_counter' => 'boolean',
            'is_downloadable' => 'boolean',
            'isActive' => 'boolean',
        ];
    }
}
