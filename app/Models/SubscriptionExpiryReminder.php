<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_subscription_id',
    'reminder_days',
    'sent_at',
    'status',
])]
class SubscriptionExpiryReminder extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    public function userSubscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'reminder_days' => 'integer',
            'sent_at' => 'datetime',
        ];
    }
}
