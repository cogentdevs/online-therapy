<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['bank_name', 'account_title', 'iban', 'account_no', 'branch_code', 'is_active'])]
class PaymentAccount extends Model
{
    use HasAuditOwnership;

    /** @var array<string, mixed> */
    protected $attributes = [
        'is_active' => true,
    ];

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
