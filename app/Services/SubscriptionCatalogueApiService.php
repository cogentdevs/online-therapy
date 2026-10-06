<?php

namespace App\Services;

use App\Models\SubscriptionProduct;
use Illuminate\Support\Collection;

class SubscriptionCatalogueApiService
{
    /** @return Collection<int, SubscriptionProduct> */
    public function products(string $type): Collection
    {
        return SubscriptionProduct::query()->availableToFrontend()->where('product_for', $type)
            ->with([
                'currency:id,name,code,symbol,isActive',
                'subscriptionTypes' => fn ($query) => $query->where('subscription_types.isActive', true)
                    ->orderBy('subscription_types.name')->select(['subscription_types.id', 'name', 'slug', 'isActive']),
            ])->orderBy('price')->orderBy('id')->get();
    }
}
