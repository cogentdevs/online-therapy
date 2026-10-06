<?php

namespace App\Services;

use App\Models\SubscriptionType;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SubscriptionEntitlementService
{
    /** @return EloquentCollection<int, UserSubscription> */
    public function activeSubscriptions(User $user): EloquentCollection
    {
        return $user->userSubscriptions()
            ->currentlyActive()
            ->with(['subscriptionTypes', 'currency'])
            ->orderBy('id')
            ->get();
    }

    public function hasEntitlement(User $user, SubscriptionType|int|string $type): bool
    {
        $typeId = $this->resolveTypeId($type);

        return $typeId !== null && $user->userSubscriptions()
            ->currentlyActive()
            ->whereHas('subscriptionTypes', fn (Builder $query): Builder => $query->whereKey($typeId))
            ->exists();
    }

    /** @return Collection<int, SubscriptionType> */
    public function activeEntitlementTypes(User $user): Collection
    {
        return $this->activeSubscriptions($user)
            ->flatMap(fn (UserSubscription $subscription) => $subscription->subscriptionTypes)
            ->unique(fn (SubscriptionType $type): int => $type->getKey())
            ->values();
    }

    public function subscriptionGrantingEntitlement(User $user, SubscriptionType|int|string $type): ?UserSubscription
    {
        $typeId = $this->resolveTypeId($type);

        if ($typeId === null) {
            return null;
        }

        return $user->userSubscriptions()
            ->currentlyActive()
            ->whereHas('subscriptionTypes', fn (Builder $query): Builder => $query->whereKey($typeId))
            ->with(['subscriptionTypes' => fn ($query) => $query->whereKey($typeId)])
            ->orderBy('id')
            ->first();
    }

    private function resolveTypeId(SubscriptionType|int|string $type): ?int
    {
        if ($type instanceof SubscriptionType) {
            return $type->exists ? (int) $type->getKey() : null;
        }

        if (is_int($type) || ctype_digit($type)) {
            $typeId = (int) $type;

            return $typeId > 0 && SubscriptionType::query()->whereKey($typeId)->exists() ? $typeId : null;
        }

        $normalized = Str::lower(trim($type));
        $knownNames = match ($normalized) {
            'magazine', 'magazines' => ['magazine', 'magazines'],
            'article', 'articles' => ['article', 'articles'],
            'audio' => ['audio'],
            default => [$normalized],
        };

        return SubscriptionType::query()
            ->where(function (Builder $query) use ($normalized, $knownNames): void {
                $query->whereRaw('LOWER(name) IN ('.implode(',', array_fill(0, count($knownNames), '?')).')', $knownNames)
                    ->orWhereRaw('LOWER(slug) = ?', [$normalized]);
            })
            ->value('id');
    }
}
