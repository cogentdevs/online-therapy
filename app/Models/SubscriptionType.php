<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'isActive'])]
class SubscriptionType extends Model
{
    public function userSubscriptions(): BelongsToMany
    {
        return $this->belongsToMany(UserSubscription::class, 'user_sub_types')
            ->withTimestamps();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'isActive' => 'boolean',
        ];
    }
}
