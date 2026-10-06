<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'order_no',
    'invoice_no',
    'transaction_id',
    'subscription_product_id',
    'product_for',
    'product_name',
    'currency_id',
    'price',
    'discount',
    'total',
    'payment_method',
    'payment_account_id',
    'payment_slip',
    'payment_status',
    'payment_submitted_at',
    'reviewed_at',
    'reviewed_by',
    'rejection_reason',
    'duration_value_snapshot',
    'duration_unit_snapshot',
    'start_date',
    'end_date',
    'status',
    'is_active',
])]
#[Hidden(['payment_slip'])]
class UserSubscription extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_REJECTED = 'rejected';

    public const PAYMENT_STATUS_PENDING = 'pending';

    public const PAYMENT_STATUS_APPROVED = 'approved';

    public const PAYMENT_STATUS_REJECTED = 'rejected';

    public function hasFinalInvoice(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE || ! $this->is_active) {
            return false;
        }

        if ($this->payment_status === self::PAYMENT_STATUS_APPROVED) {
            return filled($this->invoice_no);
        }

        return $this->payment_status === null;
    }

    public function scopeCurrentlyActive(Builder $query): Builder
    {
        $today = today()->toDateString();

        return $query
            ->where('is_active', true)
            ->where('status', self::STATUS_ACTIVE)
            ->where(function (Builder $query) use ($today): void {
                $query->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->where(function (Builder $query) use ($today): void {
                $query->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
            });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionProduct(): BelongsTo
    {
        return $this->belongsTo(SubscriptionProduct::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(PaymentAccount::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function subscriptionTypes(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionType::class, 'user_sub_types')
            ->withTimestamps();
    }

    public function expiryReminders(): HasMany
    {
        return $this->hasMany(SubscriptionExpiryReminder::class);
    }

    public function expiryNotification(): HasOne
    {
        return $this->hasOne(SubscriptionExpiryNotification::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'payment_submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'duration_value_snapshot' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
