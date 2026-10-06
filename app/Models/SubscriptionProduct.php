<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'product_for',
    'name',
    'currency_id',
    'price',
    'duration_value',
    'duration_unit',
    'discount_type',
    'discount_value',
    'promotion_id',
    'isActive',
])]
class SubscriptionProduct extends Model
{
    use HasAuditOwnership;

    public const FOR_PLAN = 'plan';

    public const FOR_MEMBERSHIP = 'membership';

    public function effectivePrice(): float
    {
        $price = (float) $this->price;
        $discountValue = (float) $this->discount_value;
        $discountAmount = match ($this->discount_type) {
            'percentage' => $price * min($discountValue, 100) / 100,
            'fixed' => min($discountValue, $price),
            default => 0,
        };

        return round(max(0, $price - $discountAmount), 2);
    }

    public function scopeAvailableToFrontend(Builder $query): Builder
    {
        return $query
            ->where('isActive', true)
            ->whereIn('product_for', [self::FOR_PLAN, self::FOR_MEMBERSHIP])
            ->whereHas('currency', fn (Builder $query): Builder => $query->where('isActive', true))
            ->where(function (Builder $query): void {
                $query->where('product_for', self::FOR_MEMBERSHIP)
                    ->orWhere(function (Builder $query): void {
                        $query->where('product_for', self::FOR_PLAN)
                            ->whereHas('subscriptionTypes', fn (Builder $query): Builder => $query->where('subscription_types.isActive', true));
                    });
            });
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function subscriptionTypes(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionType::class, 'subscription_product_types')
            ->withTimestamps();
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'subscription_product_videos')
            ->withTimestamps();
    }

    public function typeMappings(): HasMany
    {
        return $this->hasMany(SubscriptionProductType::class);
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'isActive' => 'boolean',
        ];
    }
}
