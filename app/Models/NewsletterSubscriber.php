<?php

namespace App\Models;

use Database\Factories\NewsletterSubscriberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['email'])]
class NewsletterSubscriber extends Model
{
    /** @use HasFactory<NewsletterSubscriberFactory> */
    use HasFactory;

    public const STATUS_SUBSCRIBED = 'subscribed';

    public const STATUS_UNSUBSCRIBED = 'unsubscribed';

    public const SYNC_PENDING = 'pending';

    public const SYNC_SYNCED = 'synced';

    public const SYNC_FAILED = 'failed';

    /** @var array<string, string> */
    public const STATUS_LABELS = [
        self::STATUS_SUBSCRIBED => 'Subscribed',
        self::STATUS_UNSUBSCRIBED => 'Unsubscribed',
    ];

    /** @var array<string, string> */
    public const SYNC_STATUS_LABELS = [
        self::SYNC_PENDING => 'Pending',
        self::SYNC_SYNCED => 'Synced',
        self::SYNC_FAILED => 'Failed',
    ];

    protected $attributes = [
        'status' => self::STATUS_SUBSCRIBED,
        'brevo_sync_status' => self::SYNC_PENDING,
    ];

    protected static function booted(): void
    {
        static::creating(function (NewsletterSubscriber $subscriber): void {
            $subscriber->unsubscribe_token ??= Str::random(64);
        });
    }

    protected function casts(): array
    {
        return [
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'brevo_synced_at' => 'datetime',
        ];
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function syncStatusLabel(): string
    {
        return self::SYNC_STATUS_LABELS[$this->brevo_sync_status] ?? $this->brevo_sync_status;
    }

    public function ensureUnsubscribeToken(): string
    {
        if ($this->unsubscribe_token === null || $this->unsubscribe_token === '') {
            $this->forceFill(['unsubscribe_token' => Str::random(64)])->save();
        }

        return (string) $this->unsubscribe_token;
    }

    public function maskedEmail(): string
    {
        [$local, $domain] = array_pad(explode('@', $this->email, 2), 2, '');
        $visible = mb_substr($local, 0, 1);

        return $visible.str_repeat('*', max(3, mb_strlen($local) - 1)).'@'.$domain;
    }
}
