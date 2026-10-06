<?php

namespace App\Models;

use App\Models\Concerns\HasAuditOwnership;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code', 'symbol', 'isActive'])]
class Currency extends Model
{
    use HasAuditOwnership;

    /** @var array<string, mixed> */
    protected $attributes = [
        'isActive' => true,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'isActive' => 'boolean',
        ];
    }
}
