<?php

namespace App\Services;

use App\Models\SubscriptionNotificationSetting;
use App\Models\SubscriptionProduct;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SubscriptionRenewalService
{
    public const STATE_AVAILABLE = 'available';

    public const STATE_ACTIVE_LOCKED = 'active_locked';

    public const STATE_RENEWAL_AVAILABLE = 'renewal_available';

    public const STATE_RENEWAL_ALREADY_SCHEDULED = 'renewal_already_scheduled';

    public const STATE_PAYMENT_PENDING = 'payment_pending';

    public function renewalWindowDays(): int
    {
        $settings = SubscriptionNotificationSetting::query()->first();

        if ($settings === null || ! $settings->isActive) {
            return 0;
        }

        return collect([
            $settings->first_reminder_days,
            $settings->second_reminder_days,
            $settings->third_reminder_days,
        ])->filter(fn (mixed $days): bool => is_numeric($days) && (int) $days > 0)
            ->map(fn (mixed $days): int => (int) $days)
            ->max() ?? 0;
    }

    /**
     * @param  Collection<int, SubscriptionProduct>  $products
     * @return Collection<int, array{state: string, current_subscription: ?UserSubscription, future_subscription: ?UserSubscription, renewal_window_days: int, remaining_days: ?int}>
     */
    public function statesFor(User $user, Collection $products): Collection
    {
        $productIds = $products->pluck('id')->map(fn (mixed $id): int => (int) $id)->all();
        $renewalWindowDays = $this->renewalWindowDays();

        if ($productIds === []) {
            return collect();
        }

        $today = today();
        $pendingProductIds = $user->userSubscriptions()
            ->whereIn('subscription_product_id', $productIds)
            ->where('status', UserSubscription::STATUS_PENDING)
            ->where('payment_status', UserSubscription::PAYMENT_STATUS_PENDING)
            ->pluck('subscription_product_id')
            ->mapWithKeys(fn (mixed $id): array => [(int) $id => true]);
        $subscriptions = $user->userSubscriptions()
            ->whereIn('subscription_product_id', $productIds)
            ->where('is_active', true)
            ->where('status', 'active')
            ->where(function (Builder $query) use ($today): void {
                $query->where(function (Builder $query) use ($today): void {
                    $query->where(function (Builder $query) use ($today): void {
                        $query->whereNull('start_date')->orWhereDate('start_date', '<=', $today->toDateString());
                    })->where(function (Builder $query) use ($today): void {
                        $query->whereNull('end_date')->orWhereDate('end_date', '>=', $today->toDateString());
                    });
                })->orWhereDate('start_date', '>', $today->toDateString());
            })
            ->orderByDesc('end_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy('subscription_product_id');

        return $products->mapWithKeys(function (SubscriptionProduct $product) use ($pendingProductIds, $subscriptions, $renewalWindowDays, $today): array {
            if ($pendingProductIds->has((int) $product->getKey())) {
                return [$product->getKey() => $this->state(self::STATE_PAYMENT_PENDING, renewalWindowDays: $renewalWindowDays)];
            }

            $sameProductSubscriptions = $subscriptions->get($product->getKey(), collect());
            $futureSubscription = $sameProductSubscriptions
                ->first(fn (UserSubscription $subscription): bool => $subscription->start_date?->isAfter($today) === true);

            if ($futureSubscription !== null) {
                return [$product->getKey() => $this->state(
                    self::STATE_RENEWAL_ALREADY_SCHEDULED,
                    futureSubscription: $futureSubscription,
                    renewalWindowDays: $renewalWindowDays,
                )];
            }

            $currentSubscription = $sameProductSubscriptions
                ->first(fn (UserSubscription $subscription): bool => $subscription->start_date === null || ! $subscription->start_date->isAfter($today));

            if ($currentSubscription === null) {
                return [$product->getKey() => $this->state(self::STATE_AVAILABLE, renewalWindowDays: $renewalWindowDays)];
            }

            $remainingDays = $currentSubscription->end_date !== null
                ? (int) $today->diffInDays($currentSubscription->end_date)
                : null;
            $renewalAvailable = $renewalWindowDays > 0
                && $remainingDays !== null
                && $remainingDays <= $renewalWindowDays;

            return [$product->getKey() => $this->state(
                $renewalAvailable ? self::STATE_RENEWAL_AVAILABLE : self::STATE_ACTIVE_LOCKED,
                currentSubscription: $currentSubscription,
                renewalWindowDays: $renewalWindowDays,
                remainingDays: $remainingDays,
            )];
        });
    }

    /**
     * @return array{state: string, current_subscription: ?UserSubscription, future_subscription: ?UserSubscription, renewal_window_days: int, remaining_days: ?int}
     */
    public function stateFor(User $user, SubscriptionProduct $product): array
    {
        return $this->statesFor($user, collect([$product]))->get($product->getKey());
    }

    /**
     * @return array{state: string, current_subscription: ?UserSubscription, future_subscription: ?UserSubscription, renewal_window_days: int, remaining_days: ?int}
     */
    private function state(
        string $state,
        ?UserSubscription $currentSubscription = null,
        ?UserSubscription $futureSubscription = null,
        int $renewalWindowDays = 0,
        ?int $remainingDays = null,
    ): array {
        return [
            'state' => $state,
            'current_subscription' => $currentSubscription,
            'future_subscription' => $futureSubscription,
            'renewal_window_days' => $renewalWindowDays,
            'remaining_days' => $remainingDays,
        ];
    }
}
