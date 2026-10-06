<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subscription_product_id', 'subscription_type_id'])]
class SubscriptionProductType extends Model
{
    public function subscriptionProduct(): BelongsTo
    {
        return $this->belongsTo(SubscriptionProduct::class);
    }

    public function subscriptionType(): BelongsTo
    {
        return $this->belongsTo(SubscriptionType::class);
    }
}
