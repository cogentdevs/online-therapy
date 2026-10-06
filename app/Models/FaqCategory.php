<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['language', 'name', 'icon', 'isActive'])]
class FaqCategory extends Model
{
    use HasAuditOwnership;

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['isActive' => 'boolean'];
    }
}
