<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title', 'thumbnail', 'video_link', 'short_description', 'is_free', 'is_active', 'is_share', 'status', 'published_at'])]
class Video extends Model
{
    use HasAuditOwnership;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    /** @var array<string, mixed> */
    protected $attributes = [
        'is_free' => false,
        'is_active' => true,
        'is_share' => false,
        'status' => self::STATUS_DRAFT,
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function subscriptionProducts(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionProduct::class, 'subscription_product_videos')
            ->withTimestamps();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_free' => 'boolean',
            'is_active' => 'boolean',
            'is_share' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
