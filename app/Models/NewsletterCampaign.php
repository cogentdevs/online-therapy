<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'short_description'])]
class NewsletterCampaign extends Model
{
    use HasAuditOwnership;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_SENDING = 'sending';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    protected $attributes = ['status' => self::STATUS_DRAFT];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime', 'sent_at' => 'datetime'];
    }

    public function contents(): HasMany
    {
        return $this->hasMany(NewsletterCampaignContent::class)->orderBy('sort_order')->orderBy('id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_SENDING => 'Sending',
            self::STATUS_SENT => 'Sent',
            self::STATUS_FAILED => 'Failed',
            default => ucfirst((string) $this->status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'text-bg-secondary',
            self::STATUS_SCHEDULED => 'text-bg-info',
            self::STATUS_SENDING => 'text-bg-warning',
            self::STATUS_SENT => 'text-bg-success',
            self::STATUS_FAILED => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }
}
